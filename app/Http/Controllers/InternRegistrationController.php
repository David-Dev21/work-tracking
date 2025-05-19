<?php

namespace App\Http\Controllers;

use App\Http\Requests\InternRegistrationStoreRequest;
use App\Http\Requests\InternRegistrationUpdateRequest;
use App\Models\InternRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InternRegistrationController extends Controller
{
    public function index(Request $request): Response
    {
        $internRegistrations = InternRegistration::all();

        return view('internRegistration.index', [
            'internRegistrations' => $internRegistrations,
        ]);
    }

    public function create(Request $request): Response
    {
        return view('internRegistration.create');
    }

    public function store(InternRegistrationStoreRequest $request): Response
    {
        $internRegistration = InternRegistration::create($request->validated());

        $request->session()->flash('internRegistration.id', $internRegistration->id);

        return redirect()->route('internRegistrations.index');
    }

    public function show(Request $request, InternRegistration $internRegistration): Response
    {
        return view('internRegistration.show', [
            'internRegistration' => $internRegistration,
        ]);
    }

    public function edit(Request $request, InternRegistration $internRegistration): Response
    {
        return view('internRegistration.edit', [
            'internRegistration' => $internRegistration,
        ]);
    }

    public function update(InternRegistrationUpdateRequest $request, InternRegistration $internRegistration): Response
    {
        $internRegistration->update($request->validated());

        $request->session()->flash('internRegistration.id', $internRegistration->id);

        return redirect()->route('internRegistrations.index');
    }

    public function destroy(Request $request, InternRegistration $internRegistration): Response
    {
        $internRegistration->delete();

        return redirect()->route('internRegistrations.index');
    }
}
