<?php

namespace App\Filament\Resources\ActivityResource\Pages;

use App\Filament\Resources\ActivityResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateActivity extends CreateRecord
{
    protected static string $resource = ActivityResource::class;

    protected function getCreateFormAction(): Action
    {
        return Action::make('create')
            ->submit(null)
            ->label(__('headings.Create Activity'))
            ->requiresConfirmation()
            ->action(function () {
                $this->closeActionModal();
                $this->create();
            });
    }


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        // Obtener la actividad que acabamos de crear
        $activity = $this->record;

        // Asegurarnos de que la actividad esté fresca desde la base de datos
        $activity->refresh();

        // Solo crear asignación automática si la actividad NO tiene project_id
        if (!$activity->project_id) {
            // Obtener el usuario autenticado
            $user = filament()->auth()->user();

            // Verificar si el usuario existe y si es un pasante (intern)
            if ($user) {
                $intern = \App\Models\Intern::where('user_id', $user->id)->first();

                // Si es un pasante, crear una asignación
                if ($intern) {
                    // Crear una asignación para este pasante y esta actividad
                    \App\Models\Assignment::create([
                        'intern_id' => $intern->id,
                        'activity_id' => $activity->id,
                        'project_id' => null, // No hay proyecto asociado
                        'role' => 'creador', // Puedes ajustar este rol según tus necesidades
                        'assigned_date' => now(),
                    ]);
                }
            }
        }
    }
}
