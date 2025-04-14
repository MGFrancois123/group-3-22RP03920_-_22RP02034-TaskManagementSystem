<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManagerScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'manager_id',
        'task_id',
        'quality_score',
        'timeliness_score',
        'feedback',
        'evaluation_notes',
        'evaluation_type',
        'evaluation_criteria',
        'needs_follow_up',
        'follow_up_date',
        'evaluated_at'
    ];

    protected $casts = [
        'evaluated_at' => 'datetime',
        'evaluation_criteria' => 'array',
        'needs_follow_up' => 'boolean',
        'follow_up_date' => 'date'
    ];

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
