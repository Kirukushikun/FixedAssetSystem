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
        $assets = Asset::where('is_deleted', false)
            ->where('is_archived', false)
            
            // Search filter
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('ref_id', 'like', '%' . $this->search . '%')
                        ->orWhere('category_type', 'like', '%' . $this->search . '%')
                        ->orWhere('category', 'like', '%' . $this->search . '%')
                        ->orWhere('sub_category', 'like', '%' . $this->search . '%')
                        ->orWhere('brand', 'like', '%' . $this->search . '%')
                        ->orWhere('model', 'like', '%' . $this->search . '%')
                        ->orWhere('status', 'like', '%' . $this->search . '%')
                        ->orWhere('condition', 'like', '%' . $this->search . '%')
                        ->orWhere('item_cost', 'like', '%' . $this->search . '%')
                        ->orWhere('assigned_name', 'like', '%' . $this->search . '%')
                        ->orWhere('assigned_id', 'like', '%' . $this->search . '%')
                        ->orWhere('farm', 'like', '%' . $this->search . '%')
                        ->orWhere('department', 'like', '%' . $this->search . '%')
                        ->orWhere('remarks', 'like', '%' . $this->search . '%');
                });
            })
            
            // Category Type filter
            ->when($this->filterCategoryType, function ($query) {
                $query->where('category_type', $this->filterCategoryType);
            })
            
            // Category filter
            ->when($this->filterCategory, function ($query) {
                $query->where('category', $this->filterCategory);
            })
            
            // Sub-category filter
            ->when($this->filterSubCategory, function ($query) {
                $query->where('sub_category', $this->filterSubCategory);
            })
            
            // Farm filter
            ->when($this->filterFarm, function ($query) {
                $query->where('farm', $this->filterFarm);
            })
            
            // Department filter
            ->when($this->filterDepartment, function ($query) {
                $query->where('department', $this->filterDepartment);
            })
            
            // Assigned To filter
            ->when($this->filterAssignedTo, function ($query) {
                $query->where('assigned_name', $this->filterAssignedTo);
            })
            
            // Status filter
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            
            // Condition filter
            ->when($this->filterCondition, function ($query) {
                $query->where('condition', $this->filterCondition);
            })
            
            // Date range filter
            ->when($this->filterDateFrom, function ($query) {
                $query->whereDate('acquisition_date', '>=', $this->filterDateFrom);
            })
            ->when($this->filterDateTo, function ($query) {
                $query->whereDate('acquisition_date', '<=', $this->filterDateTo);
            })
            
            // Cost range filter
            ->when($this->filterCostMin, function ($query) {
                $query->where('item_cost', '>=', $this->filterCostMin);
            })
            ->when($this->filterCostMax, function ($query) {
                $query->where('item_cost', '<=', $this->filterCostMax);
            })
            
            ->latest()
            ->paginate(10);
            
        return view('livewire.assetmanagement-table', compact('assets'));
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
