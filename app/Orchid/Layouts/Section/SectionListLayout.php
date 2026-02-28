<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Section;

use App\Models\Section;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class SectionListLayout extends Table
{
    protected $target = 'sections';

    public function columns(): array
    {
        return [
            TD::make('id', 'ID')->sort()->width('80px'),
            TD::make('name', 'Название')->sort()->filter(),
            TD::make('number', 'Номер')->filter(),
            TD::make('building.name', 'Здание')->sort(),
            TD::make('floors_count_in_section', 'Этажей в секции')->sort(),
            TD::make('floors_count', 'Этажей'),
            TD::make(__('Действия'))
                ->align(TD::ALIGN_CENTER)
                ->width('120px')
                ->render(fn (Section $s) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        Link::make(__('Редактировать'))
                            ->route('platform.sections.edit', $s)
                            ->icon('bs.pencil'),
                        Link::make(__('Этажи'))
                            ->route('platform.floors', ['section' => $s->id])
                            ->icon('bs.grid'),
                    ])),
        ];
    }
}
