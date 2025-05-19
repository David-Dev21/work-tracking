<?php

namespace App\Http\Controllers;

use App\Http\Requests\InternTypeStoreRequest;
use App\Http\Requests\InternTypeUpdateRequest;
use App\Models\InternType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InternTypeController extends Controller
{
    public function index(Request $request): Response
    {
        $internTypes = InternType::all();

        return view('internType.index', [
            'internTypes' => $internTypes,
        ]);
    }

    public function create(Request $request): Response
    {
        return view('internType.create');
    }

    public function store(InternTypeStoreRequest $request): Response
    {
        $internType = InternType::create($request->validated());

        $request->session()->flash('internType.id', $internType->id);

        return redirect()->route('internTypes.index');
    }

    public function show(Request $request, InternType $internType): Response
    {
        return view('internType.show', [
            'internType' => $internType,
        ]);
    }

    public function edit(Request $request, InternType $internType): Response
    {
        return view('internType.edit', [
            'internType' => $internType,
        ]);
    }

    public function update(InternTypeUpdateRequest $request, InternType $internType): Response
    {
        $internType->update($request->validated());

        $request->session()->flash('internType.id', $internType->id);

        return redirect()->route('internTypes.index');
    }

    public function destroy(Request $request, InternType $internType): Response
    {
        $internType->delete();

        return redirect()->route('internTypes.index');
    }
}
