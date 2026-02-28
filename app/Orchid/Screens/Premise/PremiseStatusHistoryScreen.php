<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Premise;

use App\Models\Premise;
use App\Models\PremiseStatusHistory;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class PremiseStatusHistoryScreen extends Screen
{
    public function query(?Premise $premise = null): iterable
    {
        $query = PremiseStatusHistory::with(['premise.floor.section.building.complex', 'changedBy'])
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
            ? 'История статусов: ' . $premise->apartment_number
            : 'История изменений статусов';
    }

    public function description(): ?string
    {
        return 'Все изменения статусов помещений';
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
                    ->render(fn (PremiseStatusHistory $h) => Link::make($h->premise?->apartment_number ?? '-')
                        ->route('platform.premises.edit', $h->premise_id)),
                TD::make('floor.section.building.complex.name', 'Комплекс')
                    ->render(fn (PremiseStatusHistory $h) => $h->premise?->floor?->section?->building?->complex?->name ?? '-'),
                TD::make('old_status', 'Было')
                    ->render(fn (PremiseStatusHistory $h) => $h->old_status ?? '—'),
                TD::make('new_status', 'Стало')
                    ->render(fn (PremiseStatusHistory $h) => $h->new_status),
                TD::make('changed_at', 'Дата')
                    ->render(fn (PremiseStatusHistory $h) => $h->changed_at?->format('d.m.Y H:i')),
                TD::make('changedBy.name', 'Пользователь'),
            ]),
        ];
    }
}
