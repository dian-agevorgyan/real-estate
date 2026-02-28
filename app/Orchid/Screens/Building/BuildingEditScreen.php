<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Building;

use App\Models\Building;
use App\Models\Complex;
use App\Orchid\Layouts\Building\BuildingEditLayout;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class BuildingEditScreen extends Screen
{
    public function query(?Building $building = null, Request $request): iterable
    {
        $building = $building ?? new Building();
        if (($complexId = $request->get('complex')) && !$building->exists) {
            $building->complex_id = (int) $complexId;
        }
        return [
            'building' => $building,
            'complexes' => Complex::orderBy('name')->get(),
        ];
    }

    public function name(): ?string
    {
        return $this->query()['building']->exists ? 'Редактировать здание' : 'Создать здание';
    }

    public function description(): ?string
    {
        return 'Информация о здании';
    }

    public function commandBar(): iterable
    {
        return [
            Button::make(__('Сохранить'))
                ->icon('bs.check-circle')
                ->method('save'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::block(BuildingEditLayout::class)
                ->title('Информация')
                ->description('Название, этажность, год постройки'),
        ];
    }

    public function save(Request $request, ?Building $building = null): \Illuminate\Http\RedirectResponse
    {
        $building ??= new Building();
        $data = $request->validate([
            'building.complex_id' => 'required|exists:complexes,id',
            'building.name' => 'required|string|max:255',
            'building.number' => 'nullable|string|max:50',
            'building.floors_count' => 'required|integer|min:1',
            'building.built_year' => 'nullable|integer|min:1900|max:2100',
        ]);

        $building->fill($data['building'])->save();
        Toast::info(__('Сохранено'));

        return redirect()->route('platform.buildings', ['complex' => $building->complex_id]);
    }
}
