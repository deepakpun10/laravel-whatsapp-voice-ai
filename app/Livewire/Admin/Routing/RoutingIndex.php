<?php

namespace App\Livewire\Admin\Routing;

use App\Models\Business;
use App\Models\BusinessRoutingRule;
use App\Models\RoutingLog;
use App\Services\Routing\BusinessRoutingEngine;
use App\Services\TenantContext;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin', ['title' => 'Business Routing', 'breadcrumb' => 'Workspace / Routing'])]
class RoutingIndex extends Component
{
    use WithPagination;

    public string $businessFilter = 'all';

    public string $search = '';

    // Live Test Simulator State
    public string $testMessage = '';

    public ?array $testResult = null;

    public function mount(): void
    {
        $activeId = TenantContext::getActiveBusinessId();
        if ($activeId) {
            $this->businessFilter = (string) $activeId;
        }
    }

    public function testRoute(BusinessRoutingEngine $engine): void
    {
        if (empty(trim($this->testMessage))) {
            $this->testResult = null;

            return;
        }

        $result = $engine->route($this->testMessage);

        $this->testResult = [
            'status' => $result->status,
            'business_name' => $result->business?->name,
            'business_code' => $result->business?->code,
            'confidence' => $result->confidence,
            'reason' => $result->reason,
            'keywords' => $result->matchedKeywords,
        ];
    }

    public function toggleRule(int $id): void
    {
        $rule = BusinessRoutingRule::findOrFail($id);
        $rule->is_active = ! $rule->is_active;
        $rule->save();

        $this->dispatch('toast', message: "Rule '{$rule->name}' ".($rule->is_active ? 'activated' : 'paused').'.');
    }

    public function deleteRule(int $id): void
    {
        $rule = BusinessRoutingRule::findOrFail($id);
        $rule->delete();

        $this->dispatch('toast', message: "Routing rule '{$rule->name}' deleted.");
    }

    public function setSampleTest(string $text): void
    {
        $this->testMessage = $text;
        $this->testRoute(app(BusinessRoutingEngine::class));
    }

    public function render()
    {
        $rules = BusinessRoutingRule::with('business')
            ->when($this->businessFilter !== 'all', fn ($q) => $q->where('business_id', $this->businessFilter))
            ->when($this->search !== '', fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('priority', 'desc')
            ->latest()
            ->paginate(10);

        $recentLogs = RoutingLog::with(['matchedBusiness', 'contact'])
            ->latest()
            ->take(8)
            ->get();

        return view('livewire.admin.routing.index', [
            'rules' => $rules,
            'businesses' => Business::where('status', 'active')->get(),
            'recentLogs' => $recentLogs,
        ]);
    }
}
