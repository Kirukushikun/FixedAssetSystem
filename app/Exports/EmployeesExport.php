<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmployeesExport implements FromCollection, WithHeadings
{
    /**
     * Return the collection of data to export
     */
    public function collection()
    {
        return Employee::select(
            'employee_id',
            'employee_name',
            'position',
            'farm',
            'department',
        )->get();
    }

    /**
     * Define the column headings
     */
    public function headings(): array
    {
        return [
            'Employee ID',
            'Employee Name',
            'Position',
            'Farm',
            'Department'
        ];
    }
}