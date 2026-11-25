<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InteractiveMessageSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_id',
        'title',
        'position',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(InteractiveMessageTemplate::class, 'template_id');
    }

    public function rows(): HasMany
    {
        return $this->hasMany(InteractiveMessageRow::class, 'section_id')->orderBy('position');
    }
}
