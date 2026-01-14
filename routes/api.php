<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;

Route::prefix('v1')->middleware(['api.key', 'throttle:60,1'])->group(function () {
    // Get all assets
    Route::get('/assets', [AssetController::class, 'index']);
    
    // Search assets
    Route::get('/assets/search', [AssetController::class, 'search']);
    
    // Get single asset by ID
    Route::get('/assets/{id}', [AssetController::class, 'show']);
});