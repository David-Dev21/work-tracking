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
                ->label(__('headings.Create Activity')),            // Botón para generar informe PDF
            Actions\Action::make('generatePdfReport')
                ->label('Reporte PDF')
                ->size('xl')
                ->icon('heroicon-o-document-text')
                ->color('danger')
                ->modalHeading('Seleccionar mes para el reporte')
                ->modalDescription('Seleccione el mes para el que desea generar el reporte de actividades.')
                ->modalSubmitActionLabel('Generar PDF')
                ->form([
                    \Filament\Forms\Components\Select::make('month')
                        ->label('Mes')
                        ->options([
                            '01' => 'Enero',
                            '02' => 'Febrero',
                            '03' => 'Marzo',
                            '04' => 'Abril',
                            '05' => 'Mayo',
                            '06' => 'Junio',
                            '07' => 'Julio',
                            '08' => 'Agosto',
                            '09' => 'Septiembre',
                            '10' => 'Octubre',
                            '11' => 'Noviembre',
                            '12' => 'Diciembre',
                        ])
                        ->default(now()->format('m'))
                        ->required(),
                    \Filament\Forms\Components\Select::make('year')
                        ->label('Año')
                        ->options([
                            '2024' => '2024',
                            '2025' => '2025',
                            '2026' => '2026',
                        ])
                        ->default('2025')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $month = $data['month'];
                    $year = $data['year'];

                    // Generate the URL for the PDF report
                    $pdfUrl = route('activities.pdf.report', [
                        'months' => $month,
                        'year' => $year,
                    ]);

                    // Open in new tab using JavaScript
                    $this->dispatch('open-pdf-in-new-tab', url: $pdfUrl);
                }),
        ];
    }

    public function getListeners(): array
    {
        return [
            'open-pdf-in-new-tab' => 'openPdfInNewTab',
        ];
    }

    public function openPdfInNewTab($url)
    {
        $this->js("window.open('$url', '_blank');");
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
