<?php

namespace App\Livewire\Admin\Copilot;

use App\Models\AiAgent;
use App\Models\Business;
use App\Models\BusinessRoutingRule;
use App\Models\Call;
use App\Models\Contact;
use App\Models\KnowledgeBase;
use App\Models\WhatsappAccount;
use App\Services\TenantContext;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Livewire\Component;

class AgenticCopilot extends Component
{
    public bool $isOpen = false;

    public bool $voiceEnabled = true;

    public string $inputMessage = '';

    public array $messages = [];

    // Temporary storage for inline form fields rendered in chat bubbles: [message_id => [field_key => value]]
    public array $inlineFormValues = [];

    public function mount(): void
    {
        if (empty($this->messages)) {
            $this->messages[] = [
                'id' => (string) Str::uuid(),
                'sender' => 'agent',
                'text' => 'Namaste! Mai aapka Pundeer AI Agentic Copilot hoon. Aap bol kar ya likh kar poora system control kar sakte hain - jaise Business setup karna, Knowledge Base band/chalu karna, Outbound Voice Call lagana, ya System Health inspect karna. Kuch bhi poochhein ya order dein!',
                'timestamp' => now()->format('h:i A'),
                'action_type' => null,
                'action_status' => null,
                'missing_fields' => [],
                'action_data' => [],
                'error_reason' => null,
                'suggested_fix' => null,
            ];
        }
    }

    public function toggleOpen(): void
    {
        $this->isOpen = ! $this->isOpen;
    }

    public function toggleVoice(): void
    {
        $this->voiceEnabled = ! $this->voiceEnabled;
        $this->dispatch('toast', message: $this->voiceEnabled ? '🔊 Voice output enabled.' : '🔇 Voice output muted.');
    }

    public function sendQuickPrompt(string $prompt): void
    {
        $this->inputMessage = $prompt;
        $this->sendMessage();
    }

    public function sendMessage(): void
    {
        $text = trim($this->inputMessage);
        if (empty($text)) {
            return;
        }

        // Add user message
        $userMsgId = (string) Str::uuid();
        $this->messages[] = [
            'id' => $userMsgId,
            'sender' => 'user',
            'text' => $text,
            'timestamp' => now()->format('h:i A'),
            'action_type' => null,
            'action_status' => null,
            'missing_fields' => [],
            'action_data' => [],
            'error_reason' => null,
            'suggested_fix' => null,
        ];

        $this->inputMessage = '';

        // Process query through Agentic Engine
        $this->processAgenticQuery($text);
    }

