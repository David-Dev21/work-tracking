<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\AreaRole;
use App\Models\Intern;
use App\Models\InternRegistration;
use App\Models\InternType;
use App\Models\Responsible;
use Illuminate\Support\Facades\DB;

class SyncController extends Controller
{
    public function syncAll()
    {
        $this->syncAreas();
        $this->syncAreaRoles();
        $this->syncResponsibles();
        $this->syncInterns();
        $this->syncInternTypes();
        $this->syncInternRegistrations();

        return back()->with('success', 'Sincronización completada.');
    }

    public function syncAreas()
    {
        $areas = DB::connection('mariadb2')->table('area')->get();

        foreach ($areas as $area) {
            Area::updateOrCreate(
                [
                    'id' => $area->id_area,
                ],
                [
                    'name' => $area->nombre_area
                ]
            );
        }

        return response()->json(['message' => 'Áreas sincronizadas correctamente.']);
    }

    public function syncAreaRoles()
    {
        $cargos = DB::connection('mariadb2')->table('cargo')->get();

        foreach ($cargos as $cargo) {
            AreaRole::updateOrCreate(
                ['id' => $cargo->id_cargo],
                [
                    'name' => $cargo->nombre_cargo,
                    'area_id' => $cargo->id_area_fk,
                ]
            );
        }

        return response()->json(['message' => 'Cargos sincronizados correctamente.']);
    }

    public function syncResponsibles()
    {
        $supervisores = DB::connection('mariadb2')->table('supervisor')->get();

        foreach ($supervisores as $supervisor) {
            Responsible::updateOrCreate(
                ['id' => $supervisor->id_supervisor],
                [
                    'name' => $supervisor->nombre_supervisor,
                    'last_name' => $supervisor->paterno_supervisor . ' ' . $supervisor->materno_supervisor,
                    'identity_card' => $supervisor->ci_supervisor,
                    'academic_degree' => $supervisor->grado_academico_supervisor,
                    'area_role_id' => $supervisor->cargo_supervisor,
                ]
            );
        }

        return response()->json(['message' => 'Supervisores sincronizados correctamente.']);
    }

    public function syncInterns()
    {
        $pasantes = DB::connection('mariadb2')->table('pasante')->get();

        foreach ($pasantes as $pasante) {
            Intern::updateOrCreate(
                ['id' => $pasante->id_pasante],
                [
                    'name' => $pasante->nombres,
                    'last_name' => $pasante->apellidos,
                    'identity_card' => $pasante->ci_pasante,
                    'university_registration' => $pasante->registro_universitario,
                ]
            );
        }

        return response()->json(['message' => 'Pasantes sincronizados correctamente.']);
    }

    public function syncInternTypes()
    {
        $tipos = DB::connection('mariadb2')->table('tipo_pasante')->get();

        foreach ($tipos as $tipo) {
            InternType::updateOrCreate(
                ['id' => $tipo->id_tipo_pasante],
                [
                    'name' => $tipo->nombre_tipo_pasante,
                ]
            );
        }

        return response()->json(['message' => 'Tipos de pasantes sincronizados correctamente.']);
    }

    public function syncInternRegistrations()
    {
        $registros = DB::connection('mariadb2')->table('registro_pasante')->get();

        foreach ($registros as $registro) {
            InternRegistration::updateOrCreate(
                ['id' => $registro->id_registro],
                [
                    'intern_id' => $registro->id_pasante_fk,
                    'area_id' => $registro->id_area_fk,
                    'intern_type_id' => $registro->id_tipo_pasante_fk,
                    'start_date' => $registro->fecha_inicio,
                    'end_date' => $registro->fecha_fin,
                ]
            );
        }

        return response()->json(['message' => 'Registros de pasantes sincronizados correctamente.']);
    }
}
