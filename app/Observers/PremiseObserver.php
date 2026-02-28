<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Premise;
use App\Models\PremisePriceHistory;
use App\Models\PremiseStatusHistory;
use Illuminate\Support\Facades\Auth;

class PremiseObserver
{
    public function updating(Premise $premise): void
    {
        $this->logStatusChange($premise);
        $this->logPriceChange($premise);
    }

    private function logStatusChange(Premise $premise): void
    {
        if (!$premise->isDirty('status')) {
            return;
        }

        $newStatus = $premise->status;
        $newStatusValue = $newStatus instanceof \BackedEnum ? $newStatus->value : (string) $newStatus;

        PremiseStatusHistory::create([
            'premise_id' => $premise->id,
            'old_status' => $premise->getOriginal('status'),
            'new_status' => $newStatusValue,
            'changed_by' => Auth::id(),
            'changed_at' => now(),
        ]);
    }

    private function logPriceChange(Premise $premise): void
    {
        if (!$premise->isDirty('price_base') && !$premise->isDirty('price_discount')) {
            return;
        }

        $oldPrice = $premise->getOriginal('price_base');
        $newPrice = $premise->price_base;
        $oldDiscount = $premise->getOriginal('price_discount') ?? 0;
        $newDiscount = $premise->price_discount ?? 0;

        $oldFinal = $oldPrice !== null ? round((float) $oldPrice - (float) $oldDiscount, 2) : null;
        $newFinal = $newPrice !== null ? round((float) $newPrice - (float) $newDiscount, 2) : null;

        if ($oldFinal === $newFinal) {
            return;
        }

        PremisePriceHistory::create([
            'premise_id' => $premise->id,
            'old_price' => $oldFinal,
            'new_price' => $newFinal,
            'changed_by' => Auth::id(),
            'changed_at' => now(),
        ]);
    }
}
