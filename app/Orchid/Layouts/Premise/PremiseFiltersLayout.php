<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Premise;

use App\Orchid\Filters\Premise\AreaRangeFilter;
use App\Orchid\Filters\Premise\BuildingFilter;
use App\Orchid\Filters\Premise\ComplexFilter;
use App\Orchid\Filters\Premise\FloorFilter;
use App\Orchid\Filters\Premise\PriceRangeFilter;
use App\Orchid\Filters\Premise\RoomsFilter;
use App\Orchid\Filters\Premise\SectionFilter;
use App\Orchid\Filters\Premise\StatusFilter;
use App\Orchid\Filters\Premise\TypeFilter;
use Orchid\Screen\Layouts\Selection;

class PremiseFiltersLayout extends Selection
{
    public function filters(): array
    {
        return [
            ComplexFilter::class,
            BuildingFilter::class,
            SectionFilter::class,
            FloorFilter::class,
            TypeFilter::class,
            StatusFilter::class,
            RoomsFilter::class,
            PriceRangeFilter::class,
            AreaRangeFilter::class,
        ];
    }
}
