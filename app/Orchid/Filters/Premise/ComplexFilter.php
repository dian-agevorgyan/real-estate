<?php

declare(strict_types=1);

namespace App\Orchid\Filters\Premise;

use App\Models\Premise;
use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\Select;

class ComplexFilter extends Filter
{
    public function name(): string
    {
        return 'Комплекс';
    }

    public function parameters(): array
    {
        return ['complex_id'];
    }

    public function run(Builder $builder): Builder
    {
        $id = $this->request->get('complex_id');
        if ($id === null || $id === '') {
            return $builder;
        }
        return $builder->byComplex((int) $id);
    }

    public function display(): array
    {
        return [
            Select::make('complex_id')
                ->fromModel(\App\Models\Complex::class, 'name', 'id')
                ->empty()
                ->value($this->request->get('complex_id'))
                ->title($this->name()),
        ];
    }

    public function value(): string
    {
        $id = $this->request->get('complex_id');
        if (!$id) {
            return '';
        }
        $complex = \App\Models\Complex::find($id);
        return $complex ? $this->name() . ': ' . $complex->name : '';
    }
}
