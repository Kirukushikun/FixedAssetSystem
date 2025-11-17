<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Exports\EmployeesExport;
use App\Imports\EmployeesImport;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    /**
     * Export employees data to Excel
     */
    public function export()
    {
        return Excel::download(
            new EmployeesExport(), 
            'FIXED ASSET System - Employees.xlsx'
        );
    }
    
    public function import(Request $request){
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        $import = new EmployeesImport;
        Excel::import($import, $request->file('file'));

        session()->flash('notif', [
            'type' => 'success',
            'header' => 'Import Successful',
            'message' => "Import finished: {$import->createdCount} new rows, {$import->updatedCount} updated."
        ]);

        return back();
    }
}
