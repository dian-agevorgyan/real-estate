<?php

declare(strict_types=1);

namespace App\Orchid\Filters\Premise;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\Select;

class FloorFilter extends Filter
{
    public function name(): string
    {
        return 'Этаж';
    }

    public function parameters(): array
    {
        return ['floor_id'];
    }

    public function run(Builder $builder): Builder
    {
        $id = $this->request->get('floor_id');
        if ($id === null || $id === '') {
            return $builder;
        }
        return $builder->byFloor((int) $id);
    }

    public function display(): array
    {
        return [
            Select::make('floor_id')
                ->fromModel(\App\Models\Floor::class, 'number', 'id')
                ->empty()
                ->value($this->request->get('floor_id'))
                ->title($this->name()),
        ];
    }

    public function value(): string
    {
        $id = $this->request->get('floor_id');
        if (!$id) {
            return '';
        }
        $f = \App\Models\Floor::find($id);
        return $f ? $this->name() . ': ' . $f->number : '';
    }
}
