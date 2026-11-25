<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InteractiveMessageButton extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_id',
        'button_id',
        'title',
        'action_type',
        'action_value',
        'position',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(InteractiveMessageTemplate::class, 'template_id');
    }
}
