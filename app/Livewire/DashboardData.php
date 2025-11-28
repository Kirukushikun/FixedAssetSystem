<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Asset;
use App\Models\Employee;
use Illuminate\Support\Facades\Log;

class DashboardData extends Component
{   

    public $targetFarm;

    public function setFarm($code)
    {
        $this->targetFarm = $code;
        Log::info('Target farm set to: ' . $this->targetFarm);
    }

    public $employee_id, $employee_name, $position, $farm, $department;


    protected $rules = [
        'employee_id' => 'required',
        'employee_name' => 'required',
        'position' => 'required',
        'farm' => 'required',
        'department' => 'required',
    ];

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

            // Use NORELOAD - stays on same page, table refreshes automatically
            $this->noreloadNotif('success', 'Employee Added', 'Employee ' . $this->employee_name . ' has been successfully added.');

        } catch (\Exception $e) {
            Log::error('Employee creation failed: ' . $e->getMessage());
            $this->noreloadNotif('failed', 'Creation Failed', 'Unable to add employee. Please try again.');
        }
    }

    public function render()
    {   
        // MAIN CONTAINERS
        $total_assets = Asset::where('is_deleted', false)->get();
        $assigned_assets = Asset::where('is_deleted', false)->whereNotNull('assigned_id')->get();
        $total_employees = Employee::where('is_deleted', false)->get();

        // ASSET STATUS OVERVIEW DATA =========
        // Get counts for each condition
        $conditions = [
            'good' => Asset::where('is_deleted', false)->where('condition', 'Good')->count(),
            'defective' => Asset::where('is_deleted', false)->where('condition', 'Defective')->count(),
            'repair' => Asset::where('is_deleted', false)->where('condition', 'Repair')->count(),
            'replace' => Asset::where('is_deleted', false)->where('condition', 'Replace')->count(),
        ];
        
        // Get counts for each status
        $statuses = [
            'available' => Asset::where('is_deleted', false)->where('status', 'Available')->count(),
            'issued' => Asset::where('is_deleted', false)->where('status', 'Issued')->count(),
            'transferred' => Asset::where('is_deleted', false)->where('status', 'Transferred')->count(),
            'for_disposal' => Asset::where('is_deleted', false)->where('status', 'For disposal')->count(),
            'disposed' => Asset::where('is_deleted', false)->where('status', 'Disposed')->count(),
            'lost' => Asset::where('is_deleted', false)->where('status', 'Lost')->count(),
        ];

        // Calculate totals and percentages
        $totalAssets = Asset::where('is_deleted', false)->count();
        $maxCondition = max($conditions) ?: 1;

        // Calculate percentage for each status
        $statusPercentages = [];
        foreach ($statuses as $key => $count) {
            $statusPercentages[$key] = $totalAssets > 0 ? ($count / $totalAssets) * 100 : 0;
        }

        // ========

        // FARM DISTRIBUTION DATA
        $farms = [
            'BFC' => 'BROOKSIDE FARMS',
            'BDL' => 'BROOKDALE FARMS',
            'PFC' => 'POULTRYPURE FARMS',
            'RH' => 'RH FARMS',
        ];

        $farmDistribution = [];

        foreach ($farms as $code => $name) {
            $count = Asset::where('is_deleted', false)->where('farm', $code)->count();
            $percentage = $totalAssets > 0 ? round(($count / $totalAssets) * 100, 1) : 0;
            
            $farmDistribution[] = [
                'code' => $code,
                'name' => $name,
                'count' => $count,
                'percentage' => $percentage
            ];
        }

        // ========

        return view('livewire.dashboard-data', [
            'total_assets' => $total_assets,
            'assigned_assets' => $assigned_assets,
            'total_employees' => $total_employees,
            
            'conditions' => $conditions,
            'maxCondition' => $maxCondition,
            'statuses' => $statuses,
            'statusPercentages' => $statusPercentages,
            'totalAssets' => $totalAssets,

            'farmDistribution' => $farmDistribution
        ]);
    }

    private function noreloadNotif($type, $header, $message){
        $this->dispatch('notif', type: $type, header: $header, message: $message);
    }

}