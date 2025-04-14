<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $projects = Project::with(['creator', 'tasks'])->paginate(10);

        if ($user->isManager()) {
            return view('manager.projects.index', compact('projects'));
        }

        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $viewPrefix = $user->isManager() ? 'manager.' : '';
        return view($viewPrefix . 'projects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();

        $project = Project::create($validated);

        /** @var \App\Models\User $user */
        $user = auth()->user();
        $routePrefix = $user->isManager() ? 'manager.' : '';
        return redirect()->route($routePrefix . 'projects.show', $project)
            ->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        $project->load(['creator', 'tasks.assignedTo']);
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $viewPrefix = $user->isManager() ? 'manager.' : '';
        return view($viewPrefix . 'projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if (!($user->isAdmin() || $user->isManager())) {
            $routePrefix = $user->isManager() ? 'manager.' : '';
            return redirect()->route($routePrefix . 'projects.index')
                ->with('error', 'You do not have permission to edit projects.');
        }

        $viewPrefix = $user->isManager() ? 'manager.' : '';
        return view($viewPrefix . 'projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if (!($user->isAdmin() || $user->isManager())) {
            $routePrefix = $user->isManager() ? 'manager.' : '';
            return redirect()->route($routePrefix . 'projects.index')
                ->with('error', 'You do not have permission to edit projects.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project->update($validated);

        $routePrefix = $user->isManager() ? 'manager.' : '';
        return redirect()->route($routePrefix . 'projects.show', $project)
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if (!($user->isAdmin() || $user->isManager())) {
            $routePrefix = $user->isManager() ? 'manager.' : '';
            return redirect()->route($routePrefix . 'projects.index')
                ->with('error', 'You do not have permission to delete projects.');
        }

        $project->delete();

        $routePrefix = $user->isManager() ? 'manager.' : '';
        return redirect()->route($routePrefix . 'projects.index')
            ->with('success', 'Project deleted successfully.');
    }

    private function userHasProjectAccess(User $user, Project $project): bool
    {
        if ($user->isManager()) {
            return $project->created_by === $user->id;
        }

        return $project->tasks()->where('assigned_to', $user->id)->exists();
    }
}
