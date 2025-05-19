<?php

namespace App\Http\Controllers;

use App\Http\Requests\InternStoreRequest;
use App\Http\Requests\InternUpdateRequest;
use App\Models\Intern;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InternController extends Controller
{
    public function index(Request $request): Response
    {
        $interns = Intern::all();

        return view('intern.index', [
            'interns' => $interns,
        ]);
    }

    public function create(Request $request): Response
    {
        return view('intern.create');
    }

    public function store(InternStoreRequest $request): Response
    {
        $intern = Intern::create($request->validated());

        $request->session()->flash('intern.id', $intern->id);

        return redirect()->route('interns.index');
    }

    public function show(Request $request, Intern $intern): Response
    {
        return view('intern.show', [
            'intern' => $intern,
        ]);
    }

    public function edit(Request $request, Intern $intern): Response
    {
        return view('intern.edit', [
            'intern' => $intern,
        ]);
    }

    public function update(InternUpdateRequest $request, Intern $intern): Response
    {
        $intern->update($request->validated());

        $request->session()->flash('intern.id', $intern->id);

        return redirect()->route('interns.index');
    }

    public function destroy(Request $request, Intern $intern): Response
    {
        $intern->delete();

        return redirect()->route('interns.index');
    }
}
