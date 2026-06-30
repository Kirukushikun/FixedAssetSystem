<?php

namespace App\Imports;

use App\Models\Asset;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;

/**
 * Full system restore import — mirrors AssetExport exactly.
 * Accepts Reference ID: if it already exists the row is updated; if not, the asset is created
 * with that ref_id. Omitting Reference ID lets the system auto-generate one.
 * Category must be the stored code (e.g. 'COMP'), Assigned ID must be a valid employee ID.
 */
class AssetImport implements
    OnEachRow,
    WithHeadingRow,
    WithCalculatedFormulas,
    WithChunkReading
{
    public int $createdCount = 0;
    public int $updatedCount = 0;
    public int $skippedCount = 0;

    public function onRow(Row $row): void
    {
        $r = $row->toArray();

        // Skip blank rows
        if (empty(trim((string) ($r['brand'] ?? ''))) || empty(trim((string) ($r['model'] ?? '')))) {
            return;
        }

        if (empty(trim((string) ($r['farm'] ?? '')))) {
            $this->skippedCount++;
            return;
        }

        $refId = trim((string) ($r['reference_id'] ?? ''));

        $data = [
            'category_type'     => strtoupper(trim((string) ($r['category_type'] ?? 'NON-IT'))) ?: 'NON-IT',
            'category'          => trim((string) ($r['category'] ?? '')),
            'sub_category'      => trim((string) ($r['sub_category'] ?? '')),
            'brand'             => trim((string) ($r['brand'] ?? '')),
            'model'             => trim((string) ($r['model'] ?? '')),
            'serial_no'         => trim((string) ($r['serial_number'] ?? '')) ?: null,
            'status'            => trim((string) ($r['status'] ?? 'Available'))     ?: 'Available',
            'condition'         => trim((string) ($r['condition'] ?? 'Good'))       ?: 'Good',
            'acquisition_date'  => $this->parseDate($r['acquisition_date'] ?? null),
            'item_cost'         => $this->parseCost($r['item_cost'] ?? null),
            'depreciated_value' => $this->parseCost($r['depreciated_value'] ?? null),
            'usable_life'       => $this->parseInteger($r['usable_life'] ?? null),
            'assigned_name'     => trim((string) ($r['assigned_name'] ?? '')) ?: null,
            'assigned_id'       => !empty($r['assigned_id']) ? (int) $r['assigned_id'] : null,
            'farm'              => strtoupper(trim((string) ($r['farm'] ?? ''))),
            'department'        => trim((string) ($r['department'] ?? '')) ?: null,
            'location'          => trim((string) ($r['location'] ?? ''))   ?: null,
            'remarks'           => trim((string) ($r['remarks'] ?? ''))    ?: null,
        ];

        if ($refId !== '') {
            $existing = Asset::where('ref_id', $refId)->first();

            if ($existing) {
                $existing->update($data);
                $this->updatedCount++;
            } else {
                // Create preserving the original ref_id (booted() skips generation when ref_id is set)
                Asset::create(array_merge($data, ['ref_id' => $refId]));
                $this->createdCount++;
            }
        } else {
            // No ref_id → let booted() auto-generate
            Asset::create($data);
            $this->createdCount++;
        }
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

    private function parseInteger(mixed $value): ?string
    {
        if ($value === null || $value === '') return null;
        $clean = preg_replace('/[^0-9]/', '', (string) $value);
        return $clean === '' ? null : $clean;
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
