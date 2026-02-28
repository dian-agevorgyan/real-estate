<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Building;

use App\Models\Building;
use App\Models\Complex;
use App\Orchid\Layouts\Building\BuildingListLayout;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class BuildingListScreen extends Screen
{
    public function query(Request $request): iterable
    {
        $query = Building::with('complex')->withCount('sections');
        if ($complexId = $request->get('complex')) {
            $query->where('complex_id', $complexId);
        }
        return [
            'buildings' => $query->defaultSort('id', 'desc')->paginate(),
            'complex' => $complexId ? Complex::find($complexId) : null,
        ];
    }

    public function name(): ?string
    {
        return 'Здания';
    }

    public function description(): ?string
    {
        return 'Список зданий';
    }

    public function commandBar(): iterable
    {
        $complexId = request()->get('complex');
        return [
            Link::make(__('Добавить'))
                ->icon('bs.plus-circle')
                ->route('platform.buildings.create', $complexId ? ['complex' => $complexId] : []),
        ];
    }

    public function layout(): iterable
    {
        return [
            BuildingListLayout::class,
        ];
    }
}
