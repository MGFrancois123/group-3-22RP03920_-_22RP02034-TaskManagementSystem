<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'last_login_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
    ];

    protected $appends = [
        'tasks_created_count',
        'tasks_completed_count'
    ];

    /**
     * Check if the user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user is a manager
     */
    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    /**
     * Check if the user is a regular user
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Get the projects created by the user
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }

    /**
     * Get tasks assigned to the user
     */
    public function tasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    /**
     * Get tasks created by the user
     */
    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    public function teamProjects()
    {
        return $this->belongsToMany(Project::class, 'project_user', 'user_id', 'project_id');
    }

    public function getTasksCreatedCountAttribute()
    {
        return $this->createdTasks()->count();
    }

    public function getTasksCompletedCountAttribute()
    {
        return $this->tasks()->where('status', 'Completed')->count();
    }

    public function updateAverageScore()
    {
        $scores = $this->tasks()
            ->whereNotNull('quality_score')
            ->get();

        if ($scores->count() > 0) {
            $this->average_quality_score = $scores->avg('quality_score');
            $this->save();
        }
    }

    public function updateLastLoginAt()
    {
        $this->update(['last_login_at' => now()]);
    }

    public function getAverageManagerScore()
    {
        $scores = $this->tasks()
            ->whereHas('managerScores')
            ->with('managerScores')
            ->get()
            ->pluck('managerScores')
            ->flatten()
            ->pluck('quality_score');

        if ($scores->isEmpty()) {
            return null;
        }

        return round($scores->avg(), 2);
    }
}
