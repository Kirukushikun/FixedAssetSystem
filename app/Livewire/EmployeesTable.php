<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use App\Models\Employee;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class EmployeesTable extends Component
{   
    use WithPagination;

    use WithPagination;
    
    protected $paginationTheme = 'tailwind';

    public $search = '';

    public function goToPage($page)
    {
       $this->setPage($page);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

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
        try {
            $this->validate();

            Employee::create([
                'employee_id' => $this->employee_id,
                'employee_name' => $this->employee_name,
                'position' => $this->position,
                'farm' => $this->farm,
                'department' => $this->department,
            ]);

            $this->clear();

            // Audit Trail
            $this->audit('Added Employee: ' . $this->employee_id . ' - ' . $this->employee_name);

            // Use NORELOAD - stays on same page, table refreshes automatically
            $this->noreloadNotif('success', 'Employee Added', 'Employee ' . $this->employee_name . ' has been successfully added.');

        } catch (\Exception $e) {
            Log::error('Employee creation failed: ' . $e->getMessage());
            $this->noreloadNotif('failed', 'Creation Failed', 'Unable to add employee. Please try again.');
        }
    }

    public function update()
    {
        try {
            $this->validate();

            $employee = Employee::find($this->target);

            if (!$employee) {
                $this->noreloadNotif('failed', 'Update Failed', 'Employee not found.');
                return;
            }

            $employee->employee_id = $this->employee_id;
            $employee->employee_name = $this->employee_name;
            $employee->position = $this->position;
            $employee->farm = $this->farm;
            $employee->department = $this->department;
            $employee->save();

            $this->clear();

            // Audit Trail
            $this->audit('Updated Employee: ' . $employee->employee_id . ' - ' . $employee->employee_name);

            // Use NORELOAD - stays on same page, table refreshes automatically
            $this->noreloadNotif('success', 'Employee Updated', 'Employee ' . $this->employee_name . ' has been successfully updated.');

        } catch (\Exception $e) {
            Log::error('Employee update failed: ' . $e->getMessage());
            $this->noreloadNotif('failed', 'Update Failed', 'Unable to update employee. Please try again.');
        }
    }

    public function delete()
    {
        try {
            $employee = Employee::find($this->target);

            if (!$employee) {
                $this->noreloadNotif('failed', 'Deletion Failed', 'Employee not found.');
                return;
            }

            $employeeName = $employee->employee_name;
            
            $employee->is_deleted = true;
            $employee->save();
            
            $this->clear();

            // Audit Trail
            $this->audit('Deleted Employee: ' . $employee->employee_id . ' - ' . $employeeName);    

            // Use NORELOAD - stays on same page, table refreshes automatically
            $this->noreloadNotif('success', 'Employee Deleted', 'Employee ' . $employeeName . ' has been successfully deleted.');

        } catch (\Exception $e) {
            Log::error('Employee deletion failed: ' . $e->getMessage());
            $this->noreloadNotif('failed', 'Deletion Failed', 'Unable to delete employee. Please try again.');
        }
    }

    public function clear()
    {   
        $this->reset(['target', 'employee_id', 'employee_name', 'position', 'farm', 'department']);
    }


    public function render()
    {   
        $employees = Employee::where('is_deleted', false)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('employee_id', 'like', '%' . $this->search . '%')
                        ->orWhere('employee_name', 'like', '%' . $this->search . '%')
                        ->orWhere('position', 'like', '%' . $this->search . '%')
                        ->orWhere('farm', 'like', '%' . $this->search . '%')
                        ->orWhere('department', 'like', '%' . $this->search . '%');
                });
            })
            ->with(['flags'])
            ->withCount(['assets', 'flags'])
            ->latest()
            ->paginate(10);
            
        return view('livewire.employees-table', compact('employees'));
    }

    private function noreloadNotif($type, $header, $message){
        $this->dispatch('notif', type: $type, header: $header, message: $message);
    }

    private function reloadNotif($type, $header, $message){
        session()->flash('notif', [
            'type' => $type,
            'header' => $header,
            'message' => $message
        ]);
    }

    private function audit($action){
        $user = auth()->user();
        \App\Models\AuditTrail::create([
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'action' => $action,
        ]);
    }
}