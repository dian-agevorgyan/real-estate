<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Complex;
use App\Models\Floor;
use App\Models\Premise;
use App\Models\Section;
use Illuminate\Database\Seeder;

class RealEstateSeeder extends Seeder
{
    public function run(): void
    {
        $complexes = Complex::factory(3)->create();

        foreach ($complexes as $complex) {
            $buildings = Building::factory(2)->create(['complex_id' => $complex->id]);

            foreach ($buildings as $building) {
                $sections = Section::factory(2)->create([
                    'building_id' => $building->id,
                    'floors_count_in_section' => $building->floors_count,
                ]);

                foreach ($sections as $section) {
                    for ($floorNum = 1; $floorNum <= $section->floors_count_in_section; $floorNum++) {
                        $floor = Floor::create([
                            'section_id' => $section->id,
                            'number' => $floorNum,
                            'apartments_count' => fake()->numberBetween(2, 5),
                        ]);

                        Premise::factory(fake()->numberBetween(2, 6))->create([
                            'floor_id' => $floor->id,
                            'floor_number' => $floorNum,
                        ]);
                    }
                }
            }
        }

        $total = Premise::count();
        $this->command->info("Created {$complexes->count()} complexes with ~{$total} premises.");
    }
}
