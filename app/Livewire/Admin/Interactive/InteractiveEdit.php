<?php

namespace App\Livewire\Admin\Interactive;

use App\Models\Business;
use App\Models\InteractiveMessageButton;
use App\Models\InteractiveMessageRow;
use App\Models\InteractiveMessageSection;
use App\Models\InteractiveMessageTemplate;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Edit Interactive Template', 'breadcrumb' => 'AI Studio / Interactive Messages / Edit'])]
class InteractiveEdit extends Component
{
    public InteractiveMessageTemplate $template;

    public ?int $business_id = null;

    public string $name = '';

    public string $category = 'sales_qualification';

    public string $interactive_type = 'button';

    public string $header_text = '';

    public string $body_text = '';

    public string $footer_text = '';

    public string $list_button_label = 'Select Option';

    public string $cta_url = 'https://pundeer.ai';

    public string $cta_display_text = 'Open Website';

    public string $language = 'en';

    public string $status = 'active';

    public bool $ai_availability = true;

    public string $trigger_intent = '';

    public array $buttons = [];

    public array $sections = [];

    public function mount(InteractiveMessageTemplate $template): void
    {
        $this->template = $template->load(['buttons', 'sections.rows']);

        $this->business_id = $template->business_id;
        $this->name = $template->name;
        $this->category = $template->category;
        $this->interactive_type = $template->interactive_type;
        $this->header_text = $template->header_text ?? '';
        $this->body_text = $template->body_text;
        $this->footer_text = $template->footer_text ?? '';
        $this->list_button_label = $template->list_button_label ?? 'Select Option';
        $this->cta_url = $template->cta_url ?? 'https://pundeer.ai';
        $this->cta_display_text = $template->cta_display_text ?? 'Open Website';
        $this->language = $template->language;
        $this->status = $template->status;
        $this->ai_availability = (bool) $template->ai_availability;
        $this->trigger_intent = $template->trigger_intent ?? '';

        foreach ($template->buttons as $btn) {
            $this->buttons[] = [
                'button_id' => $btn->button_id,
                'title' => $btn->title,
                'action_type' => $btn->action_type,
                'action_value' => $btn->action_value ?? '',
            ];
        }

        if (empty($this->buttons) && $this->interactive_type === 'button') {
            $this->buttons[] = ['button_id' => 'btn_1', 'title' => 'Option 1', 'action_type' => 'set_service', 'action_value' => ''];
        }

        foreach ($template->sections as $sec) {
            $rows = [];
            foreach ($sec->rows as $r) {
                $rows[] = [
                    'row_id' => $r->row_id,
                    'title' => $r->title,
                    'description' => $r->description ?? '',
                    'action_type' => $r->action_type,
                    'action_value' => $r->action_value ?? '',
                ];
            }
            $this->sections[] = [
                'title' => $sec->title,
                'rows' => $rows,
            ];
        }

        if (empty($this->sections) && $this->interactive_type === 'list') {
            $this->sections[] = [
                'title' => 'Section 1',
                'rows' => [
                    ['row_id' => 'row_1', 'title' => 'Choice 1', 'description' => '', 'action_type' => 'set_service', 'action_value' => ''],
                ],
            ];
        }
    }

    public function addButton(): void
    {
        if (count($this->buttons) >= 3) {
            $this->dispatch('toast', message: 'WhatsApp allows a maximum of 3 reply buttons.');

            return;
        }

        $nextNum = count($this->buttons) + 1;
        $this->buttons[] = [
            'button_id' => 'btn_opt_'.$nextNum,
            'title' => 'Option '.$nextNum,
            'action_type' => 'set_service',
            'action_value' => '',
        ];
    }

    public function removeButton(int $index): void
    {
        unset($this->buttons[$index]);
        $this->buttons = array_values($this->buttons);
    }

    public function addSection(): void
    {
        $this->sections[] = [
            'title' => 'Section '.(count($this->sections) + 1),
            'rows' => [
                ['row_id' => 'row_'.rand(100, 999), 'title' => 'New Item', 'description' => '', 'action_type' => 'set_service', 'action_value' => ''],
            ],
        ];
    }

