<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Premise;

use App\Models\Premise;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class PremiseListLayout extends Table
{
    protected $target = 'premises';

    public function columns(): array
    {
        return [
            TD::make('id', 'ID')->sort()->width('60px'),
            TD::make('apartment_number', '№')->sort()->filter(),
            TD::make('type', 'Тип')
                ->render(fn (Premise $p) => $p->type->label()),
            TD::make('status', 'Статус')
                ->render(fn (Premise $p) => $p->status->label()),
            TD::make('rooms', 'Комнат')->sort(),
            TD::make('area_total', 'Площадь (м²)')->sort(),
            TD::make('price_base', 'Цена')->sort()
                ->render(fn (Premise $p) => $p->price_base ? number_format($p->price_base, 0, '', ' ') : '—'),
            TD::make('floor.section.building.complex.name', 'Комплекс'),
            TD::make('floor_number', 'Этаж')->sort(),
            TD::make('updated_at', 'Обновлено')
                ->usingComponent(DateTimeSplit::class)
                ->sort(),
            TD::make(__('Действия'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn (Premise $p) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        Link::make(__('Редактировать'))
                            ->route('platform.premises.edit', $p)
                            ->icon('bs.pencil'),
                        Link::make(__('История статусов'))
                            ->route('platform.premises.status-history.premise', $p)
                            ->icon('bs.clock-history'),
                        Link::make(__('История цен'))
                            ->route('platform.premises.price-history.premise', $p)
                            ->icon('bs.currency-dollar'),
                    ])),
        ];
    }
}
