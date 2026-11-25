<?php

namespace App\Livewire\Admin\Workflows;

use App\Models\AiAgent;
use App\Models\Business;
use App\Models\CallingWorkflow;
use App\Models\CallingWorkflowRun;
use App\Services\TenantContext;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Calling Workflows', 'breadcrumb' => 'Automation / Calling Workflows'])]
class CallingWorkflowIndex extends Component
{
    public string $businessFilter = 'all';

    public string $triggerFilter = 'all';

    public string $search = '';

    // Modal state for creating/editing workflow
    public bool $showModal = false;

    public ?int $editingId = null;

    public ?int $form_business_id = null;

    public ?int $form_agent_id = null;

    public string $form_name = '';

    public string $form_trigger_type = 'callback_requested';

    public int $form_priority = 10;

    public bool $form_is_active = true;

    // Conditions
    public int $cond_min_score = 40;

    public int $cond_min_budget = 0;

    public string $cond_lead_stage = 'any';

    public bool $cond_require_consent = true;

    // Actions
    public string $action_primary = 'initiate_call'; // initiate_call, schedule_call, assign_human, send_whatsapp

    public int $action_delay_minutes = 0;

    public string $action_whatsapp_template = '';

    public function mount(): void
    {
        $activeId = TenantContext::getActiveBusinessId();
        if ($activeId) {
            $this->businessFilter = (string) $activeId;
            $this->form_business_id = $activeId;
        }
    }

    public function openCreateModal(): void
    {
        $this->reset([
            'editingId',
            'form_name',
            'action_whatsapp_template',
        ]);
        $this->form_trigger_type = 'callback_requested';
        $this->form_priority = 10;
        $this->form_is_active = true;
        $this->cond_min_score = 40;
        $this->cond_min_budget = 0;
        $this->cond_lead_stage = 'any';
        $this->cond_require_consent = true;
        $this->action_primary = 'initiate_call';
        $this->action_delay_minutes = 0;

        $this->showModal = true;
    }

    public function editWorkflow(int $id): void
    {
        $wf = CallingWorkflow::findOrFail($id);
        $this->editingId = $wf->id;
        $this->form_business_id = $wf->business_id;
        $this->form_agent_id = $wf->ai_agent_id;
        $this->form_name = $wf->name;
        $this->form_trigger_type = $wf->trigger_type;
        $this->form_priority = $wf->priority;
        $this->form_is_active = $wf->is_active;

        $conds = $wf->conditions ?? [];
        $this->cond_min_score = $conds['min_score'] ?? 40;
        $this->cond_min_budget = $conds['min_budget'] ?? 0;
        $this->cond_lead_stage = $conds['lead_stage'] ?? 'any';
        $this->cond_require_consent = $conds['require_consent'] ?? true;

        $acts = $wf->actions ?? [];
        $this->action_primary = $acts['primary_action'] ?? 'initiate_call';
        $this->action_delay_minutes = $acts['delay_minutes'] ?? 0;
        $this->action_whatsapp_template = $acts['whatsapp_template'] ?? '';

        $this->showModal = true;
    }

    public function saveWorkflow(): void
    {
        $this->validate([
            'form_name' => 'required|string|max:255',
            'form_business_id' => 'required|exists:businesses,id',
            'form_trigger_type' => 'required|string',
        ]);

        $conditions = [
            'min_score' => $this->cond_min_score,
            'min_budget' => $this->cond_min_budget,
            'lead_stage' => $this->cond_lead_stage,
            'require_consent' => $this->cond_require_consent,
        ];

        $actions = [
            'primary_action' => $this->action_primary,
            'delay_minutes' => $this->action_delay_minutes,
            'whatsapp_template' => $this->action_whatsapp_template,
        ];

        CallingWorkflow::updateOrCreate(
            ['id' => $this->editingId],
            [
                'business_id' => $this->form_business_id,
                'ai_agent_id' => $this->form_agent_id,
                'name' => $this->form_name,
                'trigger_type' => $this->form_trigger_type,
                'conditions' => $conditions,
                'actions' => $actions,
                'priority' => $this->form_priority,
                'is_active' => $this->form_is_active,
            ]
        );

        $this->showModal = false;
        $this->dispatch('toast', message: 'Calling workflow saved successfully.');
    }

    public function toggleActive(int $id): void
    {
        $wf = CallingWorkflow::findOrFail($id);
        $wf->is_active = ! $wf->is_active;
        $wf->save();
        $this->dispatch('toast', message: "Workflow '{$wf->name}' is now ".($wf->is_active ? 'Active' : 'Paused').'.');
    }

    public function duplicateWorkflow(int $id): void
    {
        $orig = CallingWorkflow::findOrFail($id);
        $copy = $orig->replicate(['slug']);
        $copy->name = $orig->name.' (Copy)';
        $copy->execution_count = 0;
        $copy->save();

        $this->dispatch('toast', message: "Workflow duplicated as '{$copy->name}'.");
    }

    public function deleteWorkflow(int $id): void
    {
        CallingWorkflow::findOrFail($id)->delete();
        $this->dispatch('toast', message: 'Workflow deleted.');
    }

    public function render()
    {
        $workflows = CallingWorkflow::with(['business', 'aiAgent', 'runs'])
            ->when($this->businessFilter !== 'all', fn ($q) => $q->where('business_id', $this->businessFilter))
            ->when($this->triggerFilter !== 'all', fn ($q) => $q->where('trigger_type', $this->triggerFilter))
            ->when($this->search !== '', fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('priority', 'asc')
            ->get();

        $businesses = Business::where('status', 'active')->get();
        $agents = AiAgent::where('is_active', true)->get();
        $recentRuns = CallingWorkflowRun::with('workflow')->latest()->take(6)->get();

        return view('livewire.admin.workflows.calling-workflow-index', [
            'workflows' => $workflows,
            'businesses' => $businesses,
            'agents' => $agents,
            'recentRuns' => $recentRuns,
        ]);
    }
}
