<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Floor;

use App\Models\Floor;
use App\Models\Section;
use App\Orchid\Layouts\Floor\FloorListLayout;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class FloorListScreen extends Screen
{
    public function query(Request $request): iterable
    {
        $query = Floor::with('section.building')->withCount('premises');
        if ($sectionId = $request->get('section')) {
            $query->where('section_id', $sectionId);
        }
        return [
            'floors' => $query->defaultSort('number')->paginate(),
            'section' => $sectionId ? Section::find($sectionId) : null,
        ];
    }

    public function name(): ?string
    {
        return 'Этажи';
    }

    public function description(): ?string
    {
        return 'Список этажей';
    }

    public function commandBar(): iterable
    {
        $sectionId = request()->get('section');
        return [
            Link::make(__('Добавить'))
                ->icon('bs.plus-circle')
                ->route('platform.floors.create', $sectionId ? ['section' => $sectionId] : []),
        ];
    }

    public function layout(): iterable
    {
        return [
            FloorListLayout::class,
        ];
    }
}
