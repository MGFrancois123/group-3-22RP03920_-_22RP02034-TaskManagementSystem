<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::whereHas('users', function ($query) {
            $query->where('user_id', auth()->id());
        })->latest()->paginate(10);

        return view('manager.projects.index', compact('projects'));
    }

    public function create()
    {
        return view('manager.projects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:pending,in_progress,completed'
        ]);

        $validated['created_by'] = auth()->id();

        $project = Project::create($validated);

        // Attach the current manager to the project
        $project->users()->attach(auth()->id());

        return redirect()
            ->route('manager.projects.show', $project)
            ->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        if (!$this->canAccess($project)) {
            abort(403);
        }

        $project->load(['tasks', 'users']);
        return view('manager.projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        if (!$this->canAccess($project)) {
            abort(403);
        }

        return view('manager.projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        if (!$this->canAccess($project)) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:pending,in_progress,completed'
        ]);

        $project->update($validated);

        return redirect()
            ->route('manager.projects.show', $project)
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        if (!$this->canAccess($project)) {
            abort(403);
        }

        $project->delete();

        return redirect()
            ->route('manager.projects.index')
            ->with('success', 'Project deleted successfully.');
    }

    protected function canAccess(Project $project)
    {
        return $project->users()->where('user_id', auth()->id())->exists();
    }
}
