<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class AiAgent extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'api_connection_id',
        'fallback_connection_id',
        'name',
        'agent_type',
        'slug',
        'description',
        'model',
        'system_instructions',
        'personality_tone',
        'supported_languages',
        'confidence_threshold',
        'max_response_tokens',
        'unknown_query_policy',
        'human_handover_policy',
        'voice_config',
        'is_active',
    ];

    protected $casts = [
        'supported_languages' => 'array',
        'voice_config' => 'array',
        'confidence_threshold' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (AiAgent $agent) {
            if (empty($agent->slug)) {
                $agent->slug = Str::slug($agent->name);
            }
        });
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function apiConnection(): BelongsTo
    {
        return $this->belongsTo(ApiConnection::class);
    }

    public function fallbackConnection(): BelongsTo
    {
        return $this->belongsTo(ApiConnection::class, 'fallback_connection_id');
    }

    public function knowledgeBases(): BelongsToMany
    {
        return $this->belongsToMany(KnowledgeBase::class, 'ai_agent_knowledge_base')
            ->withTimestamps();
    }

    public function interactiveSetting(): HasOne
    {
        return $this->hasOne(InteractiveAgentSetting::class);
    }

    public function interactiveRules(): HasMany
    {
        return $this->hasMany(InteractiveAutomationRule::class);
    }
}
