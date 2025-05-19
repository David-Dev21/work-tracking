<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocationStoreRequest;
use App\Http\Requests\LocationUpdateRequest;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(Request $request): Response
    {
        $locations = Location::all();

        return view('location.index', [
            'locations' => $locations,
        ]);
    }

    public function create(Request $request): Response
    {
        return view('location.create');
    }

    public function store(LocationStoreRequest $request): Response
    {
        $location = Location::create($request->validated());

        $request->session()->flash('location.id', $location->id);

        return redirect()->route('locations.index');
    }

    public function show(Request $request, Location $location): Response
    {
        return view('location.show', [
            'location' => $location,
        ]);
    }

    public function edit(Request $request, Location $location): Response
    {
        return view('location.edit', [
            'location' => $location,
        ]);
    }

    public function update(LocationUpdateRequest $request, Location $location): Response
    {
        $location->update($request->validated());

        $request->session()->flash('location.id', $location->id);

        return redirect()->route('locations.index');
    }

    public function destroy(Request $request, Location $location): Response
    {
        $location->delete();

        return redirect()->route('locations.index');
    }
}
