<?php

namespace App\Livewire\Admin\Telephony;

use App\Models\AiAgent;
use App\Models\ApiConnection;
use App\Models\Business;
use App\Models\SarvamPhoneNumber;
use App\Services\Telephony\SarvamTelephonyService;
use App\Services\TenantContext;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Phone Numbers Management', 'breadcrumb' => 'AI Studio / Phone Numbers'])]
class PhoneNumbersIndex extends Component
{
    public string $search = '';

    public string $businessFilter = 'all';

    public string $statusFilter = 'all';

    // Add/Edit Number Modal state
    public bool $showModal = false;

    public ?int $editingId = null;

    public ?int $form_business_id = null;

    public ?int $form_agent_id = null;

    public string $form_phone_number = '';

    public string $form_label = '';

    public string $form_status = 'active';

    public bool $form_inbound_enabled = true;

    public bool $form_outbound_enabled = true;

    public string $form_deployment_id = '';

    public string $form_connection_id = '';

    public ?string $routingTestResult = null;

    public function mount(): void
    {
        $activeId = TenantContext::getActiveBusinessId();
        if ($activeId) {
            $this->businessFilter = (string) $activeId;
            $this->form_business_id = $activeId;
        }
    }

    public function openCreateModal(): void
    {
        $this->reset([
            'editingId',
            'form_phone_number',
            'form_label',
            'form_deployment_id',
            'form_connection_id',
            'routingTestResult',
        ]);
        $this->form_status = 'active';
        $this->form_inbound_enabled = true;
        $this->form_outbound_enabled = true;
        $this->showModal = true;
    }

    public function editNumber(int $id): void
    {
        $num = SarvamPhoneNumber::findOrFail($id);
        $this->editingId = $num->id;
        $this->form_business_id = $num->business_id;
        $this->form_agent_id = $num->ai_agent_id;
        $this->form_phone_number = $num->phone_number;
        $this->form_label = $num->label ?? '';
        $this->form_status = $num->status;
        $this->form_inbound_enabled = $num->inbound_enabled;
        $this->form_outbound_enabled = $num->outbound_enabled;
        $this->form_deployment_id = $num->inbound_deployment_id ?? '';
        $this->form_connection_id = $num->telephony_connection_id ?? '';
        $this->showModal = true;
    }

    public function saveNumber(): void
    {
        $this->validate([
            'form_phone_number' => 'required|string|max:30',
            'form_business_id' => 'nullable|exists:businesses,id',
            'form_agent_id' => 'nullable|exists:ai_agents,id',
        ]);

        $cleaned = preg_replace('/[^0-9+]/', '', $this->form_phone_number);

        SarvamPhoneNumber::updateOrCreate(
            ['id' => $this->editingId],
            [
                'phone_number' => $cleaned,
                'business_id' => $this->form_business_id,
                'ai_agent_id' => $this->form_agent_id,
                'label' => $this->form_label ?: 'Sarvam AI Line',
                'status' => $this->form_status,
                'inbound_enabled' => $this->form_inbound_enabled,
                'outbound_enabled' => $this->form_outbound_enabled,
                'inbound_deployment_id' => $this->form_deployment_id ?: null,
                'telephony_connection_id' => $this->form_connection_id ?: null,
            ]
        );

        $this->showModal = false;
        $this->dispatch('toast', message: 'Phone number configuration saved.');
    }

    public function syncFromSarvam(SarvamTelephonyService $telephonyService): void
    {
        $conn = ApiConnection::where('provider_name', 'sarvam')->where('is_active', true)->first();

        if (! $conn) {
            $this->dispatch('toast', message: 'No active Sarvam AI connection found. Please configure API key in Integrations first.');

            return;
        }

        $res = $telephonyService->syncPhoneNumbers($conn);

        if ($res['success']) {
            $this->dispatch('toast', message: $res['message']);
        } else {
            $this->dispatch('toast', message: 'Sarvam API sync note: '.($res['error'] ?? 'No remote numbers found. You can enter your existing number manually below.'));
        }
    }

    public function toggleStatus(int $id): void
    {
        $num = SarvamPhoneNumber::findOrFail($id);
        $num->status = $num->status === 'active' ? 'archived' : 'active';
        $num->save();
        $this->dispatch('toast', message: "Phone number {$num->phone_number} is now {$num->status}.");
    }

    public function toggleInbound(int $id): void
    {
        $num = SarvamPhoneNumber::findOrFail($id);
        $num->inbound_enabled = ! $num->inbound_enabled;
        $num->save();
        $this->dispatch('toast', message: 'Inbound calling '.($num->inbound_enabled ? 'enabled' : 'disabled').'.');
    }

    public function toggleOutbound(int $id): void
    {
        $num = SarvamPhoneNumber::findOrFail($id);
        $num->outbound_enabled = ! $num->outbound_enabled;
        $num->save();
        $this->dispatch('toast', message: 'Outbound calling '.($num->outbound_enabled ? 'enabled' : 'disabled').'.');
    }

    public function testRouting(int $id): void
    {
        $num = SarvamPhoneNumber::with(['business', 'aiAgent'])->findOrFail($id);

        if (! $num->business) {
            $this->routingTestResult = "⚠️ Phone {$num->phone_number} is unassigned. Please assign a Business for inbound and outbound routing.";

            return;
        }

        if (! $num->aiAgent) {
            $this->routingTestResult = "⚠️ Assigned to {$num->business->name}, but no Inbound Voice Agent is linked. Incoming calls will not be answered by AI.";

            return;
        }

        $this->routingTestResult = "✓ Routing Validated: Calls to/from {$num->phone_number} are correctly routed to '{$num->business->name}' handled by Voice Agent '{$num->aiAgent->name}'.";
        $this->dispatch('toast', message: 'Routing verification passed.');
    }

    public function render()
    {
        $numbers = SarvamPhoneNumber::with(['business', 'aiAgent', 'calls'])
            ->when($this->businessFilter !== 'all', fn ($q) => $q->where('business_id', $this->businessFilter))
            ->when($this->statusFilter !== 'all', fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->search !== '', fn ($q) => $q->where('phone_number', 'like', "%{$this->search}%")->orWhere('label', 'like', "%{$this->search}%"))
            ->orderBy('id', 'desc')
            ->get();

        $businesses = Business::where('status', 'active')->get();
        $agents = AiAgent::where('is_active', true)->get();

        return view('livewire.admin.telephony.phone-numbers', [
            'numbers' => $numbers,
            'businesses' => $businesses,
            'agents' => $agents,
        ]);
    }
}
