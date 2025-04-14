<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Models\TaskSubmission;
use App\Models\ManagerScore;
use App\Models\ManagerEvaluation;
use App\Models\TaskScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $query = Task::query()->with(['project', 'assignedTo']);

        // Apply role-based filters
        if ($user->isAdmin()) {
            // Admin sees all tasks
        } elseif ($user->isManager()) {
            $query->whereHas('project', function($q) use ($user) {
                $q->where('created_by', $user->id);
            });
        } else {
            $query->where('assigned_to', $user->id);
        }

        // Handle status filter
        if (request('status')) {
            $query->where('status', request('status'));
        }

        // Handle search
        if (request('search')) {
            $searchTerm = request('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('project', function($q) use ($searchTerm) {
                      $q->where('name', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        $tasks = $query->latest()->paginate(10);

        $viewPrefix = $user->isManager() ? 'manager.' : ($user->role === 'user' ? 'user.' : '');
        return view($viewPrefix . 'tasks.index', compact('tasks'));
    }

    public function create(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $project = null;
        $projects = collect();

        if ($request->has('project_id')) {
            $project = Project::findOrFail($request->project_id);

            if (!($user->isAdmin() ||
                ($user->isManager() && $project->created_by === $user->id))) {
                $routePrefix = $user->isManager() ? 'manager.' : '';
                return redirect()->route($routePrefix . 'projects.show', $project)
                    ->with('error', 'You do not have permission to add tasks to this project.');
            }
        } else {
            // If no specific project is selected, get all projects the user has access to
            if ($user->isAdmin()) {
                $projects = Project::all();
            } elseif ($user->isManager()) {
                $projects = Project::where('created_by', $user->id)->get();
            }
        }

        $users = User::where('role', 'user')->get();
        $viewPrefix = $user->isManager() ? 'manager.' : ($user->role === 'user' ? 'user.' : '');
        return view($viewPrefix . 'tasks.create', compact('project', 'projects', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'required|exists:users,id',
            'due_date' => 'required|date',
            'status' => 'required|in:Pending,In-Progress,Completed'
        ]);

        $project = Project::findOrFail($validated['project_id']);

        /** @var \App\Models\User $user */
        $user = auth()->user();
        if (!($user->isAdmin() ||
            ($user->isManager() && $project->created_by === $user->id))) {
            $routePrefix = $user->isManager() ? 'manager.' : '';
            return redirect()->route($routePrefix . 'projects.show', $project)
                ->with('error', 'You do not have permission to add tasks to this project.');
        }

        $task = Task::create([
            ...$validated,
            'created_by' => $user->id
        ]);

        $routePrefix = $user->isManager() ? 'manager.' : ($user->role === 'user' ? 'user.' : '');
        return redirect()->route($routePrefix . 'tasks.show', $task)
            ->with('success', 'Task created successfully.');
    }

    public function show(Task $task)
    {
        /** @var User $user */
        $user = Auth::user();
        $task->load(['project', 'creator', 'assignedTo', 'submissions', 'managerScores.manager']);

        $viewPrefix = $user->isManager() ? 'manager.' : ($user->role === 'user' ? 'user.' : '');
        return view($viewPrefix . 'tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $users = User::where('role', 'user')->get();
        $projects = collect();  // Initialize empty collection

        // Get projects based on user role
        if ($user->isAdmin()) {
            $projects = Project::all();
        } elseif ($user->isManager()) {
            $projects = Project::where('created_by', $user->id)->get();
        }

        $viewPrefix = $user->isManager() ? 'manager.' : ($user->role === 'user' ? 'user.' : '');
        return view($viewPrefix . 'tasks.edit', compact('task', 'users', 'projects'));
    }

    public function update(Request $request, Task $task)
    {
        /** @var User $user */
        $user = auth()->user();

        // Handle task submission and status update
        $request->validate([
            'submission_notes' => 'nullable|string',
            'submission_file' => 'nullable|file|max:10240|mimes:pdf,doc,docx,zip', // 10MB max
            'status' => 'sometimes|in:Pending,In-Progress,Completed'
        ]);

        // Create submission if notes or file are provided
        if ($request->filled('submission_notes') || $request->hasFile('submission_file')) {
            $submission = new TaskSubmission([
                'task_id' => $task->id,
                'user_id' => $user->id,
                'submission_notes' => $request->submission_notes,
                'submitted_at' => now(),
            ]);

            if ($request->hasFile('submission_file')) {
                $file = $request->file('submission_file');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('task_submissions', $filename, 'public');

                $submission->file_path = $path;
                $submission->original_filename = $file->getClientOriginalName();
            }

            $submission->save();
        }

        // Update task status if provided
        if ($request->has('status')) {
            $task->update(['status' => $request->status]);
        }

        return redirect()->route('user.tasks.show', $task)
            ->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $task->delete();

        $routePrefix = $user->isManager() ? 'manager.' : ($user->role === 'user' ? 'user.' : '');
        return redirect()->route($routePrefix . 'tasks.index')
            ->with('success', 'Task deleted successfully.');
    }

    public function updateStatus(Request $request, Task $task)
    {
        $request->validate([
            'status' => 'required|in:Pending,In-Progress,Completed',
        ]);

        $task->updateStatus($request->status);

        if (request()->wantsJson()) {
            return Response::json($task);
        }

        return back()->with('success', 'Task status updated successfully.');
    }

    public function showEvaluationForm(Task $task)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user || (!$user->isAdmin() && !$user->isManager())) {
            return back()->with('error', 'Unauthorized. Admin or Manager access required.');
        }

        $submission = TaskSubmission::findOrFail(request('submission'));
        $viewPrefix = $user->isManager() ? 'manager.' : '';
        return view($viewPrefix . 'tasks.evaluate', compact('task', 'submission'));
    }

    public function evaluate(Request $request, Task $task)
    {
        $this->authorize('evaluate', $task);

        $submission = TaskSubmission::findOrFail($request->submission);

        Log::info('Evaluation Request Data:', [
            'all_data' => $request->all(),
            'quality_score' => $request->quality_score,
            'timeliness_score' => $request->timeliness_score,
            'feedback' => $request->feedback,
            'submission_id' => $submission->id,
            'task_id' => $task->id,
            'assigned_to' => $task->assigned_to,
            'manager_id' => auth()->id()
        ]);

        $validated = $request->validate([
            'quality_score' => 'required|integer|min:0|max:100',
            'timeliness_score' => 'required|integer|min:0|max:100',
            'feedback' => 'nullable|string|max:1000',
            'evaluation_notes' => 'nullable|string|max:1000',
            'evaluation_type' => 'required|in:regular,follow_up,final',
            'evaluation_criteria' => 'nullable|array',
            'evaluation_criteria.*' => 'in:quality,timeliness,communication,initiative',
            'needs_follow_up' => 'nullable|boolean',
            'follow_up_date' => 'nullable|date|required_if:needs_follow_up,1',
            'status' => 'required|in:Completed,In-Progress,Pending'
        ]);

        Log::info('Validated Data:', $validated);

        try {
            DB::beginTransaction();

            // Calculate average score
            $averageScore = round(($validated['quality_score'] + $validated['timeliness_score']) / 2, 1);

            // Update the submission with evaluation data
            $submission->update([
                'quality_score' => $validated['quality_score'],
                'timeliness_score' => $validated['timeliness_score'],
                'average_score' => $averageScore,
                'feedback' => $validated['feedback'],
                'evaluation_notes' => $validated['evaluation_notes'],
                'evaluation_type' => $validated['evaluation_type'],
                'evaluation_criteria' => $validated['evaluation_criteria'],
                'needs_follow_up' => $validated['needs_follow_up'] ?? false,
                'follow_up_date' => $validated['follow_up_date'],
                'evaluated_by' => auth()->id(),
                'evaluated_at' => now(),
            ]);

            // Create manager evaluation
            $managerEvaluation = ManagerEvaluation::create([
                'manager_id' => auth()->id(),
                'user_id' => $task->assigned_to,
                'task_id' => $task->id,
                'submission_id' => $submission->id,
                'quality_score' => $validated['quality_score'],
                'timeliness_score' => $validated['timeliness_score'],
                'average_score' => $averageScore,
                'feedback' => $validated['feedback'],
                'evaluation_notes' => $validated['evaluation_notes'],
                'evaluation_type' => $validated['evaluation_type'],
                'evaluation_criteria' => $validated['evaluation_criteria'],
                'needs_follow_up' => $validated['needs_follow_up'] ?? false,
                'follow_up_date' => $validated['follow_up_date'],
            ]);

            // Create or update task score
            TaskScore::updateOrCreate(
                ['task_id' => $task->id, 'user_id' => $task->assigned_to],
                [
                    'quality_score' => $validated['quality_score'],
                    'timeliness_score' => $validated['timeliness_score'],
                    'average_score' => $averageScore,
                    'feedback' => $validated['feedback'],
                    'evaluated_by' => auth()->id(),
                    'evaluated_at' => now(),
                ]
            );

            Log::info('Submission Updated:', $submission->fresh()->toArray());

            // Update task status
            $task->update([
                'status' => $validated['status'],
                'completed_at' => $validated['status'] === 'Completed' ? now() : null
            ]);

            DB::commit();

            return redirect()->route('manager.tasks.show', $task)
                ->with('success', 'Submission has been evaluated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Evaluation Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withInput()->with('error', 'Failed to save evaluation. Please try again.');
        }
    }

    public function report()
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user || (!$user->isAdmin() && !$user->isManager())) {
            return back()->with('error', 'Unauthorized. Admin or Manager access required.');
        }

        $tasks = Task::with(['project', 'assignedTo'])
            ->when($user->isManager(), function ($query) use ($user) {
                return $query->whereHas('project', function ($q) use ($user) {
                    $q->where('created_by', $user->id);
                });
            })
            ->get();

        $statistics = [
            'total_tasks' => $tasks->count(),
            'completed_tasks' => $tasks->where('status', 'Completed')->count(),
            'in_progress_tasks' => $tasks->where('status', 'In-Progress')->count(),
            'pending_tasks' => $tasks->where('status', 'Pending')->count(),
            'average_quality_score' => $tasks->whereNotNull('quality_score')->avg('quality_score'),
            'average_timeliness_score' => $tasks->whereNotNull('timeliness_score')->avg('timeliness_score'),
        ];

        $viewPrefix = $user->isManager() ? 'manager.' : '';
        return view($viewPrefix . 'tasks.report', compact('tasks', 'statistics'));
    }

    public function submit(Request $request, Task $task)
    {
        // Redirect if the request method is GET
        if ($request->isMethod('get')) {
            return redirect()->route('tasks.show', $task)
                ->with('error', 'Invalid request method. Please use the form to submit the task.');
        }

        // Ensure the user is authorized to submit the task
        $this->authorize('submit', $task);

        // Validate the request
        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
            'file' => 'nullable|file|max:10240', // 10MB max
        ]);

        // Create a new submission
        $submission = new TaskSubmission([
            'user_id' => auth()->id(),
            'notes' => $validated['notes'] ?? null,
            'submitted_at' => now(),
        ]);

        // Handle file upload if provided
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('task_submissions', $filename, 'public');

            $submission->file_path = $path;
            $submission->original_filename = $file->getClientOriginalName();
        }

        // Save the submission to the task
        $task->submissions()->save($submission);

        // Update task status to "In-Progress" if it was "Pending"
        if ($task->status === 'Pending') {
            $task->update(['status' => 'In-Progress']);
        }

        // Redirect back with a success message
        return redirect()->route('tasks.show', $task)
            ->with('success', 'Task submitted successfully.');
    }

    public function downloadSubmission(Task $task, TaskSubmission $submission)
    {
        /** @var User $user */
        $user = Auth::user();

        // Check if user has permission to download
        if (!$user->isAdmin() &&
            !($user->isManager() && $task->project->created_by === $user->id) &&
            $submission->user_id !== $user->id) {
            return back()->with('error', 'You do not have permission to download this file.');
        }

        if (!$submission->file_path) {
            return back()->with('error', 'No file attached to this submission.');
        }

        // Get the storage path - note that file_path already includes the directory
        $path = storage_path('app/public/' . $submission->file_path);

        if (!file_exists($path)) {
            // Try legacy path as fallback
            $legacyPath = storage_path('app/public/submissions/' . basename($submission->file_path));
            if (!file_exists($legacyPath)) {
                return back()->with('error', 'File not found.');
            }
            $path = $legacyPath;
        }

        return response()->download($path, $submission->original_filename);
    }

    public function showSubmission(Task $task, TaskSubmission $submission)
    {
        $this->authorize('view', $task);

        return view('tasks.submission', [
            'task' => $task,
            'submission' => $submission->load(['user', 'evaluator'])
        ]);
    }
}
