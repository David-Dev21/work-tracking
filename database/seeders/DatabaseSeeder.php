<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Solo crear datos para Project, Activity y Assignment
        // usando los datos existentes en otras tablas

        // Obtener datos existentes
        $existingAreas = \App\Models\Area::all();
        $existingLocations = \App\Models\Location::all();
        $existingInterns = \App\Models\Intern::all();

        if ($existingAreas->isEmpty()) {
            echo "❌ No hay áreas en la base de datos. Crea áreas primero.\n";
            return;
        }

        // 1. Crear Proyectos usando áreas existentes
        $projects = \App\Models\Project::factory(5)
            ->recycle($existingAreas)
            ->create();

        // 2. Crear Actividades usando datos existentes
        // Crear 10 actividades con proyecto asignado
        $activitiesWithProject = \App\Models\Activity::factory(10)
            ->recycle($existingAreas)
            ->recycle($existingLocations->isNotEmpty() ? $existingLocations : null)
            ->recycle($projects)
            ->create();

        // Crear 5 actividades sin proyecto (project_id = null)
        $activitiesWithoutProject = \App\Models\Activity::factory(5)
            ->state(['project_id' => null])
            ->recycle($existingAreas)
            ->recycle($existingLocations->isNotEmpty() ? $existingLocations : null)
            ->create();

        $activities = $activitiesWithProject->merge($activitiesWithoutProject);

        // 3. Crear Asignaciones usando datos existentes
        if ($existingInterns->isNotEmpty()) {
            \App\Models\Assignment::factory(20)
                ->recycle($existingInterns)
                ->recycle($activities)
                ->recycle($projects)
                ->create();
        }

        echo "✅ Creados " . $projects->count() . " proyectos\n";
        echo "✅ Creadas " . $activities->count() . " actividades (10 con proyecto, 5 sin proyecto)\n";
        echo "✅ Creadas 20 asignaciones\n";
    }
}
