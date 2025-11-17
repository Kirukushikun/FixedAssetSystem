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