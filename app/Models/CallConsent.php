<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CallConsent extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id',
        'business_id',
        'phone',
        'consent_status',
        'consent_source',
        'purpose',
        'consented_at',
        'opt_out_at',
        'ip_address',
    ];

    protected $casts = [
        'consent_status' => 'boolean',
        'consented_at' => 'datetime',
        'opt_out_at' => 'datetime',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public static function hasValidConsent(string $phone, ?int $businessId = null): bool
    {
        $cleaned = preg_replace('/[^0-9]/', '', $phone);

        return static::where(function ($q) use ($phone, $cleaned) {
            $q->where('phone', $phone)->orWhere('phone', $cleaned);
        })
            ->when($businessId, fn ($q) => $q->where('business_id', $businessId))
            ->where('consent_status', true)
            ->whereNull('opt_out_at')
            ->exists();
    }
}
