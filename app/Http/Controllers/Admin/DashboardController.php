<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Dashboard statistics
        $stats = [
            'users' => [
                'total' => User::count(),
                'active' => User::where('last_login_at', '>=', now()->subDays(30))->count(),
                'by_role' => User::select('role', DB::raw('count(*) as count'))
                    ->groupBy('role')
                    ->get()
            ],
            'tasks' => [
                'total' => Task::count(),
                'completed' => Task::where('status', 'completed')->count(),
                'pending' => Task::where('status', 'pending')->count(),
                'completion_rate' => $this->calculateTaskCompletionRate()
            ],
            'projects' => [
                'total' => Project::count(),
                'active' => Project::where('status', 'active')->count()
            ]
        ];

        return view('admin.dashboard', compact('stats'));
    }

    private function calculateTaskCompletionRate()
    {
        $totalTasks = Task::count();
        if ($totalTasks === 0) return 0;

        $completedTasks = Task::where('status', 'completed')->count();
        return round(($completedTasks / $totalTasks) * 100, 2);
    }
}
