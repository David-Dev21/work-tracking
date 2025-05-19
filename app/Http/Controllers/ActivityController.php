<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActivityStoreRequest;
use App\Http\Requests\ActivityUpdateRequest;
use App\Models\Activity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(Request $request): Response
    {
        $activities = Activity::all();

        return view('activity.index', [
            'activities' => $activities,
        ]);
    }

    public function create(Request $request): Response
    {
        return view('activity.create');
    }

    public function store(ActivityStoreRequest $request): Response
    {
        $activity = Activity::create($request->validated());

        $request->session()->flash('activity.id', $activity->id);

        return redirect()->route('activities.index');
    }

    public function show(Request $request, Activity $activity): Response
    {
        return view('activity.show', [
            'activity' => $activity,
        ]);
    }

    public function edit(Request $request, Activity $activity): Response
    {
        return view('activity.edit', [
            'activity' => $activity,
        ]);
    }

    public function update(ActivityUpdateRequest $request, Activity $activity): Response
    {
        $activity->update($request->validated());

        $request->session()->flash('activity.id', $activity->id);

        return redirect()->route('activities.index');
    }

    public function destroy(Request $request, Activity $activity): Response
    {
        $activity->delete();

        return redirect()->route('activities.index');
    }
}
