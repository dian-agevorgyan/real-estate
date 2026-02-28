<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Floor;

use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Layouts\Rows;

class FloorEditLayout extends Rows
{
    public function fields(): array
    {
        return [
            Relation::make('floor.section_id')
                ->fromModel(\App\Models\Section::class, 'name')
                ->title('Секция')
                ->required(),

            Input::make('floor.number')
                ->type('number')
                ->title('Номер этажа')
                ->required()
                ->placeholder('1'),

            Input::make('floor.apartments_count')
                ->type('number')
                ->title('Квартир на этаже')
                ->placeholder('4'),

            Upload::make('floor.plan_image')
                ->title('План этажа')
                ->groups('floor_plan')
                ->maxFiles(1)
                ->help('Один файл — план этажа'),
        ];
    }
}
