<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Floor;
use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;

class FloorFactory extends Factory
{
    protected $model = Floor::class;

    public function definition(): array
    {
        return [
            'section_id' => Section::factory(),
            'number' => fake()->numberBetween(1, 20),
            'apartments_count' => fake()->numberBetween(2, 6),
            'plan_image' => null,
        ];
    }
}
