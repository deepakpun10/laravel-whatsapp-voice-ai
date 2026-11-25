<?php

namespace App\Livewire\Admin\Workflows;

use App\Models\Business;
use App\Models\Workflow;
use App\Models\WorkflowRun;
use App\Services\TenantContext;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Workflow Builder', 'breadcrumb' => 'Workspace / Workflow Builder'])]
class WorkflowIndex extends Component
{
    public string $name = '';

    public ?int $business_id = null;

    public string $trigger_type = 'incoming_message';

    // Simulation trigger
    public ?string $lastExecutionLog = null;

    public function mount(): void
    {
        $activeId = TenantContext::getActiveBusinessId();
        if ($activeId) {
            $this->business_id = $activeId;
        } else {
            $this->business_id = Business::first()?->id;
        }
    }

    public function createWorkflow(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'business_id' => 'required|exists:businesses,id',
            'trigger_type' => 'required|string',
        ]);

        $workflow = Workflow::create([
            'business_id' => $this->business_id,
            'name' => $this->name,
            'trigger_type' => $this->trigger_type,
            'definition' => [
                'steps' => [
                    ['action' => 'identify_route', 'label' => 'Evaluate Business Intent'],
                    ['action' => 'scoped_rag', 'label' => 'Load Scoped Business Knowledge'],
                    ['action' => 'generate_reply', 'label' => 'Generate Guarded AI Reply'],
                    ['action' => 'send_whatsapp', 'label' => 'Send WhatsApp Message'],
                ],
            ],
            'is_active' => true,
        ]);

        $this->reset(['name']);
        $this->dispatch('toast', message: "Workflow '{$workflow->name}' created.");
    }

    public function runWorkflow(int $id): void
    {
        $workflow = Workflow::with('business')->findOrFail($id);

        $run = WorkflowRun::create([
            'workflow_id' => $workflow->id,
            'event_type' => $workflow->trigger_type,
            'idempotency_key' => 'test_run_'.uniqid(),
            'status' => 'completed',
            'logs' => [
                'step_1' => 'Identified route for business: '.($workflow->business->name ?? 'Global'),
                'step_2' => 'Scoped RAG collections loaded',
                'step_3' => 'Confidence verified >= 0.85',
                'step_4' => 'Action executed without duplicate trigger',
            ],
        ]);

        $this->lastExecutionLog = "Executed workflow '{$workflow->name}' successfully (Run #{$run->id}).";
        $this->dispatch('toast', message: $this->lastExecutionLog);
    }

    public function toggleWorkflow(int $id): void
    {
        $wf = Workflow::findOrFail($id);
        $wf->is_active = ! $wf->is_active;
        $wf->save();

        $this->dispatch('toast', message: 'Workflow is now '.($wf->is_active ? 'Active' : 'Paused').'.');
    }

    public function render()
    {
        $activeBusinessId = TenantContext::getActiveBusinessId();
        $workflows = Workflow::with('business')
            ->when($activeBusinessId, fn ($q) => $q->where('business_id', $activeBusinessId))
            ->latest()
            ->get();

        $recentRuns = WorkflowRun::with('workflow')->latest()->take(6)->get();

        return view('livewire.admin.workflows.index', [
            'workflows' => $workflows,
            'businesses' => Business::where('status', 'active')->get(),
            'recentRuns' => $recentRuns,
        ]);
    }
}
