<?php

namespace App\Http\Controllers;

use App\Services\Telephony\SarvamTelephonyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SarvamWebhookController extends Controller
{
    public function __construct(
        protected SarvamTelephonyService $telephonyService
    ) {}

    /**
     * Webhook health / verification check (for GET requests from browser or gateway)
     */
    public function verify(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'healthy',
            'service' => 'Pundeer AI SalesHub — Sarvam Voice Calling Webhook',
            'supported_methods' => ['POST (for call events)', 'GET (for health verification)'],
            'endpoint' => url('/webhook/sarvam/calls'),
            'timestamp' => now()->toIso8601String(),
        ], 200);
    }

    /**
     * Handle incoming Sarvam AI Call Webhooks (Lifecycle, Transcript, Recording, Outcome)
     */
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();
        $signature = $request->header('X-Sarvam-Signature')
            ?? $request->header('webhook-signature')
            ?? $request->header('svix-signature')
            ?? $request->header('X-Signature');

        Log::info('Sarvam Call Webhook received', [
            'call_id' => $payload['call_id'] ?? $payload['session_id'] ?? null,
            'status' => $payload['status'] ?? null,
            'has_signature' => ! empty($signature),
        ]);

        try {
            $result = $this->telephonyService->handleCallWebhook($payload, $signature, $request->getContent());

            return response()->json([
                'success' => true,
                'status' => 'processed',
                'data' => $result,
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Sarvam Call Webhook error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'error' => config('app.debug') ? $e->getMessage() : 'Webhook processing encountered an error',
            ], 500);
        }
    }
}
