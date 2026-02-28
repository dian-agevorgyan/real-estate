<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Dashboard;

use App\Models\Complex;
use App\Models\Premise;
use App\Models\PremiseStatusHistory;
use Illuminate\Support\Facades\DB;
use App\Orchid\Layouts\Dashboard\ComplexStatusChartLayout;
use App\Orchid\Layouts\Dashboard\SalesChartLayout;
use App\Services\RealEstateCacheService;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class DashboardScreen extends Screen
{
    public function __construct(
        private readonly RealEstateCacheService $cache
    ) {
    }

    public function query(): iterable
    {
        $stats = $this->cache->getDashboardStats();

        $complexStats = Complex::selectRaw('status, count(*) as cnt')
            ->groupBy('status')
            ->pluck('cnt', 'status')
            ->toArray();

        $salesByMonth = match (DB::getDriverName()) {
            'mysql' => Premise::where('status', 'sold')
                ->selectRaw('DATE_FORMAT(updated_at, "%Y-%m") as month, count(*) as cnt')
                ->groupBy('month')
                ->orderBy('month')
                ->get(),
            'pgsql' => Premise::where('status', 'sold')
                ->selectRaw("to_char(updated_at, 'YYYY-MM') as month, count(*) as cnt")
                ->groupBy('month')
                ->orderBy('month')
                ->get(),
            default => Premise::where('status', 'sold')
                ->get()
                ->groupBy(fn ($p) => $p->updated_at->format('Y-m'))
                ->map(fn ($g, $m) => (object) ['month' => $m, 'cnt' => $g->count()])
                ->sortKeys()
                ->values(),
        };

        $topPremises = Premise::with(['floor.section.building.complex'])
            ->whereNotNull('price_base')
            ->orderByDesc('price_base')
            ->limit(10)
            ->get();

        $recentStatusChanges = PremiseStatusHistory::with(['premise.floor.section.building.complex', 'changedBy'])
            ->orderByDesc('changed_at')
            ->limit(10)
            ->get();

        $salesValues = $salesByMonth->pluck('cnt')->toArray();
        $salesLabels = $salesByMonth->pluck('month')->toArray();
        if (empty($salesValues)) {
            $salesValues = [0];
            $salesLabels = [date('Y-m')];
        }

        return [
            'stats' => $stats,
            'metrics' => [
                'total_complexes' => ['value' => (string) $stats['total_complexes'], 'diff' => null],
                'total_premises' => ['value' => (string) $stats['total_premises'], 'diff' => null],
                'available' => ['value' => (string) $stats['available'], 'diff' => null],
                'sold' => ['value' => (string) $stats['sold'], 'diff' => null],
                'reserved' => ['value' => (string) $stats['reserved'], 'diff' => null],
            ],
            'salesChart' => [
                [
                    'name' => 'Продажи по месяцам',
                    'values' => $salesValues,
                    'labels' => $salesLabels,
                ],
            ],
            'complexChart' => [
                [
                    'name' => 'Комплексы по статусам',
                    'values' => $complexStats ? array_values($complexStats) : [1],
                    'labels' => $complexStats
                        ? array_map(
                            fn (string $s) => match ($s) {
                                'planning' => 'Планирование',
                                'construction' => 'Строительство',
                                'completed' => 'Сдан',
                                default => $s,
                            },
                            array_keys($complexStats)
                        )
                        : ['Нет данных'],
                ],
            ],
            'topPremises' => $topPremises,
            'recentStatusChanges' => $recentStatusChanges,
        ];
    }

    public function name(): ?string
    {
        return 'Дашборд';
    }

    public function description(): ?string
    {
        return 'Статистика и обзор недвижимости';
    }

    public function commandBar(): iterable
    {
        return [];
    }

    public function layout(): iterable
    {
        return [
            Layout::metrics([
                'Комплексов' => 'metrics.total_complexes',
                'Помещений' => 'metrics.total_premises',
                'Доступно' => 'metrics.available',
                'Продано' => 'metrics.sold',
                'Забронировано' => 'metrics.reserved',
            ]),

            Layout::columns([
                SalesChartLayout::make('salesChart', 'Продажи по месяцам')
                    ->description('Количество проданных помещений'),
                ComplexStatusChartLayout::make('complexChart', 'Комплексы по статусам')
                    ->description('Распределение по статусам'),
            ]),

            Layout::columns([
                Layout::table('topPremises', [
                    TD::make('apartment_number', '№'),
                    TD::make('floor.section.building.complex.name', 'Комплекс'),
                    TD::make('price_base', 'Цена')
                        ->render(fn ($p) => $p->price_base ? number_format($p->price_base, 0, '', ' ') : '—'),
                    TD::make('area_total', 'Площадь')
                        ->render(fn ($p) => $p->area_total ? $p->area_total . ' м²' : '—'),
                ])->title('Топ-10 самых дорогих помещений'),
                Layout::table('recentStatusChanges', [
                    TD::make('premise.apartment_number', 'Квартира'),
                    TD::make('new_status', 'Новый статус')
                        ->render(fn ($h) => $h->new_status),
                    TD::make('changed_at', 'Дата')
                        ->render(fn ($h) => $h->changed_at?->format('d.m.Y H:i')),
                    TD::make('changedBy.name', 'Пользователь'),
                ])->title('Последние изменения статусов'),
            ]),
        ];
    }
}
