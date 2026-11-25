<?php

namespace App\Livewire\Admin\Conversations;

use App\Models\Business;
use App\Models\Conversation;
use App\Models\InteractiveMessageTemplate;
use App\Models\Message;
use App\Services\TenantContext;
use App\Services\WhatsApp\WhatsAppCloudApiService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'WhatsApp Inbox', 'breadcrumb' => 'Workspace / WhatsApp Inbox'])]
class ConversationIndex extends Component
{
    public ?int $selectedConversationId = null;

    public string $search = '';

    public string $businessFilter = 'all';

    public string $messageText = '';

    public ?int $selectedInteractiveTemplateId = null;

    public function mount(): void
    {
        $activeId = TenantContext::getActiveBusinessId();
        if ($activeId) {
            $this->businessFilter = (string) $activeId;
        }

        if (request()->has('conversation')) {
            $this->selectedConversationId = (int) request('conversation');
        } elseif (request()->has('contact_id')) {
            $conv = Conversation::where('contact_id', request('contact_id'))->first();
            if ($conv) {
                $this->selectedConversationId = $conv->id;
            }
        }

        if (! $this->selectedConversationId) {
            $first = Conversation::when($this->businessFilter !== 'all', fn ($q) => $q->where('business_id', $this->businessFilter))
                ->latest('last_message_at')
                ->first();
            $this->selectedConversationId = $first?->id;
        }
    }

    public function selectConversation(int $id): void
    {
        $this->selectedConversationId = $id;
    }

    public function toggleTakeover(): void
    {
        if (! $this->selectedConversationId) {
            return;
        }

        $conversation = Conversation::findOrFail($this->selectedConversationId);

        if ($conversation->isAiActive()) {
            $conversation->update([
                'ai_mode' => 'paused',
                'takeover_user_id' => auth()->id(),
                'takeover_at' => now(),
            ]);
            $this->dispatch('toast', message: 'Human takeover active — AI automated replies paused.');
        } else {
            $conversation->update([
                'ai_mode' => 'active',
                'takeover_user_id' => null,
                'takeover_at' => null,
            ]);
            $this->dispatch('toast', message: 'AI replies resumed for this conversation.');
        }
    }

    public function sendMessage(WhatsAppCloudApiService $waService): void
    {
        if (empty(trim($this->messageText)) || ! $this->selectedConversationId) {
            return;
        }

        $conversation = Conversation::with('contact')->findOrFail($this->selectedConversationId);
        $text = trim($this->messageText);
        $this->messageText = '';

        // Save local message in database
        $msg = Message::create([
            'conversation_id' => $conversation->id,
            'sender_type' => $conversation->isAiActive() ? 'ai' : 'human',
            'sender_id' => auth()->id(),
            'message_type' => 'text',
            'content' => $text,
            'status' => 'queued',
        ]);

        $conversation->update(['last_message_at' => now()]);

        // Attempt delivery via WhatsApp Cloud API
        if ($conversation->contact && $conversation->contact->phone) {
            $result = $waService->sendMessage(
                $conversation->contact->phone,
                $text,
                $conversation->whatsapp_account_id
            );

            if ($result['success']) {
                $msg->update([
                    'status' => 'sent',
                    'message_id' => $result['message_id'] ?? null,
                ]);
                $this->dispatch('toast', message: 'Message sent via WhatsApp Cloud API.');
            } else {
                $msg->update([
                    'status' => 'failed',
                    'error_message' => $result['error'] ?? 'API error',
                ]);
                $this->dispatch('toast', message: 'Message logged. WhatsApp API: '.($result['error'] ?? 'Delivery pending.'));
            }
        }
    }

    public function sendInteractiveTemplate(WhatsAppCloudApiService $waService): void
    {
        if (! $this->selectedInteractiveTemplateId || ! $this->selectedConversationId) {
            $this->dispatch('toast', message: 'Please select an interactive template first.');

            return;
        }

        $conversation = Conversation::with('contact')->findOrFail($this->selectedConversationId);
        $template = InteractiveMessageTemplate::with(['buttons', 'sections.rows'])->findOrFail($this->selectedInteractiveTemplateId);

        // Strict business isolation check
        if ($conversation->business_id && $template->business_id !== $conversation->business_id) {
            $this->dispatch('toast', message: 'Template does not belong to the routed business.');

            return;
        }

        // Pause AI when human sends interactive template
        if ($conversation->isAiActive()) {
            $conversation->pauseAi('Human support agent dispatched interactive template.');
        }

        $msg = Message::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'human',
            'sender_id' => auth()->id(),
            'message_type' => 'interactive',
            'content' => $template->body_text,
            'status' => 'queued',
            'raw_payload' => $template->toWhatsAppPayload($conversation->contact->phone ?? ''),
        ]);

        $conversation->update(['last_message_at' => now()]);

        if ($conversation->contact && $conversation->contact->phone) {
            $result = $waService->sendInteractiveMessage(
                $conversation->contact->phone,
                $template,
                $conversation->whatsapp_account_id,
                $conversation->id
            );

            if ($result['success']) {
                $msg->update([
                    'status' => 'sent',
                    'message_id' => $result['message_id'] ?? null,
                ]);
                $this->dispatch('toast', message: "Interactive template '{$template->name}' dispatched successfully!");
            } else {
                $msg->update([
                    'status' => 'failed',
                    'error_message' => $result['error'] ?? 'API error',
                ]);
                $this->dispatch('toast', message: 'Interactive message logged. Error: '.($result['error'] ?? 'Delivery failed'));
            }
        }

        $this->selectedInteractiveTemplateId = null;
    }

    public function render()
    {
        $conversations = Conversation::with(['contact', 'business', 'messages'])
            ->when($this->businessFilter !== 'all', function ($q) {
                if ($this->businessFilter === 'unassigned') {
                    $q->whereNull('business_id');
                } else {
                    $q->where('business_id', $this->businessFilter);
                }
            })
            ->when($this->search !== '', function ($q) {
                $q->whereHas('contact', function ($sub) {
                    $sub->where('name', 'like', "%{$this->search}%")
                        ->orWhere('phone', 'like', "%{$this->search}%");
                });
            })
            ->latest('last_message_at')
            ->get();

        $selectedConversation = $this->selectedConversationId
            ? Conversation::with(['contact', 'business.aiAgents', 'business.knowledgeBases', 'messages'])->find($this->selectedConversationId)
            : null;

        $availableTemplates = ($selectedConversation && $selectedConversation->business_id)
            ? InteractiveMessageTemplate::where('business_id', $selectedConversation->business_id)
                ->where('status', 'active')
                ->orderBy('name')
                ->get()
            : collect();

        return view('livewire.admin.conversations.index', [
            'conversations' => $conversations,
            'selectedConversation' => $selectedConversation,
            'businesses' => Business::where('status', 'active')->get(),
            'availableTemplates' => $availableTemplates,
        ]);
    }
}
