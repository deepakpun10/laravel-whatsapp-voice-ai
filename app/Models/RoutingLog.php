<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoutingLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'whatsapp_account_id',
        'contact_id',
        'incoming_text',
        'matched_business_id',
        'confidence',
        'status',
        'reason',
        'details',
    ];

    protected $casts = [
        'confidence' => 'decimal:2',
        'details' => 'array',
    ];

    public function whatsappAccount(): BelongsTo
    {
        return $this->belongsTo(WhatsappAccount::class);
    }

    public function matchedBusiness(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'matched_business_id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }
}
