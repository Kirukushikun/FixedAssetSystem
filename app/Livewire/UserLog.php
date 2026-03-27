<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AccessLog;

class UserLog extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public string $search = '';
    public string $filterSuccess = '';
    public string $filterDateFrom = '';
    public string $filterDateTo = '';
    public string $filterTimeFrom = '';
    public string $filterTimeTo = '';

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilterSuccess(): void { $this->resetPage(); }
    public function updatingFilterDateFrom(): void { $this->resetPage(); }
    public function updatingFilterDateTo(): void { $this->resetPage(); }
    public function updatingFilterTimeFrom(): void { $this->resetPage(); }
    public function updatingFilterTimeTo(): void { $this->resetPage(); }

    public function resetFilters(): void
    {
        $this->reset('search', 'filterSuccess', 'filterDateFrom', 'filterDateTo', 'filterTimeFrom', 'filterTimeTo');
        $this->resetPage();
    }

    public function render()
    {
        $userLogs = AccessLog::query()
            ->when($this->search, fn($q) =>
                $q->where('email', 'like', "%{$this->search}%")
                  ->orWhere('ip_address', 'like', "%{$this->search}%")
                  ->orWhere('user_agent', 'like', "%{$this->search}%")
            )
            ->when($this->filterSuccess !== '', fn($q) =>
                $q->where('success', $this->filterSuccess)
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

        return view('livewire.user-log', compact('userLogs'));
    }
}