<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Students; // ✅ Correct Model Name (Singular)

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($name = "Student") // Default value for '/' route
    {
        return view('student.dashboard')->with(['name' => $name]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('student.new');
    }

    /**
     * Display all students.
     */
    public function show()
    {
        $students = Students::all(); // ✅ Fetch all students
        return view('student.all', compact('students'));
    }

    /**
     * Store a newly created student in storage.
     */
    public function store(Request $request)
    {
        // ✅ Validate Input
        $request->validate([
            'name' => 'required|string|max:255',
            'nid' => 'required|numeric|unique:students,nid',
        ]);

        // ✅ Save Student Data
        Students::create([
            'name' => $request->name,
            'nid' => $request->nid,
        ]);

        return redirect()->route('student.all')->with('success', 'Student added successfully!');
    }
}
