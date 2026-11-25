<?php

namespace App\Livewire\Admin\Contacts;

use App\Models\Business;
use App\Models\Contact;
use App\Models\User;
use App\Services\TenantContext;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Create Contact', 'breadcrumb' => 'Workspace / Contacts / Create'])]
class ContactCreate extends Component
{
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

    public function mount(): void
    {
        $activeId = TenantContext::getActiveBusinessId();
        if ($activeId) {
            $this->business_id = $activeId;
        }
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

    public function save()
    {
        $validated = $this->validate();

        $contact = Contact::create($validated);

        session()->flash('success', "Contact {$contact->name} created successfully.");

        return $this->redirect(route('admin.contacts.show', $contact), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.contacts.create', [
            'businesses' => Business::where('status', 'active')->orderBy('name')->get(),
            'users' => User::orderBy('name')->get(),
        ]);
    }
}
