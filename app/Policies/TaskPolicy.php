<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Task $task): bool
    {
        return $user->isAdmin() ||
               $user->isManager() ||
               $task->assigned_to === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    public function submit(User $user, Task $task): bool
    {
        return $user->isAdmin() || $user->id === $task->assigned_to;
    }


    public function update(User $user, Task $task): bool
    {
        return $user->isAdmin() ||
               ($user->isManager() && $task->project->created_by === $user->id) ||
               $task->assigned_to === $user->id;
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->isAdmin() ||
               ($user->isManager() && $task->project->created_by === $user->id);
    }

    public function evaluate(User $user, Task $task): bool
    {
        return $user->isAdmin() ||
               ($user->isManager() && $task->project->created_by === $user->id);
    }

    public function viewReport(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }
}
