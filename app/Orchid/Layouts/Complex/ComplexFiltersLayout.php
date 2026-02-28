<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Complex;

use App\Orchid\Filters\ComplexStatusFilter;
use Orchid\Screen\Layouts\Selection;

class ComplexFiltersLayout extends Selection
{
    public function filters(): array
    {
        return [
            ComplexStatusFilter::class,
        ];
    }
}
