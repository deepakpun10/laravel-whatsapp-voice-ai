<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InteractiveMessageRow extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_id',
        'row_id',
        'title',
        'description',
        'action_type',
        'action_value',
        'position',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(InteractiveMessageSection::class, 'section_id');
    }
}
