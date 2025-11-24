```
composer require maatwebsite/excel
```

```
php artisan make:export EmployeesExport --model=Employee

php artisan make:import EmployeesImport --model=Employee
```

```
<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmployeesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Employee::select(
            'company_id',
            'full_name',
            'position',
            'farm',
            'department'
        )->get();
    }

    public function headings(): array
    {
        return [
            'Employee ID',
            'Employee Full Name',
            'Position',
            'Farm',
            'Department'
        ];
    }
}

```


```
<?php

namespace App\Imports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;


class EmployeesImport implements ToModel, WithHeadingRow, WithCalculatedFormulas
{   
    public $createdCount = 0;
    public $updatedCount = 0;

    public function model(array $row)
    {
        // Check if record already exists
        $existing = Employee::where('company_id', $row['employee_id'])
            ->where('full_name', $row['employee_full_name'])
            ->where('position', $row['position'])
            ->first();

        if ($existing) {
            return null; // skip duplicates
        }

        // Create new record
        $model = Employee::create([
            'company_id'     => $row['employee_id'],
            'full_name'      => $row['employee_full_name'],
            'position'       => $row['position'],
            'farm'           => $row['farm'],
            'department'     => $row['department'],
        ]);

        $this->createdCount++;

        return $model;
    }


}

```

```
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Exports\EmployeesExport;
use App\Imports\EmployeesImport;
use Maatwebsite\Excel\Facades\Excel;

class FixedController extends Controller
{
    public function export()
    {
        return Excel::download(new EmployeesExport(), 'FIXED ASSET System - Employees.xlsx');
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

```

```
    <form id="import-form" action="/import" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" id="import-file" name="file" accept=".xlsx,.xls,.csv" class="hidden" required>
        <div id="import-button" class="bg-blue-600 text-white hover:bg-blue-700 rounded-md cursor-pointer px-3 py-2">
            <i class="fa-solid fa-file-import"></i>
            import
        </div>
    </form>

    <script>
        document.getElementById('import-button').addEventListener('click', () => {
            document.getElementById('import-file').click();
        });

        document.getElementById('import-file').addEventListener('change', () => {
            document.getElementById('import-form').submit();
        });
    </script>

    <div class="bg-blue-600 text-white hover:bg-blue-700 rounded-md cursor-pointer px-3 py-2" onclick="window.location.href='/export'">
        <i class="fa-solid fa-file-export"></i>
        Export
    </div>

```

```
    use App\Http\Controllers\EmployeeController;
    
    Route::get('/employees/export', [EmployeeController::class, 'export'])->name('employees.export');
    Route::post('/employees/import', [EmployeeController::class, 'import'])->name('employees.import');
```

```
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accountability Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 py-12 px-4">
    <div class="max-w-4xl mx-auto bg-white shadow-2xl" style="box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06), 0 0 0 1px rgba(0, 0, 0, 0.05);">
        <!-- Header Section -->
        <div class="px-12 pt-10 pb-6 border-b-2 border-gray-900">
            <div class="flex items-center justify-center mb-6">
                <div class="border-2 border-gray-900 px-8 py-3">
                    <span class="text-3xl font-bold text-gray-900" style="font-family: 'Times New Roman', serif;">BGC</span>
                </div>
            </div>
            <h1 class="text-center text-2xl font-bold text-gray-900 tracking-widest" style="font-family: 'Times New Roman', serif;">ACCOUNTABILITY FORM</h1>
        </div>

        <!-- Content Section -->
        <div class="px-12 py-8">
            <!-- Info Grid -->
            <div class="mb-8 text-sm space-y-1">
                <div class="flex">
                    <span class="font-bold text-gray-900 w-20">Date:</span>
                    <span class="text-gray-900">11/17/2025</span>
                </div>
                <div class="flex">
                    <span class="font-bold text-gray-900 w-20">To:</span>
                    <span class="text-gray-900">DIANNE MANLICLIC</span>
                </div>
                <div class="flex">
                    <span class="font-bold text-gray-900 w-20">From:</span>
                    <span class="text-gray-900">N/A</span>
                </div>
            </div>

            <!-- Acknowledgment Text -->
            <div class="mb-8">
                <p class="text-sm text-gray-900 leading-relaxed">
                    I, <span class="font-bold">DIANNE MANLICLIC</span>, acknowledge the receipt of the item/s as listed below, today, <span class="font-bold">11/17/2025</span>.
                </p>
            </div>

            <!-- Items Table -->
            <div class="mb-8">
                <table class="w-full border-2 border-gray-900">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-3 text-left text-sm font-bold text-gray-900 uppercase tracking-wide border-b-2 border-gray-900 border-r border-gray-900">Item</th>
                            <th class="px-4 py-3 text-center text-sm font-bold text-gray-900 uppercase tracking-wide border-b-2 border-gray-900 w-24">Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-900">Lenovo IG-4231</td>
                            <td class="px-4 py-3 text-sm text-center text-gray-900 font-semibold">1</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Terms Section -->
            <div class="mb-10 space-y-5 text-sm text-gray-900 leading-relaxed">
                <p class="text-justify">
                    I understand that the item/s has/have been assigned to me as it is a requirement for my job in
                    <span class="font-bold underline">FONTE FRESCA, FONTE FRESCA DIVISION/DEPARTMENT, FONTE FRESCA SECTION</span>, as
                    <span class="font-bold underline">ACCOUNTING ASSISTANT</span>. I recognize that these item/s are private properties 
                    of the Company and are only assigned to me during my employment or when no longer necessary
                    for my use due to promotion, transfer, or related situations.
                </p>

                <p class="text-justify">
                    I am responsible for the safeguarding of the listed property/asset. In case of loss or damage,
                    the replacement cost will be charged to me through salary deduction.
                </p>
            </div>

            <!-- Signature Section -->
            <div class="space-y-6">
                <!-- Employee Signature -->
                <div class="flex flex-col sm:flex-row sm:items-end gap-4 pb-4 border-b border-gray-200">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Signature:</label>
                        <div class="border-b-2 border-gray-400 pb-1 min-h-[40px]"></div>
                    </div>
                    <div class="sm:w-40">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date:</label>
                        <div class="text-sm font-semibold text-gray-900">11/17/2025</div>
                    </div>
                </div>

                <!-- Noted By -->
                <div class="flex flex-col sm:flex-row sm:items-end gap-4 pb-4 border-b border-gray-200">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Noted By:</label>
                        <div class="border-b-2 border-gray-400 pb-1 min-h-[40px]"></div>
                    </div>
                    <div class="sm:w-40">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date:</label>
                        <div class="text-sm font-semibold text-gray-900">11/17/2025</div>
                    </div>
                </div>

                <!-- Issued By -->
                <div class="flex flex-col sm:flex-row sm:items-end gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Issued By:</label>
                        <div class="border-b-2 border-blue-600 pb-1 min-h-[40px] flex items-end">
                            <span class="font-bold text-gray-900">MARK LESTER DELA CRUZ</span>
                        </div>
                    </div>
                    <div class="sm:w-40">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date:</label>
                        <div class="text-sm font-semibold text-gray-900">11/17/2025</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-100 px-8 py-4 text-center">
            <p class="text-xs text-gray-600">This is an official company document. Please keep for your records.</p>
        </div>
    </div>
</body>
</html>
```