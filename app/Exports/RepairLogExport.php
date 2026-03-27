<?php

namespace App\Exports;

use App\Models\AssetRepair;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RepairLogExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(
        protected $date_from,
        protected $date_to,
        protected $type = null,
    ) {}

    public function query()
    {
        return AssetRepair::with('asset')
            ->when($this->date_from, fn($q) => $q->whereDate('date', '>=', $this->date_from))
            ->when($this->date_to, fn($q) => $q->whereDate('date', '<=', $this->date_to))
            ->when($this->type, fn($q) => $q->where('type', $this->type))
            ->orderByDesc('date');
    }

    public function headings(): array
    {
        return [
            'Date',
            'Reference ID',
            'Brand',
            'Model',
            'Farm',
            'Type',
            'Cost',
            'Notes',
        ];
    }

    public function map($repair): array
    {
        return [
            \Carbon\Carbon::parse($repair->date)->format('m/d/Y'),
            $repair->asset->ref_id ?? '—',
            $repair->asset->brand ?? '—',
            $repair->asset->model ?? '—',
            $repair->asset->farm ?? '—',
            $repair->type,
            $repair->cost ? '₱' . number_format($repair->cost, 2) : '—',
            $repair->notes ?? '—',
        ];
    }
}