    public function removeSection(int $index): void
    {
        unset($this->sections[$index]);
        $this->sections = array_values($this->sections);
    }

    public function addRow(int $sectionIndex): void
    {
        $totalRows = 0;
        foreach ($this->sections as $s) {
            $totalRows += count($s['rows'] ?? []);
        }

        if ($totalRows >= 10) {
            $this->dispatch('toast', message: 'WhatsApp allows a maximum of 10 list rows across all sections.');

            return;
        }

        $this->sections[$sectionIndex]['rows'][] = [
            'row_id' => 'row_'.rand(100, 999),
            'title' => 'Option '.($totalRows + 1),
            'description' => '',
            'action_type' => 'set_service',
            'action_value' => '',
        ];
    }

    public function removeRow(int $sectionIndex, int $rowIndex): void
    {
        unset($this->sections[$sectionIndex]['rows'][$rowIndex]);
        $this->sections[$sectionIndex]['rows'] = array_values($this->sections[$sectionIndex]['rows']);
    }

    public function updateTemplate()
    {
        $this->validate([
            'business_id' => 'required|exists:businesses,id',
            'name' => 'required|string|max:255',
            'body_text' => 'required|string|max:1024',
            'interactive_type' => 'required|in:button,list,cta_url',
        ]);

        $this->template->update([
            'business_id' => $this->business_id,
            'name' => $this->name,
            'category' => $this->category,
            'interactive_type' => $this->interactive_type,
            'header_text' => $this->header_text ?: null,
            'body_text' => $this->body_text,
            'footer_text' => $this->footer_text ?: null,
            'list_button_label' => $this->list_button_label ?: null,
            'cta_url' => $this->cta_url ?: null,
            'cta_display_text' => $this->cta_display_text ?: null,
            'language' => $this->language,
            'status' => $this->status,
            'ai_availability' => $this->ai_availability,
            'trigger_intent' => $this->trigger_intent ?: null,
        ]);

        // Recreate buttons
        $this->template->buttons()->delete();
        if ($this->interactive_type === 'button') {
            foreach ($this->buttons as $idx => $btn) {
                if (! empty($btn['title'])) {
                    InteractiveMessageButton::create([
                        'template_id' => $this->template->id,
                        'button_id' => $btn['button_id'] ?: 'btn_'.$this->template->id.'_'.$idx,
                        'title' => mb_substr($btn['title'], 0, 20),
                        'action_type' => $btn['action_type'] ?? 'set_service',
                        'action_value' => $btn['action_value'] ?? null,
                        'position' => $idx,
                    ]);
                }
            }
        } elseif ($this->interactive_type === 'list') {
            // Delete old sections and rows
            foreach ($this->template->sections as $oldSec) {
                $oldSec->rows()->delete();
                $oldSec->delete();
            }

            $totalCount = 0;
            foreach ($this->sections as $sIdx => $sec) {
                $section = InteractiveMessageSection::create([
                    'template_id' => $this->template->id,
                    'title' => mb_substr($sec['title'] ?? 'Section', 0, 24),
                    'position' => $sIdx,
                ]);

                foreach ($sec['rows'] ?? [] as $rIdx => $row) {
                    if ($totalCount >= 10) {
                        break;
                    }
                    if (! empty($row['title'])) {
                        InteractiveMessageRow::create([
                            'section_id' => $section->id,
                            'row_id' => $row['row_id'] ?: 'row_'.$this->template->id.'_'.$totalCount,
                            'title' => mb_substr($row['title'], 0, 24),
                            'description' => ! empty($row['description']) ? mb_substr($row['description'], 0, 72) : null,
                            'action_type' => $row['action_type'] ?? 'set_service',
                            'action_value' => $row['action_value'] ?? null,
                            'position' => $rIdx,
                        ]);
                        $totalCount++;
                    }
                }
            }
        }

        $this->dispatch('toast', message: 'Template updated successfully.');

        return redirect()->route('admin.interactive.show', $this->template);
    }

    public function render()
    {
        return view('livewire.admin.interactive.edit', [
            'businesses' => Business::where('status', 'active')->get(),
        ]);
    }
}
