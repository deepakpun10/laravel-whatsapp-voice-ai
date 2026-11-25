<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CallingSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'is_automated_calling_enabled',
        'inbound_enabled',
        'outbound_enabled',
        'default_phone_number_id',
        'default_ai_agent_id',
        'allowed_days',
        'start_time',
        'end_time',
        'timezone',
        'emergency_pause',
        'min_lead_score',
        'min_budget',
        'require_explicit_consent',
        'max_retry_attempts',
        'retry_interval_minutes',
        'retry_on_busy',
        'retry_on_no_answer',
        'stop_on_decline',
    ];

    protected $casts = [
        'is_automated_calling_enabled' => 'boolean',
        'inbound_enabled' => 'boolean',
        'outbound_enabled' => 'boolean',
        'allowed_days' => 'array',
        'emergency_pause' => 'boolean',
        'require_explicit_consent' => 'boolean',
        'retry_on_busy' => 'boolean',
        'retry_on_no_answer' => 'boolean',
        'stop_on_decline' => 'boolean',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function defaultPhoneNumber(): BelongsTo
    {
        return $this->belongsTo(SarvamPhoneNumber::class, 'default_phone_number_id');
    }

    public function defaultAiAgent(): BelongsTo
    {
        return $this->belongsTo(AiAgent::class, 'default_ai_agent_id');
    }

    /**
     * Check whether current time is within permitted calling hours
     */
    public function isWithinCallingHours(?Carbon $now = null): bool
    {
        if ($this->emergency_pause) {
            return false;
        }

        $now = $now ?? Carbon::now($this->timezone ?? 'Asia/Kolkata');
        $currentDay = strtolower($now->format('l'));

        $allowedDays = $this->allowed_days ?? ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        if (! in_array($currentDay, array_map('strtolower', $allowedDays))) {
            return false;
        }

        $startTime = Carbon::createFromTimeString($this->start_time ?? '09:30', $this->timezone ?? 'Asia/Kolkata');
        $endTime = Carbon::createFromTimeString($this->end_time ?? '19:30', $this->timezone ?? 'Asia/Kolkata');

        return $now->between($startTime, $endTime);
    }
}
