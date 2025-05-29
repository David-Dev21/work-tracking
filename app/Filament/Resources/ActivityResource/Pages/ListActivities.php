<?php

namespace App\Filament\Resources\ActivityResource\Pages;

use App\Filament\Resources\ActivityResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Components\Tab;

class ListActivities extends ListRecords
{
    protected static string $resource = ActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->size('xl')
                ->label(__('headings.Create Activity')),
        ];
    }

    public function getTabs(): array
    {
        $modelClass = $this->getModel();
        $area_id = $modelClass::getUserAreaId();

        // Obtener el usuario autenticado
        $user = Filament::auth()->user();
        $intern = null;

        if ($user) {
            $intern = \App\Models\Intern::where('user_id', $user->id)->first();
        }

        // Función para aplicar los filtros adecuados según el tipo de usuario
        $getQueryForUser = function ($baseQuery) use ($area_id, $intern) {
            $query = clone $baseQuery;

            // Si es un pasante, filtrar por actividades asignadas y de sus proyectos
            if ($intern) {
                // Obtener IDs de actividades asignadas directamente al pasante
                $assignedActivityIds = \App\Models\Assignment::where('intern_id', $intern->id)
                    ->pluck('activity_id')
                    ->toArray();

                // Obtener IDs de proyectos asignados al pasante
                $assignedProjectIds = \App\Models\Assignment::where('intern_id', $intern->id)
                    ->whereNotNull('project_id')
                    ->pluck('project_id')
                    ->unique()
                    ->toArray();

                // Aplicar filtros para pasantes
                return $query->where(function ($query) use ($assignedActivityIds, $assignedProjectIds) {
                    $query->whereIn('id', $assignedActivityIds)
                        ->orWhereIn('project_id', $assignedProjectIds);
                });
            }

            // Para responsables y otros usuarios, aplicar el filtro por área
            if ($area_id !== null) {
                $query->where('area_id', $area_id);
            }

            return $query;
        };

        return [
            'all' => Tab::make(__('headings.Activities'))
                ->badge(function () use ($getQueryForUser) {
                    $query = $this->getModel()::query();
                    return $getQueryForUser($query)->count();
                }),
            'without_project' => Tab::make(__('Sin proyecto'))
                ->badge(function () use ($getQueryForUser) {
                    $query = $this->getModel()::query()->whereNull('project_id');
                    return $getQueryForUser($query)->count();
                })
                ->modifyQueryUsing(function (Builder $query) use ($getQueryForUser, $intern) {
                    $filtered = $getQueryForUser($query);

                    // Si no es un pasante, aplicar el filtro de project_id=null directamente
                    if (!$intern) {
                        $filtered->whereNull('project_id');
                    } else {
                        // Para pasantes, necesitamos mantener los filtros de asignación
                        // pero añadir la condición de que project_id sea null solo para las actividades
                        // que no están en sus proyectos asignados
                        $assignedProjectIds = \App\Models\Assignment::where('intern_id', $intern->id)
                            ->whereNotNull('project_id')
                            ->pluck('project_id')
                            ->toArray();

                        // Si hay actividades de proyectos asignados, filtrar para excluirlas
                        if (!empty($assignedProjectIds)) {
                            $filtered->where(function ($query) use ($assignedProjectIds) {
                                $query->whereNull('project_id')
                                    ->orWhereNotIn('project_id', $assignedProjectIds);
                            });
                        } else {
                            $filtered->whereNull('project_id');
                        }
                    }

                    return $filtered;
                }),
            'with_project' => Tab::make(__('Con proyecto'))
                ->badge(function () use ($getQueryForUser) {
                    $query = $this->getModel()::query()->whereNotNull('project_id');
                    return $getQueryForUser($query)->count();
                })
                ->modifyQueryUsing(function (Builder $query) use ($getQueryForUser, $intern) {
                    $filtered = $getQueryForUser($query);

                    // Si no es un pasante, aplicar el filtro de project_id!=null directamente
                    if (!$intern) {
                        $filtered->whereNotNull('project_id');
                    } else {
                        // Para pasantes, necesitamos mantener los filtros de asignación
                        // pero añadir la condición de que solo muestre actividades de sus proyectos
                        $assignedProjectIds = \App\Models\Assignment::where('intern_id', $intern->id)
                            ->whereNotNull('project_id')
                            ->pluck('project_id')
                            ->toArray();

                        if (!empty($assignedProjectIds)) {
                            $filtered->whereIn('project_id', $assignedProjectIds);
                        }
                    }

                    return $filtered;
                }),
        ];
    }
}
