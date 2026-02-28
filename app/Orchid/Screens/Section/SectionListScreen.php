<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Section;

use App\Models\Building;
use App\Models\Section;
use App\Orchid\Layouts\Section\SectionListLayout;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class SectionListScreen extends Screen
{
    public function query(Request $request): iterable
    {
        $query = Section::with('building')->withCount('floors');
        if ($buildingId = $request->get('building')) {
            $query->where('building_id', $buildingId);
        }
        return [
            'sections' => $query->defaultSort('id', 'desc')->paginate(),
            'building' => $buildingId ? Building::find($buildingId) : null,
        ];
    }

    public function name(): ?string
    {
        return 'Секции';
    }

    public function description(): ?string
    {
        return 'Список секций';
    }

    public function commandBar(): iterable
    {
        $buildingId = request()->get('building');
        return [
            Link::make(__('Добавить'))
                ->icon('bs.plus-circle')
                ->route('platform.sections.create', $buildingId ? ['building' => $buildingId] : []),
        ];
    }

    public function layout(): iterable
    {
        return [
            SectionListLayout::class,
        ];
    }
}
