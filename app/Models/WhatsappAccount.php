<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsappAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'phone_number',
        'phone_number_id',
        'waba_id',
        'access_token',
        'webhook_verify_token',
        'is_shared',
        'status',
        'last_error',
        'last_synced_at',
    ];

    protected $casts = [
        'access_token' => 'encrypted',
        'is_shared' => 'boolean',
        'last_synced_at' => 'datetime',
    ];

    /**
     * Attributes hidden from serialization to prevent accidental credential leakage
     */
    protected $hidden = [
        'access_token',
        'webhook_verify_token',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    public function isConfigured(): bool
    {
        return ! empty($this->phone_number_id) && ! empty($this->access_token);
    }
}
