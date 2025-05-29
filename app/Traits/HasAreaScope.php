<?php

namespace App\Traits;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;

trait HasAreaScope
{
    /**
     * Obtiene el ID del área asignada al usuario autenticado.
     *
     * @return int|null
     */
    public static function getUserAreaId()
    {
        $user = Filament::auth()->user();

        if (!$user) {
            return null;
        }

        // Si el usuario es un Responsible, obtener su área
        if ($responsible = $user->responsible) {
            return $responsible->areaRole->area_id;
        }

        // Si el usuario es un Intern, obtener su área del último registro
        if ($intern = $user->interns) {
            $latestRegistration = $intern->internRegistrations()
                ->latest('start_date')
                ->first();
            if ($latestRegistration) {
                return $latestRegistration->area_id;
            }
        }

        return null;
    }

    /**
     * Aplica el filtro de área al query basado en el área del usuario autenticado.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function scopeFilterByUserArea(Builder $query): Builder
    {
        $areaId = self::getUserAreaId();

        if ($areaId !== null) {
            return $query->where('area_id', $areaId);
        }

        return $query;
    }
}
