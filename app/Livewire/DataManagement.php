<?php

namespace App\Livewire;

use App\Models\AuditTrail;
use App\Models\UserLog;
use App\Models\Asset;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class DataManagement extends Component
{
    // ── Audit Trail ──────────────────────────────────────────────────────
    public string $auditMode     = '';
    public string $auditDateFrom = '';
    public string $auditDateTo   = '';
    public string $auditYear     = '';
    public ?int   $auditCount    = null;

    // ── Asset Attachments ────────────────────────────────────────────────
    public string $attachMode     = '';
    public string $attachYear     = '';
    public string $attachQuarter  = '';
    public string $attachDateFrom = '';
    public string $attachDateTo   = '';
    public ?int   $attachCount    = null;

    // ── User Logs ────────────────────────────────────────────────────────
    public string $logMode     = '';
    public string $logDateFrom = '';
    public string $logDateTo   = '';
    public string $logYear     = '';
    public ?int   $logCount    = null;

    // ── Reset count whenever a filter changes ────────────────────────────
    public function updatedAuditMode(): void     { $this->auditCount  = null; }
    public function updatedAuditDateFrom(): void  { $this->auditCount  = null; }
    public function updatedAuditDateTo(): void    { $this->auditCount  = null; }
    public function updatedAuditYear(): void      { $this->auditCount  = null; }

    public function updatedAttachMode(): void     { $this->attachCount = null; }
    public function updatedAttachYear(): void     { $this->attachCount = null; }
    public function updatedAttachQuarter(): void  { $this->attachCount = null; }
    public function updatedAttachDateFrom(): void { $this->attachCount = null; }
    public function updatedAttachDateTo(): void   { $this->attachCount = null; }

    public function updatedLogMode(): void     { $this->logCount = null; }
    public function updatedLogDateFrom(): void { $this->logCount = null; }
    public function updatedLogDateTo(): void   { $this->logCount = null; }
    public function updatedLogYear(): void     { $this->logCount = null; }

    // ── Preview counts ───────────────────────────────────────────────────
    public function previewAudit(): void
    {
        $q = AuditTrail::query();
        $this->applyDateFilter($q, $this->auditMode, $this->auditDateFrom, $this->auditDateTo, $this->auditYear);
        $this->auditCount = $q->count();
    }

    public function previewAttach(): void
    {
        $q = Asset::whereNotNull('attachment')->where('attachment', '!=', '');
        $this->applyAttachFilter($q);
        $this->attachCount = $q->count();
    }

    public function previewLog(): void
    {
        $q = UserLog::query();
        $this->applyDateFilter($q, $this->logMode, $this->logDateFrom, $this->logDateTo, $this->logYear);
        $this->logCount = $q->count();
    }

    // ── Execute actions ──────────────────────────────────────────────────
    public function executeAudit(): void
    {
        $q = AuditTrail::query();
        $this->applyDateFilter($q, $this->auditMode, $this->auditDateFrom, $this->auditDateTo, $this->auditYear);
        $deleted = $q->count();
        $q->delete();

        $this->reset(['auditMode', 'auditDateFrom', 'auditDateTo', 'auditYear', 'auditCount']);
        $this->dispatch('notif', type: 'success', header: 'Audit Trail Purged',
            message: "{$deleted} audit trail entries permanently deleted.");
    }

    public function executeAttach(): void
    {
        $q = Asset::whereNotNull('attachment')->where('attachment', '!=', '');
        $this->applyAttachFilter($q);

        $paths = $q->pluck('attachment', 'id');

        foreach ($paths as $path) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }
        }

        Asset::whereIn('id', $paths->keys())->update(['attachment' => null, 'attachment_name' => null]);

        $count = $paths->count();
        $this->reset(['attachMode', 'attachYear', 'attachQuarter', 'attachDateFrom', 'attachDateTo', 'attachCount']);
        $this->dispatch('notif', type: 'success', header: 'Attachments Purged',
            message: "Attachment files removed from {$count} assets.");
    }

    public function executeLog(): void
    {
        $q = UserLog::query();
        $this->applyDateFilter($q, $this->logMode, $this->logDateFrom, $this->logDateTo, $this->logYear);
        $deleted = $q->count();
        $q->delete();

        $this->reset(['logMode', 'logDateFrom', 'logDateTo', 'logYear', 'logCount']);
        $this->dispatch('notif', type: 'success', header: 'User Logs Purged',
            message: "{$deleted} user log entries permanently deleted.");
    }

    // ── Filter helpers ───────────────────────────────────────────────────
    private function applyDateFilter($query, string $mode, string $from, string $to, string $year): void
    {
        if ($mode === 'date_range' && $from && $to) {
            $query->whereBetween('created_at', ["{$from} 00:00:00", "{$to} 23:59:59"]);
        } elseif ($mode === 'year' && $year) {
            $query->whereYear('created_at', $year);
        }
        // 'all' → no filter applied; deletes everything
    }

    private function applyAttachFilter($query): void
    {
        if ($this->attachMode === 'quarter' && $this->attachYear && $this->attachQuarter) {
            $ranges = [
                'Q1' => ['01-01', '03-31'],
                'Q2' => ['04-01', '06-30'],
                'Q3' => ['07-01', '09-30'],
                'Q4' => ['10-01', '12-31'],
            ];
            if (isset($ranges[$this->attachQuarter])) {
                [$start, $end] = $ranges[$this->attachQuarter];
                $query->whereBetween('created_at', [
                    "{$this->attachYear}-{$start} 00:00:00",
                    "{$this->attachYear}-{$end} 23:59:59",
                ]);
            }
        } elseif ($this->attachMode === 'date_range' && $this->attachDateFrom && $this->attachDateTo) {
            $query->whereBetween('created_at', [
                "{$this->attachDateFrom} 00:00:00",
                "{$this->attachDateTo} 23:59:59",
            ]);
        }
    }

    public function render()
    {
        return view('livewire.data-management', [
            'years' => range(2022, (int) date('Y')),
        ]);
    }
}
