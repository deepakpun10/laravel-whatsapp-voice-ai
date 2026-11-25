<?php

namespace App\Livewire\Admin\Contacts;

use App\Models\Business;
use App\Models\Contact;
use App\Services\TenantContext;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin', ['title' => 'Contacts & Leads', 'breadcrumb' => 'Workspace / Contacts'])]
class ContactIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $businessFilter = 'all';

    public string $statusFilter = 'all';

    public function mount(): void
    {
        $activeId = TenantContext::getActiveBusinessId();
        if ($activeId) {
            $this->businessFilter = (string) $activeId;
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingBusinessFilter(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function deleteContact(int $id): void
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();
        $this->dispatch('toast', message: "Contact {$contact->name} deleted.");
    }

    public function render()
    {
        $contacts = Contact::with(['business', 'assignedUser'])
            ->when($this->businessFilter !== 'all', function ($q) {
                if ($this->businessFilter === 'unassigned') {
                    $q->whereNull('business_id');
                } else {
                    $q->where('business_id', $this->businessFilter);
                }
            })
            ->when($this->statusFilter !== 'all', fn ($q) => $q->where('lead_status', $this->statusFilter))
            ->when($this->search !== '', function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', "%{$this->search}%")
                        ->orWhere('phone', 'like', "%{$this->search}%")
                        ->orWhere('requirement', 'like', "%{$this->search}%");
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.contacts.index', [
            'contacts' => $contacts,
            'businesses' => Business::where('status', 'active')->get(),
            'totalLeads' => Contact::count(),
            'qualifiedLeads' => Contact::where('lead_status', 'qualified')->count(),
        ]);
    }
}
