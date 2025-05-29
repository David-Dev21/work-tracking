<?php

namespace App\Filament\Resources\ActivityResource\Pages;

use App\Filament\Resources\ActivityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditActivity extends EditRecord
{
    protected static string $resource = ActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterSave(): void
    {
        // Obtener la actividad que acabamos de editar
        $activity = $this->record;

        // Verificar si la actividad pertenece a un proyecto
        if ($activity->project_id) {
            $project = $activity->project;

            // Refrescar la relación para asegurarnos de tener todos los datos actualizados
            $project->load('activities');

            // Si la actividad se cambió a 'en_progreso' y el proyecto está 'pendiente', actualizar el proyecto a 'en_progreso'
            if ($activity->state === 'en_progreso' && $project->state === 'pendiente') {
                $project->state = 'en_progreso';
                $project->save();
            }

            // Si el proyecto está 'finalizado' pero hay actividades 'en_progreso', cambiar el estado a 'en_progreso'
            if ($project->state === 'finalizado' && $activity->state === 'en_progreso') {
                $project->state = 'en_progreso';
                $project->save();
            }

            // Verificar si todas las actividades del proyecto están finalizadas
            $allActivitiesFinished = true;

            // Si el proyecto tiene actividades
            if ($project->activities->count() > 0) {
                foreach ($project->activities as $projectActivity) {
                    if ($projectActivity->state !== 'finalizado') {
                        $allActivitiesFinished = false;
                        break;
                    }
                }

                // Si todas las actividades están finalizadas, cambiar el estado del proyecto a 'finalizado'
                if ($allActivitiesFinished && $project->state !== 'finalizado') {
                    $project->state = 'finalizado';
                    $project->save();
                }
            }
        }
    }
}
