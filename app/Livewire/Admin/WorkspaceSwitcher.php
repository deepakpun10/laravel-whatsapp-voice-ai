<?php

namespace App\Livewire\Admin;

use App\Models\Business;
use App\Services\TenantContext;
use Livewire\Component;

class WorkspaceSwitcher extends Component
{
    public ?string $selectedBusinessId = 'all';

    public function mount(): void
    {
        $activeId = TenantContext::getActiveBusinessId();
        $this->selectedBusinessId = $activeId ? (string) $activeId : 'all';
    }

    public function updatedSelectedBusinessId(string $value): void
    {
        if ($value === 'all') {
            TenantContext::setActiveBusinessId(null);
            $this->dispatch('workspace-changed', businessId: null);
            $this->dispatch('toast', message: 'Showing all businesses');
        } else {
            $business = Business::find((int) $value);
            if ($business) {
                TenantContext::setActiveBusinessId($business->id);
                $this->dispatch('workspace-changed', businessId: $business->id);
                $this->dispatch('toast', message: 'Switched active business to '.$business->name);
            } else {
                TenantContext::setActiveBusinessId(null);
                $this->selectedBusinessId = 'all';
                $this->dispatch('workspace-changed', businessId: null);
            }
        }

        $this->redirect(request()->header('Referer') ?: route('admin.dashboard'));
    }

    public function render()
    {
        $businesses = TenantContext::getAllBusinesses();

        return view('livewire.admin.workspace-switcher', [
            'businesses' => $businesses,
        ]);
    }
}
