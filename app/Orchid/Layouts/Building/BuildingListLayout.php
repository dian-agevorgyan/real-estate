<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Building;

use App\Models\Building;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class BuildingListLayout extends Table
{
    protected $target = 'buildings';

    public function columns(): array
    {
        return [
            TD::make('id', 'ID')->sort()->width('80px'),
            TD::make('name', 'Название')->sort()->filter(),
            TD::make('number', 'Номер')->filter(),
            TD::make('complex.name', 'Комплекс')->sort(),
            TD::make('floors_count', 'Этажей')->sort(),
            TD::make('built_year', 'Год постройки')->sort(),
            TD::make('sections_count', 'Секций'),
            TD::make(__('Действия'))
                ->align(TD::ALIGN_CENTER)
                ->width('120px')
                ->render(fn (Building $b) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        Link::make(__('Редактировать'))
                            ->route('platform.buildings.edit', $b)
                            ->icon('bs.pencil'),
                        Link::make(__('Секции'))
                            ->route('platform.sections', ['building' => $b->id])
                            ->icon('bs.layers'),
                    ])),
        ];
    }
}
