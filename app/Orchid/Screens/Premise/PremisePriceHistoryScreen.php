<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Premise;

use App\Models\Premise;
use App\Models\PremisePriceHistory;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class PremisePriceHistoryScreen extends Screen
{
    public function query(?Premise $premise = null): iterable
    {
        $query = PremisePriceHistory::with(['premise.floor.section.building.complex', 'changedBy'])
            ->orderByDesc('changed_at');

        if ($premise) {
            $query->where('premise_id', $premise->id);
        }

        return [
            'history' => $query->paginate(25),
            'premise' => $premise,
        ];
    }

    public function name(): ?string
    {
        $premise = request()->route('premise');
        return $premise instanceof Premise
            ? 'История цен: ' . $premise->apartment_number
            : 'История изменений цен';
    }

    public function description(): ?string
    {
        return 'Все изменения цен помещений';
    }

    public function commandBar(): iterable
    {
        return [
            Link::make(__('Помещения'))
                ->icon('bs.house')
                ->route('platform.premises'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::table('history', [
                TD::make('premise.apartment_number', 'Квартира')
                    ->render(fn (PremisePriceHistory $h) => Link::make($h->premise?->apartment_number ?? '-')
                        ->route('platform.premises.edit', $h->premise_id)),
                TD::make('floor.section.building.complex.name', 'Комплекс')
                    ->render(fn (PremisePriceHistory $h) => $h->premise?->floor?->section?->building?->complex?->name ?? '-'),
                TD::make('old_price', 'Было')
                    ->render(fn (PremisePriceHistory $h) => $h->old_price ? number_format($h->old_price, 0, '', ' ') : '—'),
                TD::make('new_price', 'Стало')
                    ->render(fn (PremisePriceHistory $h) => number_format($h->new_price, 0, '', ' ')),
                TD::make('changed_at', 'Дата')
                    ->render(fn (PremisePriceHistory $h) => $h->changed_at?->format('d.m.Y H:i')),
                TD::make('changedBy.name', 'Пользователь'),
            ]),
        ];
    }
}
