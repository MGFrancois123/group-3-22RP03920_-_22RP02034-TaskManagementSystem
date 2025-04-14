<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'created_by',
    ];

    /**
     * Get the user who created the project.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the tasks for the project.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get the users assigned to this project.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    /**
     * Get the team members (users) assigned to tasks in this project.
     */
    public function teamMembers()
    {
        return $this->hasManyThrough(
            User::class,
            Task::class,
            'project_id', // Foreign key on tasks table
            'id', // Foreign key on users table
            'id', // Local key on projects table
            'assigned_to' // Local key on tasks table
        )->distinct();
    }

    public function getTeamMembersCountAttribute()
    {
        return $this->teamMembers()->count();
    }

    /**
     * Get the progress percentage of the project.
     */
    public function getProgressAttribute()
    {
        $totalTasks = $this->tasks->count();
        if ($totalTasks === 0) {
            return 0;
        }
        $completedTasks = $this->tasks->where('status', 'Completed')->count();
        return round(($completedTasks / $totalTasks) * 100);
    }
} 