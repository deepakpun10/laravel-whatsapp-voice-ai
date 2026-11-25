<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'contact_id',
        'whatsapp_account_id',
        'channel',
        'status',
        'ai_mode',
        'summary',
        'extracted_facts',
        'assigned_agent_id',
        'assigned_user_id',
        'takeover_user_id',
        'takeover_at',
        'last_message_at',
    ];

    protected $casts = [
        'extracted_facts' => 'array',
        'takeover_at' => 'datetime',
        'last_message_at' => 'datetime',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function whatsappAccount(): BelongsTo
    {
        return $this->belongsTo(WhatsappAccount::class);
    }

    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(AiAgent::class, 'assigned_agent_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function takeoverUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'takeover_user_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    public function isAiActive(): bool
    {
        return $this->ai_mode === 'active';
    }

    public function pauseAi(string $reason = ''): void
    {
        $this->update([
            'ai_mode' => 'paused',
            'takeover_at' => now(),
        ]);
    }

    public function resumeAi(): void
    {
        $this->update([
            'ai_mode' => 'active',
            'takeover_user_id' => null,
            'takeover_at' => null,
        ]);
    }

    public function updateFact(string $key, mixed $value): void
    {
        $facts = $this->extracted_facts ?? [];
        $facts[$key] = $value;
        $this->update(['extracted_facts' => $facts]);
    }
}
