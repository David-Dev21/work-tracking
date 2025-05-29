<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Components\Tab;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->size('xl'),
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

            // Si es un pasante, filtrar por proyectos asignados
            if ($intern) {
                // Obtener IDs de proyectos asignados al pasante
                $assignedProjectIds = \App\Models\Assignment::where('intern_id', $intern->id)
                    ->whereNotNull('project_id')
                    ->pluck('project_id')
                    ->unique()
                    ->toArray();

                // Aplicar filtros para pasantes
                return $query->whereIn('id', $assignedProjectIds);
            }

            // Para responsables y otros usuarios, aplicar el filtro por área
            if ($area_id !== null) {
                $query->where('area_id', $area_id);
            }

            return $query;
        };

        return [
            'all' => Tab::make(__('headings.Projects'))
                ->badge(function () use ($getQueryForUser) {
                    $query = $this->getModel()::query();
                    return $getQueryForUser($query)->count();
                }),
            'pendiente' => Tab::make(__('headings.Pending'))
                ->badge(function () use ($getQueryForUser) {
                    $query = $this->getModel()::query()->where('state', 'pendiente');
                    return $getQueryForUser($query)->count();
                })
                ->badgeColor('danger')
                ->modifyQueryUsing(function (Builder $query) use ($getQueryForUser) {
                    $filtered = $getQueryForUser($query);
                    return $filtered->where('state', 'pendiente');
                }),
            'en_progreso' => Tab::make(__('headings.In Progress'))
                ->badge(function () use ($getQueryForUser) {
                    $query = $this->getModel()::query()->where('state', 'en_progreso');
                    return $getQueryForUser($query)->count();
                })
                ->badgeColor('warning')
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(function (Builder $query) use ($getQueryForUser) {
                    $filtered = $getQueryForUser($query);
                    return $filtered->where('state', 'en_progreso');
                }),
            'finalizado' => Tab::make(__('headings.Finished'))
                ->badge(function () use ($getQueryForUser) {
                    $query = $this->getModel()::query()->where('state', 'finalizado');
                    return $getQueryForUser($query)->count();
                })
                ->badgeColor('success')
                ->modifyQueryUsing(function (Builder $query) use ($getQueryForUser) {
                    $filtered = $getQueryForUser($query);
                    return $filtered->where('state', 'finalizado');
                }),
        ];
    }
}
