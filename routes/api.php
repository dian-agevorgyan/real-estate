<?php

declare(strict_types=1);

use App\Http\Controllers\Api\PremiseController;
use App\Http\Controllers\Api\StatsController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('premises', [PremiseController::class, 'index']);
    Route::get('premises/{premise}', [PremiseController::class, 'show']);
    Route::get('stats', [StatsController::class, 'index']);
});
