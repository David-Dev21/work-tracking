<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Area;
use App\Models\Intern;
use App\Models\InternRegistration;
use App\Models\InternType;

class InternRegistrationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = InternRegistration::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'intern_type_id' => InternType::factory(),
            'intern_id' => Intern::factory(),
            'area_id' => Area::factory(),
            'start_date' => fake()->date(),
            'end_date' => fake()->date(),
        ];
    }
}
