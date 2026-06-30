<?php

use App\Models\User;
use App\Models\Asset;
use App\Models\Employee;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LoginController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\EmployeeDocumentController;

Route::get('/login', fn() => view('auth.login'))->name('login');

Route::post('/login', [LoginController::class, 'postLogin'])->name('login.post');

Route::get('/logout', [LoginController::class, 'logout']);


Route::middleware('auth')->group(function () {
    Route::redirect('/', '/dashboard');

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware('permission:dashboard.view')->name('dashboard');

    Route::get('/assetmanagement', function () {
        return view('assetmanagement');
    })->middleware('permission:assets.view');

    Route::get('/assetmanagement/qr', function () {
        return view('qr-management');
    })->middleware('permission:assets.qr')->name('assets.qr');
    Route::get('/assetmanagement/qr/print', [AssetController::class, 'printQr'])->middleware('permission:assets.qr')->name('assets.qr.print');

    Route::get('/assetmanagement/{mode}', function (Request $request, $mode) {
        $targetID = null;
        $category_type = null;
        $category = null;
        $sub_category = null;

        if ($mode == 'create') {
            $category_type = $request->category_type;
            $category = $request->category;
            $sub_category = $request->sub_category;
        } elseif ($mode == 'edit') {
            $targetID = decrypt($request->targetID);
        } elseif ($mode == 'view') {
            $targetID = decrypt($request->targetID);
        } elseif ($mode == 'audit') {
            $targetID = decrypt($request->targetID);
        } else {
            abort(404);
        }

        return view('assetmanagement-view', compact('mode', 'targetID', 'category_type', 'category', 'sub_category'));
    })->middleware('permission:assets.view,assets.audit,assets.create,assets.update');

    Route::get('/employees', function () {
        return view('employees');
    })->middleware('permission:employees.view');

    Route::get('/employees/view', function (Request $request) {
        $targetID = $request->targetID;
        return view('employees-view', compact('targetID'));
    })->middleware('permission:employees.view');

    Route::get('/systemrecords', function () {
        return view('systemrecords');
    })->middleware('permission:audit.view,activity.view,users.view');

    Route::get('/settings', function () {
        return view('settings');
    })->middleware('permission:settings.view');

    Route::get('/sme-workspace', function () {
        return view('sme-workspace');
    })->middleware('permission:sme.view')->name('sme.workspace');

    Route::get('/transfer-workspace', function () {
        return view('transfer-workspace');
    })->middleware('permission:transfer.view')->name('transfer.workspace');

    Route::get('/it-analytics', function () {
        return view('it-analytics');
    })->middleware('permission:analytics.view')->name('it.analytics');

    Route::get('/disposal-workspace', function () {
        return view('disposal-workspace');
    })->middleware('permission:disposal.view')->name('disposal.workspace');

    Route::get('/disposal-form/{asset}', function (Asset $asset) {
        $request = $asset->latestDisposalRequest;

        abort_unless($request, 404);

        return view('disposal-form', compact('asset', 'request'));
    })->middleware('permission:disposal.form.view')->name('assets.disposal-form');

    Route::get('/accountability-form', [EmployeeDocumentController::class, 'accountability'])->middleware('permission:forms.accountability')->name('employees.forms.accountability');
    Route::get('/transfer-form', [EmployeeDocumentController::class, 'transfer'])->middleware('permission:forms.transfer')->name('employees.forms.transfer');
    Route::get('/employees/forms', [EmployeeDocumentController::class, 'library'])->middleware('permission:forms.view')->name('employees.forms.library');
    Route::get('/employees/forms/{form}', [EmployeeDocumentController::class, 'show'])->middleware('permission:forms.view')->name('employees.forms.show');

    Route::get('/employees/export', [EmployeeController::class, 'export'])->middleware('permission:employees.export')->name('employees.export');
    Route::post('/employees/import', [EmployeeController::class, 'import'])->middleware('permission:employees.import')->name('employees.import');

    Route::get('/assets/export', [AssetController::class, 'export'])->middleware('permission:assets.export')->name('assets.export');
    Route::post('/assets/import', [AssetController::class, 'import'])->middleware('permission:assets.import')->name('assets.import');
    Route::get('/assets/migration-template', [AssetController::class, 'downloadMigrationTemplate'])->middleware('permission:assets.import')->name('assets.migration-template');
    Route::post('/assets/migration-import', [AssetController::class, 'migrationImport'])->middleware('permission:assets.import')->name('assets.migration-import');

    Route::get('/assets/export-audit-log', [AssetController::class, 'exportAuditLog'])->middleware('permission:audit.export')->name('assets.export.audit-log');
});


Route::get('/viewasset/{targetID}', function (Request $request, $targetID) {
    $targetID = decrypt($targetID);
    $asset = Asset::with('latestDisposalRequest')->find($targetID);
    $categoryDetails = \App\Models\Category::where('code', $asset->category)->first();
    return view('view-asset', compact('asset', 'categoryDetails'));
})->middleware(['auth', 'permission:assets.view']);