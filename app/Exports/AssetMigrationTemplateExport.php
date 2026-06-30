<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Blank migration template — human-friendly fill-in form for entering old asset data.
 * Has one highlighted sample row showing valid values for each column.
 * Person filling in the file should delete the sample row before importing.
 */
class AssetMigrationTemplateExport implements
    FromCollection,
    WithHeadings,
    WithStyles,
    ShouldAutoSize
{
    public function collection(): Collection
    {
        // One sample row so the person knows exactly what to put in each column
        return collect([
            [
                'IT',               // Category Type
                'Computer',         // Category (write the name, e.g. Computer, Monitor, Printer)
                'Laptop',           // Sub Category
                'Dell',             // Brand
                'Latitude 5520',    // Model
                'SN-EXAMPLE-001',   // Serial Number (leave blank if unknown)
                'Available',        // Status: Available / Issued / Disposed / Lost (leave blank = Available)
                'Good',             // Condition: Good / Defective / Repair / Replace (leave blank = Good)
                '2022-01-15',       // Acquisition Date (YYYY-MM-DD or MM/DD/YYYY, leave blank if unknown)
                '45000',            // Item Cost (numbers only, no currency symbols or commas)
                'Juan Dela Cruz',   // Assigned To (full name of employee, leave blank if unassigned)
                'BFC',              // Farm: BFC / BDL / PFC / RH / BBGC / HATCHERY
                'IT Department',    // Department (leave blank if unknown)
                'SAMPLE ROW — DELETE THIS ROW BEFORE IMPORTING', // Remarks
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'Category Type',
            'Category',
            'Sub Category',
            'Brand',
            'Model',
            'Serial Number',
            'Status',
            'Condition',
            'Acquisition Date',
            'Item Cost',
            'Assigned To',
            'Farm',
            'Department',
            'Remarks',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [ // header row — light teal
                'font' => ['bold' => true, 'color' => ['rgb' => '1A535C']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'CBF0EE']],
            ],
            2 => [ // sample row — amber/yellow warning colour
                'font' => ['italic' => true, 'color' => ['rgb' => '92400E']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF3C7']],
            ],
        ];
    }
}
