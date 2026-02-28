<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Premise;

use App\Enums\PremiseStatus;
use App\Enums\PremiseType;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Layouts\Rows;

class PremiseEditLayout extends Rows
{
    public function fields(): array
    {
        return [
            Relation::make('premise.floor_id')
                ->fromModel(\App\Models\Floor::class, 'number', 'id')
                ->title('Этаж')
                ->required(),

            Input::make('premise.apartment_number')
                ->title('Номер квартиры')
                ->required()
                ->placeholder('101'),

            Select::make('premise.type')
                ->title('Тип')
                ->options(PremiseType::options())
                ->required(),

            Select::make('premise.status')
                ->title('Статус')
                ->options(PremiseStatus::options())
                ->required(),

            Input::make('premise.rooms')
                ->type('number')
                ->title('Комнат')
                ->required()
                ->value(1),

            Input::make('premise.area_total')
                ->type('number')
                ->step(0.01)
                ->title('Общая площадь (м²)'),

            Input::make('premise.area_living')
                ->type('number')
                ->step(0.01)
                ->title('Жилая площадь (м²)'),

            Input::make('premise.area_kitchen')
                ->type('number')
                ->step(0.01)
                ->title('Площадь кухни (м²)'),

            Input::make('premise.price_base')
                ->type('number')
                ->step(1000)
                ->title('Базовая цена'),

            Input::make('premise.price_discount')
                ->type('number')
                ->step(1000)
                ->title('Скидка'),

            Input::make('premise.price_per_m2')
                ->type('number')
                ->step(0.01)
                ->title('Цена за м²'),

            Input::make('premise.floor_number')
                ->type('number')
                ->title('Номер этажа (дубликат)'),

            Upload::make('premise.layout_image')
                ->title('Планировка')
                ->groups('premise_layout')
                ->maxFiles(1),

            Upload::make('premise.gallery')
                ->title('Галерея')
                ->groups('premise_gallery')
                ->maxFiles(10),

            CheckBox::make('premise.extras.balcony')
                ->title('Балкон')
                ->value(1)
                ->placeholder('Есть балкон'),

            CheckBox::make('premise.extras.loggia')
                ->title('Лоджия')
                ->value(1)
                ->placeholder('Есть лоджия'),

            Select::make('premise.extras.view')
                ->title('Вид из окна')
                ->options([
                    'city' => 'Город',
                    'park' => 'Парк',
                    'courtyard' => 'Двор',
                    'river' => 'Река',
                    'none' => 'Нет вида',
                ])
                ->empty()
                ->placeholder('Выберите'),

            Input::make('premise.extras.parking')
                ->type('number')
                ->title('Паркинг (кол-во мест)')
                ->placeholder('0'),
        ];
    }
}
