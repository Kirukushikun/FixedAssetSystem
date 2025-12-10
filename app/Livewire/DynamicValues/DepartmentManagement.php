<?php

namespace App\Livewire\DynamicValues;

use Livewire\Component;
use App\Models\Department;

class DepartmentManagement extends Component
{
        public $departments = [];
    public $newDepartment = '';
    public $editId = null;
    public $editName = '';

    public function mount()
    {
        $this->loadDepartments();
    }

    public function loadDepartments()
    {
        $this->departments = Department::latest()->get();
    }

    public function startEdit($id)
    {
        $dept = Department::find($id);
        $this->editId = $id;
        $this->editName = $dept->name;
    }

    public function saveEdit()
    {
        Department::where('id', $this->editId)
            ->update(['name' => $this->editName]);

        $this->editId = null;
        $this->editName = '';
        $this->loadDepartments();
    }

    public function cancelEdit()
    {
        $this->editId = null;
        $this->editName = '';
    }

    public function delete($id)
    {
        Department::where('id', $id)->delete();
        $this->loadDepartments();
    }

    public function add()
    {
        if (!$this->newDepartment) return;

        Department::create(['name' => $this->newDepartment]);
        $this->newDepartment = '';
        $this->loadDepartments();
    }

    public function render()
    {
        return view('livewire.dynamic-values.department-management');
    }
}
