<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResponsibleStoreRequest;
use App\Http\Requests\ResponsibleUpdateRequest;
use App\Models\Responsible;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResponsibleController extends Controller
{
    public function index(Request $request)
    {
        $responsibles = Responsible::all();

        return view('responsible.index', [
            'responsibles' => $responsibles,
        ]);
    }

    public function create(Request $request)
    {
        return view('responsible.create');
    }

    public function store(ResponsibleStoreRequest $request)
    {
        $responsible = Responsible::create($request->validated());

        $request->session()->flash('responsible.id', $responsible->id);

        return redirect()->route('responsibles.index');
    }

    public function show(Request $request, Responsible $responsible)
    {
        return view('responsible.show', [
            'responsible' => $responsible,
        ]);
    }

    public function edit(Request $request, Responsible $responsible)
    {
        return view('responsible.edit', [
            'responsible' => $responsible,
        ]);
    }

    public function update(ResponsibleUpdateRequest $request, Responsible $responsible)
    {
        $responsible->update($request->validated());

        $request->session()->flash('responsible.id', $responsible->id);

        return redirect()->route('responsibles.index');
    }

    public function destroy(Request $request, Responsible $responsible)
    {
        $responsible->delete();

        return redirect()->route('responsibles.index');
    }
}
