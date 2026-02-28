<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Floor;

use App\Models\Floor;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class FloorListLayout extends Table
{
    protected $target = 'floors';

    public function columns(): array
    {
        return [
            TD::make('id', 'ID')->sort()->width('80px'),
            TD::make('number', 'Этаж')->sort()->filter(),
            TD::make('section.name', 'Секция')->sort(),
            TD::make('section.building.name', 'Здание'),
            TD::make('apartments_count', 'Квартир')->sort(),
            TD::make('premises_count', 'Помещений'),
            TD::make(__('Действия'))
                ->align(TD::ALIGN_CENTER)
                ->width('120px')
                ->render(fn (Floor $f) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        Link::make(__('Редактировать'))
                            ->route('platform.floors.edit', $f)
                            ->icon('bs.pencil'),
                        Link::make(__('Помещения'))
                            ->route('platform.premises', ['floor' => $f->id])
                            ->icon('bs.house'),
                    ])),
        ];
    }
}
