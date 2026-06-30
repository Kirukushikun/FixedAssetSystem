<?php

namespace App\Imports;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;

/**
 * Migration import — reads the human-friendly migration template.
 * Category is matched by name (e.g. "Computer") to the stored code.
 * Assigned To is matched by employee name; left null if not found.
 * Status and Condition default to Available / Good when blank.
 * Always creates new assets — never updates existing ones.
 * Duplicate check: by Serial Number if provided, otherwise by Brand + Model + Sub Category.
 */
class AssetMigrationImport implements
    OnEachRow,
    WithHeadingRow,
    WithCalculatedFormulas,
    WithChunkReading
{
    public int $createdCount = 0;
    public int $skippedCount = 0;

    private Collection $categoryMap; // 'Computer' => 'COMP'
    private Collection $employeeMap; // 'Juan Dela Cruz' => 42

    public function __construct()
    {
        $this->categoryMap = Category::pluck('code', 'name');
        $this->employeeMap = Employee::pluck('id', 'employee_name');
    }

    public function onRow(Row $row): void
    {
        $r = $row->toArray();

        // Skip blank or sample rows
        $brand = trim((string) ($r['brand'] ?? ''));
        $model = trim((string) ($r['model'] ?? ''));
        if ($brand === '' || $model === '') {
            return;
        }

        // Skip rows missing farm
        if (empty(trim((string) ($r['farm'] ?? '')))) {
            $this->skippedCount++;
            return;
        }

        $serialNo    = trim((string) ($r['serial_number'] ?? ''));
        $subCategory = trim((string) ($r['sub_category'] ?? ''));

        // Duplicate check
        if ($serialNo !== '') {
            if (Asset::where('serial_no', $serialNo)->exists()) {
                $this->skippedCount++;
                return;
            }
        } else {
            if (Asset::where('brand', $brand)->where('model', $model)->where('sub_category', $subCategory)->exists()) {
                $this->skippedCount++;
                return;
            }
        }

        // Category name → code lookup; fall through if already a code
        $rawCategory  = trim((string) ($r['category'] ?? ''));
        $categoryCode = $this->categoryMap->get($rawCategory, $rawCategory);

        // Employee name → ID (optional, left null if name not found)
        $assignedTo = trim((string) ($r['assigned_to'] ?? ''));
        $assignedId = $assignedTo !== '' ? $this->employeeMap->get($assignedTo) : null;

        Asset::create([
            'category_type'    => strtoupper(trim((string) ($r['category_type'] ?? 'NON-IT'))) ?: 'NON-IT',
            'category'         => $categoryCode,
            'sub_category'     => $subCategory,
            'brand'            => $brand,
            'model'            => $model,
            'serial_no'        => $serialNo ?: null,
            'status'           => trim((string) ($r['status'] ?? 'Available'))  ?: 'Available',
            'condition'        => trim((string) ($r['condition'] ?? 'Good'))    ?: 'Good',
            'acquisition_date' => $this->parseDate($r['acquisition_date'] ?? null),
            'item_cost'        => $this->parseCost($r['item_cost'] ?? null),
            'assigned_name'    => $assignedTo ?: null,
            'assigned_id'      => $assignedId,
            'farm'             => strtoupper(trim((string) ($r['farm'] ?? ''))),
            'department'       => trim((string) ($r['department'] ?? '')) ?: null,
            'location'         => trim((string) ($r['location'] ?? ''))   ?: null,
            'remarks'          => trim((string) ($r['remarks'] ?? ''))    ?: null,
        ]);

        $this->createdCount++;
    }

    private function parseDate(mixed $value): ?string
    {
        if ($value === null || $value === '') return null;

        if (is_numeric($value)) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $value)
                    ->format('Y-m-d');
            } catch (\Throwable) {
                return null;
            }
        }

        try {
            return Carbon::parse((string) $value)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    private function parseCost(mixed $value): ?string
    {
        if ($value === null || $value === '') return null;
        $clean = preg_replace('/[^0-9.]/', '', str_replace(',', '', (string) $value));
        return $clean === '' ? null : $clean;
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
