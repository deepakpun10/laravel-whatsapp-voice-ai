<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KbChunk extends Model
{
    use HasFactory;

    protected $fillable = [
        'kb_document_id',
        'chunk_index',
        'content',
        'token_count',
        'vector_id',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(KbDocument::class, 'kb_document_id');
    }
}
