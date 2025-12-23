<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;

Route::prefix('v1')->middleware('api.key')->group(function () {
    // Get all assets
    Route::get('/assets', [AssetController::class, 'index']);
    
    // Get single asset by ID
    Route::get('/assets/{id}', [AssetController::class, 'show']);
});
