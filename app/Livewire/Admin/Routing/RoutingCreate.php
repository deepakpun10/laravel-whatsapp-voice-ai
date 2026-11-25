<?php

namespace App\Livewire\Admin\Routing;

use App\Models\Business;
use App\Models\BusinessRoutingRule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Create Routing Rule', 'breadcrumb' => 'Workspace / Routing / Create'])]
class RoutingCreate extends Component
{
    public ?int $business_id = null;

    public string $name = '';

    public string $keywords = ''; // Comma-separated

    public string $priority = 'normal';

    public float $min_confidence = 0.85;

    public bool $is_active = true;

    public function mount(): void
    {
        if (request()->has('business_id')) {
            $this->business_id = (int) request('business_id');
        } else {
            $first = Business::first();
            $this->business_id = $first?->id;
        }
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

    public function save()
    {
        $this->validate();

        $kwList = array_values(array_filter(array_map('trim', explode(',', $this->keywords))));

        $rule = BusinessRoutingRule::create([
            'business_id' => $this->business_id,
            'name' => $this->name,
            'keywords' => $kwList,
            'priority' => $this->priority,
            'min_confidence' => $this->min_confidence,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', "Routing rule '{$rule->name}' created successfully.");

        return $this->redirect(route('admin.routing.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.routing.create', [
            'businesses' => Business::where('status', 'active')->orderBy('name')->get(),
        ]);
    }
}
