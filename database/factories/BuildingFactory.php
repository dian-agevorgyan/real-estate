<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Building;
use App\Models\Complex;
use Illuminate\Database\Eloquent\Factories\Factory;

class BuildingFactory extends Factory
{
    protected $model = Building::class;

    public function definition(): array
    {
        return [
            'complex_id' => Complex::factory(),
            'name' => 'Building ' . fake()->numberBetween(1, 10),
            'number' => (string) fake()->numberBetween(1, 20),
            'floors_count' => fake()->numberBetween(5, 25),
            'built_year' => fake()->numberBetween(2018, 2025),
        ];
    }
}
