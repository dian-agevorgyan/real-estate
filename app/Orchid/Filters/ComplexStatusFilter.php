<?php

declare(strict_types=1);

namespace App\Orchid\Filters;

use App\Enums\ComplexStatus;
use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\Select;

class ComplexStatusFilter extends Filter
{
    public function name(): string
    {
        return 'Статус';
    }

    public function parameters(): array
    {
        return ['status'];
    }

    public function run(Builder $builder): Builder
    {
        $status = $this->request->get('status');
        if ($status === null || $status === '') {
            return $builder;
        }
        return $builder->where('status', $status);
    }

    public function display(): array
    {
        return [
            Select::make('status')
                ->options(ComplexStatus::options())
                ->empty()
                ->value($this->request->get('status'))
                ->title($this->name()),
        ];
    }

    public function value(): string
    {
        $status = $this->request->get('status');
        if (!$status) {
            return '';
        }
        $e = ComplexStatus::tryFrom($status);
        return $e ? $this->name() . ': ' . $e->label() : '';
    }
}
