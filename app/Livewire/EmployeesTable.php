<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use App\Models\Employee;
use App\Models\Department;


class EmployeesTable extends Component
{   
    use WithPagination;
    
    protected $paginationTheme = 'tailwind';

    public $search = '';

    // ── Filters ──
    public $filterFarm       = '';
    public $filterDepartment = '';
    public $filterPosition   = '';
    public $filterFlag       = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterFarm()       { $this->resetPage(); }
    public function updatedFilterDepartment() { $this->resetPage(); }
    public function updatedFilterPosition()   { $this->resetPage(); }
    public function updatedFilterFlag()       { $this->resetPage(); }

    public function resetFilters()
    {
        $this->reset(['filterFarm', 'filterDepartment', 'filterPosition', 'filterFlag']);
        $this->resetPage();
    }

    public function goToPage($page)
    {
       $this->setPage($page);
    }

    public $target;
    public $employee_id, $employee_name, $position, $farm, $department;
    public bool $showModal = false;
    public string $modalTemplate = '';

    protected function rules()
    {
        return [
            'employee_id'   => ['required', Rule::unique('employees', 'employee_id')->ignore($this->target)],
            'employee_name' => 'required',
            'position'      => 'required',
            'farm'          => 'required',
            'department'    => 'required',
        ];
    }

    public function targetID($id)
    {   
        $this->target = $id;

        $employee = Employee::findOrFail($id);

        $this->employee_id   = $employee->employee_id;
        $this->employee_name = $employee->employee_name;
        $this->position      = $employee->position;
        $this->farm          = $employee->farm;
        $this->department    = $employee->department;
    }

    public function openCreate()
    {
        $this->clear();
        $this->modalTemplate = 'create';
        $this->showModal = true;
    }

    public function openEdit($id)
    {
        $this->targetID($id);
        $this->modalTemplate = 'edit';
        $this->showModal = true;
    }

    public function openDelete($id)
    {
        $this->targetID($id);
        $this->modalTemplate = 'delete';
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->modalTemplate = '';
        $this->clear();
    }

    public function submit()
    {
        try {
            $this->validate();

            $employeeName = $this->employee_name;

            Employee::create([
                'employee_id'   => $this->employee_id,
                'employee_name' => $employeeName,
                'position'      => $this->position,
                'farm'          => $this->farm,
                'department'    => $this->department,
            ]);

            $this->clearEmployeeCache();
            $this->audit('Added Employee: ' . $this->employee_id . ' - ' . $employeeName);
            $this->closeModal();
            $this->noreloadNotif('success', 'Employee Added', 'Employee ' . $employeeName . ' has been successfully added.');

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

            $employee->employee_id   = $this->employee_id;
            $employee->employee_name = $this->employee_name;
            $employee->position      = $this->position;
            $employee->farm          = $this->farm;
            $employee->department    = $this->department;
            $employee->save();
            $employeeName = $employee->employee_name;

            $this->clearEmployeeCache();
            $this->audit('Updated Employee: ' . $employee->employee_id . ' - ' . $employee->employee_name);
            $this->closeModal();
            $this->noreloadNotif('success', 'Employee Updated', 'Employee ' . $employeeName . ' has been successfully updated.');

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
            
            $this->clearEmployeeCache();
            $this->closeModal();
            $this->audit('Deleted Employee: ' . $employee->employee_id . ' - ' . $employeeName);    
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

    private function clearEmployeeCache()
    {
        Cache::flush();
    }

    public function render()
    {   
        $departments = Department::latest()->get()->sortBy('name');

        // Distinct positions for the filter dropdown
        $positions = Employee::where('is_deleted', false)
            ->whereNotNull('position')
            ->distinct()
            ->orderBy('position')
            ->pluck('position');

        // Cache key includes all active filters
        $cacheKey = 'employee_table_' . md5(json_encode([
            'search'           => $this->search,
            'filterFarm'       => $this->filterFarm,
            'filterDepartment' => $this->filterDepartment,
            'filterPosition'   => $this->filterPosition,
            'filterFlag'       => $this->filterFlag,
            'page'             => $this->getPage(),
        ]));

        $employees = Cache::remember($cacheKey, 600, function () {
            return Employee::where('is_deleted', false)
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('employee_id',   'like', '%' . $this->search . '%')
                          ->orWhere('employee_name', 'like', '%' . $this->search . '%')
                          ->orWhere('position',      'like', '%' . $this->search . '%')
                          ->orWhere('farm',          'like', '%' . $this->search . '%')
                          ->orWhere('department',    'like', '%' . $this->search . '%');
                    });
                })
                ->when($this->filterFarm,       fn($q) => $q->where('farm', $this->filterFarm))
                ->when($this->filterDepartment, fn($q) => $q->where('department', $this->filterDepartment))
                ->when($this->filterPosition,   fn($q) => $q->where('position', $this->filterPosition))
                ->when($this->filterFlag, function ($query) {
                    $query->whereHas('flags', fn($q) => $q->where('flag_type', $this->filterFlag));
                })
                ->with(['flags'])
                ->withCount(['assets', 'flags'])
                ->latest()
                ->paginate(10);
        });
            
        return view('livewire.employees-table', compact('employees', 'departments', 'positions'));
    }

    private function noreloadNotif($type, $header, $message)
    {
        $this->dispatch('notif', type: $type, header: $header, message: $message);
    }

    private function reloadNotif($type, $header, $message)
    {
        session()->flash('notif', [
            'type'    => $type,
            'header'  => $header,
            'message' => $message,
        ]);
    }

    private function audit($action)
    {
        \App\Models\AuditTrail::create([
            'user_id'   => Auth::id(),
            'user_name' => Auth::user()->name,
            'action'    => $action,
        ]);
    }
}