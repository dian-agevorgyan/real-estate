<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Floor;

use App\Models\Floor;
use App\Orchid\Layouts\Floor\FloorEditLayout;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class FloorEditScreen extends Screen
{
    public function query(?Floor $floor = null, Request $request): iterable
    {
        $floor = $floor ?? new Floor();
        if (($sectionId = $request->get('section')) && !$floor->exists) {
            $floor->section_id = (int) $sectionId;
        }
        return [
            'floor' => $floor,
        ];
    }

    public function name(): ?string
    {
        return $this->query()['floor']->exists ? 'Редактировать этаж' : 'Создать этаж';
    }

    public function description(): ?string
    {
        return 'Информация об этаже';
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
            Layout::block(FloorEditLayout::class)
                ->title('Информация')
                ->description('Номер этажа, план'),
        ];
    }

    public function save(Request $request, ?Floor $floor = null): \Illuminate\Http\RedirectResponse
    {
        $floor ??= new Floor();
        $data = $request->validate([
            'floor.section_id' => 'required|exists:sections,id',
            'floor.number' => 'required|integer|min:1',
            'floor.apartments_count' => 'nullable|integer|min:0',
            'floor.plan_image' => 'nullable|array',
            'floor.plan_image.*' => 'integer|exists:attachments,id',
        ]);

        $planIds = $data['floor']['plan_image'] ?? [];
        $planIds = is_array($planIds) ? array_slice($planIds, 0, 1) : ($planIds ? [(int) $planIds] : []);
        unset($data['floor']['plan_image']);

        $floor->fill($data['floor'])->save();
        $floor->attachments('floor_plan')->sync($planIds);
        Toast::info(__('Сохранено'));

        return redirect()->route('platform.floors', ['section' => $floor->section_id]);
    }
}
