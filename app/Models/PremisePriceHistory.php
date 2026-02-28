<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Orchid\Screen\AsSource;

class PremisePriceHistory extends Model
{
    use AsSource;
    protected $table = 'premise_price_history';

    protected $fillable = [
        'premise_id',
        'old_price',
        'new_price',
        'changed_by',
        'changed_at',
    ];

    protected function casts(): array
    {
        return [
            'old_price' => 'float',
            'new_price' => 'float',
            'changed_at' => 'datetime',
        ];
    }

    public function premise(): BelongsTo
    {
        return $this->belongsTo(Premise::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
