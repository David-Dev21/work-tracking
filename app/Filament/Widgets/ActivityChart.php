<?php

namespace App\Filament\Widgets;

use App\Models\Activity;
use App\Models\Area;
use Filament\Widgets\ChartWidget;

class ActivityChart extends ChartWidget
{
    protected static ?string $heading = 'Actividades por Estado y Área';

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        // Obtener todas las áreas con sus actividades por estado
        $areas = Area::withCount([
            'activities as completed_activities' => function ($query) {
                $query->where('state', 'finalizado');
            },
            'activities as pending_activities' => function ($query) {
                $query->where('state', 'pendiente');
            },
            'activities as in_progress_activities' => function ($query) {
                $query->where('state', 'en_progreso');
            }
        ])->get();

        $labels = [];
        $completedData = [];
        $pendingData = [];
        $inProgressData = [];

        foreach ($areas as $area) {
            $labels[] = $area->name;
            $completedData[] = $area->completed_activities;
            $pendingData[] = $area->pending_activities;
            $inProgressData[] = $area->in_progress_activities;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Finalizadas',
                    'data' => $completedData,
                    'backgroundColor' => '#10B981', // Verde
                    'borderColor' => '#10B981',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'En Progreso',
                    'data' => $inProgressData,
                    'backgroundColor' => '#F59E0B', // Amarillo
                    'borderColor' => '#F59E0B',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Pendientes',
                    'data' => $pendingData,
                    'backgroundColor' => '#EF4444', // Rojo
                    'borderColor' => '#EF4444',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                    'labels' => [
                        'padding' => 20,
                        'usePointStyle' => true,
                    ]
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ]
            ],
            'responsive' => true,
            'interaction' => [
                'mode' => 'nearest',
                'axis' => 'x',
                'intersect' => false,
            ],
            'scales' => [
                'x' => [
                    'display' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Áreas'
                    ]
                ],
                'y' => [
                    'display' => true,
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Cantidad de Actividades'
                    ],
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }
}
