<?php

namespace App\Filament\Widgets;

use App\Models\InternRegistration;
use App\Models\Area;
use Filament\Widgets\ChartWidget;

class InternAreaChar extends ChartWidget
{
    protected static ?string $heading = 'Pasantes por Área';

    public ?string $filter = 'current';

    public function getHeading(): string
    {
        return $this->filter === 'all' ? 'Todos los Pasantes por Área' : 'Pasantes Actuales por Área';
    }

    protected function getFilters(): ?array
    {
        return [
            'current' => 'Pasantes Actuales',
            'all' => 'Todos los Pasantes',
        ];
    }

    protected function getData(): array
    {
        if ($this->filter === 'all') {
            // Obtener todas las áreas con la cantidad total de pasantes registrados
            $areas = Area::withCount('internRegistrations as intern_count')->get();
        } else {
            // Obtener todas las áreas con la cantidad de pasantes registrados ACTUALMENTE (por defecto)
            $areas = Area::withCount(['internRegistrations as intern_count' => function ($query) {
                $today = now()->format('Y-m-d');
                $query->where('start_date', '<=', $today)
                    ->where(function ($q) use ($today) {
                        $q->where('end_date', '>=', $today)
                            ->orWhereNull('end_date');
                    });
            }])->get();
        }

        $labels = [];
        $data = [];
        $colors = [];

        // Colores para el gráfico de pastel
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
            if ($area->intern_count > 0) { // Solo mostrar áreas con pasantes
                $labels[] = $area->name;
                $data[] = $area->intern_count;
                $colors[] = $colorPalette[$index % count($colorPalette)];
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Cantidad de Pasantes',
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
        return 'pie';
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
