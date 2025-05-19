<?php

namespace App\Http\Controllers;

use App\Http\Requests\AreaStoreRequest;
use App\Http\Requests\AreaUpdateRequest;
use App\Models\Area;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AreaController extends Controller
{
    public function index(Request $request): Response
    {
        $areas = Area::all();

        return view('area.index', [
            'areas' => $areas,
        ]);
    }

    public function create(Request $request): Response
    {
        return view('area.create');
    }

    public function store(AreaStoreRequest $request): Response
    {
        $area = Area::create($request->validated());

        $request->session()->flash('area.id', $area->id);

        return redirect()->route('areas.index');
    }

    public function show(Request $request, Area $area): Response
    {
        return view('area.show', [
            'area' => $area,
        ]);
    }

    public function edit(Request $request, Area $area): Response
    {
        return view('area.edit', [
            'area' => $area,
        ]);
    }

    public function update(AreaUpdateRequest $request, Area $area): Response
    {
        $area->update($request->validated());

        $request->session()->flash('area.id', $area->id);

        return redirect()->route('areas.index');
    }

    public function destroy(Request $request, Area $area): Response
    {
        $area->delete();

        return redirect()->route('areas.index');
    }
}
