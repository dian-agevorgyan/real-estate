<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ComplexStatus;
use App\Models\Complex;
use Illuminate\Database\Eloquent\Factories\Factory;

class ComplexFactory extends Factory
{
    protected $model = Complex::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company() . ' Residential Complex',
            'description' => fake()->paragraphs(2, true),
            'address' => fake()->address(),
            'status' => fake()->randomElement(ComplexStatus::cases())->value,
            'gallery' => [],
            'lat' => fake()->latitude(55.5, 56.0),
            'lng' => fake()->longitude(37.3, 37.9),
        ];
    }
}
