<?php

namespace App\Livewire\Admin\Calls;

use App\Models\Call;
use App\Services\Telephony\SarvamTelephonyService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Call Details & Transcript', 'breadcrumb' => 'Workspace / Voice Calls / Details'])]
class CallShow extends Component
{
    public Call $call;

    public function mount(Call $call): void
    {
        $this->call = $call->load(['business', 'contact', 'apiConnection', 'aiAgent', 'sarvamPhoneNumber']);
    }

    public function updateOutcome(string $outcome): void
    {
        $this->call->update(['outcome' => $outcome, 'status' => 'completed']);
        $this->dispatch('toast', message: "Call outcome updated to {$outcome}.");
    }

    public function retryCall(SarvamTelephonyService $telephonyService): void
    {
        if (! $this->call->business) {
            $this->dispatch('toast', message: 'Cannot retry call: Business is unassigned.');

            return;
        }

        $result = $telephonyService->initiateOutboundCall(
            toNumber: $this->call->to_number,
            business: $this->call->business,
            contact: $this->call->contact,
            isManual: true,
        );

        if ($result['success']) {
            $this->dispatch('toast', message: 'Retry call initiated successfully via Sarvam AI.');
        } else {
            $this->dispatch('toast', message: 'Retry failed: '.($result['error'] ?? 'Unknown error'));
        }
    }

    public function render()
    {
        return view('livewire.admin.calls.show');
    }
}
