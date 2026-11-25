<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InteractiveMessageEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'conversation_id',
        'contact_id',
        'template_id',
        'interactive_type',
        'event_type',
        'selected_id',
        'selected_title',
        'wamid',
        'raw_meta_payload',
    ];

    protected $casts = [
        'raw_meta_payload' => 'array',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(InteractiveMessageTemplate::class, 'template_id');
    }
}
