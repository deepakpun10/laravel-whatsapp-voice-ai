<?php

namespace App\Livewire\Admin\Routing;

use App\Models\Business;
use App\Models\BusinessRoutingRule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Edit Routing Rule', 'breadcrumb' => 'Workspace / Routing / Edit'])]
class RoutingEdit extends Component
{
    public BusinessRoutingRule $rule;

    public int $business_id;

    public string $name = '';

    public string $keywords = '';

    public string $priority = 'normal';

    public float $min_confidence = 0.85;

    public bool $is_active = true;

    public function mount(BusinessRoutingRule $rule): void
    {
        $this->rule = $rule;
        $this->business_id = $rule->business_id;
        $this->name = $rule->name;
        $this->keywords = implode(', ', $rule->keywords ?? []);
        $this->priority = $rule->priority;
        $this->min_confidence = (float) $rule->min_confidence;
        $this->is_active = $rule->is_active;
    }

    protected function rules(): array
    {
        return [
            'business_id' => ['required', 'exists:businesses,id'],
            'name' => ['required', 'string', 'max:255'],
            'keywords' => ['required', 'string'],
            'priority' => ['required', 'in:high,normal,low'],
            'min_confidence' => ['required', 'numeric', 'min:0.5', 'max:1.0'],
            'is_active' => ['boolean'],
        ];
    }

    public function update()
    {
        $this->validate();

        $kwList = array_values(array_filter(array_map('trim', explode(',', $this->keywords))));

        $this->rule->update([
            'business_id' => $this->business_id,
            'name' => $this->name,
            'keywords' => $kwList,
            'priority' => $this->priority,
            'min_confidence' => $this->min_confidence,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', "Routing rule '{$this->rule->name}' updated successfully.");

        return $this->redirect(route('admin.routing.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.routing.edit', [
            'businesses' => Business::where('status', 'active')->orderBy('name')->get(),
        ]);
    }
}
