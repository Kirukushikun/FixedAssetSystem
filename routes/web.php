<?php

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/assetmanagement', function () {
    $assets = Asset::where('is_deleted', false)
        ->orWhere('is_archived', false)
        ->get();
    return view('assetmanagement', compact('assets'));
});

Route::get('/assetmanagement/create', function (Request $request) {
    $category_type = $request->category_type;
    $category = $request->category;
    $sub_category = $request->sub_category;
    return view('assetmanagement-create', compact('category_type', 'category', 'sub_category'));
});