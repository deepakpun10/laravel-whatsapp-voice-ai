<?php

namespace App\Livewire\Admin\Businesses;

use App\Models\Business;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Edit Business', 'breadcrumb' => 'Workspace / Businesses / Edit'])]
class BusinessEdit extends Component
{
    public Business $business;

    public string $name = '';

    public string $code = '';

    public string $category = '';

    public string $description = '';

    public string $status = 'active';

    public string $timezone = 'Asia/Kolkata';

    public string $default_language = 'hi_en';

    public function mount(Business $business): void
    {
        $this->business = $business;
        $this->name = $business->name;
        $this->code = $business->code ?? '';
        $this->category = $business->category ?? '';
        $this->description = $business->description ?? '';
        $this->status = $business->status ?? 'active';
        $this->timezone = $business->timezone ?? 'Asia/Kolkata';
        $this->default_language = $business->default_language ?? 'hi_en';
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:20', Rule::unique('businesses', 'code')->ignore($this->business->id)],
            'category' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'in:active,inactive'],
            'timezone' => ['required', 'string', 'max:50'],
            'default_language' => ['required', 'string', 'max:20'],
        ];
    }

    public function update()
    {
        $validated = $this->validate();

        $this->business->update($validated);

        session()->flash('success', "Business '{$this->business->name}' updated successfully.");

        return $this->redirect(route('admin.businesses.show', $this->business), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.businesses.edit');
    }
}
