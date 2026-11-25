<?php

namespace App\Livewire\Admin\KnowledgeBases;

use App\Models\Business;
use App\Models\KnowledgeBase;
use App\Services\TenantContext;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin', ['title' => 'Knowledge Bases', 'breadcrumb' => 'Workspace / Knowledge Bases'])]
class KnowledgeBaseIndex extends Component
{
    use WithPagination;

    public string $businessFilter = 'all';

    public function mount(): void
    {
        $activeId = TenantContext::getActiveBusinessId();
        if ($activeId) {
            $this->businessFilter = (string) $activeId;
        }
    }

    public function toggleStatus(int $id): void
    {
        $kb = KnowledgeBase::findOrFail($id);
        $newStatus = in_array($kb->status, ['inactive', 'disabled']) ? 'active' : 'inactive';
        $kb->update(['status' => $newStatus]);
        $this->dispatch('toast', message: "Knowledge Base '{$kb->name}' is now ".strtoupper($newStatus).'.');
    }

    public function deleteKb(int $id): void
    {
        $kb = KnowledgeBase::findOrFail($id);
        $kb->delete();
        $this->dispatch('toast', message: "Knowledge Base {$kb->name} deleted.");
    }

    public function render()
    {
        $kbs = KnowledgeBase::with(['business', 'documents'])
            ->withCount('documents')
            ->when($this->businessFilter !== 'all', fn ($q) => $q->where('business_id', $this->businessFilter))
            ->latest()
            ->paginate(12);

        return view('livewire.admin.knowledge-bases.index', [
            'knowledgeBases' => $kbs,
            'businesses' => Business::where('status', 'active')->get(),
        ]);
    }
}
