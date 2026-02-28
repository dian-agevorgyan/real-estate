<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Section;

use App\Models\Section;
use App\Orchid\Layouts\Section\SectionEditLayout;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class SectionEditScreen extends Screen
{
    public function query(?Section $section = null, Request $request): iterable
    {
        $section = $section ?? new Section();
        if (($buildingId = $request->get('building')) && !$section->exists) {
            $section->building_id = (int) $buildingId;
        }
        return [
            'section' => $section,
        ];
    }

    public function name(): ?string
    {
        return $this->query()['section']->exists ? 'Редактировать секцию' : 'Создать секцию';
    }

    public function description(): ?string
    {
        return 'Информация о секции';
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
            Layout::block(SectionEditLayout::class)
                ->title('Информация')
                ->description('Название, этажность'),
        ];
    }

    public function save(Request $request, ?Section $section = null): \Illuminate\Http\RedirectResponse
    {
        $section ??= new Section();
        $data = $request->validate([
            'section.building_id' => 'required|exists:buildings,id',
            'section.name' => 'required|string|max:255',
            'section.number' => 'nullable|string|max:50',
            'section.floors_count_in_section' => 'required|integer|min:1',
        ]);

        $section->fill($data['section'])->save();
        Toast::info(__('Сохранено'));

        return redirect()->route('platform.sections', ['building' => $section->building_id]);
    }
}
