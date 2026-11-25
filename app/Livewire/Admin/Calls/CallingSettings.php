<?php

namespace App\Livewire\Admin\Calls;

use App\Models\AiAgent;
use App\Models\Business;
use App\Models\CallingSetting;
use App\Models\SarvamPhoneNumber;
use App\Services\TenantContext;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Voice Calling Settings', 'breadcrumb' => 'AI Studio / Calling Settings'])]
class CallingSettings extends Component
{
    public ?int $business_id = null;

    public bool $is_automated_calling_enabled = false;

    public bool $inbound_enabled = true;

    public bool $outbound_enabled = true;

    public ?int $default_phone_number_id = null;

    public ?int $default_ai_agent_id = null;

    public array $allowed_days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

    public string $start_time = '09:30';

    public string $end_time = '19:30';

    public string $timezone = 'Asia/Kolkata';

    public bool $emergency_pause = false;

    public int $min_lead_score = 30;

    public int $min_budget = 0;

    public bool $require_explicit_consent = true;

    public int $max_retry_attempts = 3;

    public int $retry_interval_minutes = 60;

    public bool $retry_on_busy = true;

    public bool $retry_on_no_answer = true;

    public bool $stop_on_decline = true;

    public function mount(?int $business_id = null): void
    {
        $this->business_id = $business_id ?? TenantContext::getActiveBusinessId();
        $this->loadSettings();
    }

    public function updatedBusinessId(): void
    {
        $this->loadSettings();
    }

    protected function loadSettings(): void
    {
        $setting = CallingSetting::where('business_id', $this->business_id)->first()
            ?? CallingSetting::first();

        if ($setting) {
            $this->is_automated_calling_enabled = $setting->is_automated_calling_enabled;
            $this->inbound_enabled = $setting->inbound_enabled;
            $this->outbound_enabled = $setting->outbound_enabled;
            $this->default_phone_number_id = $setting->default_phone_number_id;
            $this->default_ai_agent_id = $setting->default_ai_agent_id;
            $this->allowed_days = $setting->allowed_days ?? ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
            $this->start_time = $setting->start_time ?? '09:30';
            $this->end_time = $setting->end_time ?? '19:30';
            $this->timezone = $setting->timezone ?? 'Asia/Kolkata';
            $this->emergency_pause = $setting->emergency_pause;
            $this->min_lead_score = $setting->min_lead_score;
            $this->min_budget = $setting->min_budget;
            $this->require_explicit_consent = $setting->require_explicit_consent;
            $this->max_retry_attempts = $setting->max_retry_attempts;
            $this->retry_interval_minutes = $setting->retry_interval_minutes;
            $this->retry_on_busy = $setting->retry_on_busy;
            $this->retry_on_no_answer = $setting->retry_on_no_answer;
            $this->stop_on_decline = $setting->stop_on_decline;
        }
    }

    public function saveSettings(): void
    {
        CallingSetting::updateOrCreate(
            ['business_id' => $this->business_id],
            [
                'is_automated_calling_enabled' => $this->is_automated_calling_enabled,
                'inbound_enabled' => $this->inbound_enabled,
                'outbound_enabled' => $this->outbound_enabled,
                'default_phone_number_id' => $this->default_phone_number_id,
                'default_ai_agent_id' => $this->default_ai_agent_id,
                'allowed_days' => $this->allowed_days,
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
                'timezone' => $this->timezone,
                'emergency_pause' => $this->emergency_pause,
                'min_lead_score' => $this->min_lead_score,
                'min_budget' => $this->min_budget,
                'require_explicit_consent' => $this->require_explicit_consent,
                'max_retry_attempts' => $this->max_retry_attempts,
                'retry_interval_minutes' => $this->retry_interval_minutes,
                'retry_on_busy' => $this->retry_on_busy,
                'retry_on_no_answer' => $this->retry_on_no_answer,
                'stop_on_decline' => $this->stop_on_decline,
            ]
        );

        $this->dispatch('toast', message: 'Calling Settings saved successfully.');
    }

    public function toggleEmergencyPause(): void
    {
        $this->emergency_pause = ! $this->emergency_pause;
        $this->saveSettings();
        $this->dispatch('toast', message: $this->emergency_pause ? '⚠️ EMERGENCY PAUSE ACTIVATED: All automated voice calls stopped.' : 'Emergency pause lifted.');
    }

    public function render()
    {
        $businesses = Business::where('status', 'active')->get();
        $phoneNumbers = SarvamPhoneNumber::where('status', 'active')->get();
        $agents = AiAgent::where('is_active', true)->get();

        return view('livewire.admin.calls.calling-settings', [
            'businesses' => $businesses,
            'phoneNumbers' => $phoneNumbers,
            'agents' => $agents,
        ]);
    }
}
