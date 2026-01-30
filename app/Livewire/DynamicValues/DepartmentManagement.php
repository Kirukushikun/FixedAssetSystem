<?php

namespace App\Livewire\DynamicValues;

use Livewire\Component;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
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
            $this->noreloadNotif('failed', 'Load Error', 'Failed to load departments: ' . $e->getMessage());
        }
    }

    public function startEdit($id)
    {
        try {
            $dept = Department::find($id);
            
            if (!$dept) {
                $this->noreloadNotif('failed', 'Not Found', 'Department not found.');
                return;
            }
            
            $this->editId = $id;
            $this->editName = $dept->name;
        } catch (Exception $e) {
            $this->noreloadNotif('failed', 'Edit Error', 'Failed to load department: ' . $e->getMessage());
        }
    }

    public function saveEdit()
    {
        try {
            if (!$this->editName) {
                $this->noreloadNotif('failed', 'Validation Error', 'Department name is required.');
                return;
            }

            // Check if any assets are using this department
            $assetCount = \App\Models\Asset::where('department', $this->editName)->count();
            
            if ($assetCount > 0) {
                $this->noreloadNotif('failed', 'Cannot Update', "Cannot update '{$this->editName}'. It is being used by {$assetCount} asset(s).");
                return;
            }

            $department = Department::find($this->editId);
            $oldName = $department->name;

            Department::where('id', $this->editId)
                ->update(['name' => $this->editName]);

            $this->editId = null;
            $this->editName = '';
            $this->loadDepartments();
            
            $this->audit("Updated department from '{$oldName}' to '{$this->editName}'");
            $this->noreloadNotif('success', 'Department Updated', 'Department has been successfully updated.');
        } catch (Exception $e) {
            $this->noreloadNotif('failed', 'Update Error', 'Failed to update department: ' . $e->getMessage());
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
                $this->noreloadNotif('failed', 'Not Found', 'Department not found.');
                return;
            }

            // Check if any assets are using this department
            $assetCount = \App\Models\Asset::where('department', $department->name)->count();
            if ($assetCount > 0) {
                $this->noreloadNotif('failed', 'Cannot Delete', "Cannot delete '{$department->name}'. It is being used by {$assetCount} asset(s).");
                return;
            }
            
            $departmentName = $department->name;
            $department->delete();
            $this->loadDepartments();
            
            $this->audit("Deleted department: {$departmentName}");
            $this->noreloadNotif('success', 'Department Deleted', 'Department has been successfully deleted.');
        } catch (Exception $e) {
            $this->noreloadNotif('failed', 'Delete Error', 'Failed to delete department: ' . $e->getMessage());
        }
    }

    public function add()
    {
        try {
            if (!$this->newDepartment) {
                $this->noreloadNotif('failed', 'Validation Error', 'Department name is required.');
                return;
            }

            Department::create(['name' => $this->newDepartment]);
            
            $this->audit("Created department: {$this->newDepartment}");
            
            $this->newDepartment = '';
            $this->loadDepartments();
            
            $this->noreloadNotif('success', 'Department Added', 'Department has been successfully created.');
        } catch (Exception $e) {
            $this->noreloadNotif('failed', 'Add Error', 'Failed to add department: ' . $e->getMessage());
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

    private function audit($action)
    {
        try {
            \App\Models\AuditTrail::create([
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'action' => $action,
            ]);
        } catch (Exception $e) {
            // Log error but don't break the flow
            \Log::error('Audit trail error: ' . $e->getMessage());
        }
    }
}