<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Orchid\Screen\AsSource;

class PremiseStatusHistory extends Model
{
    use AsSource;
    protected $table = 'premise_status_history';

    protected $fillable = [
        'premise_id',
        'old_status',
        'new_status',
        'changed_by',
        'changed_at',
    ];

    protected function casts(): array
    {
        return [
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
