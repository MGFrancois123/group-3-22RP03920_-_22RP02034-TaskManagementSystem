<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'quality_score',
        'timeliness_score',
        'average_score',
        'feedback',
        'evaluated_by',
        'evaluated_at'
    ];

    protected $casts = [
        'evaluated_at' => 'datetime',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }
}
