<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Call extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'contact_id',
        'api_connection_id',
        'ai_agent_id',
        'sarvam_phone_number_id',
        'direction',
        'call_type',
        'provider_call_id',
        'campaign_id',
        'sarvam_session_id',
        'from_number',
        'to_number',
        'duration_seconds',
        'started_at',
        'ended_at',
        'status',
        'transcript',
        'summary',
        'recording_url',
        'cost',
        'outcome',
        'extracted_facts',
        'requires_followup',
        'consent_verified_at',
        'consent_source',
        'error_message',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'consent_verified_at' => 'datetime',
        'requires_followup' => 'boolean',
        'extracted_facts' => 'array',
        'cost' => 'decimal:4',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function apiConnection(): BelongsTo
    {
        return $this->belongsTo(ApiConnection::class);
    }

    public function aiAgent(): BelongsTo
    {
        return $this->belongsTo(AiAgent::class);
    }

    public function sarvamPhoneNumber(): BelongsTo
    {
        return $this->belongsTo(SarvamPhoneNumber::class, 'sarvam_phone_number_id');
    }
}
