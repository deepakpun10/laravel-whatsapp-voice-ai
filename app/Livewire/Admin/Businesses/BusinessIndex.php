<?php

namespace App\Livewire\Admin\Businesses;

use App\Models\Business;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin', ['title' => 'Businesses (Tenants)', 'breadcrumb' => 'Workspace / Businesses'])]
class BusinessIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = 'all';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function deleteBusiness(int $id): void
    {
        $business = Business::findOrFail($id);

        // Safety check: protect if it has active conversations or contacts
        if ($business->conversations()->exists() || $business->contacts()->exists()) {
            $this->dispatch('toast', message: "Cannot delete {$business->name}: business has linked conversations or contacts.");

            return;
        }

        $business->delete();
        $this->dispatch('toast', message: "Business {$business->name} deleted successfully.");
    }

    public function render()
    {
        $query = Business::withCount(['contacts', 'conversations', 'aiAgents', 'knowledgeBases'])
            ->when($this->search !== '', function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', "%{$this->search}%")
                        ->orWhere('code', 'like', "%{$this->search}%")
                        ->orWhere('category', 'like', "%{$this->search}%");
                });
            })
            ->when($this->statusFilter !== 'all', function ($q) {
                $q->where('status', $this->statusFilter);
            })
            ->latest();

        return view('livewire.admin.businesses.index', [
            'businesses' => $query->paginate(10),
            'totalCount' => Business::count(),
            'activeCount' => Business::where('status', 'active')->count(),
        ]);
    }
}
