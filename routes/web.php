<?php

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/assetmanagement', function () {
    return view('assetmanagement');
});

Route::get('/assetmanagement/{mode}', function (Request $request, $mode) {
    $targetID = null;
    $category_type = null;
    $category = null;
    $sub_category = null;

    if ($mode == 'create') {
        $category_type = $request->category_type;
        $category = $request->category;
        $sub_category = $request->sub_category;        
    } else {
        $targetID = $request->targetID;
    }

    return view('assetmanagement-view', compact('mode', 'targetID', 'category_type', 'category', 'sub_category'));
});

Route::get('/employees', function () {
    return view('employees');
});