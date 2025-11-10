<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/assetmanagement', function () {
    return view('assetmanagement');
});

Route::get('/assetmanagement/create', function (Request $request) {
    $category_type = $request->category_type;
    $category = $request->category;
    return view('assetmanagement-create', compact('category_type', 'category'));
});