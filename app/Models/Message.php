<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_type',
        'sender_id',
        'message_id',
        'message_type',
        'content',
        'media_url',
        'media_type',
        'status',
        'error_message',
        'metadata',
        'raw_payload',
    ];

    protected $casts = [
        'metadata' => 'array',
        'raw_payload' => 'array',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }
}
