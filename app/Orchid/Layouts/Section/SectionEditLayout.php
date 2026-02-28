<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Section;

use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Layouts\Rows;

class SectionEditLayout extends Rows
{
    public function fields(): array
    {
        return [
            Relation::make('section.building_id')
                ->fromModel(\App\Models\Building::class, 'name')
                ->title('Здание')
                ->required(),

            Input::make('section.name')
                ->title('Название')
                ->required()
                ->placeholder('Секция А'),

            Input::make('section.number')
                ->title('Номер')
                ->placeholder('1'),

            Input::make('section.floors_count_in_section')
                ->type('number')
                ->title('Этажей в секции')
                ->required()
                ->value(1),
        ];
    }
}
