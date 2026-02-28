<?php

declare(strict_types=1);

namespace App\Orchid\Filters\Premise;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\Input;

class RoomsFilter extends Filter
{
    public function name(): string
    {
        return 'Комнаты';
    }

    public function parameters(): array
    {
        return ['rooms_min', 'rooms_max'];
    }

    public function run(Builder $builder): Builder
    {
        $min = $this->request->get('rooms_min');
        $max = $this->request->get('rooms_max');
        return $builder->roomsBetween(
            $min !== null && $min !== '' ? (int) $min : null,
            $max !== null && $max !== '' ? (int) $max : null
        );
    }

    public function display(): array
    {
        return [
            Input::make('rooms_min')
                ->type('number')
                ->value($this->request->get('rooms_min'))
                ->title('Комнат от')
                ->placeholder('мин'),
            Input::make('rooms_max')
                ->type('number')
                ->value($this->request->get('rooms_max'))
                ->title('Комнат до')
                ->placeholder('макс'),
        ];
    }

    public function value(): string
    {
        $min = $this->request->get('rooms_min');
        $max = $this->request->get('rooms_max');
        if (!$min && !$max) {
            return '';
        }
        $parts = [];
        if ($min) {
            $parts[] = 'от ' . $min;
        }
        if ($max) {
            $parts[] = 'до ' . $max;
        }
        return $this->name() . ': ' . implode(' ', $parts);
    }
}
