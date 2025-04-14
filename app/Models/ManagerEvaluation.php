<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManagerEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'manager_id',
        'user_id',
        'task_id',
        'submission_id',
        'quality_score',
        'timeliness_score',
        'average_score',
        'feedback',
        'evaluation_notes',
        'evaluation_type',
        'evaluation_criteria',
        'needs_follow_up',
        'follow_up_date'
    ];

    protected $casts = [
        'evaluation_criteria' => 'array',
        'needs_follow_up' => 'boolean',
        'follow_up_date' => 'date'
    ];

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function submission()
    {
        return $this->belongsTo(TaskSubmission::class);
    }
}