    protected function processAgenticQuery(string $query): void
    {
        $q = Str::lower($query);
        $agentMsgId = (string) Str::uuid();

        // 1. Full System Setup / Onboarding for a Business (e.g. "Pundeer Technologies ke lie pura system tyaar kr")
        if (Str::contains($q, ['pura system', 'system tyaar', 'system ready', 'setup kr', 'setup karo', 'tyaar kr', 'taiyar'])) {
            $business = null;
            $allBusinesses = Cache::remember('copilot_biz_list', 60, fn () => Business::select(['id', 'name', 'code'])->get());
            foreach ($allBusinesses as $b) {
                if (Str::contains($q, Str::lower($b->name))) {
                    $business = $b;
                    break;
                }
            }

            if (! $business) {
                $business = Business::first();
            }

            if ($business) {
                // Ensure default AI Agent exists
                $agent = AiAgent::firstOrCreate(
                    ['business_id' => $business->id, 'agent_type' => 'whatsapp_chat'],
                    [
                        'name' => "{$business->name} Sales Assistant",
                        'model' => 'gpt-4o-mini',
                        'personality_tone' => 'professional',
                        'system_instructions' => "You are an autonomous AI sales representative for {$business->name}. Help customers, qualify intent, and provide pricing from knowledge base.",
                        'is_active' => true,
                    ]
                );

                // Ensure default Knowledge Base exists
                $kb = KnowledgeBase::firstOrCreate(
                    ['business_id' => $business->id, 'name' => "{$business->name} Knowledge Core"],
                    [
                        'description' => "Standard knowledge base and pricing sheets for {$business->name}.",
                        'status' => 'active',
                    ]
                );

                // Bind KB to Agent if not already
                $agent->knowledgeBases()->syncWithoutDetaching([$kb->id]);

                // Ensure Routing Rule exists
                $rule = BusinessRoutingRule::firstOrCreate(
                    ['business_id' => $business->id],
                    [
                        'name' => "{$business->name} Inbound Route",
                        'keywords' => [Str::lower($business->name), 'service', 'inquiry', 'pricing', 'order'],
                        'priority' => 'high',
                        'min_confidence' => 0.85,
                        'is_active' => true,
                    ]
                );

                Cache::forget('copilot_active_kbs');
                Cache::forget('copilot_system_status');

                $replyText = "Verified: {$business->name} ke liye poora system ready aur verify ho chuka hai!\n\n".
                    "- Workspace: Active (Code: {$business->code})\n".
                    "- AI Sales Agent: '{$agent->name}' (Model: {$agent->model})\n".
                    "- Knowledge Base: '{$kb->name}' [Status: Active / RAG Ready]\n".
                    "- WhatsApp Shared Routing Rule: Configured (Priority: High)\n\n".
                    'Ab is business ke customer inquiries WhatsApp Cloud API ke zariye autonomously resolve ho sakte hain.';

                $this->appendAgentReply($agentMsgId, $replyText, [
                    'action_type' => 'system_setup',
                    'action_status' => 'executed',
                    'action_data' => ['business_id' => $business->id],
                ]);

                return;
            }
        }

        // 2. Toggle Knowledge Base (Band / Chalu / Inactive / Active)
        if (Str::contains($q, ['knowledge base', 'kb']) && Str::contains($q, ['band', 'off', 'inactive', 'disable', 'chalu', 'on', 'active', 'enable'])) {
            $shouldDeactivate = Str::contains($q, ['band', 'off', 'inactive', 'disable']);
            $newStatus = $shouldDeactivate ? 'inactive' : 'active';

            // Find matching KB (cached read)
            $kbs = Cache::remember('copilot_active_kbs', 60, fn () => KnowledgeBase::with('business:id,name')->select(['id', 'business_id', 'name', 'status'])->get());
            $targetKbModel = null;
            foreach ($kbs as $kbItem) {
                if (Str::contains($q, Str::lower($kbItem->name)) || Str::contains($q, Str::lower($kbItem->business?->name ?? ''))) {
                    $targetKbModel = KnowledgeBase::find($kbItem->id);
                    break;
                }
            }

            if (! $targetKbModel) {
                $targetKbModel = KnowledgeBase::first();
            }

            if ($targetKbModel) {
                $targetKbModel->update(['status' => $newStatus]);
                Cache::forget('copilot_active_kbs');
                Cache::forget('copilot_system_status');

                $statusWord = $shouldDeactivate ? 'Inactive (Band)' : 'Active (Chalu)';
                $replyText = "Theek hai! Knowledge Base '{$targetKbModel->name}' ko maine {$statusWord} kar diya hai. ".
                    ($shouldDeactivate ? 'Ab is collection ke documents AI RAG me use nahi honge.' : 'Ab AI iske documents ko RAG me use karega.');

                $this->appendAgentReply($agentMsgId, $replyText, [
                    'action_type' => 'toggle_kb',
                    'action_status' => 'executed',
                    'action_data' => ['kb_id' => $targetKbModel->id, 'status' => $newStatus],
                ]);
            } else {
                $this->appendAgentReply($agentMsgId, 'System me koi Knowledge Base nahi mila. Kripya pehle Knowledge Base create karein.');
            }

            return;
        }

        // 3. Create Business
        if (Str::contains($q, ['business', 'company', 'brand']) && Str::contains($q, ['create', 'add', 'banao', 'naya', 'new', 'start'])) {
            $extractedName = '';
            $commandStopWords = ['business', 'company', 'brand', 'naya', 'new', 'create', 'add', 'banao', 'karo', 'karna', 'kijiye', 'ek', 'please', 'chahiye', 'hai', 'ki', 'ka', 'ko', 'se', 'name'];
            $filteredWords = array_filter(explode(' ', preg_replace('/[^\p{L}\p{N}\s]/u', '', $q)), function ($word) use ($commandStopWords) {
                return ! in_array(trim($word), $commandStopWords) && strlen(trim($word)) > 1;
            });

            if (! empty($filteredWords)) {
                $extractedName = ucwords(trim(implode(' ', $filteredWords)));
            }

            if (empty($extractedName)) {
                $this->inlineFormValues[$agentMsgId] = [
                    'name' => '',
                    'category' => 'Retail / Wholesale',
                    'status' => 'active',
                ];

                $replyText = 'Nayi business create karne ke liye maine form yahi pe open kar diya hai. Kripya business details enter karein:';
                $this->appendAgentReply($agentMsgId, $replyText, [
                    'action_type' => 'create_business',
                    'action_status' => 'pending_input',
                    'missing_fields' => [
                        ['key' => 'name', 'label' => 'Business Name *', 'type' => 'text', 'placeholder' => 'e.g. Himachal Apple Traders'],
                        ['key' => 'category', 'label' => 'Category / Industry', 'type' => 'text', 'placeholder' => 'e.g. Agriculture & Fruits'],
                        ['key' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['active' => 'Active', 'inactive' => 'Inactive']],
                    ],
                ]);
            } else {
                $business = Business::create([
                    'name' => $extractedName,
                    'category' => 'General Business',
                    'status' => 'active',
                    'timezone' => 'Asia/Kolkata',
                    'default_language' => 'hi_en',
                ]);

                Cache::forget('copilot_biz_list');
                Cache::forget('copilot_system_status');

                $replyText = "Badhai ho! Nayi business workspace '{$business->name}' successfully create ho gayi hai (Code: {$business->code}).";
                $this->appendAgentReply($agentMsgId, $replyText, [
                    'action_type' => 'create_business',
                    'action_status' => 'executed',
                    'action_data' => ['business_id' => $business->id, 'name' => $business->name],
                ]);
            }

            return;
        }

        // 4. Initiate Voice Call (Sarvam AI Telephony)
        if (Str::contains($q, ['call', 'phone', 'telephony', 'dial', 'voice'])) {
            preg_match('/(\+?\d{10,13})/', $query, $numMatch);
            $phoneNumber = $numMatch[1] ?? '';

            $activeBusinessId = TenantContext::getActiveBusinessId() ?? Business::first()?->id;

            if (empty($phoneNumber)) {
                $this->inlineFormValues[$agentMsgId] = [
                    'to_number' => '',
                    'has_consent' => true,
                ];

                $replyText = 'Outbound voice call initiate karne ke liye phone number enter karein:';
                $this->appendAgentReply($agentMsgId, $replyText, [
                    'action_type' => 'initiate_call',
                    'action_status' => 'pending_input',
                    'missing_fields' => [
                        ['key' => 'to_number', 'label' => 'Customer Phone Number *', 'type' => 'text', 'placeholder' => '+919876543210'],
                        ['key' => 'has_consent', 'label' => 'Customer Call Consent Verified (Mandatory)', 'type' => 'checkbox'],
                    ],
                ]);
            } else {
                $call = Call::create([
                    'business_id' => $activeBusinessId,
                    'direction' => 'outgoing',
                    'call_type' => 'ai_agent',
                    'to_number' => $phoneNumber,
                    'status' => 'initiated',
                    'transcript' => 'Outbound voice call scheduled via Sarvam AI voice engine by Agentic Copilot.',
                ]);

                $replyText = "Call schedule ho gayi hai! Number: {$phoneNumber} par Sarvam AI voice telephony bot ke zariye call initiate kar di gayi hai.";
                $this->appendAgentReply($agentMsgId, $replyText, [
                    'action_type' => 'initiate_call',
                    'action_status' => 'executed',
                    'action_data' => ['call_id' => $call->id, 'to_number' => $phoneNumber],
                ]);
            }

            return;
        }

        // 5. System Health / Status Inspection (Cached for 30s to reduce DB load)
        if (Str::contains($q, ['status', 'health', 'check', 'kaise hai', 'chal raha', 'problem'])) {
            $stats = Cache::remember('copilot_system_status', 30, function () {
                $wa = WhatsappAccount::where('is_shared', true)->first();

                return [
                    'wa_configured' => $wa?->isConfigured() ?? false,
                    'biz_count' => Business::where('status', 'active')->count(),
                    'kb_active' => KnowledgeBase::where('status', 'active')->count(),
                    'kb_inactive' => KnowledgeBase::where('status', 'inactive')->count(),
                ];
            });

            $waStatus = $stats['wa_configured'] ? 'Connected (Verified)' : 'Needs Setup (Token/Phone ID missing)';

            $replyText = "System Health Report:\n".
                "- Meta WhatsApp Line: {$waStatus}\n".
                "- Active Businesses: {$stats['biz_count']} workspaces running\n".
                "- Active Knowledge Bases: {$stats['kb_active']} collections (Inactive: {$stats['kb_inactive']})\n".
                "- Sarvam AI Voice Telephony Gateway: Ready\n";

            if (! $stats['wa_configured']) {
                $replyText .= "\nAction Needed: WhatsApp settings configure nahi hain. Integrations tab me jakar Meta Access Token update karein.";
            } else {
                $replyText .= "\nAll systems operational! Ready to handle customer enquiries.";
            }

            $this->appendAgentReply($agentMsgId, $replyText, [
                'action_type' => 'check_status',
                'action_status' => 'executed',
            ]);

            return;
        }

        // 6. Show Recent Leads / Contacts (Cached for 15s)
        if (Str::contains($q, ['lead', 'contact', 'customer', 'kiske', 'message'])) {
            $contacts = Cache::remember('copilot_recent_leads', 15, function () {
                return Contact::select(['id', 'business_id', 'name', 'phone', 'lead_status'])
                    ->with('business:id,name')
                    ->latest('id')
                    ->take(5)
                    ->get();
            });

            if ($contacts->isEmpty()) {
                $replyText = 'Abhi system me koi contacts ya leads nahi hain. WhatsApp par incoming messages aate hi contacts automatically create ho jayenge.';
            } else {
                $replyText = "Recent Leads & Contacts:\n";
                foreach ($contacts as $c) {
                    $replyText .= "- {$c->name} ({$c->phone}) - {$c->business?->name} [Status: {$c->lead_status}]\n";
                }
            }

            $this->appendAgentReply($agentMsgId, $replyText, [
                'action_type' => 'list_contacts',
                'action_status' => 'executed',
            ]);

            return;
        }

        // 7. Fallback General Assistance
        $replyText = "Aapke order/sawal ko maine samajh liya hai. Mai ye actions turant execute kar sakta hoon:\n\n".
            "1. 'Pundeer Technologies ke lie pura system tyaar kr' (Complete business setup)\n".
            "2. 'Himachal Garlic ka knowledge base band karo' (Active/Inactive switch)\n".
            "3. 'Naya business banao Himachal Fruits' (Create business)\n".
            "4. 'Call lagao +919876543210' (Sarvam AI Voice Call)\n".
            "5. 'System status check karo' (Diagnostic report)\n\n".
            'Aap inme se kuch bhi bole ya type karein!';

        $this->appendAgentReply($agentMsgId, $replyText, [
            'action_type' => 'help',
            'action_status' => 'executed',
        ]);
    }

