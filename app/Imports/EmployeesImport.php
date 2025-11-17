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
        $existing = Employee::where('employee_id', $row['employee_id'])
            ->where('employee_name', $row['employee_name'])
            ->where('position', $row['position'])
            ->first();

        if ($existing) {
            return null; // skip duplicates
        }

        // Create new record
        $model = Employee::create([
            'employee_id'     => $row['employee_id'],
            'employee_name'      => $row['employee_name'],
            'position'       => $row['position'],
            'farm'           => $row['farm'],
            'department'     => $row['department'],
        ]);

        $this->createdCount++;

        return $model;
    }


}
