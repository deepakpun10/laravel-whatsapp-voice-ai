<?php

namespace App\Livewire\Admin\Businesses;

use App\Models\Business;
use Illuminate\Database\UniqueConstraintViolationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Add Business', 'breadcrumb' => 'Workspace / Businesses / Create'])]
class BusinessCreate extends Component
{
    public string $name = '';

    public string $code = '';

    public string $category = '';

    public string $description = '';

    public string $status = 'active';

    public string $timezone = 'Asia/Kolkata';

    public string $default_language = 'hi_en';

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:20', 'unique:businesses,code'],
            'category' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'in:active,inactive'],
            'timezone' => ['required', 'string', 'max:50'],
            'default_language' => ['required', 'string', 'max:20'],
        ];
    }

    public function save()
    {
        $validated = $this->validate();

        if (empty($validated['code'])) {
            $basePrefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $validated['name']), 0, 3));
            if (empty($basePrefix)) {
                $basePrefix = 'BUS';
            }
            $code = $basePrefix.'-'.rand(100, 999);
            while (Business::withTrashed()->where('code', $code)->exists()) {
                $code = $basePrefix.'-'.rand(100, 999);
            }
            $validated['code'] = $code;
        }

        try {
            $business = Business::create($validated);
        } catch (UniqueConstraintViolationException $e) {
            $this->addError('name', 'A business with an identical identifier already exists. Please choose a slightly different name.');

            return;
        }

        if (auth()->check()) {
            $business->users()->attach(auth()->id(), ['role' => 'owner']);
        }

        session()->flash('success', "Business workspace '{$business->name}' created successfully.");

        return $this->redirect(route('admin.businesses.show', $business), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.businesses.create');
    }
}
