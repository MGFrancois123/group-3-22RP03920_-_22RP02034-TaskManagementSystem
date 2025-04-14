<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;
use App\Models\TaskSubmission;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Get system statistics
        $stats = [
            'total_users' => User::count(),
            'total_tasks' => Task::count(),
            'total_projects' => Project::count(),
            'users_by_role' => User::select('role', DB::raw('count(*) as count'))
                                ->groupBy('role')
                                ->get(),
            'task_priorities' => Task::select('priority', DB::raw('count(*) as count'))
                                ->groupBy('priority')
                                ->get(),
            'task_categories' => Task::select('category', DB::raw('count(*) as count'))
                                ->groupBy('category')
                                ->get(),
            'system_performance' => [
                'completed_tasks' => Task::where('status', 'completed')->count(),
                'pending_tasks' => Task::where('status', 'pending')->count(),
                'active_users' => User::where('last_login_at', '>=', now()->subDays(30))->count(),
                'task_completion_rate' => $this->calculateTaskCompletionRate(),
            ]
        ];

        // Get user performance data
        $userPerformance = User::with(['tasks', 'createdTasks'])
            ->get()
            ->map(function ($user) {
                $completedTasks = $user->tasks->where('status', 'completed')->count();
                $totalTasks = $user->tasks->count();
                $averageScore = $user->tasks->where('status', 'completed')->avg('quality_score') ?? 0;

                return (object)[
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'completed_tasks_count' => $completedTasks,
                    'total_tasks_count' => $totalTasks,
                    'average_score' => $averageScore,
                    'is_active' => $user->last_login_at && $user->last_login_at->diffInDays(now()) <= 30
                ];
            })
            ->sortByDesc('completed_tasks_count')
            ->take(5);

        // Get project performance data
        $projectPerformance = Project::with(['tasks', 'creator'])
            ->get()
            ->map(function ($project) {
                $completedTasks = $project->tasks->where('status', 'completed')->count();
                $totalTasks = $project->tasks->count();
                $completionRate = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;

                return [
                    'id' => $project->id,
                    'name' => $project->name,
                    'manager' => [
                        'name' => $project->creator->name ?? 'N/A',
                        'email' => $project->creator->email ?? 'N/A'
                    ],
                    'tasks_count' => $totalTasks,
                    'completion_rate' => $completionRate,
                    'status' => $project->status
                ];
            })
            ->sortByDesc('completion_rate')
            ->take(5);

        return view('admin.dashboard', compact('stats', 'userPerformance', 'projectPerformance'));
    }

    private function calculateTaskCompletionRate()
    {
        $totalTasks = Task::count();
        if ($totalTasks === 0) return 0;

        $completedTasks = Task::where('status', 'completed')->count();
        return round(($completedTasks / $totalTasks) * 100, 2);
    }

    public function systemSettings()
    {
        $settings = [
            'site_name' => Setting::get('site_name', config('app.name')),
            'site_description' => Setting::get('site_description', config('app.description')),
            'default_task_priority' => Setting::get('default_task_priority', 'medium'),
            'task_reminder_days' => Setting::get('task_reminder_days', 3),
            'mail_from_address' => Setting::get('mail_from_address', config('mail.from.address')),
            'mail_from_name' => Setting::get('mail_from_name', config('mail.from.name')),
            'enable_email_notifications' => Setting::get('enable_email_notifications', true),
            'enable_task_reminders' => Setting::get('enable_task_reminders', true),
        ];
        return view('admin.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string|max:1000',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->route('admin.settings')->with('success', 'General settings updated successfully');
    }

    public function updateTaskSettings(Request $request)
    {
        $validated = $request->validate([
            'default_task_priority' => 'required|in:low,medium,high,urgent',
            'task_reminder_days' => 'required|integer|min:0|max:30',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->route('admin.settings')->with('success', 'Task settings updated successfully');
    }

    public function updateEmailSettings(Request $request)
    {
        $validated = $request->validate([
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string|max:255',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->route('admin.settings')->with('success', 'Email settings updated successfully');
    }

    public function updateNotificationSettings(Request $request)
    {
        $validated = $request->validate([
            'enable_email_notifications' => 'boolean',
            'enable_task_reminders' => 'boolean',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->route('admin.settings')->with('success', 'Notification settings updated successfully');
    }

    public function index()
    {
        $users = User::paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,manager,user',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully');
    }

    public function reports()
    {
        // Task Statistics using collections
        $taskStats = Task::all()->groupBy('status')->map(function ($tasks) {
            return [
                'count' => $tasks->count(),
                'avg_completion_days' => $tasks->avg(function ($task) {
                    return $task->created_at->diffInDays($task->updated_at);
                })
            ];
        });

        // User Activity Statistics using collections
        $userStats = User::with(['tasks', 'createdTasks'])
            ->orderBy('last_login_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($user) {
                return (object)[
                    'name' => $user->name,
                    'email' => $user->email,
                    'last_login_at' => $user->last_login_at,
                    'tasks_created_count' => $user->createdTasks->count(),
                    'tasks_completed_count' => $user->tasks->where('status', 'completed')->count()
                ];
            });

        // System Performance Metrics using collections
        $totalUsers = User::count();
        $totalTasks = Task::count();
        $completedTasks = Task::where('status', 'completed')->count();
        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        // Calculate average response time using collections
        $avgResponseTime = Task::whereNotNull('updated_at')
            ->where('updated_at', '>', DB::raw('created_at'))
            ->get()
            ->avg(function ($task) {
                return $task->created_at->diffInMinutes($task->updated_at);
            });

        // Performance metrics using collections
        $performance_metrics = [
            'total_tasks' => $totalTasks,
            'completion_rate' => $completionRate,
            'average_quality_score' => TaskSubmission::whereNotNull('quality_score')->avg('quality_score') ?? 0,
        ];

        // User performance using collections
        $user_performance = User::with(['tasks.submissions'])->get()->map(function ($user) {
            return (object)[
                'name' => $user->name,
                'tasks_count' => $user->tasks->count(),
                'completed_tasks_count' => $user->tasks->where('status', 'completed')->count(),
                'tasks_avg_quality_score' => $user->tasks->flatMap->submissions->whereNotNull('quality_score')->avg('quality_score') ?? 0
            ];
        })->sortByDesc('tasks_count');

        // Project performance using collections
        $project_performance = Project::with(['tasks.submissions'])->get()->map(function ($project) {
            return (object)[
                'name' => $project->name,
                'tasks_count' => $project->tasks->count(),
                'completed_tasks_count' => $project->tasks->where('status', 'completed')->count(),
                'tasks_avg_quality_score' => $project->tasks->flatMap->submissions->whereNotNull('quality_score')->avg('quality_score') ?? 0
            ];
        })->sortByDesc('tasks_count');

        return view('admin.reports.index', compact(
            'taskStats',
            'userStats',
            'performance_metrics',
            'user_performance',
            'project_performance'
        ));
    }

    public function manageRoles()
    {
        $users = User::with('roles')->paginate(10);
        return view('admin.users.roles', compact('users'));
    }

    public function updateUserRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|in:admin,manager,user',
        ]);

        $user->update(['role' => $validated['role']]);
        return redirect()->back()->with('success', 'User role updated successfully');
    }

    public function taskCategories()
    {
        $categories = Task::select('category as name', DB::raw('count(*) as tasks_count'))
            ->whereNotNull('category')
            ->groupBy('category')
            ->get();
        return view('admin.tasks.categories', compact('categories'));
    }

    public function storeTaskCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tasks,category',
        ]);

        // Create a new task with the category to ensure it exists
        Task::create([
            'name' => 'Category placeholder',
            'description' => 'System category placeholder',
            'category' => $validated['name'],
            'status' => 'pending',
            'priority' => 'medium',
            'assigned_to' => auth()->id(),
            'created_by' => auth()->id(),
            'project_id' => 1, // You might want to adjust this
            'due_date' => now()->addYear() // Set due date to 1 year from now
        ]);

        return redirect()->route('admin.tasks.categories')->with('success', 'Category created successfully');
    }

    public function destroyTaskCategory(Request $request, string $category)
    {
        // First, update all tasks with this category to have a default category
        Task::where('category', $category)->update(['category' => 'Uncategorized']);

        return redirect()->route('admin.tasks.categories')->with('success', 'Category deleted successfully');
    }

    public function taskPriorities()
    {
        $priorities = Task::select('priority as name', DB::raw('count(*) as tasks_count'))
            ->whereNotNull('priority')
            ->groupBy('priority')
            ->get();
        return view('admin.tasks.priorities', compact('priorities'));
    }

    public function storeTaskPriority(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|in:low,medium,high,urgent',
        ]);

        // Create a new task with the priority to ensure it exists
        Task::create([
            'name' => 'Priority placeholder',
            'description' => 'System priority placeholder',
            'priority' => $validated['name'],
            'status' => 'pending',
            'category' => 'System',
            'assigned_to' => auth()->id(),
            'created_by' => auth()->id(),
            'project_id' => 1, // You might want to adjust this
            'due_date' => now()->addYear() // Set due date to 1 year from now
        ]);

        return redirect()->route('admin.tasks.priorities')->with('success', 'Priority created successfully');
    }

    public function destroyTaskPriority(Request $request, string $priority)
    {
        // First, update all tasks with this priority to have a default priority
        Task::where('priority', $priority)->update(['priority' => 'medium']);

        return redirect()->route('admin.tasks.priorities')->with('success', 'Priority deleted successfully');
    }

    public function systemPerformance()
    {
        $performanceStats = [
            'task_completion_rate' => $this->calculateTaskCompletionRate(),
            'user_activity' => User::where('last_login_at', '>=', now()->subDays(30))->count(),
            'average_task_completion_time' => Task::where('status', 'completed')
                ->selectRaw('AVG(TIMESTAMPDIFF(DAY, created_at, updated_at)) as avg_days')
                ->first()->avg_days ?? 0,
        ];

        return view('admin.reports.performance', compact('performanceStats'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,manager,user',
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete your own account');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully');
    }
}
