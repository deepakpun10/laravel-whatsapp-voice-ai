<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SarvamPhoneNumber extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'api_connection_id',
        'ai_agent_id',
        'phone_number',
        'label',
        'status',
        'inbound_enabled',
        'outbound_enabled',
        'inbound_deployment_id',
        'telephony_connection_id',
        'provider',
        'settings',
    ];

    protected $casts = [
        'inbound_enabled' => 'boolean',
        'outbound_enabled' => 'boolean',
        'settings' => 'array',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function apiConnection(): BelongsTo
    {
        return $this->belongsTo(ApiConnection::class);
    }

    public function aiAgent(): BelongsTo
    {
        return $this->belongsTo(AiAgent::class);
    }

    public function calls(): HasMany
    {
        return $this->hasMany(Call::class, 'sarvam_phone_number_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
