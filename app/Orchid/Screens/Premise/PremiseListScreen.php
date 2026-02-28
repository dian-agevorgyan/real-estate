<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Premise;

use App\Models\Premise;
use App\Orchid\Layouts\Premise\PremiseFiltersLayout;
use App\Orchid\Layouts\Premise\PremiseListLayout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class PremiseListScreen extends Screen
{
    public function query(): iterable
    {
        return [
            'premises' => Premise::with(['floor.section.building.complex'])
                ->filters(PremiseFiltersLayout::class)
                ->defaultSort('id', 'desc')
                ->paginate(),
        ];
    }

    public function name(): ?string
    {
        return 'Помещения';
    }

    public function description(): ?string
    {
        return 'Список помещений с фильтрами';
    }

    public function commandBar(): iterable
    {
        return [
            Link::make(__('Добавить'))
                ->icon('bs.plus-circle')
                ->route('platform.premises.create'),
        ];
    }

    public function layout(): iterable
    {
        return [
            PremiseFiltersLayout::class,
            PremiseListLayout::class,
        ];
    }
}
