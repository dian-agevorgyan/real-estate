<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditObserver
{
    private const TRACKED = [
        \App\Models\Complex::class,
        \App\Models\Building::class,
        \App\Models\Section::class,
        \App\Models\Floor::class,
        \App\Models\Premise::class,
    ];

    public function created(Model $model): void
    {
        $this->log($model, 'created', [], $model->getAttributes());
    }

    public function updated(Model $model): void
    {
        if (!in_array($model::class, self::TRACKED, true)) {
            return;
        }

        $changes = $model->getChanges();
        unset($changes['updated_at']);
        if (empty($changes)) {
            return;
        }

        $old = [];
        foreach (array_keys($changes) as $key) {
            $old[$key] = $model->getOriginal($key);
        }

        $this->log($model, 'updated', $old, $changes);
    }

    public function deleted(Model $model): void
    {
        $this->log($model, 'deleted', $model->getAttributes(), []);
    }

    private function log(Model $model, string $action, array $oldValues, array $newValues): void
    {
        if (!in_array($model::class, self::TRACKED, true)) {
            return;
        }

        AuditLog::create([
            'auditable_type' => $model->getMorphClass(),
            'auditable_id' => $model->getKey(),
            'user_id' => Auth::id(),
            'action' => $action,
            'old_values' => $oldValues ?: null,
            'new_values' => $newValues ?: null,
        ]);
    }
}
