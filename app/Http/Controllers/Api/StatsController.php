<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Complex;
use App\Models\Premise;
use App\Services\RealEstateCacheService;
use Illuminate\Http\JsonResponse;

class StatsController extends Controller
{
    public function __construct(
        private readonly RealEstateCacheService $cache
    ) {
    }

    public function index(): JsonResponse
    {
        $stats = $this->cache->getDashboardStats();

        $byComplex = Complex::withCount('buildings')
            ->get()
            ->map(function ($c) {
                $premisesCount = Premise::byComplex($c->id)->count();
                $availableCount = Premise::byComplex($c->id)->byStatus('available')->count();
                return [
                    'id' => $c->id,
                    'name' => $c->name,
                    'status' => $c->status->value,
                    'buildings_count' => $c->buildings_count,
                    'premises_count' => $premisesCount,
                    'available_count' => $availableCount,
                ];
            });

        return response()->json([
            'data' => [
                'overview' => $stats,
                'by_complex' => $byComplex,
            ],
        ]);
    }
}
