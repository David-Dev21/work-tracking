<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Area;
use App\Models\Project;

class ProjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Project::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-6 months', '+1 month');
        $endDate = fake()->dateTimeBetween($startDate, '+4 months');

        return [
            'area_id' => Area::factory(),
            'name' => 'Proyecto ' . fake()->words(2, true),
            'description' => fake()->paragraph(2),
            'state' => fake()->randomElement(["pendiente", "en_progreso", "finalizado"]),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
        ];
    }
}
