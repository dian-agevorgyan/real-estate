<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Premise;

use App\Models\Premise;
use App\Orchid\Layouts\Premise\PremiseEditLayout;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class PremiseEditScreen extends Screen
{
    public function query(?Premise $premise = null): iterable
    {
        $premise = $premise ?? new Premise();
        $premise->load('floor.section.building');
        return [
            'premise' => $premise,
        ];
    }

    public function name(): ?string
    {
        return $this->query()['premise']->exists ? 'Редактировать помещение' : 'Создать помещение';
    }

    public function description(): ?string
    {
        return 'Параметры помещения';
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
            Layout::block(PremiseEditLayout::class)
                ->title('Основные данные')
                ->description('Тип, статус, площадь, цена'),
        ];
    }

    public function save(Request $request, ?Premise $premise = null): \Illuminate\Http\RedirectResponse
    {
        $premise ??= new Premise();
        $data = $request->validate([
            'premise.floor_id' => 'required|exists:floors,id',
            'premise.apartment_number' => 'required|string|max:50',
            'premise.type' => 'required|in:apartment,studio,penthouse,commercial',
            'premise.rooms' => 'required|integer|min:1',
            'premise.area_total' => 'nullable|numeric|min:0',
            'premise.area_living' => 'nullable|numeric|min:0',
            'premise.area_kitchen' => 'nullable|numeric|min:0',
            'premise.status' => 'required|in:available,reserved,sold,not_for_sale',
            'premise.price_base' => 'nullable|numeric|min:0',
            'premise.price_discount' => 'nullable|numeric|min:0',
            'premise.price_per_m2' => 'nullable|numeric|min:0',
            'premise.floor_number' => 'nullable|integer|min:1',
            'premise.layout_image' => 'nullable|array',
            'premise.layout_image.*' => 'integer|exists:attachments,id',
            'premise.gallery' => 'nullable|array',
            'premise.gallery.*' => 'integer|exists:attachments,id',
            'premise.extras' => 'nullable|array',
            'premise.extras.balcony' => 'nullable',
            'premise.extras.loggia' => 'nullable',
            'premise.extras.view' => 'nullable|string|max:50',
            'premise.extras.parking' => 'nullable|integer|min:0',
        ]);

        $premiseData = $data['premise'];
        $layoutIds = $premiseData['layout_image'] ?? [];
        $layoutIds = is_array($layoutIds) ? array_slice($layoutIds, 0, 1) : [];
        $galleryIds = $premiseData['gallery'] ?? [];
        unset($premiseData['layout_image'], $premiseData['gallery']);

        $extras = $premiseData['extras'] ?? [];
        $extras['balcony'] = !empty($extras['balcony']);
        $extras['loggia'] = !empty($extras['loggia']);
        unset($premiseData['extras']);
        $premiseData['extras'] = array_filter($extras, fn ($v) => $v !== null && $v !== '' && $v !== false);

        $premise->fill($premiseData)->save();
        $premise->attachments('premise_layout')->sync($layoutIds);
        $premise->attachments('premise_gallery')->sync($galleryIds);
        Toast::info(__('Сохранено'));

        return redirect()->route('platform.premises');
    }
}
