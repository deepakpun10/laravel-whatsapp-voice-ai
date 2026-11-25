<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Business extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'code',
        'category',
        'description',
        'status',
        'timezone',
        'default_language',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (Business $business) {
            if (empty($business->uuid)) {
                $business->uuid = (string) Str::uuid();
            }
            if (empty($business->slug)) {
                $baseSlug = Str::slug($business->name) ?: 'business';
                $slug = $baseSlug;
                $count = 1;
                while (Business::withTrashed()->where('slug', $slug)->exists()) {
                    $count++;
                    $slug = "{$baseSlug}-{$count}";
                }
                $business->slug = $slug;
            }
        });
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'business_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function whatsappAccounts(): HasMany
    {
        return $this->hasMany(WhatsappAccount::class);
    }

    public function routingRules(): HasMany
    {
        return $this->hasMany(BusinessRoutingRule::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    public function aiAgents(): HasMany
    {
        return $this->hasMany(AiAgent::class);
    }

    public function knowledgeBases(): HasMany
    {
        return $this->hasMany(KnowledgeBase::class);
    }

    public function calls(): HasMany
    {
        return $this->hasMany(Call::class);
    }

    public function workflows(): HasMany
    {
        return $this->hasMany(Workflow::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(FollowUp::class);
    }

    public function interactiveTemplates(): HasMany
    {
        return $this->hasMany(InteractiveMessageTemplate::class);
    }

    public function interactiveAutomationRules(): HasMany
    {
        return $this->hasMany(InteractiveAutomationRule::class);
    }
}
