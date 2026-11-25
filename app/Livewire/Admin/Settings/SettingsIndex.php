<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Business;
use App\Models\WhatsappAccount;
use App\Services\TenantContext;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Business Settings', 'breadcrumb' => 'Workspace / Settings'])]
class SettingsIndex extends Component
{
    public ?int $business_id = null;

    public string $timezone = 'Asia/Kolkata';

    public string $default_language = 'hi_en';

    public string $handover_destination = 'Business sales team';

    public string $unknown_query_behavior = 'ask_clarification';

    // Shared line rules
    public bool $rule_keep_existing_assignment = true;

    public bool $rule_require_confidence = true;

    public bool $rule_block_ambiguous_replies = true;

    public bool $rule_block_calls_without_route = true;

    public function mount(): void
    {
        $active = TenantContext::getActiveBusiness();
        if ($active) {
            $this->business_id = $active->id;
            $this->timezone = $active->timezone;
            $this->default_language = $active->default_language;
            $this->handover_destination = $active->settings['human_handover_destination'] ?? 'Business sales team';
            $this->unknown_query_behavior = $active->settings['unknown_query_behavior'] ?? 'ask_clarification';
        }
    }

    public function saveSettings(): void
    {
        if ($this->business_id) {
            $biz = Business::find($this->business_id);
            if ($biz) {
                $biz->update([
                    'timezone' => $this->timezone,
                    'default_language' => $this->default_language,
                    'settings' => [
                        'human_handover_destination' => $this->handover_destination,
                        'unknown_query_behavior' => $this->unknown_query_behavior,
                        'rule_keep_existing_assignment' => $this->rule_keep_existing_assignment,
                        'rule_require_confidence' => $this->rule_require_confidence,
                        'rule_block_ambiguous_replies' => $this->rule_block_ambiguous_replies,
                        'rule_block_calls_without_route' => $this->rule_block_calls_without_route,
                    ],
                ]);
            }
        }

        $this->dispatch('toast', message: 'Business settings and routing safeguards updated.');
    }

    public function render()
    {
        return view('livewire.admin.settings.index', [
            'businesses' => Business::where('status', 'active')->get(),
            'sharedLine' => WhatsappAccount::where('is_shared', true)->first(),
        ]);
    }
}
