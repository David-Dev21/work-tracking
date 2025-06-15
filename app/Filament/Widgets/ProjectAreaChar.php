<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use App\Models\Area;
use Filament\Widgets\ChartWidget;

class ProjectAreaChar extends ChartWidget
{
    protected static ?string $heading = 'Proyectos por Área';

    public ?string $filter = 'all';

    public function getHeading(): string
    {
        return match($this->filter) {
            'pendiente' => 'Proyectos Pendientes por Área',
            'en_progreso' => 'Proyectos En Progreso por Área',
            'finalizado' => 'Proyectos Finalizados por Área',
            default => 'Todos los Proyectos por Área',
        };
    }

    protected function getFilters(): ?array
    {
        return [
            'all' => 'Todos los Proyectos',
            'pendiente' => 'Proyectos Pendientes',
            'en_progreso' => 'Proyectos En Progreso',
            'finalizado' => 'Proyectos Finalizados',
        ];
    }

    protected function getData(): array
    {
        if ($this->filter === 'all') {
            // Obtener todas las áreas con la cantidad total de proyectos
            $areas = Area::withCount('projects as project_count')->get();
        } else {
            // Obtener todas las áreas con la cantidad de proyectos filtrados por estado
            $areas = Area::withCount(['projects as project_count' => function ($query) {
                $query->where('state', $this->filter);
            }])->get();
        }

        $labels = [];
        $data = [];
        $colors = [];

        // Colores para el gráfico de dona
        $colorPalette = [
            '#64cbde', // Azul claro
            '#F59E0B', // Amarillo
            '#10B981', // Verde
            '#EF4444', // Rojo
            '#8B5CF6', // Púrpura
            '#F97316', // Naranja
            '#06B6D4', // Cian
            '#84CC16', // Lima
            '#EC4899', // Rosa
            '#6B7280', // Gris
            '#3B82F6', // Azul
            '#EF4444', // Rojo claro
        ];

        foreach ($areas as $index => $area) {
            if ($area->project_count > 0) { // Solo mostrar áreas con proyectos
                $labels[] = $area->name;
                $data[] = $area->project_count;
                $colors[] = $colorPalette[$index % count($colorPalette)];
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Cantidad de Proyectos',
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                    'labels' => [
                        'padding' => 20,
                        'usePointStyle' => true,
                    ]
                ],
                'tooltip' => [
                    'enabled' => true,
                    'backgroundColor' => 'rgba(0, 0, 0, 0.8)',
                    'titleColor' => '#ffffff',
                    'bodyColor' => '#ffffff',
                    'borderColor' => '#ffffff',
                    'borderWidth' => 1,
                    'cornerRadius' => 6,
                    'displayColors' => true,
                ]
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
            'hover' => [
                'mode' => 'nearest',
                'intersect' => true,
            ],
            'scales' => [
                'x' => [
                    'display' => false
                ],
                'y' => [
                    'display' => false
                ]
            ],
        ];
    }
}
