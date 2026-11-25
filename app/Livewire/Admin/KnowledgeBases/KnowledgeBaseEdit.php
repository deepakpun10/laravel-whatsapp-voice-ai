<?php

namespace App\Livewire\Admin\KnowledgeBases;

use App\Models\KnowledgeBase;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Edit Knowledge Base', 'breadcrumb' => 'Workspace / Knowledge Bases / Edit'])]
class KnowledgeBaseEdit extends Component
{
    public KnowledgeBase $knowledgeBase;

    public string $name = '';

    public string $description = '';

    public string $status = 'active';

    public function mount(KnowledgeBase $knowledgeBase): void
    {
        $this->knowledgeBase = $knowledgeBase;
        $this->name = $knowledgeBase->name;
        $this->description = $knowledgeBase->description ?? '';
        $this->status = in_array($knowledgeBase->status, ['inactive', 'disabled']) ? 'inactive' : 'active';
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }

    public function update()
    {
        $validated = $this->validate();

        $this->knowledgeBase->update($validated);

        session()->flash('success', "Knowledge Base '{$this->knowledgeBase->name}' updated.");

        return $this->redirect(route('admin.knowledge-bases.show', $this->knowledgeBase), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.knowledge-bases.edit');
    }
}
