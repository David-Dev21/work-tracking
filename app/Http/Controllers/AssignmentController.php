<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignmentStoreRequest;
use App\Http\Requests\AssignmentUpdateRequest;
use App\Models\Assignment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssignmentController extends Controller
{
    public function index(Request $request): Response
    {
        $assignments = Assignment::all();

        return view('assignment.index', [
            'assignments' => $assignments,
        ]);
    }

    public function create(Request $request): Response
    {
        return view('assignment.create');
    }

    public function store(AssignmentStoreRequest $request): Response
    {
        $assignment = Assignment::create($request->validated());

        $request->session()->flash('assignment.id', $assignment->id);

        return redirect()->route('assignments.index');
    }

    public function show(Request $request, Assignment $assignment): Response
    {
        return view('assignment.show', [
            'assignment' => $assignment,
        ]);
    }

    public function edit(Request $request, Assignment $assignment): Response
    {
        return view('assignment.edit', [
            'assignment' => $assignment,
        ]);
    }

    public function update(AssignmentUpdateRequest $request, Assignment $assignment): Response
    {
        $assignment->update($request->validated());

        $request->session()->flash('assignment.id', $assignment->id);

        return redirect()->route('assignments.index');
    }

    public function destroy(Request $request, Assignment $assignment): Response
    {
        $assignment->delete();

        return redirect()->route('assignments.index');
    }
}
