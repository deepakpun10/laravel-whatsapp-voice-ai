<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InteractiveAgentSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'ai_agent_id',
        'mode',
        'enable_interactive',
        'prefer_buttons_over_text',
        'max_buttons_per_message',
        'allow_list_messages',
        'allow_cta_messages',
        'max_interactive_per_conversation',
        'cooldown_seconds',
        'min_confidence',
    ];

    protected $casts = [
        'enable_interactive' => 'boolean',
        'prefer_buttons_over_text' => 'boolean',
        'allow_list_messages' => 'boolean',
        'allow_cta_messages' => 'boolean',
        'min_confidence' => 'decimal:2',
    ];

    public function aiAgent(): BelongsTo
    {
        return $this->belongsTo(AiAgent::class, 'ai_agent_id');
    }

    public function isManualOnly(): bool
    {
        return $this->mode === 'manual' || ! $this->enable_interactive;
    }

    public function isRuleBased(): bool
    {
        return $this->mode === 'rule_based' && $this->enable_interactive;
    }

    public function isAiAssisted(): bool
    {
        return $this->mode === 'ai_assisted' && $this->enable_interactive;
    }
}
