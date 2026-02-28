<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Complex;

use App\Models\Complex;
use App\Orchid\Layouts\Complex\ComplexEditLayout;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class ComplexEditScreen extends Screen
{
    public function query(?Complex $complex = null): iterable
    {
        return [
            'complex' => $complex ?? new Complex(),
        ];
    }

    public function name(): ?string
    {
        $complex = request()->route('complex');
        return $complex instanceof \App\Models\Complex && $complex->exists
            ? 'Редактировать комплекс'
            : 'Создать комплекс';
    }

    public function description(): ?string
    {
        return 'Основная информация о жилом комплексе';
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
            Layout::block(ComplexEditLayout::class)
                ->title('Информация')
                ->description('Название, описание, адрес, статус'),
        ];
    }

    public function save(Request $request, ?Complex $complex = null): \Illuminate\Http\RedirectResponse
    {
        $complex ??= new Complex();
        $data = $request->validate([
            'complex.name' => 'required|string|max:255',
            'complex.description' => 'nullable|string',
            'complex.address' => 'nullable|string|max:255',
            'complex.status' => 'required|in:planning,construction,completed',
            'complex.lat' => 'nullable|numeric',
            'complex.lng' => 'nullable|numeric',
            'complex.gallery' => 'nullable|array',
            'complex.gallery.*' => 'integer|exists:attachments,id',
        ]);

        $galleryIds = $data['complex']['gallery'] ?? [];
        unset($data['complex']['gallery']);

        $complex->fill($data['complex'])->save();
        $complex->attachments('complex_gallery')->sync($galleryIds);
        Toast::info(__('Сохранено'));

        return redirect()->route('platform.complexes');
    }
}
