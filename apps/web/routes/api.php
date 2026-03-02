<?php

use App\Http\Controllers\Api\V1\PublicCampaignController;
use App\Http\Controllers\Api\V1\PublicDrawController;
use App\Http\Controllers\Api\V1\PublicStatsController;
use App\Http\Controllers\Api\V1\PublicWinnerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->middleware(['api.client'])->group(function (): void {
    Route::get('/campaign/current', [PublicCampaignController::class, 'current']);
    Route::get('/campaign/current/prizes', [PublicCampaignController::class, 'prizes']);
    Route::get('/draws/history', [PublicDrawController::class, 'history']);
    Route::get('/winners', [PublicWinnerController::class, 'index']);
    Route::get('/stats/public', PublicStatsController::class);
});
