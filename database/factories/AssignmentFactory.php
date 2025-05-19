<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Activity;
use App\Models\Assignment;
use App\Models\Intern;
use App\Models\Project;

class AssignmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Assignment::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'intern_id' => Intern::factory(),
            'activity_id' => Activity::factory(),
            'project_id' => Project::factory(),
            'role' => fake()->word(),
            'assigned_date' => fake()->date(),
        ];
    }
}
