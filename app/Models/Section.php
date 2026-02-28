<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Orchid\Screen\AsSource;

class Section extends Model
{
    use AsSource, HasFactory;

    protected $fillable = [
        'building_id',
        'name',
        'number',
        'floors_count_in_section',
    ];

    protected function casts(): array
    {
        return [
            'floors_count_in_section' => 'integer',
        ];
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function floors(): HasMany
    {
        return $this->hasMany(Floor::class);
    }
}
