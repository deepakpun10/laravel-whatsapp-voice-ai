<?php

namespace App\Livewire\Admin\Contacts;

use App\Models\Business;
use App\Models\Contact;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Contact Details', 'breadcrumb' => 'Workspace / Contacts / Details'])]
class ContactShow extends Component
{
    public Contact $contact;

    public function mount(Contact $contact): void
    {
        $this->contact = $contact->load([
            'business',
            'assignedUser',
            'conversations.messages',
            'calls',
            'followUps',
        ]);
    }

    public function updateStatus(string $status): void
    {
        $this->contact->lead_status = $status;
        $this->contact->save();

        $this->dispatch('toast', message: "Lead status updated to {$status}.");
    }

    public function assignBusiness(?int $businessId): void
    {
        $this->contact->business_id = $businessId;
        $this->contact->save();

        $this->contact->load('business');
        $this->dispatch('toast', message: $businessId ? "Assigned to {$this->contact->business?->name}." : 'Moved to Unassigned.');
    }

    public function render()
    {
        return view('livewire.admin.contacts.show', [
            'businesses' => Business::where('status', 'active')->orderBy('name')->get(),
        ]);
    }
}
