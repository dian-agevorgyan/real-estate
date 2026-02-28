<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PremiseStatus;
use App\Enums\PremiseType;
use App\Models\Floor;
use App\Models\Premise;
use Illuminate\Database\Eloquent\Factories\Factory;

class PremiseFactory extends Factory
{
    protected $model = Premise::class;

    public function definition(): array
    {
        $areaTotal = fake()->randomFloat(2, 25, 150);
        $pricePerM2 = fake()->numberBetween(80_000, 250_000);
        $priceBase = round($areaTotal * $pricePerM2, 2);
        $priceDiscount = fake()->optional(0.3)->randomFloat(2, 0, $priceBase * 0.1);

        return [
            'floor_id' => Floor::factory(),
            'apartment_number' => (string) fake()->numberBetween(1, 999),
            'type' => fake()->randomElement(PremiseType::cases())->value,
            'rooms' => fake()->numberBetween(1, 5),
            'area_total' => $areaTotal,
            'area_living' => round($areaTotal * fake()->randomFloat(2, 0.5, 0.85), 2),
            'area_kitchen' => round($areaTotal * fake()->randomFloat(2, 0.08, 0.15), 2),
            'status' => fake()->randomElement(PremiseStatus::cases())->value,
            'price_base' => $priceBase,
            'price_discount' => $priceDiscount,
            'price_per_m2' => $pricePerM2,
            'floor_number' => fake()->numberBetween(1, 25),
            'layout_image' => null,
            'gallery' => [],
            'extras' => fake()->optional(0.5)->passthrough([
                'balcony' => true,
                'view' => 'city',
                'parking' => 1,
            ]),
        ];
    }
}
