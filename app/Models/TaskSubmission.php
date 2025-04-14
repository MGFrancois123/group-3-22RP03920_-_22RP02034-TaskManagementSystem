<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'submission_notes',
        'file_path',
        'original_filename',
        'quality_score',
        'timeliness_score',
        'average_score',
        'feedback',
        'evaluation_notes',
        'evaluation_type',
        'evaluation_criteria',
        'needs_follow_up',
        'follow_up_date',
        'evaluated_by',
        'evaluated_at',
        'submitted_at'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'evaluated_at' => 'datetime',
        'evaluation_criteria' => 'array',
        'needs_follow_up' => 'boolean',
        'follow_up_date' => 'date'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }
}
