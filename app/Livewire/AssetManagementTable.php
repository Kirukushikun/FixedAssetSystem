<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Asset;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class AssetManagementTable extends Component
{   

    use WithPagination;
    
    protected $paginationTheme = 'tailwind';

    public $search = '';

    // Filter properties
    public $filterCategoryType = '';
    public $filterCategory = '';
    public $filterSubCategory = '';
    public $filterFarm = '';
    public $filterDepartment = '';
    public $filterAssignedTo = '';
    public $filterStatus = '';
    public $filterCondition = '';
    public $filterDateFrom = '';
    public $filterDateTo = '';
    public $filterCostMin = '';
    public $filterCostMax = '';

    public function goToPage($page)
    {
       $this->setPage($page);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    // Reset pagination when filters change
    public function updated($propertyName)
    {
        if (str_starts_with($propertyName, 'filter') || $propertyName === 'search') {
            $this->resetPage();
        }
        
        // Reset sub-category when category changes
        if ($propertyName === 'filterCategory') {
            $this->filterSubCategory = '';
        }
    }

    public function resetFilters()
    {
        $this->reset([
            'filterCategoryType',
            'filterCategory',
            'filterSubCategory',
            'filterFarm',
            'filterDepartment',
            'filterAssignedTo',
            'filterStatus',
            'filterCondition',
            'filterDateFrom',
            'filterDateTo',
            'filterCostMin',
            'filterCostMax'
        ]);
        
        $this->resetPage();
    }

    public function delete($targetAsset){
        $asset = Asset::findOrFail($targetAsset);
        if($asset){
            $asset->is_deleted = true;
            $asset->save();            
        }

        $this->audit('Deleted Asset: ' . $asset->ref_id . ' - ' . $asset->category_type . ' / ' . $asset->category . ' / ' . $asset->sub_category);

        $this->dispatch('notif', type: 'success', header: 'Asset Deleted', message: 'Asset has been successfully deleted.');
    }
    
    public function render()
    {
        $query = Asset::query()
            ->where('is_deleted', false)
            ->where('is_archived', false);

        if ($this->search) {
            $search = '%' . $this->search . '%';
            $query->whereRaw("
                CONCAT(
                    ref_id, ' ', category_type, ' ', category, ' ', sub_category, ' ',
                    brand, ' ', model, ' ', status, ' ', `condition`, ' ',
                    item_cost, ' ', farm
                ) LIKE ?
            ", [$search]);
        }

        $query->when($this->filterCategoryType, fn ($q) => $q->where('category_type', $this->filterCategoryType));
        $query->when($this->filterCategory, fn ($q) => $q->where('category', $this->filterCategory));
        $query->when($this->filterSubCategory, fn ($q) => $q->where('sub_category', $this->filterSubCategory));
        $query->when($this->filterFarm, fn ($q) => $q->where('farm', $this->filterFarm));
        $query->when($this->filterDepartment, fn ($q) => $q->where('department', $this->filterDepartment));
        $query->when($this->filterAssignedTo, fn ($q) => $q->where('assigned_name', $this->filterAssignedTo));
        $query->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus));
        $query->when($this->filterCondition, fn ($q) => $q->where('condition', $this->filterCondition));

        $query->when($this->filterDateFrom, fn ($q) => $q->whereDate('acquisition_date', '>=', $this->filterDateFrom));
        $query->when($this->filterDateTo, fn ($q) => $q->whereDate('acquisition_date', '<=', $this->filterDateTo));

        $query->when($this->filterCostMin, fn ($q) => $q->where('item_cost', '>=', $this->filterCostMin));
        $query->when($this->filterCostMax, fn ($q) => $q->where('item_cost', '<=', $this->filterCostMax));

        $assets = $query->latest()->paginate(10);

        return view('livewire.asset-management-table', compact('assets'));
    }

    private function audit($action){
        $user = auth()->user();
        \App\Models\AuditTrail::create([
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'action' => $action,
        ]);
    }
}