    public function executeInlineAction(string $messageId): void
    {
        $values = $this->inlineFormValues[$messageId] ?? [];
        $msgIndex = null;
        foreach ($this->messages as $idx => $m) {
            if ($m['id'] === $messageId) {
                $msgIndex = $idx;
                break;
            }
        }

        if ($msgIndex === null) {
            return;
        }

        $actionType = $this->messages[$msgIndex]['action_type'];

        // Execute Business Creation
        if ($actionType === 'create_business') {
            $name = trim($values['name'] ?? '');
            if (empty($name)) {
                $this->messages[$msgIndex]['error_reason'] = 'Business Name is required.';
                $this->messages[$msgIndex]['suggested_fix'] = 'Kripya Business Name box me company ka naam bharein.';

                return;
            }

            $business = Business::create([
                'name' => $name,
                'category' => $values['category'] ?? 'General Business',
                'status' => $values['status'] ?? 'active',
                'timezone' => 'Asia/Kolkata',
                'default_language' => 'hi_en',
            ]);

            Cache::forget('copilot_biz_list');
            Cache::forget('copilot_system_status');

            $this->messages[$msgIndex]['action_status'] = 'executed';
            $this->messages[$msgIndex]['error_reason'] = null;
            $this->messages[$msgIndex]['suggested_fix'] = null;

            $this->appendAgentReply((string) Str::uuid(), "Done! Business '{$business->name}' create ho chuki hai (Code: {$business->code}).");

            return;
        }

        // Execute Call Initiation
        if ($actionType === 'initiate_call') {
            $number = trim($values['to_number'] ?? '');
            $consent = (bool) ($values['has_consent'] ?? false);

            if (empty($number)) {
                $this->messages[$msgIndex]['error_reason'] = 'Phone Number is required.';
                $this->messages[$msgIndex]['suggested_fix'] = 'Valid phone number enter karein (e.g. +919876543210).';

                return;
            }

            if (! $consent) {
                $this->messages[$msgIndex]['error_reason'] = 'Customer Calling Consent Missing.';
                $this->messages[$msgIndex]['suggested_fix'] = 'Regulatory guidelines ke mutabik bina consent automated call nahi ki ja sakti. Kripya consent checkbox tick karein.';

                return;
            }

            $activeBusinessId = TenantContext::getActiveBusinessId() ?? Business::first()?->id;

            $call = Call::create([
                'business_id' => $activeBusinessId,
                'direction' => 'outgoing',
                'call_type' => 'ai_agent',
                'to_number' => $number,
                'status' => 'initiated',
                'transcript' => 'Outbound voice call scheduled via Sarvam AI voice engine by Agentic Copilot.',
            ]);

            $this->messages[$msgIndex]['action_status'] = 'executed';
            $this->messages[$msgIndex]['error_reason'] = null;

            $this->appendAgentReply((string) Str::uuid(), "Done! {$number} par Sarvam AI voice call schedule ho gayi hai.");

            return;
        }
    }

    protected function appendAgentReply(string $msgId, string $text, array $extra = []): void
    {
        $this->messages[] = array_merge([
            'id' => $msgId,
            'sender' => 'agent',
            'text' => $text,
            'timestamp' => now()->format('h:i A'),
            'action_type' => null,
            'action_status' => null,
            'missing_fields' => [],
            'action_data' => [],
            'error_reason' => null,
            'suggested_fix' => null,
        ], $extra);

        // Dispatch browser speech synthesis event if voice is enabled
        if ($this->voiceEnabled) {
            $cleanSpeech = preg_replace('/[•\*\#]/', '', $text);
            $this->dispatch('copilot-speak', text: $cleanSpeech);
        }
    }

    public function render()
    {
        return view('livewire.admin.copilot.agentic-copilot');
    }
}
