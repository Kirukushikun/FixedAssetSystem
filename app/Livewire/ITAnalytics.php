<?php

namespace App\Livewire;

use Livewire\Component;

class ITAnalytics extends Component
{
    public function render()
    {
        // Static metrics for now - will be enhanced with OpenRouter later
        $metrics = [
            'total_assets' => 0,
            'utilization_rate' => '0%',
            'avg_lifecycle_cost' => '₱0',
            'department_performance' => [],
            'farm_performance' => [],
        ];

        return view('livewire.i-t-analytics', compact('metrics'));
    }
}
