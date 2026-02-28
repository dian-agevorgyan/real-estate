<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Dashboard;

use Orchid\Screen\Layouts\Chart;

class ComplexStatusChartLayout extends Chart
{
    protected $type = self::TYPE_PIE;

    protected $height = 300;

    protected $title = 'Комплексы по статусам';

    protected $target = 'complexChart';
}
