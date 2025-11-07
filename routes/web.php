<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
});

Route::get('/assetmanagement', function () {
    return view('assetmanagement');
});