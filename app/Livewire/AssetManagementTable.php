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
        $this->categories = Category::with('subcategories')->get();
        $this->departments = Department::all();
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
            // Delete from Snipe-IT first if IT asset
            if ($asset->category_type === 'IT' && $asset->snipe_id) {
                try {
                    $this->deleteFromSnipeIT($asset);
                } catch (\Exception $e) {
                    Log::error('Snipe-IT deletion failed: ' . $e->getMessage());
                }
            }

            $asset->is_deleted = true;
            $asset->save();
            
            // Clear ALL asset-related caches
            $this->clearAllAssetCaches();
        }

        $this->audit('Deleted Asset: ' . $asset->ref_id . ' - ' . $asset->category_type . ' / ' . $asset->category . ' / ' . $asset->sub_category);

        $this->dispatch('notif', type: 'success', header: 'Asset Deleted', message: 'Asset has been successfully deleted.');
    }
    
    /**
     * Clear all asset-related caches
     */
    private function clearAllAssetCaches()
    {
        try {
            // Check if cache driver supports tags (Redis, Memcached)
            if (in_array(config('cache.default'), ['redis', 'memcached'])) {
                // Use cache tags to clear all asset table caches at once
                Cache::tags(['asset_table'])->flush();
            } else {
                // For file/database cache drivers, we need to manually clear possible cache keys
                // This generates all possible cache key combinations and clears them
                
                // Get all possible filter values to generate cache keys
                $categoryTypes = ['IT', 'NON-IT', '']; // Add your actual category types
                $statuses = ['Available', 'In Use', 'Under Maintenance', 'Disposed', ''];
                $conditions = ['New', 'Good', 'Fair', 'Poor', ''];
                
                // Clear caches for different pagination pages (assume max 100 pages)
                for ($page = 1; $page <= 100; $page++) {
                    // Generate some common cache key variations
                    foreach ($categoryTypes as $catType) {
                        foreach ($statuses as $status) {
                            $cacheKey = 'asset_table_' . md5(json_encode([
                                'search' => '',
                                'filterCategoryType' => $catType,
                                'filterCategory' => '',
                                'filterSubCategory' => '',
                                'filterFarm' => '',
                                'filterDepartment' => '',
                                'filterAssignedTo' => '',
                                'filterStatus' => $status,
                                'filterCondition' => '',
                                'filterDateFrom' => '',
                                'filterDateTo' => '',
                                'filterCostMin' => '',
                                'filterCostMax' => '',
                                'page' => $page
                            ]));
                            
                            Cache::forget($cacheKey);
                        }
                    }
                }
                
                // Also clear any generic caches
                Cache::forget('asset_table_query');
            }
            
            // Clear trash cache
            Cache::forget('trash_deleted_assets');
            
            Log::info('Asset caches cleared successfully');
        } catch (\Exception $e) {
            Log::error('Failed to clear asset caches: ' . $e->getMessage());
        }
    }
    
    public function render()
    {
        // Build query without caching to avoid cache issues
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

        // Order by created_at DESC, then by id DESC for consistent ordering
        $assets = $query->orderBy('created_at', 'desc')
                        ->orderBy('id', 'desc')
                        ->paginate(10);

        // Get categories as array with code as key
        $categoryCodeImage = Category::all()->keyBy('code');

        return view('livewire.asset-management-table', compact('assets', 'categoryCodeImage'));
    }

    private function audit($action){
        $user = auth()->user();
        \App\Models\AuditTrail::create([
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'action' => $action,
        ]);
    }

    public function deleteFromSnipeIT($asset)
    {
        Log::info('Deleting from Snipe-IT:', ['snipe_id' => $asset->snipe_id, 'ref_id' => $asset->ref_id]);

        $result = app(\App\Services\SnipeService::class)
            ->deleteAsset($asset->snipe_id);

        Log::info('Snipe-IT Delete Result:', $result);

        // Optional: Clear the snipe_id from your local asset
        if (isset($result['status']) && $result['status'] === 'success') {
            $asset->update(['snipe_id' => null]);
        }

        return $result;
    }
}