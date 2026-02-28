<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ComplexStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Complex extends Model
{
    use AsSource, Attachable, Filterable, HasFactory;

    protected $fillable = [
        'name',
        'description',
        'address',
        'status',
        'lat',
        'lng',
    ];

    protected function casts(): array
    {
        return [
            'status' => ComplexStatus::class,
            'lat' => 'float',
            'lng' => 'float',
        ];
    }

    /**
     * Gallery images via Orchid Attachments (group: complex_gallery).
     * Overrides DB column for form binding; attachments are source of truth.
     */
    public function getGalleryAttribute(): array
    {
        return $this->attachments('complex_gallery')->pluck('id')->map(fn ($id) => (int) $id)->all();
    }

    public function buildings(): HasMany
    {
        return $this->hasMany(Building::class);
    }

    public function scopeStatus(Builder $query, ComplexStatus|string $status): Builder
    {
        $value = $status instanceof ComplexStatus ? $status->value : $status;
        return $query->where('status', $value);
    }
}
