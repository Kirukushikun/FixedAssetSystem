<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Asset;
use App\Models\Employee;
use App\Models\Flag;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DashboardData extends Component
{   
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

    protected $rules = [
        'employee_id' => 'required',
        'employee_name' => 'required',
        'position' => 'required',
        'farm' => 'required',
        'department' => 'required',
    ];

    public function mount()
    {   
        $this->categories = Category::with('subcategories')->get();
        $this->loadDashboardData();
    }

    private function loadDashboardData()
    {
        // ASSET STATUS OVERVIEW DATA
        $this->conditions = [
            'good' => Asset::where('is_deleted', false)->where('condition', 'Good')->count(),
            'defective' => Asset::where('is_deleted', false)->where('condition', 'Defective')->count(),
            'repair' => Asset::where('is_deleted', false)->where('condition', 'Repair')->count(),
            'replace' => Asset::where('is_deleted', false)->where('condition', 'Replace')->count(),
        ];
        
        $this->statuses = [
            'available' => Asset::where('is_deleted', false)->where('status', 'Available')->count(),
            'issued' => Asset::where('is_deleted', false)->where('status', 'Issued')->count(),
            'transferred' => Asset::where('is_deleted', false)->where('status', 'Transferred')->count(),
            'for_disposal' => Asset::where('is_deleted', false)->where('status', 'For disposal')->count(),
            'disposed' => Asset::where('is_deleted', false)->where('status', 'Disposed')->count(),
            'lost' => Asset::where('is_deleted', false)->where('status', 'Lost')->count(),
        ];

        $this->totalAssets = Asset::where('is_deleted', false)->count();
        $this->maxCondition = max($this->conditions) ?: 1;

        // Calculate percentage for each status
        $this->statusPercentages = [];
        foreach ($this->statuses as $key => $count) {
            $this->statusPercentages[$key] = $this->totalAssets > 0 ? ($count / $this->totalAssets) * 100 : 0;
        }

        // FARM DISTRIBUTION DATA
        $farms = [
            'BFC' => 'BROOKSIDE FARMS',
            'BDL' => 'BROOKDALE FARMS',
            'PFC' => 'POULTRYPURE FARMS',
            'RH' => 'RH FARMS',
        ];

        $this->farmDistribution = [];
        foreach ($farms as $code => $name) {
            $count = Asset::where('is_deleted', false)->where('farm', $code)->count();
            $percentage = $this->totalAssets > 0 ? round(($count / $this->totalAssets) * 100, 1) : 0;
            
            $this->farmDistribution[] = [
                'code' => $code,
                'name' => $name,
                'count' => $count,
                'percentage' => $percentage
            ];
        }
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

            Employee::create([
                'employee_id' => $this->employee_id,
                'employee_name' => $this->employee_name,
                'position' => $this->position,
                'farm' => $this->farm,
                'department' => $this->department,
            ]);

            $this->reset(['employee_id', 'employee_name', 'position', 'farm', 'department']);
            $this->noreloadNotif('success', 'Employee Added', 'Employee ' . $this->employee_name . ' has been successfully added.');

        } catch (\Exception $e) {
            Log::error('Employee creation failed: ' . $e->getMessage());
            $this->noreloadNotif('failed', 'Creation Failed', 'Unable to add employee. Please try again.');
        }
    }

    public function getAlertsProperty()
    {
        $alerts = [];

        // Alert 1: Assets marked as Lost
        $lostAssetsCount = Asset::where('status', 'Lost')->count();
        if ($lostAssetsCount > 0) {
            $alerts[] = [
                'message' => "{$lostAssetsCount} assets are marked Lost",
                'timestamp' => $this->getLatestTimestamp(Asset::where('status', 'Lost')),
                'icon' => 'fa-solid fa-bell',
                'color' => 'text-teal-400'
            ];
        }

        // Alert 2: Assets Under Repair for more than 30 days
        $repairAssetsCount = Asset::where('condition', 'Repair')
            ->where('updated_at', '<=', Carbon::now()->subDays(30))
            ->count();
        if ($repairAssetsCount > 0) {
            $alerts[] = [
                'message' => "{$repairAssetsCount} assets are Under Repair for more than 30 days",
                'timestamp' => $this->getLatestTimestamp(
                    Asset::where('condition', 'Repair')
                        ->where('updated_at', '<=', Carbon::now()->subDays(30))
                ),
                'icon' => 'fa-solid fa-bell',
                'color' => 'text-teal-400'
            ];
        }

        // Alert 3: Employees with unreturned items
        $unreturnedCount = Asset::whereNotNull('assigned_id')
            ->whereHas('assignedEmployee', function($query) {
                $query->where('is_deleted', true);
            })
            ->count();
        if ($unreturnedCount > 0) {
            $alerts[] = [
                'message' => "{$unreturnedCount} employees have unreturned items",
                'timestamp' => $this->getLatestTimestamp(
                    Asset::whereNotNull('assigned_id')
                        ->whereHas('assignedEmployee', function($query) {
                            $query->where('is_deleted', true);
                        })
                ),
                'icon' => 'fa-solid fa-bell',
                'color' => 'text-teal-400'
            ];
        }

        return collect($alerts)->sortByDesc('timestamp')->values();
    }

    private function getLatestTimestamp($query)
    {
        $asset = $query->latest('updated_at')->first();
        return $asset ? $asset->updated_at : now();
    }

    public function render()
    {   
        // Only fetch data that changes frequently or needs to be real-time
        $total_assets = Asset::where('is_deleted', false)->get();
        $assigned_assets = Asset::where('is_deleted', false)->whereNotNull('assigned_id')->get();
        $total_employees = Employee::where('is_deleted', false)->get();
        $pending_clearances = Flag::where('flag_type', 'Pending Clearances');

        return view('livewire.dashboard-data', [
            'total_assets' => $total_assets,
            'assigned_assets' => $assigned_assets,
            'total_employees' => $total_employees,
            'pending_clearances' => $pending_clearances,
        ]);
    }

    private function noreloadNotif($type, $header, $message){
        $this->dispatch('notif', type: $type, header: $header, message: $message);
    }
}