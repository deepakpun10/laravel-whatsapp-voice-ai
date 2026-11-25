<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApiConnection extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'provider_type',
        'provider_name',
        'base_url',
        'api_key',
        'model_or_agent_id',
        'organization_id',
        'custom_headers',
        'priority',
        'timeout',
        'is_active',
        'last_health_status',
        'last_health_checked_at',
        'last_health_error',
    ];

    protected $casts = [
        'api_key' => 'encrypted',
        'custom_headers' => 'array',
        'is_active' => 'boolean',
        'last_health_checked_at' => 'datetime',
    ];

    /**
     * Attributes hidden from serialization to prevent accidental credential leakage
     */
    protected $hidden = [
        'api_key',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function aiAgents(): HasMany
    {
        return $this->hasMany(AiAgent::class);
    }

    public function calls(): HasMany
    {
        return $this->hasMany(Call::class);
    }
}
