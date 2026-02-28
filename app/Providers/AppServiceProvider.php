<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Building;
use App\Models\Complex;
use App\Models\Floor;
use App\Models\Premise;
use App\Models\Section;
use App\Observers\AuditObserver;
use App\Observers\CacheInvalidationObserver;
use App\Observers\PremiseObserver;
use App\Services\RealEstateCacheService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $cacheObserver = new CacheInvalidationObserver(app(RealEstateCacheService::class));

        Premise::observe(PremiseObserver::class);
        Premise::observe($cacheObserver);

        Complex::observe(AuditObserver::class);
        Complex::observe($cacheObserver);

        Building::observe(AuditObserver::class);
        Building::observe($cacheObserver);

        Section::observe(AuditObserver::class);
        Section::observe($cacheObserver);

        Floor::observe(AuditObserver::class);
        Floor::observe($cacheObserver);

        Premise::observe(AuditObserver::class);
    }
}
