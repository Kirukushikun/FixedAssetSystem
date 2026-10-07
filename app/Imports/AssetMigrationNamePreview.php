<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Reads a migration file without importing it, collecting each distinct
 * "Assigned To" name with how many assets (and how many Issued) carry it.
 * Uses the same row rules as AssetMigrationImport so the counts line up.
 */
class AssetMigrationNamePreview implements ToCollection, WithHeadingRow, WithCalculatedFormulas
{
    /** @var array<string, array{total: int, issued: int}> */
    public array $names = [];

    public int $rows = 0;

    public function collection(Collection $rows): void
    {
        foreach ($rows as $r) {
            $subCategory = trim((string) ($r['sub_category'] ?? ''));
            $brand       = trim((string) ($r['brand'] ?? ''));
            $model       = trim((string) ($r['model'] ?? ''));

            if (($subCategory === '' && $brand === '' && $model === '') || trim((string) ($r['farm'] ?? '')) === '') {
                continue;
            }

            $this->rows++;

            $name = trim((string) ($r['assigned_to'] ?? ''));
            if ($name === '') {
                continue;
            }

            $this->names[$name] ??= ['total' => 0, 'issued' => 0];
            $this->names[$name]['total']++;
            if (strcasecmp(trim((string) ($r['status'] ?? '')), 'Issued') === 0) {
                $this->names[$name]['issued']++;
            }
        }
    }
}
