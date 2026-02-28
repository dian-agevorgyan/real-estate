<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Orchid\Attachment\Attachable;
use Orchid\Screen\AsSource;

class Floor extends Model
{
    use AsSource, Attachable, HasFactory;

    protected $fillable = [
        'section_id',
        'number',
        'apartments_count',
    ];

    /**
     * Plan image via Orchid Attachments (group: floor_plan). Single file.
     * Overrides DB column for form binding.
     */
    public function getPlanImageAttribute(): array
    {
        return $this->attachments('floor_plan')->pluck('id')->map(fn ($id) => (int) $id)->all();
    }

    protected function casts(): array
    {
        return [
            'number' => 'integer',
            'apartments_count' => 'integer',
        ];
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function premises(): HasMany
    {
        return $this->hasMany(Premise::class);
    }
}
