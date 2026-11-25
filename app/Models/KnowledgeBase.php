<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class KnowledgeBase extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'slug',
        'description',
        'status',
    ];

    protected static function booted(): void
    {
        static::creating(function (KnowledgeBase $kb) {
            if (empty($kb->slug)) {
                $baseSlug = Str::slug($kb->name) ?: 'kb';
                $slug = $baseSlug;
                $count = 1;
                while (KnowledgeBase::where('business_id', $kb->business_id)->where('slug', $slug)->exists()) {
                    $count++;
                    $slug = "{$baseSlug}-{$count}";
                }
                $kb->slug = $slug;
            }
            if (empty($kb->status)) {
                $kb->status = 'active';
            }
        });
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(KbDocument::class);
    }

    public function aiAgents(): BelongsToMany
    {
        return $this->belongsToMany(AiAgent::class, 'ai_agent_knowledge_base')
            ->withTimestamps();
    }
}
