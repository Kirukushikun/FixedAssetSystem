<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use App\Models\Employee;

class EmployeesTable extends Component
{   
    public $target;
    public $employee_id, $employee_name, $position, $farm, $department;

    protected $rules = [
        'employee_id' => 'required',
        'employee_name' => 'required',
        'position' => 'required',
        'farm' => 'required',
        'department' => 'required',
    ];

    public function targetID($id)
    {   
        $this->target = $id;

        $employee = Employee::find($id);

        $this->employee_id = $employee->employee_id;
        $this->employee_name = $employee->employee_name;
        $this->position = $employee->position;
        $this->farm = $employee->farm;
        $this->department = $employee->department;
    }

    public function submit()
    {
        Employee::create([
            'employee_id' => $this->employee_id,
            'employee_name' => $this->employee_name,
            'position' => $this->position,
            'farm' => $this->farm,
            'department' => $this->department,
        ]);

        $this->clear();
    }

    public function update()
    {
        $employee = Employee::find($this->target);

        $employee->employee_id = $this->employee_id;
        $employee->employee_name = $this->employee_name;
        $employee->position = $this->position;
        $employee->farm = $this->farm;
        $employee->department = $this->department;
        $employee->save();

        $this->clear();
    }

    public function delete()
    {
        $employee = Employee::find($this->target);
        $employee->is_deleted = true;
        $employee->save();
        
        $this->clear();
    }

    public function clear()
    {   
        $this->reset(['target', 'employee_id', 'employee_name', 'position', 'farm', 'department']);
    }

    public function render()
    {   
        $employees = Employee::where('is_deleted', false)
            ->latest()
            ->get();
        return view('livewire.employees-table', compact('employees'));
    }
}
