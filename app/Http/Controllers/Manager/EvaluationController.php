<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\ManagerScore;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    public function index()
    {
        $evaluations = ManagerScore::where('manager_id', auth()->id())
            ->with(['task.project', 'task.assignedTo'])
            ->latest('evaluated_at')
            ->paginate(10);

        return view('manager.evaluations.index', compact('evaluations'));
    }

    public function show(ManagerScore $evaluation)
    {
        // Ensure the evaluation belongs to the current manager
        if ($evaluation->manager_id !== auth()->id()) {
            abort(403);
        }

        $evaluation->load(['task.project', 'task.assignedTo']);

        return view('manager.evaluations.show', compact('evaluation'));
    }
}
