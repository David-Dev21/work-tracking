<?php

namespace App\Http\Controllers;

use App\Http\Requests\AreaRoleStoreRequest;
use App\Http\Requests\AreaRoleUpdateRequest;
use App\Models\AreaRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AreaRoleController extends Controller
{
    public function index(Request $request): Response
    {
        $areaRoles = AreaRole::all();

        return view('areaRole.index', [
            'areaRoles' => $areaRoles,
        ]);
    }

    public function create(Request $request): Response
    {
        return view('areaRole.create');
    }

    public function store(AreaRoleStoreRequest $request): Response
    {
        $areaRole = AreaRole::create($request->validated());

        $request->session()->flash('areaRole.id', $areaRole->id);

        return redirect()->route('areaRoles.index');
    }

    public function show(Request $request, AreaRole $areaRole): Response
    {
        return view('areaRole.show', [
            'areaRole' => $areaRole,
        ]);
    }

    public function edit(Request $request, AreaRole $areaRole): Response
    {
        return view('areaRole.edit', [
            'areaRole' => $areaRole,
        ]);
    }

    public function update(AreaRoleUpdateRequest $request, AreaRole $areaRole): Response
    {
        $areaRole->update($request->validated());

        $request->session()->flash('areaRole.id', $areaRole->id);

        return redirect()->route('areaRoles.index');
    }

    public function destroy(Request $request, AreaRole $areaRole): Response
    {
        $areaRole->delete();

        return redirect()->route('areaRoles.index');
    }
}
