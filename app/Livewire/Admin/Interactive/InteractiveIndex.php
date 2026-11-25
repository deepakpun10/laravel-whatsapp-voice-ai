<?php

namespace App\Livewire\Admin\Interactive;

use App\Models\AiAgent;
use App\Models\Business;
use App\Models\InteractiveAgentSetting;
use App\Models\InteractiveAutomationRule;
use App\Models\InteractiveMessageEvent;
use App\Models\InteractiveMessageTemplate;
use App\Services\TenantContext;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin', ['title' => 'Interactive Messages', 'breadcrumb' => 'AI Studio / Interactive Messages'])]
class InteractiveIndex extends Component
{
    use WithPagination;

    public string $activeTab = 'templates'; // templates, rules, settings, analytics

    public string $search = '';

    public string $businessFilter = 'all';

    public string $typeFilter = 'all'; // button, list, cta_url

    // Rule creation modal/form state
    public string $rule_name = '';

    public ?int $rule_business_id = null;

    public ?int $rule_agent_id = null;

    public ?int $rule_template_id = null;

    public string $rule_trigger_type = 'intent_detected';

    public string $rule_keywords = '';

    public int $rule_priority = 10;

    public string $rule_send_mode = 'automatic';

    public function mount(): void
    {
        $activeId = TenantContext::getActiveBusinessId();
        if ($activeId) {
            $this->businessFilter = (string) $activeId;
            $this->rule_business_id = $activeId;
        }

        // Initialize default agent settings if missing
        $agents = AiAgent::all();
        foreach ($agents as $agent) {
            InteractiveAgentSetting::firstOrCreate(
                ['ai_agent_id' => $agent->id],
                [
                    'mode' => 'ai_assisted',
                    'enable_interactive' => true,
                    'prefer_buttons_over_text' => true,
                    'max_buttons_per_message' => 3,
                    'allow_list_messages' => true,
                    'allow_cta_messages' => true,
                    'max_interactive_per_conversation' => 5,
                    'cooldown_seconds' => 60,
                ]
            );
        }
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function toggleStatus(int $id): void
    {
        $template = InteractiveMessageTemplate::findOrFail($id);
        $template->status = $template->status === 'active' ? 'inactive' : 'active';
        $template->save();

        $this->dispatch('toast', message: "Template '{$template->name}' is now ".ucfirst($template->status).'.');
    }

    public function toggleRuleStatus(int $id): void
    {
        $rule = InteractiveAutomationRule::findOrFail($id);
        $rule->is_active = ! $rule->is_active;
        $rule->save();

        $this->dispatch('toast', message: "Rule '{$rule->name}' is now ".($rule->is_active ? 'Active' : 'Paused').'.');
    }

    public function duplicateTemplate(int $id): void
    {
        $orig = InteractiveMessageTemplate::with(['buttons', 'sections.rows'])->findOrFail($id);

        $copy = $orig->replicate(['slug']);
        $copy->name = $orig->name.' (Copy)';
        $copy->save();

        foreach ($orig->buttons as $btn) {
            $bCopy = $btn->replicate();
            $bCopy->template_id = $copy->id;
            $bCopy->button_id = $btn->button_id.'_copy_'.rand(10, 99);
            $bCopy->save();
        }

        foreach ($orig->sections as $sec) {
            $sCopy = $sec->replicate();
            $sCopy->template_id = $copy->id;
            $sCopy->save();

            foreach ($sec->rows as $row) {
                $rCopy = $row->replicate();
                $rCopy->section_id = $sCopy->id;
                $rCopy->row_id = $row->row_id.'_copy_'.rand(10, 99);
                $rCopy->save();
            }
        }

        $this->dispatch('toast', message: "Template duplicated as '{$copy->name}'.");
    }

    public function deleteTemplate(int $id): void
    {
        $template = InteractiveMessageTemplate::findOrFail($id);
        $template->delete();

        $this->dispatch('toast', message: "Template '{$template->name}' deleted.");
    }

    public function saveRule(): void
    {
        $this->validate([
            'rule_name' => 'required|string|max:255',
            'rule_business_id' => 'required|exists:businesses,id',
            'rule_template_id' => 'required|exists:interactive_message_templates,id',
            'rule_trigger_type' => 'required|string',
        ]);

        $kwList = array_values(array_filter(array_map('trim', explode(',', $this->rule_keywords))));

        InteractiveAutomationRule::create([
            'business_id' => $this->rule_business_id,
            'ai_agent_id' => $this->rule_agent_id,
            'template_id' => $this->rule_template_id,
            'name' => $this->rule_name,
            'trigger_type' => $this->rule_trigger_type,
            'trigger_keywords' => $kwList,
            'priority' => $this->rule_priority,
            'send_mode' => $this->rule_send_mode,
            'is_active' => true,
        ]);

        $this->reset(['rule_name', 'rule_keywords']);
        $this->dispatch('toast', message: 'Automation Rule created successfully.');
    }

    public function deleteRule(int $id): void
    {
        $rule = InteractiveAutomationRule::findOrFail($id);
        $rule->delete();
        $this->dispatch('toast', message: 'Rule deleted.');
    }

    public function updateAgentMode(int $settingId, string $mode): void
    {
        $setting = InteractiveAgentSetting::findOrFail($settingId);
        $setting->update(['mode' => $mode]);
        $this->dispatch('toast', message: 'AI Agent interactive mode updated to '.ucwords(str_replace('_', ' ', $mode)).'.');
    }

    public function render()
    {
        $businesses = Business::where('status', 'active')->get();

        $templates = InteractiveMessageTemplate::with(['business', 'buttons', 'sections.rows'])
            ->when($this->businessFilter !== 'all', fn ($q) => $q->where('business_id', $this->businessFilter))
            ->when($this->typeFilter !== 'all', fn ($q) => $q->where('interactive_type', $this->typeFilter))
            ->when($this->search, fn ($q) => $q->where(fn ($sub) => $sub->where('name', 'like', "%{$this->search}%")->orWhere('body_text', 'like', "%{$this->search}%")))
            ->latest()
            ->paginate(10);

        $rules = InteractiveAutomationRule::with(['business', 'aiAgent', 'template'])
            ->when($this->businessFilter !== 'all', fn ($q) => $q->where('business_id', $this->businessFilter))
            ->orderBy('priority', 'asc')
            ->get();

        $agentSettings = InteractiveAgentSetting::with('aiAgent.business')->get();

        // Real Analytics Metrics
        $totalSent = InteractiveMessageEvent::where('event_type', 'sent')
            ->when($this->businessFilter !== 'all', fn ($q) => $q->where('business_id', $this->businessFilter))
            ->count();

        $totalClicked = InteractiveMessageEvent::where('event_type', 'clicked')
            ->when($this->businessFilter !== 'all', fn ($q) => $q->where('business_id', $this->businessFilter))
            ->count();

        $ctr = $totalSent > 0 ? round(($totalClicked / $totalSent) * 100, 1) : 0;

        $topSelections = InteractiveMessageEvent::where('event_type', 'clicked')
            ->when($this->businessFilter !== 'all', fn ($q) => $q->where('business_id', $this->businessFilter))
            ->selectRaw('selected_title, count(*) as count')
            ->groupBy('selected_title')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        return view('livewire.admin.interactive.index', [
            'templates' => $templates,
            'rules' => $rules,
            'agentSettings' => $agentSettings,
            'businesses' => $businesses,
            'totalSent' => $totalSent,
            'totalClicked' => $totalClicked,
            'ctr' => $ctr,
            'topSelections' => $topSelections,
        ]);
    }
}
