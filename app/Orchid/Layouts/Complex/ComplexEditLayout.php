<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Complex;

use App\Enums\ComplexStatus;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Layouts\Rows;

class ComplexEditLayout extends Rows
{
    public function fields(): array
    {
        return [
            Input::make('complex.name')
                ->title('Название')
                ->required()
                ->placeholder('Название комплекса'),

            TextArea::make('complex.description')
                ->title('Описание')
                ->rows(4)
                ->placeholder('Описание'),

            Input::make('complex.address')
                ->title('Адрес')
                ->placeholder('Адрес'),

            Select::make('complex.status')
                ->title('Статус')
                ->options(ComplexStatus::options())
                ->required(),

            Input::make('complex.lat')
                ->type('number')
                ->step('any')
                ->title('Широта'),

            Input::make('complex.lng')
                ->type('number')
                ->step('any')
                ->title('Долгота'),

            Upload::make('complex.gallery')
                ->title('Галерея')
                ->groups('complex_gallery')
                ->maxFiles(10)
                ->help('Загрузка изображений (Orchid Attachment)'),
        ];
    }
}
