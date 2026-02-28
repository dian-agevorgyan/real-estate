<?php

declare(strict_types=1);

namespace App\Orchid\Filters\Premise;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\Input;

class PriceRangeFilter extends Filter
{
    public function name(): string
    {
        return 'Цена';
    }

    public function parameters(): array
    {
        return ['price_min', 'price_max'];
    }

    public function run(Builder $builder): Builder
    {
        $min = $this->request->get('price_min');
        $max = $this->request->get('price_max');
        return $builder->priceBetween(
            $min !== null && $min !== '' ? (float) $min : null,
            $max !== null && $max !== '' ? (float) $max : null
        );
    }

    public function display(): array
    {
        return [
            Input::make('price_min')
                ->type('number')
                ->step(1000)
                ->value($this->request->get('price_min'))
                ->title('Цена от')
                ->placeholder('мин'),
            Input::make('price_max')
                ->type('number')
                ->step(1000)
                ->value($this->request->get('price_max'))
                ->title('Цена до')
                ->placeholder('макс'),
        ];
    }

    public function value(): string
    {
        $min = $this->request->get('price_min');
        $max = $this->request->get('price_max');
        if (!$min && !$max) {
            return '';
        }
        $parts = [];
        if ($min) {
            $parts[] = 'от ' . number_format((float) $min, 0, '', ' ');
        }
        if ($max) {
            $parts[] = 'до ' . number_format((float) $max, 0, '', ' ');
        }
        return $this->name() . ': ' . implode(' ', $parts);
    }
}
