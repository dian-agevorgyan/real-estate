<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Complex;

use App\Models\Complex;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class ComplexListLayout extends Table
{
    protected $target = 'complexes';

    public function columns(): array
    {
        return [
            TD::make('id', 'ID')->sort()->width('80px'),
            TD::make('name', 'Название')->sort()->filter(),
            TD::make('address', 'Адрес')->filter(),
            TD::make('status', 'Статус')
                ->render(fn (Complex $c) => $c->status->label()),
            TD::make('buildings_count', 'Зданий')->sort(),
            TD::make('created_at', 'Создан')->sort(),
            TD::make(__('Действия'))
                ->align(TD::ALIGN_CENTER)
                ->width('120px')
                ->render(fn (Complex $c) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        Link::make(__('Редактировать'))
                            ->route('platform.complexes.edit', $c)
                            ->icon('bs.pencil'),
                        Link::make(__('Здания'))
                            ->route('platform.buildings', ['complex' => $c->id])
                            ->icon('bs.building'),
                    ])),
        ];
    }
}
