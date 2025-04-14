<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Get the current manager's ID
            $managerId = auth()->id();

            // Get team projects (projects where the manager is a member)
            $teamProjects = Project::whereHas('users', function ($query) use ($managerId) {
                $query->where('user_id', $managerId);
            })
            ->with(['tasks', 'users'])
            ->latest()
            ->get();

            // Get all tasks that are either:
            // 1. In projects where manager is a member
            // 2. Created by the manager
            // 3. Assigned by the manager
            $teamTasks = Task::where(function($query) use ($managerId) {
                $query->whereHas('project.users', function ($q) use ($managerId) {
                    $q->where('user_id', $managerId);
                })
                ->orWhere('created_by', $managerId); // Tasks created by the manager
            })
            ->get();

            // Get recent submissions for all managed tasks
            $submissions = TaskSubmission::whereHas('task', function($query) use ($managerId) {
                $query->where(function($q) use ($managerId) {
                    $q->whereHas('project.users', function ($subQ) use ($managerId) {
                        $subQ->where('user_id', $managerId);
                    })
                    ->orWhere('created_by', $managerId);
                });
            })
            ->with(['task.project', 'user'])
            ->latest('submitted_at')
            ->take(5)
            ->get();

            // Calculate statistics
            $stats = [
                'assigned_projects' => $teamProjects->count(),
                'team_tasks' => $teamTasks->count(),
                'completed_tasks' => $teamTasks->where('status', Task::STATUS_COMPLETED)->count(),
                'in_progress_tasks' => $teamTasks->where('status', Task::STATUS_IN_PROGRESS)->count(),
            ];

            // Debug information
            Log::info('Manager Dashboard Stats', [
                'manager_id' => $managerId,
                'projects_count' => $teamProjects->count(),
                'tasks_count' => $teamTasks->count(),
                'completed_tasks' => $teamTasks->where('status', Task::STATUS_COMPLETED)->count(),
                'in_progress_tasks' => $teamTasks->where('status', Task::STATUS_IN_PROGRESS)->count(),
            ]);

            return view('manager.dashboard', compact(
                'teamProjects',
                'submissions',
                'stats'
            ));

        } catch (\Exception $e) {
            Log::error('Dashboard Error: ' . $e->getMessage());
            return view('manager.dashboard', [
                'teamProjects' => collect(),
                'submissions' => collect(),
                'stats' => [
                    'assigned_projects' => 0,
                    'team_tasks' => 0,
                    'completed_tasks' => 0,
                    'in_progress_tasks' => 0,
                ]
            ])->with('error', 'Unable to load dashboard data. Please try again later.');
        }
    }
}
