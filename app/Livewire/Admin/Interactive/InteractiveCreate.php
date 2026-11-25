<?php

namespace App\Livewire\Admin\Interactive;

use App\Models\Business;
use App\Models\InteractiveMessageButton;
use App\Models\InteractiveMessageRow;
use App\Models\InteractiveMessageSection;
use App\Models\InteractiveMessageTemplate;
use App\Services\TenantContext;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Create Interactive Message', 'breadcrumb' => 'AI Studio / Interactive Messages / Create'])]
class InteractiveCreate extends Component
{
    public ?int $business_id = null;

    public string $name = '';

    public string $category = 'sales_qualification';

    public string $interactive_type = 'button'; // button, list, cta_url

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

    // Dynamic Reply Buttons (Max 3)
    public array $buttons = [
        ['button_id' => 'btn_opt_1', 'title' => 'Option 1', 'action_type' => 'set_service', 'action_value' => ''],
    ];

    // Dynamic List Sections & Rows (Max 10 rows total)
    public array $sections = [
        [
            'title' => 'Popular Options',
            'rows' => [
                ['row_id' => 'row_opt_1', 'title' => 'Choice A', 'description' => 'Fast delivery', 'action_type' => 'set_service', 'action_value' => ''],
            ],
        ],
    ];

    public function mount(): void
    {
        $activeId = TenantContext::getActiveBusinessId();
        if ($activeId) {
            $this->business_id = $activeId;
        } else {
            $this->business_id = Business::where('status', 'active')->first()?->id;
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

    public function saveTemplate()
    {
        $this->validate([
            'business_id' => 'required|exists:businesses,id',
            'name' => 'required|string|max:255',
            'body_text' => 'required|string|max:1024',
            'interactive_type' => 'required|in:button,list,cta_url',
            'header_text' => 'nullable|string|max:60',
            'footer_text' => 'nullable|string|max:60',
        ]);

        if ($this->interactive_type === 'button' && empty($this->buttons)) {
            $this->dispatch('toast', message: 'Please add at least 1 reply button.');

            return;
        }

        $template = InteractiveMessageTemplate::create([
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

        // Save buttons
        if ($this->interactive_type === 'button') {
            foreach ($this->buttons as $idx => $btn) {
                if (! empty($btn['title'])) {
                    InteractiveMessageButton::create([
                        'template_id' => $template->id,
                        'button_id' => $btn['button_id'] ?: 'btn_'.$template->id.'_'.$idx,
                        'title' => mb_substr($btn['title'], 0, 20),
                        'action_type' => $btn['action_type'] ?? 'set_service',
                        'action_value' => $btn['action_value'] ?? null,
                        'position' => $idx,
                    ]);
                }
            }
        } elseif ($this->interactive_type === 'list') {
            $totalCount = 0;
            foreach ($this->sections as $sIdx => $sec) {
                $section = InteractiveMessageSection::create([
                    'template_id' => $template->id,
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
                            'row_id' => $row['row_id'] ?: 'row_'.$template->id.'_'.$totalCount,
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

        $this->dispatch('toast', message: "Interactive template '{$template->name}' created successfully!");

        return redirect()->route('admin.interactive.index');
    }

    public function render()
    {
        return view('livewire.admin.interactive.create', [
            'businesses' => Business::where('status', 'active')->get(),
        ]);
    }
}
