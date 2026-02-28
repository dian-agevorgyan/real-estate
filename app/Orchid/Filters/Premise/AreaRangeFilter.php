<?php

declare(strict_types=1);

namespace App\Orchid\Filters\Premise;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\Input;

class AreaRangeFilter extends Filter
{
    public function name(): string
    {
        return 'Площадь';
    }

    public function parameters(): array
    {
        return ['area_min', 'area_max'];
    }

    public function run(Builder $builder): Builder
    {
        $min = $this->request->get('area_min');
        $max = $this->request->get('area_max');
        return $builder->areaBetween(
            $min !== null && $min !== '' ? (float) $min : null,
            $max !== null && $max !== '' ? (float) $max : null
        );
    }

    public function display(): array
    {
        return [
            Input::make('area_min')
                ->type('number')
                ->step(0.1)
                ->value($this->request->get('area_min'))
                ->title('Площадь от (м²)')
                ->placeholder('мин'),
            Input::make('area_max')
                ->type('number')
                ->step(0.1)
                ->value($this->request->get('area_max'))
                ->title('Площадь до (м²)')
                ->placeholder('макс'),
        ];
    }

    public function value(): string
    {
        $min = $this->request->get('area_min');
        $max = $this->request->get('area_max');
        if (!$min && !$max) {
            return '';
        }
        $parts = [];
        if ($min) {
            $parts[] = 'от ' . $min . ' м²';
        }
        if ($max) {
            $parts[] = 'до ' . $max . ' м²';
        }
        return $this->name() . ': ' . implode(' ', $parts);
    }
}
