<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Asset;
use App\Models\Employee;
use App\Models\Flag;
use App\Models\Category;
use App\Models\Department;
use App\Exports\AssetExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class DashboardData extends Component
{   
    public $departments;

    public $targetFarm;
    public $categories;
    public $openCategory = null;

    // Data properties
    public $conditions;
    public $maxCondition;
    public $statuses;
    public $statusPercentages;
    public $totalAssets;
    public $farmDistribution;

    public $employee_id, $employee_name, $position, $farm, $department;

    // Export filter properties
    public $export_category_type = '';
    public $export_category = '';
    public $export_sub_category = '';
    public $export_farm = '';
    public $export_department = '';
    public $export_age_min = '';
    public $export_age_max = '';
    public $export_categories = [];
    public $export_sub_categories = [];

    protected function rules()
    {
        return [
            'employee_id' => ['required', Rule::unique('employees', 'employee_id')],
            'employee_name' => 'required',
            'position' => 'required',
            'farm' => 'required',
            'department' => 'required',
        ];
    }

    public function exportWithFilters()
    {
        $filters = [
            'category_type' => $this->export_category_type,
            'category' => $this->export_category,
            'sub_category' => $this->export_sub_category,
            'farm' => $this->export_farm,
            'department' => $this->export_department,
            'age_min' => $this->export_age_min,
            'age_max' => $this->export_age_max,
        ];

        $this->clearExportFilters();
        
        return Excel::download(new AssetExport($filters), 'assets_filtered_' . now()->format('Y-m-d_His') . '.xlsx');
    }

    public function updatedExportCategoryType($value)
    {
        // Reset category and subcategory when category type changes
        $this->export_category = '';
        $this->export_sub_category = '';
        $this->export_sub_categories = [];
        
        // Load categories based on selected type
        if ($value === 'IT') {
            $this->export_categories = Category::whereHas('subcategories', fn ($q) => $q->where('category_type', 'IT'))
                ->get();
        } elseif ($value === 'NON-IT') {
            $this->export_categories = Category::whereDoesntHave('subcategories', fn ($q) => $q->where('category_type', 'IT'))
                ->get();
        } else {
            // Show all if no type selected
            $this->export_categories = $this->categories;
        }
    }

    public function updatedExportCategory($value)
    {
        // Reset subcategory when category changes
        $this->export_sub_category = '';
        
        // Load subcategories for selected category
        if ($value) {
            $category = \App\Models\Category::where('code', $value)->first();
            $this->export_sub_categories = $category && $category->subcategories 
                ? $category->subcategories->pluck('name')->toArray() 
                : [];
        } else {
            $this->export_sub_categories = [];
        }
    }

    public function clearExportFilters()
    {
        $this->reset([
            'export_category_type',
            'export_category',
            'export_sub_category',
            'export_farm',
            'export_department',
            'export_age_min',
            'export_age_max',
            'export_sub_categories'
        ]);
        
        // Reset to show all categories
        $this->export_categories = $this->categories;
    }

    public function mount()
    {   
        $this->departments = Department::pluck('name')->toArray();
        sort($this->departments);
        $this->categories = Category::with('subcategories')->get();
        
        // Initialize export_categories to all categories
        $this->export_categories = $this->categories;
        
        $this->loadDashboardData();
    }

    private function loadDashboardData()
    {
        $stats = Cache::remember('dashboard_stats', 120, function () {
            // 3 GROUP BY queries replace 18 individual COUNT queries
            $conditionCounts = Asset::where('is_deleted', false)
                ->selectRaw('`condition`, count(*) as total')
                ->groupBy('condition')
                ->pluck('total', 'condition');

            $statusCounts = Asset::where('is_deleted', false)
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');

            $farmCounts = Asset::where('is_deleted', false)
                ->selectRaw('farm, count(*) as total')
                ->groupBy('farm')
                ->pluck('total', 'farm');

            $totalAssets = $statusCounts->sum();

            $farms = [
                'BFC'     => 'BROOKSIDE FARMS',
                'BDL'     => 'BROOKDALE FARMS',
                'PFC'     => 'POULTRYPURE FARMS',
                'RH'      => 'RH FARMS',
                'BBGC'    => 'BROOKSIDE BREEDING & GENETICS CORP.',
                'HATCHERY'=> 'HATCHERY',
            ];

            $farmDistribution = [];
            foreach ($farms as $code => $name) {
                $count = $farmCounts->get($code, 0);
                $farmDistribution[] = [
                    'code'       => $code,
                    'name'       => $name,
                    'count'      => $count,
                    'percentage' => $totalAssets > 0 ? round(($count / $totalAssets) * 100, 1) : 0,
                ];
            }

            $conditions = [
                'good'      => $conditionCounts->get('Good', 0),
                'defective' => $conditionCounts->get('Defective', 0),
                'repair'    => $conditionCounts->get('Repair', 0),
                'replace'   => $conditionCounts->get('Replace', 0),
            ];

            $statuses = [
                'available'    => $statusCounts->get('Available', 0),
                'issued'       => $statusCounts->get('Issued', 0),
                'transferred'  => $statusCounts->get('Transferred', 0),
                'for_transfer' => $statusCounts->get('For Transfer', 0),
                'for_disposal' => $statusCounts->get('For Disposal', 0),
                'disposed'     => $statusCounts->get('Disposed', 0),
                'lost'         => $statusCounts->get('Lost', 0),
            ];

            $statusPercentages = [];
            foreach ($statuses as $key => $count) {
                $statusPercentages[$key] = $totalAssets > 0 ? ($count / $totalAssets) * 100 : 0;
            }

            return compact('conditions', 'statuses', 'statusPercentages', 'totalAssets', 'farmDistribution');
        });

        $this->conditions        = $stats['conditions'];
        $this->statuses          = $stats['statuses'];
        $this->statusPercentages = $stats['statusPercentages'];
        $this->totalAssets       = $stats['totalAssets'];
        $this->farmDistribution  = $stats['farmDistribution'];
        $this->maxCondition      = max($this->conditions) ?: 1;
    }

    public function toggleCategory($categoryId)
    {
        if ($this->openCategory === $categoryId) {
            $this->openCategory = null;
        } else {
            $this->openCategory = $categoryId;
        }
    }

    public function setFarm($code)
    {
        $this->targetFarm = $code;
        Log::info('Target farm set to: ' . $this->targetFarm);
    }

    public function clear()
    {   
        $this->reset(['employee_id', 'employee_name', 'position', 'farm', 'department']);
    }

    public function submit()
    {
        try {
            $this->validate();

            $employeeName = $this->employee_name;

            Employee::create([
                'employee_id' => $this->employee_id,
                'employee_name' => $employeeName,
                'position' => $this->position,
                'farm' => $this->farm,
                'department' => $this->department,
            ]);

            // Clear all caches to ensure employee data syncs across modules
            \Illuminate\Support\Facades\Cache::flush();

            $this->reset(['employee_id', 'employee_name', 'position', 'farm', 'department']);
            $this->noreloadNotif('success', 'Employee Added', 'Employee ' . $employeeName . ' has been successfully added.');

        } catch (\Exception $e) {
            Log::error('Employee creation failed: ' . $e->getMessage());
            $this->noreloadNotif('failed', 'Creation Failed', 'Unable to add employee. Please try again.');
        }
    }

    public function getAlertsProperty()
    {
        $alerts = [];

        // Each alert reuses one query result — no duplicate DB calls
        $lostAsset = Asset::where('status', 'Lost')
            ->selectRaw('count(*) as total, max(updated_at) as latest')
            ->first();
        if ($lostAsset->total > 0) {
            $alerts[] = [
                'message'   => "{$lostAsset->total} assets are marked Lost",
                'timestamp' => $lostAsset->latest ?? now(),
                'icon'      => 'fa-solid fa-bell',
                'color'     => 'text-teal-400',
            ];
        }

        $repairAsset = Asset::where('condition', 'Repair')
            ->where('updated_at', '<=', Carbon::now()->subDays(30))
            ->selectRaw('count(*) as total, max(updated_at) as latest')
            ->first();
        if ($repairAsset->total > 0) {
            $alerts[] = [
                'message'   => "{$repairAsset->total} assets are Under Repair for more than 30 days",
                'timestamp' => $repairAsset->latest ?? now(),
                'icon'      => 'fa-solid fa-bell',
                'color'     => 'text-teal-400',
            ];
        }

        $unreturnedAsset = Asset::whereNotNull('assigned_id')
            ->whereHas('assignedEmployee', fn ($q) => $q->where('is_deleted', true))
            ->selectRaw('count(*) as total, max(updated_at) as latest')
            ->first();
        if ($unreturnedAsset->total > 0) {
            $alerts[] = [
                'message'   => "{$unreturnedAsset->total} employees have unreturned items",
                'timestamp' => $unreturnedAsset->latest ?? now(),
                'icon'      => 'fa-solid fa-bell',
                'color'     => 'text-teal-400',
            ];
        }

        return collect($alerts)->sortByDesc('timestamp')->values();
    }

    public function render()
    {
        return view('livewire.dashboard-data', [
            'total_assets'       => Asset::where('is_deleted', false)->count(),
            'assigned_assets'    => Asset::where('is_deleted', false)->whereNotNull('assigned_id')->count(),
            'total_employees'    => Employee::where('is_deleted', false)->count(),
            'pending_clearances' => Flag::where('flag_type', 'Pending Clearances')->count(),
        ]);
    }

    private function noreloadNotif($type, $header, $message){
        $this->dispatch('notif', type: $type, header: $header, message: $message);
    }
}