<?php

declare(strict_types=1);

namespace App\Orchid\Filters\Premise;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\Select;

class BuildingFilter extends Filter
{
    public function name(): string
    {
        return 'Здание';
    }

    public function parameters(): array
    {
        return ['building_id'];
    }

    public function run(Builder $builder): Builder
    {
        $id = $this->request->get('building_id');
        if ($id === null || $id === '') {
            return $builder;
        }
        return $builder->byBuilding((int) $id);
    }

    public function display(): array
    {
        return [
            Select::make('building_id')
                ->fromModel(\App\Models\Building::class, 'name', 'id')
                ->empty()
                ->value($this->request->get('building_id'))
                ->title($this->name()),
        ];
    }

    public function value(): string
    {
        $id = $this->request->get('building_id');
        if (!$id) {
            return '';
        }
        $b = \App\Models\Building::find($id);
        return $b ? $this->name() . ': ' . $b->name : '';
    }
}
