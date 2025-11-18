<?php

use App\Models\Asset;
use App\Models\Employee;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LoginController;
use App\Http\Controllers\EmployeeController;

Route::get('/login', fn() => view('auth.login'))->name('login');

Route::post('/login', [LoginController::class, 'postLogin'])->name('login.post');

Route::get('/logout', [LoginController::class, 'logout']);


Route::middleware('auth')->group(function () {
    Route::redirect('/', '/dashboard');

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

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

    Route::get('/employees/view', function (Request $request) {
        $targetID = $request->targetID;
        return view('employees-view', compact('targetID'));
    });

    Route::get('/systemrecords', function () {
        return view('systemrecords');
    });

    Route::get('/settings', function () {
        return view('settings');
    });

    Route::get('/accountability-form', function () {
        return view('accountability-form');
    });

    Route::get('/employees/export', [EmployeeController::class, 'export'])->name('employees.export');
    Route::post('/employees/import', [EmployeeController::class, 'import'])->name('employees.import');
});




