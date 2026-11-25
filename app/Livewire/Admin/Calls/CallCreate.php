<?php

namespace App\Livewire\Admin\Calls;

use App\Models\Business;
use App\Models\Contact;
use App\Services\Telephony\SarvamTelephonyService;
use App\Services\TenantContext;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Initiate Outbound Call', 'breadcrumb' => 'Workspace / Voice Calls / New'])]
class CallCreate extends Component
{
    public ?int $business_id = null;

    public ?int $contact_id = null;

    public string $to_number = '';

    public string $call_type = 'ai_agent';

    public bool $has_consent = false;

    public function mount(): void
    {
        $activeId = TenantContext::getActiveBusinessId();
        if ($activeId) {
            $this->business_id = $activeId;
        }

        if (request()->has('contact_id')) {
            $contact = Contact::find(request('contact_id'));
            if ($contact) {
                $this->contact_id = $contact->id;
                $this->to_number = $contact->phone;
                $this->business_id = $contact->business_id ?? $this->business_id;
                $this->has_consent = $contact->consent_status;
            }
        }
    }

    protected function rules(): array
    {
        return [
            'business_id' => ['required', 'exists:businesses,id'],
            'to_number' => ['required', 'string', 'max:25'],
            'call_type' => ['required', 'in:ai_agent,callback,human'],
            'has_consent' => ['accepted'],
        ];
    }

    protected $messages = [
        'has_consent.accepted' => 'Customer calling consent must be verified before initiating an automated voice call.',
    ];

    public function initiateCall(SarvamTelephonyService $telephonyService)
    {
        $this->validate();

        $business = Business::findOrFail($this->business_id);
        $contact = $this->contact_id ? Contact::find($this->contact_id) : null;

        $result = $telephonyService->initiateOutboundCall(
            toNumber: $this->to_number,
            business: $business,
            contact: $contact,
            isManual: true,
        );

        if ($result['success']) {
            session()->flash('success', $result['message']);

            return $this->redirect(route('admin.calls.show', $result['call']), navigate: true);
        }

        $this->addError('to_number', $result['error'] ?? 'Could not initiate voice call.');
    }

    public function render()
    {
        return view('livewire.admin.calls.create', [
            'businesses' => Business::where('status', 'active')->orderBy('name')->get(),
            'contacts' => Contact::orderBy('name')->get(),
        ]);
    }
}
