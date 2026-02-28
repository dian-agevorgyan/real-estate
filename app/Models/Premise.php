<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PremiseStatus;
use App\Enums\PremiseType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Premise extends Model
{
    use AsSource, Attachable, Filterable, HasFactory;

    protected $fillable = [
        'floor_id',
        'apartment_number',
        'type',
        'rooms',
        'area_total',
        'area_living',
        'area_kitchen',
        'status',
        'price_base',
        'price_discount',
        'price_per_m2',
        'floor_number',
        'extras',
    ];

    protected function casts(): array
    {
        return [
            'type' => PremiseType::class,
            'status' => PremiseStatus::class,
            'rooms' => 'integer',
            'area_total' => 'float',
            'area_living' => 'float',
            'area_kitchen' => 'float',
            'price_base' => 'float',
            'price_discount' => 'float',
            'price_per_m2' => 'float',
            'floor_number' => 'integer',
            'extras' => 'array',
        ];
    }

    /**
     * Layout image via Orchid Attachments (group: premise_layout). Single file.
     */
    public function getLayoutImageAttribute(): array
    {
        return $this->attachments('premise_layout')->pluck('id')->map(fn ($id) => (int) $id)->all();
    }

    /**
     * Gallery images via Orchid Attachments (group: premise_gallery).
     */
    public function getGalleryAttribute(): array
    {
        return $this->attachments('premise_gallery')->pluck('id')->map(fn ($id) => (int) $id)->all();
    }

    public function floor(): BelongsTo
    {
        return $this->belongsTo(Floor::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(PremiseStatusHistory::class);
    }

    public function priceHistory(): HasMany
    {
        return $this->hasMany(PremisePriceHistory::class);
    }

    public function scopeByComplex(Builder $query, int $complexId): Builder
    {
        return $query->whereHas('floor.section.building', fn (Builder $q) => $q->where('complex_id', $complexId));
    }

    public function scopeByBuilding(Builder $query, int $buildingId): Builder
    {
        return $query->whereHas('floor.section', fn (Builder $q) => $q->where('building_id', $buildingId));
    }

    public function scopeBySection(Builder $query, int $sectionId): Builder
    {
        return $query->whereHas('floor', fn (Builder $q) => $q->where('section_id', $sectionId));
    }

    public function scopeByFloor(Builder $query, int $floorId): Builder
    {
        return $query->where('floor_id', $floorId);
    }

    public function scopeByType(Builder $query, PremiseType|string $type): Builder
    {
        $value = $type instanceof PremiseType ? $type->value : $type;
        return $query->where('type', $value);
    }

    public function scopeByStatus(Builder $query, PremiseStatus|string $status): Builder
    {
        $value = $status instanceof PremiseStatus ? $status->value : $status;
        return $query->where('status', $value);
    }

    public function scopeRoomsBetween(Builder $query, ?int $min, ?int $max): Builder
    {
        if ($min !== null) {
            $query->where('rooms', '>=', $min);
        }
        if ($max !== null) {
            $query->where('rooms', '<=', $max);
        }
        return $query;
    }

    public function scopePriceBetween(Builder $query, ?float $min, ?float $max): Builder
    {
        $priceCol = 'price_base';
        if ($min !== null) {
            $query->where($priceCol, '>=', $min);
        }
        if ($max !== null) {
            $query->where($priceCol, '<=', $max);
        }
        return $query;
    }

    public function scopeAreaBetween(Builder $query, ?float $min, ?float $max): Builder
    {
        if ($min !== null) {
            $query->where('area_total', '>=', $min);
        }
        if ($max !== null) {
            $query->where('area_total', '<=', $max);
        }
        return $query;
    }

    public function getFinalPriceAttribute(): ?float
    {
        if ($this->price_base === null) {
            return null;
        }
        $discount = $this->price_discount ?? 0;
        return round($this->price_base - $discount, 2);
    }
}
