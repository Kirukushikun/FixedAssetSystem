<?php

namespace App\Exports;

use App\Models\Audit;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AuditLogExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(
        protected $date_from,
        protected $date_to,
        protected $farm = null,
    ) {}

    public function query()
    {
        return Audit::with('asset')
            ->when($this->date_from, fn($q) => $q->whereDate('audited_at', '>=', $this->date_from))
            ->when($this->date_to, fn($q) => $q->whereDate('audited_at', '<=', $this->date_to))
            ->when($this->farm, fn($q) => $q->where('farm', $this->farm))
            ->orderByDesc('audited_at');
    }

    public function headings(): array
    {
        return [
            'Date',
            'Reference ID',
            'Brand',
            'Model',
            'Farm',
            'Auditor',
            'Finding',
            'Notes',
        ];
    }

    public function map($audit): array
    {
        return [
            $audit->audited_at->format('m/d/Y'),
            $audit->asset->ref_id ?? '—',
            $audit->asset->brand ?? '—',
            $audit->asset->model ?? '—',
            $audit->farm ?? '—',
            $audit->audited_by_name,
            $audit->finding ?? '—',
            $audit->notes ?? '—',
        ];
    }
}