<?php

namespace App\Livewire\Admin\Contacts;

use App\Models\Business;
use App\Models\Contact;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Edit Contact', 'breadcrumb' => 'Workspace / Contacts / Edit'])]
class ContactEdit extends Component
{
    public Contact $contact;

    public string $name = '';

    public string $phone = '';

    public string $email = '';

    public ?int $business_id = null;

    public string $lead_status = 'new';

    public string $requirement = '';

    public string $budget = '';

    public string $location = '';

    public ?int $assigned_user_id = null;

    public string $notes = '';

    public function mount(Contact $contact): void
    {
        $this->contact = $contact;
        $this->name = $contact->name;
        $this->phone = $contact->phone;
        $this->email = $contact->email ?? '';
        $this->business_id = $contact->business_id;
        $this->lead_status = $contact->lead_status;
        $this->requirement = $contact->requirement ?? '';
        $this->budget = $contact->budget ?? '';
        $this->location = $contact->location ?? '';
        $this->assigned_user_id = $contact->assigned_user_id;
        $this->notes = $contact->notes ?? '';
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'business_id' => ['nullable', 'exists:businesses,id'],
            'lead_status' => ['required', 'in:new,interested,qualified,follow_up,converted,lost'],
            'requirement' => ['nullable', 'string', 'max:255'],
            'budget' => ['nullable', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:100'],
            'assigned_user_id' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function update()
    {
        $validated = $this->validate();

        $this->contact->update($validated);

        session()->flash('success', "Contact {$this->contact->name} updated successfully.");

        return $this->redirect(route('admin.contacts.show', $this->contact), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.contacts.edit', [
            'businesses' => Business::where('status', 'active')->orderBy('name')->get(),
            'users' => User::orderBy('name')->get(),
        ]);
    }
}
