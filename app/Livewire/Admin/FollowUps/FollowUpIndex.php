<?php

namespace App\Livewire\Admin\FollowUps;

use App\Models\Business;
use App\Models\Contact;
use App\Models\FollowUp;
use App\Services\TenantContext;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Follow-ups', 'breadcrumb' => 'Workspace / Follow-ups'])]
class FollowUpIndex extends Component
{
    public ?int $contact_id = null;

    public ?int $business_id = null;

    public string $title = '';

    public string $type = 'callback';

    public string $due_at = '';

    public string $notes = '';

    public function mount(): void
    {
        $activeId = TenantContext::getActiveBusinessId();
        if ($activeId) {
            $this->business_id = $activeId;
        } else {
            $this->business_id = Business::first()?->id;
        }

        $this->due_at = now()->addDay()->format('Y-m-d\TH:i');
    }

    public function createFollowUp(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'contact_id' => 'required|exists:contacts,id',
            'business_id' => 'required|exists:businesses,id',
            'due_at' => 'required|date',
        ]);

        FollowUp::create([
            'business_id' => $this->business_id,
            'contact_id' => $this->contact_id,
            'assigned_user_id' => auth()->id(),
            'type' => $this->type,
            'title' => $this->title,
            'notes' => $this->notes,
            'due_at' => $this->due_at,
            'status' => 'pending',
        ]);

        $this->reset(['title', 'notes']);
        $this->dispatch('toast', message: 'Follow-up scheduled.');
    }

    public function markCompleted(int $id): void
    {
        $f = FollowUp::findOrFail($id);
        $f->update(['status' => 'completed', 'completed_at' => now()]);
        $this->dispatch('toast', message: 'Follow-up marked completed.');
    }

    public function render()
    {
        $activeBusinessId = TenantContext::getActiveBusinessId();
        $followUps = FollowUp::with(['contact', 'business', 'assignedUser'])
            ->when($activeBusinessId, fn ($q) => $q->where('business_id', $activeBusinessId))
            ->latest('due_at')
            ->get();

        return view('livewire.admin.follow-ups.index', [
            'followUps' => $followUps,
            'contacts' => Contact::orderBy('name')->get(),
            'businesses' => Business::where('status', 'active')->get(),
        ]);
    }
}
