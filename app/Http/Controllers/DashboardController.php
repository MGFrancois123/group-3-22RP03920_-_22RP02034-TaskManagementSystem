<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\TaskSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'manager') {
            return redirect()->route('manager.dashboard');
        } else {
            return redirect()->route('user.dashboard');
        }
    }

    public function userDashboard()
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->role !== 'user') {
            return redirect()->route('dashboard');
        }

        // Calculate average scores from evaluated submissions
        $submissions = TaskSubmission::where('user_id', $user->id)
            ->whereNotNull('quality_score')
            ->whereNotNull('timeliness_score')
            ->whereNotNull('evaluated_at')
            ->get();

        // Calculate averages manually to ensure proper handling
        $avgQualityScore = $submissions->isEmpty() ? 0 : $submissions->average(function($submission) {
            return $submission->quality_score;
        });

        $avgTimelinessScore = $submissions->isEmpty() ? 0 : $submissions->average(function($submission) {
            return $submission->timeliness_score;
        });

        // Calculate overall average
        $overallAverage = $submissions->isEmpty() ? 0 : $submissions->average(function($submission) {
            return ($submission->quality_score + $submission->timeliness_score) / 2;
        });

        $statistics = [
            'assigned_tasks' => Task::where('assigned_to', $user->id)->count(),
            'completed_tasks' => Task::where('assigned_to', $user->id)
                ->where('status', 'completed')
                ->count(),
            'pending_tasks' => Task::where('assigned_to', $user->id)
                ->where('status', '!=', 'completed')
                ->count(),
            'avg_quality_score' => round($avgQualityScore, 1),
            'avg_timeliness_score' => round($avgTimelinessScore, 1),
            'average_score' => round($overallAverage, 1)
        ];

        // Get all tasks with their submissions and evaluations
        $tasks = Task::where('assigned_to', $user->id)
            ->with(['project', 'submissions' => function($query) {
                $query->latest('submitted_at');
            }])
            ->orderBy('due_date')
            ->get();

        // Get submitted tasks with evaluations
        $submittedTasks = $tasks->map(function($task) {
            $task->latestSubmission = $task->submissions->first();
            return $task;
        })->filter(function($task) {
            return $task->latestSubmission !== null;
        });

        return view('user.dashboard', compact('statistics', 'tasks', 'submittedTasks'));
    }

    public function managerDashboard()
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->role !== 'manager') {
            return redirect()->route('dashboard');
        }

        $statistics = [
            'assigned_projects' => Project::where('created_by', $user->id)->count(),
            'team_tasks' => Task::whereHas('project', function ($query) use ($user) {
                $query->where('created_by', $user->id);
            })->count(),
            'completed_tasks' => Task::whereHas('project', function ($query) use ($user) {
                $query->where('created_by', $user->id);
            })->where('status', 'completed')->count(),
        ];

        $teamProjects = Project::where('created_by', $user->id)
            ->withCount(['tasks', 'users'])
            ->get();

        $submissions = TaskSubmission::whereHas('task', function ($query) use ($user) {
            $query->whereHas('project', function ($query) use ($user) {
                $query->where('created_by', $user->id);
            });
        })
        ->with(['task', 'user'])
        ->orderBy('submitted_at', 'desc')
        ->take(10)
        ->get();

        return view('manager.dashboard', compact('statistics', 'teamProjects', 'submissions'));
    }
}
