<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Asset;
use App\Models\Category;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;


// Import the job
use App\Jobs\SyncAssetToSnipeIT;

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

    public $categories;
    public $subCategories = [];
    public $openCategory = null;
    public $departments;

    public function toggleCategory($categoryId)
    {
        if ($this->openCategory === $categoryId) {
            $this->openCategory = null;
        } else {
            $this->openCategory = $categoryId;
        }
    }
    
    public function updatedFilterCategory($value)
    {
        // Reset subcategory when category changes
        $this->filterSubCategory = '';
        
        // Load subcategories for selected category
        if ($value) {
            $category = Category::where('code', $value)->first();
            $this->subCategories = $category ? $category->subCategories : [];
        } else {
            $this->subCategories = [];
        }
    }

    public function goToPage($page)
    {
       $this->setPage($page);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {   
        // OPTIMIZED: Cache categories with subcategories
        $this->categories = Cache::remember('categories_with_subcategories', 3600, function() {
            return Category::with('subcategories')->get();
        });
        
        $this->departments = Cache::remember('departments_list', 3600, function() {
            return Department::all();
        });
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

    /**
     * OPTIMIZED: Snipe-IT deletion moved to background job
     */
    public function delete($targetAsset)
    {
        DB::beginTransaction();
        
        try {
            $asset = Asset::findOrFail($targetAsset);
            
            // OPTIMIZED: Dispatch Snipe-IT deletion to background queue
            if ($asset->category_type === 'IT' && $asset->snipe_id) {
                SyncAssetToSnipeIT::dispatch($asset, 'delete');
            }

            $asset->is_deleted = true;
            $asset->save();
            
            // Audit Trail
            $this->audit('Deleted Asset: ' . $asset->ref_id . ' - ' . $asset->category_type . ' / ' . $asset->category . ' / ' . $asset->sub_category);

            DB::commit();

            // Clear caches
            $this->clearAllAssetCaches();

            $this->dispatch('notif', type: 'success', header: 'Asset Deleted', message: 'Asset has been successfully deleted.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Asset deletion failed', [
                'error' => $e->getMessage(),
                'asset_id' => $targetAsset,
                'user_id' => auth()->id()
            ]);
            
            $this->dispatch('notif', type: 'error', header: 'Delete Failed', message: 'Unable to delete asset. Please try again.');
        }
    }
    
    /**
     * OPTIMIZED: Simplified cache clearing
     */
    private function clearAllAssetCaches()
    {
        $cachesToForget = [
            'api.assets.index',
            'asset_table_query',
            'trash_deleted_assets',
            'categories_with_subcategories',
            'departments_list',
            'employees_dropdown',
            'categories_by_code',
        ];

        foreach ($cachesToForget as $cacheKey) {
            Cache::forget($cacheKey);
        }

        // If using Redis/Memcached with tags support
        if (in_array(config('cache.default'), ['redis', 'memcached'])) {
            try {
                Cache::tags(['asset_table'])->flush();
            } catch (\Exception $e) {
                Log::warning('Cache tag flush failed: ' . $e->getMessage());
            }
        }
        
        Log::info('Asset caches cleared successfully');
    }
    
    /**
     * OPTIMIZED: Added select() to limit columns and improve query performance
     */
    public function render()
    {
        // Build query - select only needed columns
        $query = Asset::select([
            'id', 'ref_id', 'category_type', 'category', 'sub_category',
            'brand', 'model', 'status', 'condition', 'item_cost', 
            'farm', 'department', 'assigned_name', 'acquisition_date',
            'qr_code', 'created_at', 'updated_at'
        ])
        ->where('is_deleted', false)
        ->where('is_archived', false);

        // Search filter
        if ($this->search) {
            $search = '%' . $this->search . '%';
            $query->whereRaw("
                CONCAT(
                    ref_id, ' ', category_type, ' ', category, ' ', sub_category, ' ',
                    brand, ' ', model, ' ', status, ' ', `condition`, ' ',
                    COALESCE(item_cost, ''), ' ', COALESCE(farm, '')
                ) LIKE ?
            ", [$search]);
        }

        // Apply filters
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

        // Order by created_at DESC, then by id DESC for consistent ordering
        $assets = $query->orderBy('created_at', 'desc')
                        ->orderBy('id', 'desc')
                        ->paginate(10);

        // Get categories as array with code as key (cached)
        $categoryCodeImage = Cache::remember('categories_by_code', 3600, function() {
            return Category::all()->keyBy('code');
        });

        return view('livewire.asset-management-table', compact('assets', 'categoryCodeImage'));
    }

    private function audit($action)
    {
        \App\Models\AuditTrail::create([
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'action' => $action,
        ]);
    }
}