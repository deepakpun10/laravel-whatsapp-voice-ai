<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessRoutingRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'keywords',
        'priority',
        'min_confidence',
        'is_active',
    ];

    protected $casts = [
        'keywords' => 'array',
        'min_confidence' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
