<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SyncButton extends Widget
{
    protected static string $view = 'filament.widgets.sync-button';
    protected int | string | array $columnSpan = 'full';

    // public static function canView(): bool
    // {
    //     $user = Auth::user();

    //     if (!$user) {
    //         return false;
    //     }

    //     // Verificar si el usuario tiene rol de pasante - no puede ver este widget
    //     $isPasante = DB::table('model_has_roles')
    //         ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
    //         ->where('model_has_roles.model_id', $user->id)
    //         ->where('model_has_roles.model_type', get_class($user))
    //         ->where('roles.name', 'pasante')
    //         ->exists();

    //     if ($isPasante) {
    //         return false;
    //     }

    //     // Verificar si tiene el permiso directamente asignado
    //     $hasDirectPermission = DB::table('model_has_permissions')
    //         ->join('permissions', 'permissions.id', '=', 'model_has_permissions.permission_id')
    //         ->where('model_has_permissions.model_id', $user->id)
    //         ->where('model_has_permissions.model_type', get_class($user))
    //         ->where('permissions.name', 'widget_SyncButton')
    //         ->exists();

    //     // Verificar si tiene el permiso a través de algún rol
    //     $hasRolePermission = DB::table('model_has_roles')
    //         ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
    //         ->join('role_has_permissions', 'role_has_permissions.role_id', '=', 'roles.id')
    //         ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
    //         ->where('model_has_roles.model_id', $user->id)
    //         ->where('model_has_roles.model_type', get_class($user))
    //         ->where('permissions.name', 'widget_SyncButton')
    //         ->exists();

    //     return $hasDirectPermission || $hasRolePermission;
    // }
}
