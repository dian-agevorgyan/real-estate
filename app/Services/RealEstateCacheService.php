<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Building;
use App\Models\Complex;
use App\Models\Premise;
use App\Models\Section;
use Illuminate\Support\Facades\Cache;

class RealEstateCacheService
{
    private const TTL_COMPLEXES = 1800; // 30 min
    private const TTL_BUILDINGS = 1800;
    private const TTL_SECTIONS = 1800;
    private const TTL_DASHBOARD = 900;  // 15 min
    private const KEY_PREFIX = 'real_estate:';

    private function supportsTags(): bool
    {
        $store = Cache::getStore();
        return method_exists($store, 'supportsTags') && $store->supportsTags();
    }

    private function key(string $name): string
    {
        return self::KEY_PREFIX . $name;
    }

    public function getComplexesList(): \Illuminate\Database\Eloquent\Collection
    {
        $key = $this->key('complexes:list');
        return $this->supportsTags()
            ? Cache::tags(['real_estate:complexes'])->remember($key, self::TTL_COMPLEXES, fn () => Complex::orderBy('name')->get())
            : Cache::remember($key, self::TTL_COMPLEXES, fn () => Complex::orderBy('name')->get());
    }

    public function getBuildingsList(int $complexId): \Illuminate\Database\Eloquent\Collection
    {
        $key = $this->key("buildings:list:{$complexId}");
        return $this->supportsTags()
            ? Cache::tags(['real_estate:buildings'])->remember($key, self::TTL_BUILDINGS, fn () => Building::where('complex_id', $complexId)->orderBy('name')->get())
            : Cache::remember($key, self::TTL_BUILDINGS, fn () => Building::where('complex_id', $complexId)->orderBy('name')->get());
    }

    public function getSectionsList(int $buildingId): \Illuminate\Database\Eloquent\Collection
    {
        $key = $this->key("sections:list:{$buildingId}");
        return $this->supportsTags()
            ? Cache::tags(['real_estate:sections'])->remember($key, self::TTL_SECTIONS, fn () => Section::where('building_id', $buildingId)->orderBy('name')->get())
            : Cache::remember($key, self::TTL_SECTIONS, fn () => Section::where('building_id', $buildingId)->orderBy('name')->get());
    }

    public function getDashboardStats(): array
    {
        $key = $this->key('dashboard:stats');
        $callback = fn () => [
            'total_premises' => Premise::count(),
            'available' => Premise::where('status', 'available')->count(),
            'sold' => Premise::where('status', 'sold')->count(),
            'reserved' => Premise::where('status', 'reserved')->count(),
            'total_complexes' => Complex::count(),
        ];
        return $this->supportsTags()
            ? Cache::tags(['real_estate:dashboard'])->remember($key, self::TTL_DASHBOARD, $callback)
            : Cache::remember($key, self::TTL_DASHBOARD, $callback);
    }

    public function invalidateComplexes(): void
    {
        if ($this->supportsTags()) {
            Cache::tags(['real_estate:complexes'])->flush();
        } else {
            Cache::forget($this->key('complexes:list'));
        }
    }

    public function invalidateBuildings(?int $complexId = null): void
    {
        if ($this->supportsTags()) {
            $complexId !== null
                ? Cache::tags(['real_estate:buildings'])->forget($this->key("buildings:list:{$complexId}"))
                : Cache::tags(['real_estate:buildings'])->flush();
        } else {
            if ($complexId !== null) {
                Cache::forget($this->key("buildings:list:{$complexId}"));
            } else {
                foreach (Complex::pluck('id') as $id) {
                    Cache::forget($this->key("buildings:list:{$id}"));
                }
            }
        }
    }

    public function invalidateSections(?int $buildingId = null): void
    {
        if ($this->supportsTags()) {
            $buildingId !== null
                ? Cache::tags(['real_estate:sections'])->forget($this->key("sections:list:{$buildingId}"))
                : Cache::tags(['real_estate:sections'])->flush();
        } else {
            if ($buildingId !== null) {
                Cache::forget($this->key("sections:list:{$buildingId}"));
            } else {
                foreach (Building::pluck('id') as $id) {
                    Cache::forget($this->key("sections:list:{$id}"));
                }
            }
        }
    }

    public function invalidatePremises(): void
    {
        if ($this->supportsTags()) {
            Cache::tags(['real_estate:premises'])->flush();
        }
        // Без тегов — premises не кешируем отдельно, только dashboard
    }

    public function invalidateDashboard(): void
    {
        if ($this->supportsTags()) {
            Cache::tags(['real_estate:dashboard'])->flush();
        } else {
            Cache::forget($this->key('dashboard:stats'));
        }
    }

    public function invalidateAll(): void
    {
        if ($this->supportsTags()) {
            Cache::tags([
                'real_estate:complexes',
                'real_estate:buildings',
                'real_estate:sections',
                'real_estate:premises',
                'real_estate:dashboard',
            ])->flush();
        } else {
            $this->invalidateComplexes();
            $this->invalidateBuildings();
            $this->invalidateSections();
            $this->invalidateDashboard();
        }
    }
}
