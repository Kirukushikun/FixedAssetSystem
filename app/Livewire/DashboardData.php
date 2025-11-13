<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Asset;
use App\Models\Employee;

class DashboardData extends Component
{   
    public function render()
    {   
        // MAIN CONTAINERS
        $total_assets = Asset::all();
        $assigned_assets = Asset::whereNotNull('assigned_id')->get();
        $total_employees = Employee::all();

        // ASSET STATUS OVERVIEW DATA =========
        // Get counts for each condition
        $conditions = [
            'good' => Asset::where('condition', 'Good')->count(),
            'defective' => Asset::where('condition', 'Defective')->count(),
            'repair' => Asset::where('condition', 'Repair')->count(),
            'replace' => Asset::where('condition', 'Replace')->count(),
        ];
        
        // Get counts for each status
        $statuses = [
            'available' => Asset::where('status', 'Available')->count(),
            'issued' => Asset::where('status', 'Issued')->count(),
            'transferred' => Asset::where('status', 'Transferred')->count(),
            'for_disposal' => Asset::where('status', 'For disposal')->count(),
            'disposed' => Asset::where('status', 'Disposed')->count(),
            'lost' => Asset::where('status', 'Lost')->count(),
        ];

        // Calculate totals and percentages
        $totalAssets = Asset::count();
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
            $count = Asset::where('farm', $code)->count();
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
}
