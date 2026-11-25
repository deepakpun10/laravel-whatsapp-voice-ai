<?php

namespace App\Livewire\Admin\Interactive;

use App\Models\InteractiveMessageTemplate;
use App\Services\WhatsApp\WhatsAppCloudApiService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Template Details', 'breadcrumb' => 'AI Studio / Interactive Messages / View'])]
class InteractiveShow extends Component
{
    public InteractiveMessageTemplate $template;

    public string $test_phone_number = '';

    public ?string $testResult = null;

    public function mount(InteractiveMessageTemplate $template): void
    {
        $this->template = $template->load(['business', 'buttons', 'sections.rows', 'automationRules', 'events']);
    }

    public function sendTestMessage(WhatsAppCloudApiService $waService): void
    {
        $phone = preg_replace('/[^0-9]/', '', $this->test_phone_number);

        if (strlen($phone) < 10) {
            $this->dispatch('toast', message: 'Please enter a valid 10-12 digit mobile number.');

            return;
        }

        $result = $waService->sendInteractiveMessage($phone, $this->template);

        if ($result['success']) {
            $msgId = $result['message_id'] ?? $result['wamid'] ?? 'sent';
            $this->testResult = "✓ Interactive message sent successfully! Message ID: {$msgId}";
            $this->dispatch('toast', message: 'Test interactive message delivered to WhatsApp!');
        } else {
            $this->testResult = '✕ Failed to send: '.($result['error'] ?? 'Unknown WhatsApp API error');
            $this->dispatch('toast', message: 'Failed: '.($result['error'] ?? 'API error'));
        }
    }

    public function toggleStatus(): void
    {
        $this->template->status = $this->template->status === 'active' ? 'inactive' : 'active';
        $this->template->save();
        $this->dispatch('toast', message: "Status changed to {$this->template->status}.");
    }

    public function render()
    {
        return view('livewire.admin.interactive.show');
    }
}
