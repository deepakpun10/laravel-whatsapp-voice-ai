<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CallingWorkflowRun extends Model
{
    use HasFactory;

    protected $fillable = [
        'calling_workflow_id',
        'contact_id',
        'call_id',
        'idempotency_key',
        'status',
        'logs',
        'error_message',
    ];

    protected $casts = [
        'logs' => 'array',
    ];

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(CallingWorkflow::class, 'calling_workflow_id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function call(): BelongsTo
    {
        return $this->belongsTo(Call::class);
    }
}
