<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\AuditTrail as AuditModel;
use Livewire\WithPagination;

class AuditTrail extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public string $search = '';
    public string $filterDateFrom = '';
    public string $filterDateTo = '';
    public string $filterTimeFrom = '';
    public string $filterTimeTo = '';

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilterDateFrom(): void { $this->resetPage(); }
    public function updatingFilterDateTo(): void { $this->resetPage(); }
    public function updatingFilterTimeFrom(): void { $this->resetPage(); }
    public function updatingFilterTimeTo(): void { $this->resetPage(); }

    public function resetFilters(): void
    {
        $this->reset('search', 'filterDateFrom', 'filterDateTo', 'filterTimeFrom', 'filterTimeTo');
        $this->resetPage();
    }

    public function render()
    {
        $audits = AuditModel::query()
            ->when($this->search, fn($q) =>
                $q->where('user_id', 'like', "%{$this->search}%")
                  ->orWhere('user_name', 'like', "%{$this->search}%")
                  ->orWhere('action', 'like', "%{$this->search}%")
            )
            ->when($this->filterDateFrom, fn($q) =>
                $q->whereDate('created_at', '>=', $this->filterDateFrom)
            )
            ->when($this->filterDateTo, fn($q) =>
                $q->whereDate('created_at', '<=', $this->filterDateTo)
            )
            ->when($this->filterTimeFrom, fn($q) =>
                $q->whereTime('created_at', '>=', $this->filterTimeFrom)
            )
            ->when($this->filterTimeTo, fn($q) =>
                $q->whereTime('created_at', '<=', $this->filterTimeTo)
            )
            ->latest()
            ->paginate(10);

        return view('livewire.audit-trail', compact('audits'));
    }
}