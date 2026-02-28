<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Building;

use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Layouts\Rows;

class BuildingEditLayout extends Rows
{
    public function fields(): array
    {
        return [
            Relation::make('building.complex_id')
                ->fromModel(\App\Models\Complex::class, 'name')
                ->title('Комплекс')
                ->required(),

            Input::make('building.name')
                ->title('Название')
                ->required()
                ->placeholder('Название здания'),

            Input::make('building.number')
                ->title('Номер')
                ->placeholder('Номер корпуса'),

            Input::make('building.floors_count')
                ->type('number')
                ->title('Количество этажей')
                ->required()
                ->value(1),

            Input::make('building.built_year')
                ->type('number')
                ->title('Год постройки')
                ->placeholder('2024'),
        ];
    }
}
