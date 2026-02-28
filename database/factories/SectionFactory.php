<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Building;
use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;

class SectionFactory extends Factory
{
    protected $model = Section::class;

    public function definition(): array
    {
        $floorsCount = fake()->numberBetween(3, 15);
        return [
            'building_id' => Building::factory(),
            'name' => 'Section ' . fake()->randomLetter(),
            'number' => (string) fake()->numberBetween(1, 10),
            'floors_count_in_section' => $floorsCount,
        ];
    }
}
