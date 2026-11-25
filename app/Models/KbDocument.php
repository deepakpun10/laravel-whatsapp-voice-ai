<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KbDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'knowledge_base_id',
        'title',
        'source_type',
        'file_path',
        'file_type',
        'file_size',
        'chunk_count',
        'status',
        'error_message',
        'metadata',
        'indexed_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'indexed_at' => 'datetime',
    ];

    public function knowledgeBase(): BelongsTo
    {
        return $this->belongsTo(KnowledgeBase::class);
    }

    public function chunks(): HasMany
    {
        return $this->hasMany(KbChunk::class);
    }
}
