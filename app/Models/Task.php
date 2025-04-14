<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    // Define status constants
    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'name',
        'description',
        'project_id',
        'assigned_to',
        'status',
        'due_date',
        'created_by',
        'max_attempts',
        'time_limit',
        'passing_grade',
        'category',
        'priority'
    ];

    protected $casts = [
        'due_date' => 'date',
        'max_attempts' => 'integer',
        'time_limit' => 'integer',
        'passing_grade' => 'float'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function grade()
    {
        return $this->hasOne(TaskGrade::class);
    }

    public function getGradeAttribute()
    {
        return $this->grade()->first();
    }

    public function updateStatus($status)
    {
        $validStatuses = [
            self::STATUS_PENDING,
            self::STATUS_IN_PROGRESS,
            self::STATUS_COMPLETED
        ];

        $status = strtolower($status);
        if (in_array($status, $validStatuses)) {
            $this->update(['status' => $status]);
            return true;
        }
        return false;
    }

    public function submissions()
    {
        return $this->hasMany(TaskSubmission::class);
    }

    public function latestSubmission()
    {
        return $this->hasOne(TaskSubmission::class)->latest('submitted_at');
    }

    public function bestSubmission()
    {
        return $this->hasOne(TaskSubmission::class)
            ->where(function($query) {
                $query->whereNotNull('quality_score')
                    ->orWhereNotNull('timeliness_score');
            })
            ->orderByRaw('(COALESCE(quality_score, 0) + COALESCE(timeliness_score, 0))/2 DESC');
    }

    public function managerScores()
    {
        return $this->hasMany(ManagerScore::class);
    }

    public function getAverageManagerScore()
    {
        return $this->managerScores()->avg('quality_score');
    }

    public function taskScores()
    {
        return $this->hasMany(TaskScore::class);
    }

    public function getUserScore($userId)
    {
        return $this->taskScores()
            ->where('user_id', $userId)
            ->latest('evaluated_at')
            ->first();
    }

    // Helper method to get status label for display
    public function getStatusLabelAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->status));
    }
}
