<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Activity;
use App\Models\Area;
use App\Models\Location;
use App\Models\Project;

class ActivityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Activity::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'location_id' => Location::factory(),
            'area_id' => Area::factory(),
            'project_id' => Project::factory(),
            'name' => fake()->sentence(3),
            'description' => fake()->paragraph(1),
            'state' => fake()->randomElement(["pendiente", "en_progreso", "finalizado"]),
            'priority' => fake()->randomElement(["baja", "media", "alta"]),
        ];
    }
}
