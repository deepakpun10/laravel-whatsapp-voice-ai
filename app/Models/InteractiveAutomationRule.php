<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InteractiveAutomationRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'ai_agent_id',
        'template_id',
        'name',
        'trigger_type',
        'trigger_keywords',
        'condition_lead_stage',
        'priority',
        'is_active',
        'send_mode',
    ];

    protected $casts = [
        'trigger_keywords' => 'array',
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function aiAgent(): BelongsTo
    {
        return $this->belongsTo(AiAgent::class, 'ai_agent_id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(InteractiveMessageTemplate::class, 'template_id');
    }
}
