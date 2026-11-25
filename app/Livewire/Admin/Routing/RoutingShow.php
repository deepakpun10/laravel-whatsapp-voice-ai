<?php

namespace App\Livewire\Admin\Routing;

use App\Models\BusinessRoutingRule;
use App\Models\RoutingLog;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Routing Rule Details', 'breadcrumb' => 'Workspace / Routing / Details'])]
class RoutingShow extends Component
{
    public BusinessRoutingRule $rule;

    public function mount(BusinessRoutingRule $rule): void
    {
        $this->rule = $rule->load('business');
    }

    public function toggle(): void
    {
        $this->rule->is_active = ! $this->rule->is_active;
        $this->rule->save();

        $this->dispatch('toast', message: 'Rule status changed to '.($this->rule->is_active ? 'active' : 'paused').'.');
    }

    public function render()
    {
        $logs = RoutingLog::where('matched_business_id', $this->rule->business_id)
            ->latest()
            ->take(15)
            ->get();

        return view('livewire.admin.routing.show', [
            'logs' => $logs,
        ]);
    }
}
