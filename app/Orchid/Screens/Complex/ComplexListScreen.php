<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Complex;

use App\Models\Complex;
use App\Orchid\Layouts\Complex\ComplexFiltersLayout;
use App\Orchid\Layouts\Complex\ComplexListLayout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class ComplexListScreen extends Screen
{
    public function query(): iterable
    {
        return [
            'complexes' => Complex::withCount('buildings')
                ->filters(ComplexFiltersLayout::class)
                ->defaultSort('id', 'desc')
                ->paginate(),
        ];
    }

    public function name(): ?string
    {
        return 'Жилые комплексы';
    }

    public function description(): ?string
    {
        return 'Список жилых комплексов';
    }

    public function commandBar(): iterable
    {
        return [
            Link::make(__('Добавить'))
                ->icon('bs.plus-circle')
                ->route('platform.complexes.create'),
        ];
    }

    public function layout(): iterable
    {
        return [
            ComplexFiltersLayout::class,
            ComplexListLayout::class,
        ];
    }
}
