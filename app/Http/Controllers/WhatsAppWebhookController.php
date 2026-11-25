<?php

namespace App\Http\Controllers;

use App\Services\WhatsApp\WhatsAppCloudApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    public function __construct(
        protected WhatsAppCloudApiService $whatsAppService
    ) {}

    /**
     * Webhook Verification Challenge (GET)
     * Meta requires exact HTTP 200 response with hub.challenge in text/plain
     */
    public function verify(Request $request): Response
    {
        return $this->whatsAppService->verifyWebhook($request);
    }

    /**
     * Webhook Ingestion (POST) with X-Hub-Signature-256 validation
     */
    public function handle(Request $request): JsonResponse
    {
        // 1. Validate X-Hub-Signature-256 if Meta App Secret is configured
        $appSecret = config('services.whatsapp.app_secret');
        if (! empty($appSecret)) {
            $signatureHeader = $request->header('X-Hub-Signature-256');

            if (! $signatureHeader || ! str_starts_with($signatureHeader, 'sha256=')) {
                Log::warning('WhatsApp webhook rejected: Missing or invalid X-Hub-Signature-256 header.');

                return response()->json([
                    'error' => 'Unauthorized: Missing or invalid X-Hub-Signature-256 header',
                ], 401);
            }

            $rawBody = $request->getContent();
            $expectedSignature = 'sha256='.hash_hmac('sha256', $rawBody, $appSecret);

            if (! hash_equals($expectedSignature, $signatureHeader)) {
                Log::warning('WhatsApp webhook rejected: X-Hub-Signature-256 mismatch.');

                return response()->json([
                    'error' => 'Unauthorized: X-Hub-Signature-256 signature verification failed',
                ], 401);
            }
        }

        // 2. Process payload with strict idempotency & multi-tenant routing
        $payload = $request->all();
        $result = $this->whatsAppService->processWebhook($payload);

        return response()->json([
            'status' => 'success',
            'processed_count' => count($result),
            'items' => $result,
        ], 200);
    }
}
