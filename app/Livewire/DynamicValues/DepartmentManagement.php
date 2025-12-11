<?php

namespace App\Livewire\DynamicValues;

use Livewire\Component;
use App\Models\Department;
use Exception;

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
        try {
            $this->departments = Department::latest()->get();
        } catch (Exception $e) {
            $this->noreloadNotif('Failed', 'Load Error', 'Failed to load departments: ' . $e->getMessage());
        }
    }

    public function startEdit($id)
    {
        try {
            $dept = Department::find($id);
            
            if (!$dept) {
                $this->noreloadNotif('Failed', 'Not Found', 'Department not found.');
                return;
            }
            
            $this->editId = $id;
            $this->editName = $dept->name;
        } catch (Exception $e) {
            $this->noreloadNotif('Failed', 'Edit Error', 'Failed to load department: ' . $e->getMessage());
        }
    }

    public function saveEdit()
    {
        try {
            if (!$this->editName) {
                $this->noreloadNotif('Failed', 'Validation Error', 'Department name is required.');
                return;
            }

            Department::where('id', $this->editId)
                ->update(['name' => $this->editName]);

            $this->editId = null;
            $this->editName = '';
            $this->loadDepartments();
            
            $this->noreloadNotif('Success', 'Department Updated', 'Department has been successfully updated.');
        } catch (Exception $e) {
            $this->noreloadNotif('Failed', 'Update Error', 'Failed to update department: ' . $e->getMessage());
        }
    }

    public function cancelEdit()
    {
        $this->editId = null;
        $this->editName = '';
    }

    public function delete($id)
    {
        try {
            $department = Department::find($id);
            
            if (!$department) {
                $this->noreloadNotif('Failed', 'Not Found', 'Department not found.');
                return;
            }
            
            $department->delete();
            $this->loadDepartments();
            
            $this->noreloadNotif('Success', 'Department Deleted', 'Department has been successfully deleted.');
        } catch (Exception $e) {
            $this->noreloadNotif('Failed', 'Delete Error', 'Failed to delete department: ' . $e->getMessage());
        }
    }

    public function add()
    {
        try {
            if (!$this->newDepartment) {
                $this->noreloadNotif('Failed', 'Validation Error', 'Department name is required.');
                return;
            }

            Department::create(['name' => $this->newDepartment]);
            $this->newDepartment = '';
            $this->loadDepartments();
            
            $this->noreloadNotif('Success', 'Department Added', 'Department has been successfully created.');
        } catch (Exception $e) {
            $this->noreloadNotif('Failed', 'Add Error', 'Failed to add department: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.dynamic-values.department-management');
    }

    private function noreloadNotif($type, $header, $message)
    {
        $this->dispatch('notif', type: $type, header: $header, message: $message);
    }

    private function reloadNotif($type, $header, $message)
    {
        session()->flash('notif', [
            'type' => $type,
            'header' => $header,
            'message' => $message
        ]);
    }
}