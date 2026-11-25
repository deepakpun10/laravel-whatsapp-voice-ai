<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'channel',
        'template_name',
        'audience_filter',
        'scheduled_at',
        'status',
        'total_recipients',
        'sent_count',
        'delivered_count',
        'reply_count',
        'error_message',
    ];

    protected $casts = [
        'audience_filter' => 'array',
        'scheduled_at' => 'datetime',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
