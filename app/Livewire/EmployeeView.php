<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Asset;
use App\Models\Employee;
use App\Models\Flag;
use Illuminate\Support\Facades\Log;

class EmployeeView extends Component
{   
    public $employee;

    public $flag_type, $asset;

    public function mount($targetID){
        $this->employee = Employee::find($targetID);
    }

    public function submitFlag(){
        
        Flag::create([
            'target_id' => $this->employee->id,
            'flag_type' => $this->flag_type,
            'asset' => $this->asset
        ]);

    }

    public function render()
    {   
        $assets = Asset::where('assigned_id', $this->employee->id)->get();
        $flags = Flag::where('target_id', $this->employee->id)->get();

        return view('livewire.employee-view', compact('assets', 'flags'));
    }
}
