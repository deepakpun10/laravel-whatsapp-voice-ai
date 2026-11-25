<?php

namespace App\Livewire\Admin\Businesses;

use App\Models\Business;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Business Details', 'breadcrumb' => 'Workspace / Businesses / Details'])]
class BusinessShow extends Component
{
    public Business $business;

    public function mount(Business $business): void
    {
        $this->business = $business->load([
            'users',
            'aiAgents',
            'knowledgeBases',
            'routingRules',
            'whatsappAccounts',
        ]);
    }

    public function toggleStatus(): void
    {
        $this->business->status = $this->business->status === 'active' ? 'inactive' : 'active';
        $this->business->save();

        $this->dispatch('toast', message: "Business status changed to {$this->business->status}.");
    }

    public function render()
    {
        return view('livewire.admin.businesses.show');
    }
}
