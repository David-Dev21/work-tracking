<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\AreaRole;
use App\Models\Responsible;
use App\Models\User;

class ResponsibleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Responsible::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'area_role_id' => AreaRole::factory(),
            'academic_degree' => fake()->randomNumber(),
            'name' => fake()->name(),
            'last_name' => fake()->lastName(),
            'identity_card' => fake()->word(),
        ];
    }
}
