<?php

declare(strict_types=1);

namespace App\Orchid\Filters\Premise;

use App\Enums\PremiseType;
use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\Select;

class TypeFilter extends Filter
{
    public function name(): string
    {
        return 'Тип';
    }

    public function parameters(): array
    {
        return ['type'];
    }

    public function run(Builder $builder): Builder
    {
        $type = $this->request->get('type');
        if ($type === null || $type === '') {
            return $builder;
        }
        return $builder->byType($type);
    }

    public function display(): array
    {
        return [
            Select::make('type')
                ->options(PremiseType::options())
                ->empty()
                ->value($this->request->get('type'))
                ->title($this->name()),
        ];
    }

    public function value(): string
    {
        $type = $this->request->get('type');
        if (!$type) {
            return '';
        }
        $e = PremiseType::tryFrom($type);
        return $e ? $this->name() . ': ' . $e->label() : '';
    }
}
