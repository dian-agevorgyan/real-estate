<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Premise;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PremiseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Premise::with(['floor.section.building.complex'])
            ->when($request->filled('complex_id'), fn ($q) => $q->byComplex((int) $request->complex_id))
            ->when($request->filled('building_id'), fn ($q) => $q->byBuilding((int) $request->building_id))
            ->when($request->filled('section_id'), fn ($q) => $q->bySection((int) $request->section_id))
            ->when($request->filled('floor_id'), fn ($q) => $q->byFloor((int) $request->floor_id))
            ->when($request->filled('type'), fn ($q) => $q->byType($request->type))
            ->when($request->filled('status'), fn ($q) => $q->byStatus($request->status))
            ->when($request->filled('rooms_min'), fn ($q) => $q->roomsBetween((int) $request->rooms_min, $request->filled('rooms_max') ? (int) $request->rooms_max : null))
            ->when($request->filled('rooms_max') && !$request->filled('rooms_min'), fn ($q) => $q->roomsBetween(null, (int) $request->rooms_max))
            ->when($request->filled('price_min'), fn ($q) => $q->priceBetween((float) $request->price_min, $request->filled('price_max') ? (float) $request->price_max : null))
            ->when($request->filled('price_max') && !$request->filled('price_min'), fn ($q) => $q->priceBetween(null, (float) $request->price_max))
            ->when($request->filled('area_min'), fn ($q) => $q->areaBetween((float) $request->area_min, $request->filled('area_max') ? (float) $request->area_max : null))
            ->when($request->filled('area_max') && !$request->filled('area_min'), fn ($q) => $q->areaBetween(null, (float) $request->area_max))
            ->orderBy('id');

        $perPage = min((int) ($request->get('per_page', 15)), 100);
        $premises = $query->paginate($perPage);

        return response()->json([
            'data' => $premises->through(fn (Premise $p) => $this->formatPremise($p)),
            'meta' => [
                'current_page' => $premises->currentPage(),
                'last_page' => $premises->lastPage(),
                'per_page' => $premises->perPage(),
                'total' => $premises->total(),
            ],
        ]);
    }

    public function show(Premise $premise): JsonResponse
    {
        $premise->load(['floor.section.building.complex']);

        return response()->json([
            'data' => $this->formatPremise($premise, true),
        ]);
    }

    private function formatPremise(Premise $p, bool $detail = false): array
    {
        $data = [
            'id' => $p->id,
            'apartment_number' => $p->apartment_number,
            'type' => $p->type->value,
            'type_label' => $p->type->label(),
            'status' => $p->status->value,
            'status_label' => $p->status->label(),
            'rooms' => $p->rooms,
            'area_total' => $p->area_total,
            'area_living' => $p->area_living,
            'area_kitchen' => $p->area_kitchen,
            'price_base' => $p->price_base,
            'price_discount' => $p->price_discount,
            'price_per_m2' => $p->price_per_m2,
            'final_price' => $p->final_price,
            'floor_number' => $p->floor_number,
            'complex' => $p->floor?->section?->building?->complex ? [
                'id' => $p->floor->section->building->complex->id,
                'name' => $p->floor->section->building->complex->name,
                'address' => $p->floor->section->building->complex->address,
            ] : null,
        ];

        if ($detail) {
            $layoutAttachments = $p->attachments('premise_layout');
            $data['layout_image'] = $layoutAttachments->first()?->url;
            $data['gallery'] = $p->attachments('premise_gallery')->map(fn ($a) => $a->url)->filter()->values()->all();
            $data['extras'] = $p->extras ?? [];
            $data['floor'] = $p->floor ? [
                'id' => $p->floor->id,
                'number' => $p->floor->number,
            ] : null;
            $data['building'] = $p->floor?->section?->building ? [
                'id' => $p->floor->section->building->id,
                'name' => $p->floor->section->building->name,
            ] : null;
        }

        return $data;
    }
}
