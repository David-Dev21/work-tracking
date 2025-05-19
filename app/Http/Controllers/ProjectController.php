<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectStoreRequest;
use App\Http\Requests\ProjectUpdateRequest;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::all();

        return view('project.index', [
            'projects' => $projects,
        ]);
    }

    public function create(Request $request)
    {
        return view('project.create');
    }

    public function store(ProjectStoreRequest $request)
    {
        $project = Project::create($request->validated());

        $request->session()->flash('project.id', $project->id);

        return redirect()->route('projects.index');
    }

    public function show(Request $request, Project $project)
    {
        return view('project.show', [
            'project' => $project,
        ]);
    }

    public function edit(Request $request, Project $project)
    {
        return view('project.edit', [
            'project' => $project,
        ]);
    }

    public function update(ProjectUpdateRequest $request, Project $project)
    {
        $project->update($request->validated());

        $request->session()->flash('project.id', $project->id);

        return redirect()->route('projects.index');
    }

    public function destroy(Request $request, Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index');
    }
}
