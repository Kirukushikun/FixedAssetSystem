<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Asset;
use App\Models\Employee;

class EmployeeView extends Component
{   
    public $employee;

    public function mount($targetID){
        $this->employee = Employee::find($targetID);
    }
    public function render()
    {   
        $assets = Asset::where('assigned_id', $this->employee->id)->get();
        return view('livewire.employee-view', compact('assets'));
    }
}
