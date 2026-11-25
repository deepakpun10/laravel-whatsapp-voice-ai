<?php

namespace App\Services\AI;

use App\Models\AiAgent;
use App\Models\ApiConnection;
use App\Models\Conversation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiProviderService
{
    /**
     * Run a genuine health check test against the provider API
     */
    public function testConnection(ApiConnection $connection): array
    {
        if (empty($connection->api_key)) {
            $connection->update([
                'last_health_status' => 'unconfigured',
                'last_health_checked_at' => now(),
                'last_health_error' => 'API Key is missing.',
            ]);

            return [
                'success' => false,
                'status' => 'unconfigured',
                'error' => 'API Key is not configured.',
            ];
        }

        $baseUrl = rtrim($connection->base_url ?: 'https://api.openai.com/v1', '/');
        $model = $connection->model_or_agent_id ?: 'gpt-4o-mini';

        try {
            // Test real chat completion endpoint
            $response = Http::withToken($connection->api_key)
                ->withHeaders($connection->custom_headers ?? [])
                ->timeout($connection->timeout ?: 15)
                ->post("{$baseUrl}/chat/completions", [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'user', 'content' => 'Test connection ping.'],
                    ],
                    'max_tokens' => 5,
                ]);

            if ($response->successful()) {
                $connection->update([
                    'last_health_status' => 'healthy',
                    'last_health_checked_at' => now(),
                    'last_health_error' => null,
                ]);

                return [
                    'success' => true,
                    'status' => 'healthy',
                    'message' => 'Connection verified successfully with provider API.',
                ];
            }

            $errorMessage = $response->json()['error']['message'] ?? $response->body();
            $connection->update([
                'last_health_status' => 'error',
                'last_health_checked_at' => now(),
                'last_health_error' => $errorMessage,
            ]);

            return [
                'success' => false,
                'status' => 'error',
                'error' => $errorMessage,
            ];
        } catch (\Throwable $e) {
            $connection->update([
                'last_health_status' => 'timeout',
                'last_health_checked_at' => now(),
                'last_health_error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'status' => 'timeout',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate response using assigned AI agent, persistent conversation memory, and scoped RAG chunks with fallback
     */
    public function generateAgentReply(
        AiAgent $agent,
        string $userMessage,
        array $ragChunks = [],
        ?Conversation $conversation = null
    ): array {
        $primary = $agent->apiConnection;
        $fallback = $agent->fallbackConnection;

        $ragContextText = '';
        if (! empty($ragChunks)) {
            $ragContextText = "\n\n=== RELEVANT BUSINESS KNOWLEDGE BASE (STRICT PRICING & SPECIFICATIONS) ===\n";
            foreach ($ragChunks as $chunk) {
                $ragContextText .= '- '.$chunk['content']."\n";
            }
        }

        // Build comprehensive sales-focused system instructions
        $systemPrompt = ($agent->system_instructions ?: "You are a professional sales assistant for {$agent->business->name}.")
            ."\nPersonality/Tone: {$agent->personality_tone}"
            ."\n\n=== CORE SALES CONVERSATION RULES ==="
            ."\n1. SHORT & CONCISE: Keep your response short (2 to 4 sentences maximum). WhatsApp customers prefer quick, punchy, conversational replies."
            ."\n2. ASK ONLY ONE QUESTION: Ask ONLY ONE single relevant question per message. NEVER list multiple questions or bullet points of questions."
            ."\n3. MATCH CUSTOMER LANGUAGE: Immediately adapt to the customer's language. If they write in Hindi, reply in clean natural Hindi. If Hinglish, reply in warm conversational Hinglish. If English, reply in professional English."
            ."\n4. REMEMBER CONVERSATION CONTEXT: Read the conversation history and persistent customer facts. NEVER ask for requirements, budget, or details the customer has already shared. If budget was given, acknowledge it directly and stay on topic!"
            ."\n5. GROUNDED TRUTH ONLY: Represent ONLY {$agent->business->name}. Never quote prices, discounts, or commitments not present in the Knowledge Base. If information is missing, ask for clarification or offer a phone callback."
            ."\n6. QUALIFY & HANDOVER: Help understand customer requirements step-by-step. When requirements or budget are clear, offer to arrange a call with a human sales representative."
            .$ragContextText;

        // Build multi-turn messages array with persistent memory
        $memoryService = app(ConversationMemoryService::class);
        $messagesPayload = $conversation
            ? $memoryService->buildMessagesPayload($conversation, $systemPrompt, $userMessage)
            : [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userMessage],
            ];

        $maxTokens = $agent->max_response_tokens ?: 300;

        // Try primary provider
        if ($primary && $primary->is_active && ! empty($primary->api_key)) {
            $model = $primary->model_or_agent_id ?: $agent->model ?: 'openai/gpt-oss-120b';
            $reply = $this->callProvider($primary, $model, $messagesPayload, $maxTokens);
            if ($reply['success']) {
                if ($conversation) {
                    $memoryService->updateConversationMemory($conversation, $userMessage, $reply['reply']);
                }

                return $reply;
            }
            Log::warning("Primary AI provider failed for agent {$agent->name}: ".($reply['error'] ?? ''));
        }

        // Try fallback provider
        if ($fallback && $fallback->is_active && ! empty($fallback->api_key)) {
            $fallbackModel = $fallback->model_or_agent_id ?: $agent->model ?: 'openai/gpt-oss-120b';
            $reply = $this->callProvider($fallback, $fallbackModel, $messagesPayload, $maxTokens);
            if ($reply['success']) {
                $reply['used_fallback'] = true;
                if ($conversation) {
                    $memoryService->updateConversationMemory($conversation, $userMessage, $reply['reply']);
                }

                return $reply;
            }
        }

        // If no provider configured or all failed, return clean fallback policy response
        return [
            'success' => false,
            'status' => 'provider_unconfigured',
            'reply' => "Namaste! Thank you for contacting {$agent->business->name}. Our sales team has received your enquiry and will respond shortly.",
            'error' => 'No active AI provider configured with valid credentials.',
        ];
    }

    protected function callProvider(ApiConnection $conn, string $model, array $messages, int $maxTokens): array
    {
        $baseUrl = rtrim($conn->base_url ?: 'https://api.openai.com/v1', '/');

        try {
            $response = Http::withToken($conn->api_key)
                ->withHeaders($conn->custom_headers ?? [])
                ->timeout($conn->timeout ?: 30)
                ->post("{$baseUrl}/chat/completions", [
                    'model' => $model,
                    'messages' => $messages,
                    'max_tokens' => $maxTokens,
                    'temperature' => 0.4,
                ]);

            if ($response->successful()) {
                $text = $response->json()['choices'][0]['message']['content'] ?? '';

                return [
                    'success' => true,
                    'reply' => trim($text),
                    'provider' => $conn->name,
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['error']['message'] ?? $response->body(),
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
