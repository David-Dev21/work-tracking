<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActivityReportController;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/sync', [App\Http\Controllers\SyncController::class, 'syncAll'])->name('sync.all');

// Rutas para generar PDF de actividades (requiere autenticación)
Route::get('/activities/report/pdf', [ActivityReportController::class, 'generateReport'])
    ->name('activities.pdf.report')
    ->middleware(['auth:web']);


// Route::resource('users', App\Http\Controllers\UserController::class);

// Route::resource('areas', App\Http\Controllers\AreaController::class);

// Route::resource('area-roles', App\Http\Controllers\AreaRoleController::class);

// Route::resource('responsibles', App\Http\Controllers\ResponsibleController::class);

// Route::resource('locations', App\Http\Controllers\LocationController::class);

// Route::resource('activities', App\Http\Controllers\ActivityController::class);

// Route::resource('projects', App\Http\Controllers\ProjectController::class);

// Route::resource('intern-types', App\Http\Controllers\InternTypeController::class);

// Route::resource('interns', App\Http\Controllers\InternController::class);

// Route::resource('intern-registrations', App\Http\Controllers\InternRegistrationController::class);

// Route::resource('assignments', App\Http\Controllers\AssignmentController::class);
