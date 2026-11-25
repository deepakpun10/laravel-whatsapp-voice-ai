<?php

namespace App\Services\WhatsApp;

use App\Models\AiAgent;
use App\Models\Business;
use App\Models\Contact;
use App\Models\Conversation;
use App\Models\InteractiveAgentSetting;
use App\Models\InteractiveAutomationRule;
use App\Models\InteractiveMessageButton;
use App\Models\InteractiveMessageEvent;
use App\Models\InteractiveMessageRow;
use App\Models\InteractiveMessageTemplate;
use App\Models\Message;
use App\Models\WhatsappAccount;
use App\Services\AI\AiProviderService;
use App\Services\Knowledge\RagRetrievalService;
use App\Services\Routing\BusinessRoutingEngine;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppCloudApiService
{
    public function __construct(
        protected BusinessRoutingEngine $routingEngine
    ) {}

    /**
     * Handle Meta Webhook Verification Challenge (GET request)
     */
    public function verifyWebhook(Request $request): Response
    {
        // Support both hub_mode (PHP auto-converted dots) and raw hub.mode in query/input
        $mode = $request->query('hub_mode') ?? $request->query('hub.mode') ?? $request->input('hub_mode') ?? $request->input('hub.mode');
        $token = $request->query('hub_verify_token') ?? $request->query('hub.verify_token') ?? $request->input('hub_verify_token') ?? $request->input('hub.verify_token');
        $challenge = $request->query('hub_challenge') ?? $request->query('hub.challenge') ?? $request->input('hub_challenge') ?? $request->input('hub.challenge');

        $configuredToken = config('services.whatsapp.webhook_verify_token')
            ?: env('WHATSAPP_WEBHOOK_VERIFY_TOKEN')
            ?: 'd29cfa32157646f5f478cf36a87528d8f59f';

        // 1. Direct browser access without parameters (developer testing if endpoint is reachable)
        if (empty($mode) && empty($token)) {
            return response("WhatsApp Webhook Endpoint is Active.\nExpected format from Meta: ?hub.mode=subscribe&hub.challenge=CHALLENGE&hub.verify_token=TOKEN", 200)
                ->header('Content-Type', 'text/plain');
        }

        // 2. Meta Verification Challenge
        if ($mode === 'subscribe' && ! empty($token)) {
            $accountExists = false;
            $accounts = WhatsappAccount::whereNotNull('webhook_verify_token')->get();
            foreach ($accounts as $account) {
                if (hash_equals(trim($account->webhook_verify_token), trim((string) $token))) {
                    $accountExists = true;
                    break;
                }
            }

            $tokenMatchesConfigured = ! empty($configuredToken) && hash_equals(trim($configuredToken), trim((string) $token));
            $fallbackMatches = hash_equals('pundeer_saleshub_verify_token', trim((string) $token));

            if ($tokenMatchesConfigured || $accountExists || $fallbackMatches) {
                return response((string) $challenge, 200)->header('Content-Type', 'text/plain');
            }
        }

        return response('Forbidden: Invalid verify token', 403)->header('Content-Type', 'text/plain');
    }

    /**
     * Process Incoming Webhook Event (POST request) with strict idempotency
     */
    public function processWebhook(array $payload): array
    {
        $processed = [];

        $entries = $payload['entry'] ?? [];
        foreach ($entries as $entry) {
            $changes = $entry['changes'] ?? [];
            foreach ($changes as $change) {
                $value = $change['value'] ?? [];

                // 1. Check for incoming messages
                $messages = $value['messages'] ?? [];
                $contactsMeta = $value['contacts'] ?? [];
                $metadata = $value['metadata'] ?? [];
                $phoneNumberId = $metadata['phone_number_id'] ?? null;

                $account = WhatsappAccount::where('phone_number_id', $phoneNumberId)->first()
                    ?? WhatsappAccount::where('is_shared', true)->first();

                // Process message status updates (sent, delivered, read, failed)
                $statuses = $value['statuses'] ?? [];
                foreach ($statuses as $st) {
                    $wamid = $st['id'] ?? null;
                    $status = $st['status'] ?? null;
                    if ($wamid && $status) {
                        Message::where('message_id', $wamid)->update(['status' => $status]);
                    }
                }

                foreach ($messages as $msg) {
                    $wamid = $msg['id'] ?? null;

                    // STRICT IDEMPOTENCY CHECK
                    if ($wamid && Message::where('message_id', $wamid)->exists()) {
                        Log::info("WhatsApp webhook duplicate message skipped: {$wamid}");

                        continue;
                    }

                    $fromPhone = $msg['from'] ?? null;
                    if (! $fromPhone) {
                        continue;
                    }

                    // Extract customer name if provided in metadata
                    $customerName = 'WhatsApp Lead';
                    foreach ($contactsMeta as $cMeta) {
                        if (($cMeta['wa_id'] ?? '') === $fromPhone) {
                            $customerName = $cMeta['profile']['name'] ?? $customerName;
                            break;
                        }
                    }

                    // Extract message text or media info
                    $msgType = $msg['type'] ?? 'text';
                    $bodyText = '';

                    if ($msgType === 'text') {
                        $bodyText = $msg['text']['body'] ?? '';
                    } elseif ($msgType === 'interactive') {
                        $bodyText = $msg['interactive']['button_reply']['title']
                            ?? $msg['interactive']['list_reply']['title']
                            ?? '';
                    } else {
                        $bodyText = "[{$msgType} message received]";
                    }

                    // Find or create Contact by phone number
                    $contact = Contact::where('phone', $fromPhone)->first();
                    if (! $contact) {
                        $contact = Contact::create([
                            'name' => $customerName,
                            'phone' => $fromPhone,
                            'source' => 'whatsapp',
                            'lead_status' => 'new',
                            'whatsapp_account_id' => $account?->id,
                            'consent_status' => true,
                        ]);
                    }

                    // Evaluate route through Business Routing Engine
                    $routing = $this->routingEngine->route($bodyText, $contact, $account);

                    // Update contact business if routed
                    if ($routing->isConfident() && ! $contact->business_id) {
                        $contact->update(['business_id' => $routing->business->id]);
                    }

                    // Find or create active conversation
                    $conversation = Conversation::firstOrCreate(
                        [
                            'contact_id' => $contact->id,
                            'channel' => 'whatsapp',
                            'status' => 'active',
                        ],
                        [
                            'business_id' => $routing->business?->id ?? $contact->business_id,
                            'whatsapp_account_id' => $account?->id,
                            'ai_mode' => 'active',
                            'last_message_at' => now(),
                        ]
                    );

                    // If route changed or refined
                    if ($routing->isConfident() && $conversation->business_id !== $routing->business->id) {
                        $conversation->update(['business_id' => $routing->business->id]);
                    }

                    $conversation->update(['last_message_at' => now()]);

                    // Persist message record
                    $savedMessage = Message::create([
                        'conversation_id' => $conversation->id,
                        'sender_type' => 'contact',
                        'message_id' => $wamid,
                        'message_type' => $msgType,
                        'content' => $bodyText,
                        'status' => 'delivered',
                        'raw_payload' => $msg,
                    ]);

                    // Handle interactive button/list reply tracking and actions
                    if ($msgType === 'interactive') {
                        $this->handleInteractiveReply($msg['interactive'] ?? [], $conversation, $contact, $wamid);
                    }

                    $processed[] = [
                        'message_id' => $savedMessage->id,
                        'conversation_id' => $conversation->id,
                        'business_id' => $conversation->business_id,
                        'routed_status' => $routing->status,
                    ];

                    // Trigger Autonomous AI Auto-Reply if conversation AI mode is active
                    if ($conversation->isAiActive() && ! empty($bodyText)) {
                        $this->triggerAutonomousReply($conversation, $bodyText, $fromPhone, $account, $wamid);
                    }
                }
            }
        }

        return $processed;
    }

    /**
     * Generate and dispatch autonomous AI response for customer message
     */
    protected function triggerAutonomousReply(
        Conversation $conversation,
        string $incomingText,
        string $fromPhone,
        ?WhatsappAccount $account,
        ?string $incomingWamid = null
    ): void {
        try {
            $businessId = $conversation->business_id;
            if (! $businessId) {
                // Avoid repeating the welcome/selection prompt if AI already sent one in this conversation
                $alreadyPrompted = Message::where('conversation_id', $conversation->id)
                    ->where('sender_type', 'ai')
                    ->exists();
                if ($alreadyPrompted) {
                    return;
                }

                $activeBusinesses = Business::where('status', 'active')->orderBy('id')->limit(3)->get(['id', 'name']);
                if ($activeBusinesses->isEmpty()) {
                    return;
                }

                $bodyText = 'Welcome! Please select the business you would like to connect with:';
                $buttons = $activeBusinesses->map(fn ($b) => [
                    'type' => 'reply',
                    'reply' => [
                        'id' => 'select_business_'.$b->id,
                        'title' => Str::limit($b->name, 20, ''),
                    ],
                ])->values()->toArray();

                $payload = [
                    'messaging_product' => 'whatsapp',
                    'recipient_type' => 'individual',
                    'to' => $fromPhone,
                    'type' => 'interactive',
                    'interactive' => [
                        'type' => 'button',
                        'body' => ['text' => $bodyText],
                        'action' => ['buttons' => $buttons],
                    ],
                ];

                $replyMsg = Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_type' => 'ai',
                    'message_type' => 'interactive',
                    'content' => $bodyText,
                    'status' => 'queued',
                    'raw_payload' => $payload,
                ]);

                $sendResult = $this->dispatchWhatsAppPayload($payload, $account?->id);
                if ($sendResult['success']) {
                    $replyMsg->update([
                        'message_id' => $sendResult['message_id'] ?? null,
                        'status' => 'sent',
                    ]);
                }

                return;
            }

            // Find active WhatsApp chat agent for this business
            $agent = AiAgent::where('business_id', $businessId)
                ->where('agent_type', 'whatsapp_chat')
                ->where('is_active', true)
                ->whereNotNull('api_connection_id')
                ->first()
                ?? AiAgent::where('business_id', $businessId)
                    ->where('is_active', true)
                    ->whereNotNull('api_connection_id')
                    ->first()
                ?? AiAgent::where('is_active', true)
                    ->whereNotNull('api_connection_id')
                    ->first();

            // Ensure we use a valid text LLM connection (not a telephony connection)
            if ($agent && $agent->apiConnection?->provider_type === 'voice') {
                $chatConnection = ApiConnection::where('provider_type', 'ai')->where('is_active', true)->first();
                if ($chatConnection) {
                    $agent->setRelation('apiConnection', $chatConnection);
                }
            }

            if (! $agent || ! $agent->apiConnection?->api_key) {
                return;
            }

            // 1. Immediately send WhatsApp "typing..." indicator and mark incoming message as read
            if (! empty($incomingWamid)) {
                $this->sendTypingIndicator($incomingWamid, $account?->id);
            }

            // 2. Evaluate Interactive Automation Rules & AI Interactive settings
            $interactiveTemplate = $this->matchInteractiveRule($incomingText, $conversation, $agent);
            if ($interactiveTemplate) {
                // Natural delay for realistic delivery
                $typingDelayMs = (int) config('services.whatsapp.typing_delay_ms', 1800);
                if (! app()->runningUnitTests() && $typingDelayMs > 0) {
                    usleep(min(max(strlen($interactiveTemplate->body_text) * 10, 1000), 2000) * 1000);
                }

                // Record interactive message
                $replyMsg = Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_type' => 'ai',
                    'message_type' => 'interactive',
                    'content' => $interactiveTemplate->body_text,
                    'status' => 'queued',
                    'raw_payload' => $interactiveTemplate->toWhatsAppPayload($fromPhone),
                ]);

                // Dispatch via WhatsApp Cloud API
                $sendResult = $this->sendInteractiveMessage($fromPhone, $interactiveTemplate, $account?->id, $conversation->id);

                if ($sendResult['success']) {
                    $replyMsg->update([
                        'message_id' => $sendResult['message_id'] ?? null,
                        'status' => 'sent',
                    ]);
                }

                return;
            }

            // 3. Retrieve relevant RAG chunks
            $ragService = app(RagRetrievalService::class);
            $chunks = $ragService->retrieve($incomingText, $businessId, $agent->id);

            // 4. Generate reply using AI Provider (Groq / OpenAI / etc.) with Persistent Conversation Memory
            $aiService = app(AiProviderService::class);
            $aiResult = $aiService->generateAgentReply($agent, $incomingText, $chunks, $conversation);

            if ($aiResult['success'] && ! empty($aiResult['reply'])) {
                $replyContent = $aiResult['reply'];

                // 5. Natural human reading/typing pause so customer sees "typing..." at the top of chat
                $typingDelayMs = (int) config('services.whatsapp.typing_delay_ms', 1800);
                if (! app()->runningUnitTests() && $typingDelayMs > 0) {
                    $calculatedDelay = min(max(strlen($replyContent) * 12, 1200), 2800);
                    usleep($calculatedDelay * 1000);
                }

                // 6. Record the AI message in the database
                $replyMsg = Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_type' => 'ai',
                    'message_type' => 'text',
                    'content' => $replyContent,
                    'status' => 'queued',
                ]);

                // 7. Send via WhatsApp Cloud API (clears typing indicator and delivers message)
                $sendResult = $this->sendMessage($fromPhone, $replyContent, $account?->id);

                if ($sendResult['success']) {
                    $replyMsg->update([
                        'message_id' => $sendResult['message_id'] ?? $sendResult['wamid'] ?? null,
                        'status' => 'sent',
                    ]);
                }
            }
        } catch (\Throwable $e) {
            Log::warning("Autonomous WhatsApp AI reply could not be dispatched: {$e->getMessage()}");
        }
    }

    /**
     * Process incoming interactive button click or list row selection
     */
    protected function handleInteractiveReply(
        array $interactive,
        Conversation $conversation,
        Contact $contact,
        ?string $wamid = null
    ): void {
        $selectedId = $interactive['button_reply']['id'] ?? $interactive['list_reply']['id'] ?? null;
        $selectedTitle = $interactive['button_reply']['title'] ?? $interactive['list_reply']['title'] ?? '';

        if (! $selectedId) {
            return;
        }

        // Look up button or row
        $button = InteractiveMessageButton::where('button_id', $selectedId)->first();
        $row = (! $button) ? InteractiveMessageRow::where('row_id', $selectedId)->first() : null;
        $item = $button ?? $row;
        $templateId = $button?->template_id ?? $row?->section?->template_id;

        // If template not found from button/row, find last sent interactive message event for this conversation
        if (! $templateId) {
            $lastSentEvent = InteractiveMessageEvent::where('conversation_id', $conversation->id)
                ->where('event_type', 'sent')
                ->latest()
                ->first();
            $templateId = $lastSentEvent?->template_id;
        }

        // Record clicked event in database analytics
        InteractiveMessageEvent::create([
            'business_id' => $conversation->business_id,
            'conversation_id' => $conversation->id,
            'contact_id' => $contact->id,
            'template_id' => $templateId,
            'interactive_type' => $interactive['type'] ?? ($button ? 'button' : 'list'),
            'event_type' => 'clicked',
            'selected_id' => $selectedId,
            'selected_title' => $selectedTitle,
            'wamid' => $wamid,
            'raw_meta_payload' => $interactive,
        ]);

        // Process configured action
        if ($item) {
            $actionType = $item->action_type;
            $actionValue = $item->action_value ?: $selectedTitle;

            if ($actionType === 'set_service') {
                $conversation->updateFact('requested_service', $actionValue);
            } elseif ($actionType === 'set_budget') {
                $conversation->updateFact('budget', $actionValue);
            } elseif ($actionType === 'human_agent') {
                $conversation->pauseAi('Customer requested human representative via interactive option');
            }
        }
    }

    /**
     * Match automated interactive rule or AI assisted template
     */
    public function matchInteractiveRule(
        string $incomingText,
        Conversation $conversation,
        AiAgent $agent
    ): ?InteractiveMessageTemplate {
        $setting = $agent->interactiveSetting ?? InteractiveAgentSetting::where('ai_agent_id', $agent->id)->first();

        // If manual only or disabled, return null
        if ($setting && $setting->isManualOnly()) {
            return null;
        }

        // Check max interactive messages per conversation
        $maxPerConv = $setting?->max_interactive_per_conversation ?? 5;
        $sentCount = InteractiveMessageEvent::where('conversation_id', $conversation->id)
            ->where('event_type', 'sent')
            ->count();
        if ($sentCount >= $maxPerConv) {
            return null;
        }

        // Check cooldown
        $cooldown = $setting?->cooldown_seconds ?? 60;
        if ($cooldown > 0) {
            $lastSent = InteractiveMessageEvent::where('conversation_id', $conversation->id)
                ->where('event_type', 'sent')
                ->latest()
                ->first();
            if ($lastSent && $lastSent->created_at->diffInSeconds(now()) < $cooldown) {
                return null;
            }
        }

        $businessId = $conversation->business_id;
        $messageCount = Message::where('conversation_id', $conversation->id)->count();

        // 1. Evaluate Rule-based Automation Rules for this business (strict business isolation)
        $rules = InteractiveAutomationRule::where('business_id', $businessId)
            ->where('is_active', true)
            ->where(function ($q) use ($agent) {
                $q->whereNull('ai_agent_id')->orWhere('ai_agent_id', $agent->id);
            })
            ->orderBy('priority', 'asc')
            ->get();

        foreach ($rules as $rule) {
            $isMatch = false;

            // Trigger Type Evaluation
            if ($rule->trigger_type === 'first_inquiry') {
                $isMatch = ($messageCount <= 2);
            } elseif ($rule->trigger_type === 'always') {
                $isMatch = true;
            } elseif ($rule->trigger_type === 'follow_up') {
                $isMatch = ($messageCount > 2);
            } elseif (in_array($rule->trigger_type, ['keywords', 'intent_detected', 'intent_matched'])) {
                $keywords = is_array($rule->trigger_keywords) ? $rule->trigger_keywords : [];
                if (empty($keywords) && is_string($rule->trigger_keywords)) {
                    $keywords = array_filter(array_map('trim', explode(',', $rule->trigger_keywords)));
                }

                foreach ($keywords as $kw) {
                    if (! empty($kw) && stripos($incomingText, $kw) !== false) {
                        $isMatch = true;
                        break;
                    }
                }
            }

            if ($isMatch) {
                $template = $rule->template;
                if ($template && $template->status === 'active' && $template->business_id === $businessId) {
                    // Check if setting allows this interactive type
                    if ($setting) {
                        if ($template->isListType() && ! $setting->allow_list_messages) {
                            continue;
                        }
                        if ($template->isCtaType() && ! $setting->allow_cta_messages) {
                            continue;
                        }
                    }

                    return $template;
                }
            }
        }

        // 2. If AI Assisted mode is enabled, AI can choose an approved business-specific template matching intent
        if ($setting && $setting->isAiAssisted()) {
            $aiTemplates = InteractiveMessageTemplate::where('business_id', $businessId)
                ->where('status', 'active')
                ->where('ai_availability', true)
                ->get();

            foreach ($aiTemplates as $tpl) {
                if (! empty($tpl->trigger_intent)) {
                    $intentKeywords = array_filter(array_map('trim', explode(',', $tpl->trigger_intent)));
                    foreach ($intentKeywords as $kw) {
                        if (stripos($incomingText, $kw) !== false) {
                            if ($tpl->isListType() && ! $setting->allow_list_messages) {
                                continue;
                            }
                            if ($tpl->isCtaType() && ! $setting->allow_cta_messages) {
                                continue;
                            }

                            return $tpl;
                        }
                    }
                }
            }
        }

        return null;
    }

    /**
     * Send Outbound WhatsApp Interactive Message (Buttons, List, or CTA)
     */
    public function sendInteractiveMessage(
        string $toPhone,
        InteractiveMessageTemplate $template,
        ?int $accountId = null,
        ?int $conversationId = null
    ): array {
        $account = $accountId ? WhatsappAccount::find($accountId) : WhatsappAccount::where('is_shared', true)->first();

        if (! $account || ! $account->isConfigured()) {
            return [
                'success' => false,
                'status' => 'unconfigured',
                'error' => 'WhatsApp Cloud API credentials are not configured.',
            ];
        }

        $payload = $template->toWhatsAppPayload($toPhone);

        try {
            $response = Http::withToken($account->access_token)
                ->timeout(15)
                ->post("https://graph.facebook.com/v21.0/{$account->phone_number_id}/messages", $payload);

            if ($response->successful()) {
                $data = $response->json();
                $wamid = $data['messages'][0]['id'] ?? null;

                $contactId = null;
                if ($conversationId) {
                    $contactId = Conversation::find($conversationId)?->contact_id;
                } else {
                    $cleanedPhone = preg_replace('/[^0-9]/', '', $toPhone);
                    $contact = Contact::where('phone', $toPhone)
                        ->orWhere('phone', $cleanedPhone)
                        ->orWhere('phone', 'like', "%{$cleanedPhone}")
                        ->first();

                    if (! $contact && strlen($cleanedPhone) >= 10) {
                        $contact = Contact::create([
                            'name' => 'WhatsApp Test Recipient',
                            'phone' => $toPhone,
                            'business_id' => $template->business_id,
                            'source' => 'whatsapp_test',
                            'lead_status' => 'new',
                            'whatsapp_account_id' => $account->id,
                            'consent_status' => true,
                        ]);
                    }

                    if ($contact) {
                        $contactId = $contact->id;
                        $conv = Conversation::firstOrCreate(
                            [
                                'contact_id' => $contact->id,
                                'channel' => 'whatsapp',
                                'status' => 'active',
                            ],
                            [
                                'business_id' => $template->business_id,
                                'whatsapp_account_id' => $account->id,
                                'ai_mode' => 'active',
                                'last_message_at' => now(),
                            ]
                        );
                        $conversationId = $conv->id;
                    }
                }

                // Record sent event
                InteractiveMessageEvent::create([
                    'business_id' => $template->business_id,
                    'conversation_id' => $conversationId,
                    'contact_id' => $contactId,
                    'template_id' => $template->id,
                    'interactive_type' => $template->interactive_type,
                    'event_type' => 'sent',
                    'wamid' => $wamid,
                    'raw_meta_payload' => $payload,
                ]);

                return [
                    'success' => true,
                    'status' => 'sent',
                    'message_id' => $wamid,
                    'wamid' => $wamid,
                    'data' => $data,
                ];
            }

            return [
                'success' => false,
                'status' => 'failed',
                'error' => $response->json()['error']['message'] ?? $response->body(),
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send WhatsApp "typing..." indicator and mark customer message as read
     */
    public function sendTypingIndicator(string $messageId, ?int $accountId = null): bool
    {
        $account = $accountId ? WhatsappAccount::find($accountId) : WhatsappAccount::where('is_shared', true)->first();

        if (! $account || ! $account->isConfigured() || empty($messageId)) {
            return false;
        }

        try {
            $response = Http::withToken($account->access_token)
                ->timeout(5)
                ->post("https://graph.facebook.com/v21.0/{$account->phone_number_id}/messages", [
                    'messaging_product' => 'whatsapp',
                    'status' => 'read',
                    'message_id' => $messageId,
                    'typing_indicator' => [
                        'type' => 'text',
                    ],
                ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning("Could not send WhatsApp typing indicator: {$e->getMessage()}");

            return false;
        }
    }

    /**
     * Send Outbound WhatsApp Text Message
     */
    public function sendMessage(string $toPhone, string $text, ?int $accountId = null): array
    {
        $account = $accountId ? WhatsappAccount::find($accountId) : WhatsappAccount::where('is_shared', true)->first();

        if (! $account || ! $account->isConfigured()) {
            return [
                'success' => false,
                'status' => 'unconfigured',
                'error' => 'WhatsApp Cloud API credentials (Phone Number ID and Access Token) are not configured.',
            ];
        }

        try {
            $response = Http::withToken($account->access_token)
                ->timeout(15)
                ->post("https://graph.facebook.com/v21.0/{$account->phone_number_id}/messages", [
                    'messaging_product' => 'whatsapp',
                    'recipient_type' => 'individual',
                    'to' => $toPhone,
                    'type' => 'text',
                    'text' => [
                        'preview_url' => false,
                        'body' => $text,
                    ],
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $wamid = $data['messages'][0]['id'] ?? null;

                return [
                    'success' => true,
                    'status' => 'sent',
                    'message_id' => $wamid,
                    'data' => $data,
                ];
            }

            return [
                'success' => false,
                'status' => 'failed',
                'error' => $response->json()['error']['message'] ?? $response->body(),
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }
    }
}
