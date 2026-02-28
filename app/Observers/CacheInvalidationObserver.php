<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Building;
use App\Models\Complex;
use App\Models\Floor;
use App\Models\Premise;
use App\Models\Section;
use App\Services\RealEstateCacheService;
use Illuminate\Database\Eloquent\Model;

class CacheInvalidationObserver
{
    public function __construct(
        private readonly RealEstateCacheService $cache
    ) {
    }

    public function saved(Model $model): void
    {
        $this->invalidateFor($model);
    }

    public function deleted(Model $model): void
    {
        $this->invalidateFor($model);
    }

    private function invalidateFor(Model $model): void
    {
        match ($model::class) {
            Complex::class => $this->invalidateComplex(),
            Building::class => $this->invalidateBuilding($model),
            Section::class => $this->invalidateSection($model),
            Floor::class => $this->cache->invalidateDashboard(),
            Premise::class => $this->invalidatePremise(),
            default => null,
        };
    }

    private function invalidateComplex(): void
    {
        $this->cache->invalidateComplexes();
        $this->cache->invalidateDashboard();
    }

    private function invalidateBuilding(Building $model): void
    {
        $this->cache->invalidateBuildings($model->complex_id);
        $this->cache->invalidateComplexes();
        $this->cache->invalidateDashboard();
    }

    private function invalidateSection(Section $model): void
    {
        $this->cache->invalidateSections($model->building_id);
        $this->cache->invalidateBuildings($model->building_id);
        $this->cache->invalidateDashboard();
    }

    private function invalidatePremise(): void
    {
        $this->cache->invalidatePremises();
        $this->cache->invalidateDashboard();
    }
}
