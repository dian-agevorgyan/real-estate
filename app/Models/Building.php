<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Orchid\Screen\AsSource;

class Building extends Model
{
    use AsSource, HasFactory;

    protected $fillable = [
        'complex_id',
        'name',
        'number',
        'floors_count',
        'built_year',
    ];

    protected function casts(): array
    {
        return [
            'floors_count' => 'integer',
            'built_year' => 'integer',
        ];
    }

    public function complex(): BelongsTo
    {
        return $this->belongsTo(Complex::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }
}
