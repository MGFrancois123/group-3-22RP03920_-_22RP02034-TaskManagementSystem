<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskSubmission;
use App\Models\ManagerEvaluation;
use App\Models\TaskScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    public function downloadSubmission(Task $task, TaskSubmission $submission)
    {
        // Check if the user has permission to download this submission
        if (!$this->canEvaluate($task)) {
            abort(403, 'You do not have permission to download this submission.');
        }

        // Check if the submission belongs to the task
        if ($submission->task_id !== $task->id) {
            abort(404, 'Submission not found for this task.');
        }

        // Check if file exists
        if (!$submission->file_path) {
            return back()->with('error', 'No file was uploaded with this submission.');
        }

        // Get the storage path
        $filePath = storage_path('app/' . $submission->file_path);

        if (!file_exists($filePath)) {
            return back()->with('error', 'The submission file could not be found.');
        }

        // Get the original file name or use a default
        $fileName = $submission->original_filename ?? basename($submission->file_path);

        // Return the file download
        return response()->download($filePath, $fileName);
    }

    public function showEvaluationForm(Task $task)
    {
        // Check if the task exists and the current user has permission to evaluate it
        if (!$task || !$this->canEvaluate($task)) {
            abort(404);
        }

        // Get the latest submission for this task
        $submission = $task->submissions()
            ->with(['user'])
            ->latest('submitted_at')
            ->first();

        if (!$submission) {
            return back()->with('error', 'No submission found for this task.');
        }

        return view('manager.tasks.evaluate', compact('task', 'submission'));
    }

    public function evaluate(Request $request, Task $task)
    {
        // Validate request
        $validated = $request->validate([
            'quality_score' => 'required|numeric|min:0|max:100',
            'timeliness_score' => 'required|numeric|min:0|max:100',
            'feedback' => 'required|string|min:10',
        ]);

        // Check if the task exists and the current user has permission to evaluate it
        if (!$task || !$this->canEvaluate($task)) {
            abort(404);
        }

        // Get the latest submission
        $submission = $task->submissions()
            ->latest('submitted_at')
            ->first();

        if (!$submission) {
            return back()->with('error', 'No submission found for this task.');
        }

        try {
            DB::beginTransaction();

            // Calculate average score
            $averageScore = round(($validated['quality_score'] + $validated['timeliness_score']) / 2);

            // Create manager evaluation record
            $managerEvaluation = ManagerEvaluation::create([
                'manager_id' => auth()->id(),
                'user_id' => $task->assigned_to,
                'task_id' => $task->id,
                'submission_id' => $submission->id,
                'quality_score' => $validated['quality_score'],
                'timeliness_score' => $validated['timeliness_score'],
                'average_score' => $averageScore,
                'feedback' => $validated['feedback'],
                'evaluation_type' => 'regular',
                'needs_follow_up' => false,
            ]);

            // Update the submission with evaluation scores
            $submission->update([
                'quality_score' => $validated['quality_score'],
                'timeliness_score' => $validated['timeliness_score'],
                'average_score' => $averageScore,
                'feedback' => $validated['feedback'],
                'evaluated_at' => now(),
                'evaluated_by' => auth()->id(),
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

            // Update task status if score meets passing criteria
            if ($averageScore >= ($task->passing_grade ?? 70)) {
                $task->updateStatus(Task::STATUS_COMPLETED);
            }

            DB::commit();

            return redirect()
                ->route('manager.tasks.show', $task)
                ->with('success', 'Task evaluation has been submitted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Evaluation Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withInput()
                ->with('error', 'Failed to save evaluation. Please try again.');
        }
    }

    protected function canEvaluate(Task $task)
    {
        $user = auth()->user();

        // Check if the user is the task creator or a member of the project
        return $user && (
            $task->created_by === $user->id ||
            $task->project->users()->where('user_id', $user->id)->exists()
        );
    }

    public function index()
    {
        $tasks = Task::where(function($query) {
            $managerId = auth()->id();
            $query->where('created_by', $managerId)
                ->orWhereHas('project.users', function($q) use ($managerId) {
                    $q->where('user_id', $managerId);
                });
        })
        ->with(['project', 'assignedTo'])
        ->latest()
        ->paginate(10);

        return view('manager.tasks.index', compact('tasks'));
    }

    public function show(Task $task)
    {
        if (!$this->canEvaluate($task)) {
            abort(403);
        }

        $task->load(['project', 'assignedTo', 'submissions.user']);
        return view('manager.tasks.show', compact('task'));
    }

    public function create()
    {
        $projects = \App\Models\Project::whereHas('users', function ($query) {
            $query->where('user_id', auth()->id());
        })->get();

        $users = \App\Models\User::where('role', 'user')->get();

        return view('manager.tasks.create', compact('projects', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'required|exists:users,id',
            'due_date' => 'required|date|after_or_equal:today',
            'priority' => 'required|in:Low,Medium,High',
            'status' => 'required|in:Pending,In-Progress,Completed',
        ]);

        $validated['created_by'] = auth()->id();

        // Verify that the manager has access to the project
        $project = \App\Models\Project::findOrFail($validated['project_id']);
        if (!$project->users()->where('user_id', auth()->id())->exists()) {
            return back()->with('error', 'You do not have permission to create tasks for this project.');
        }

        $task = Task::create($validated);

        return redirect()
            ->route('manager.tasks.show', $task)
            ->with('success', 'Task created successfully.');
    }

    public function edit(Task $task)
    {
        if (!$this->canEvaluate($task)) {
            abort(403, 'You do not have permission to edit this task.');
        }

        $projects = \App\Models\Project::whereHas('users', function ($query) {
            $query->where('user_id', auth()->id());
        })->get();

        $users = \App\Models\User::where('role', 'user')->get();

        return view('manager.tasks.edit', compact('task', 'projects', 'users'));
    }

    public function update(Request $request, Task $task)
    {
        if (!$this->canEvaluate($task)) {
            abort(403, 'You do not have permission to update this task.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'required|exists:users,id',
            'due_date' => 'required|date',
            'priority' => 'required|in:Low,Medium,High',
            'status' => 'required|in:Pending,In-Progress,Completed',
        ]);

        // Verify that the manager has access to the project
        $project = \App\Models\Project::findOrFail($validated['project_id']);
        if (!$project->users()->where('user_id', auth()->id())->exists()) {
            return back()->with('error', 'You do not have permission to assign tasks to this project.');
        }

        $task->update($validated);

        return redirect()
            ->route('manager.tasks.show', $task)
            ->with('success', 'Task updated successfully.');
    }

    public function report()
    {
        $managerId = auth()->id();

        $tasks = Task::where(function($query) use ($managerId) {
            $query->where('created_by', $managerId)
                ->orWhereHas('project.users', function($q) use ($managerId) {
                    $q->where('user_id', $managerId);
                });
        })
        ->with(['project', 'assignedTo', 'submissions'])
        ->latest()
        ->get();

        $statistics = [
            'total_tasks' => $tasks->count(),
            'completed_tasks' => $tasks->where('status', 'Completed')->count(),
            'in_progress_tasks' => $tasks->where('status', 'In-Progress')->count(),
            'pending_tasks' => $tasks->where('status', 'Pending')->count(),
        ];

        return view('manager.tasks.report', compact('tasks', 'statistics'));
    }
}
