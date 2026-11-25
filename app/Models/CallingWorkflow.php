<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CallingWorkflow extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'ai_agent_id',
        'name',
        'slug',
        'trigger_type',
        'conditions',
        'actions',
        'priority',
        'is_active',
        'execution_count',
    ];

    protected $casts = [
        'conditions' => 'array',
        'actions' => 'array',
        'is_active' => 'boolean',
        'priority' => 'integer',
        'execution_count' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (CallingWorkflow $wf) {
            if (empty($wf->slug)) {
                $baseSlug = Str::slug($wf->name) ?: 'calling-workflow';
                $slug = $baseSlug;
                $c = 1;
                while (static::where('slug', $slug)->exists()) {
                    $c++;
                    $slug = "{$baseSlug}-{$c}";
                }
                $wf->slug = $slug;
            }
        });
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function aiAgent(): BelongsTo
    {
        return $this->belongsTo(AiAgent::class);
    }

    public function runs(): HasMany
    {
        return $this->hasMany(CallingWorkflowRun::class);
    }
}
