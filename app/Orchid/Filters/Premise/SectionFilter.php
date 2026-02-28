<?php

declare(strict_types=1);

namespace App\Orchid\Filters\Premise;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\Select;

class SectionFilter extends Filter
{
    public function name(): string
    {
        return 'Секция';
    }

    public function parameters(): array
    {
        return ['section_id'];
    }

    public function run(Builder $builder): Builder
    {
        $id = $this->request->get('section_id');
        if ($id === null || $id === '') {
            return $builder;
        }
        return $builder->bySection((int) $id);
    }

    public function display(): array
    {
        return [
            Select::make('section_id')
                ->fromModel(\App\Models\Section::class, 'name', 'id')
                ->empty()
                ->value($this->request->get('section_id'))
                ->title($this->name()),
        ];
    }

    public function value(): string
    {
        $id = $this->request->get('section_id');
        if (!$id) {
            return '';
        }
        $s = \App\Models\Section::find($id);
        return $s ? $this->name() . ': ' . $s->name : '';
    }
}
