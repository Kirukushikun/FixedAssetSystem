<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Asset;
use App\Models\Employee;
use App\Models\TransferRequest;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;

class TransferWorkspace extends Component
{
    public $transferableAssets = [];
    public $pendingRequests;
    public $approvedRequests;
    public $employees = [];
    public $departments = [];

    public $requestAssetId = null;
    public $requestEmployeeId = null;
    public $requestReason = '';
    public $showConfirmModal = false;
    public $isExternalTransfer = false;
    public $externalFarm = '';
    public $externalDepartment = '';

    public function mount()
    {
        $this->loadTransferableAssets();
        $this->loadRequests();
        $this->loadEmployees();
        $this->departments = Department::all();
    }

    private function loadEmployees()
    {
        $user = Auth::user();
        
        if ($this->isExternalTransfer && $this->externalFarm && $this->externalDepartment) {
            // External transfer: filter by selected farm and department
            $this->employees = Employee::where('farm', $this->externalFarm)
                ->where('department', $this->externalDepartment)
                ->get();
        } elseif ($this->isExternalTransfer && $this->externalFarm) {
            // External transfer: filter by selected farm only
            $this->employees = Employee::where('farm', $this->externalFarm)->get();
        } else {
            // Internal transfer: show all employees in user's farm
            $this->employees = Employee::where('farm', $user->farm)->get();
        }
    }

    public function updatedIsExternalTransfer()
    {
        $this->loadEmployees();
    }

    public function updatedExternalFarm()
    {
        $this->loadEmployees();
    }

    public function updatedExternalDepartment()
    {
        $this->loadEmployees();
    }

    private function loadTransferableAssets()
    {
        $this->transferableAssets = Asset::where('is_deleted', false)
            ->where('is_archived', false)
            ->whereNotNull('assigned_id')
            ->where('status', '!=', 'Disposed')
            ->with(['assignedEmployee:id,employee_name,farm,department'])
            ->get();
    }

    private function loadRequests()
    {
        $user = Auth::user();
        $this->pendingRequests = TransferRequest::where('requested_by', $user->id)
            ->where('status', 'Pending Division Head Approval')
            ->with(['asset', 'requestedEmployee'])
            ->latest()
            ->get();

        $this->approvedRequests = TransferRequest::where('requested_by', $user->id)
            ->where('status', 'Approved')
            ->with(['asset', 'requestedEmployee', 'approvedByUser'])
            ->latest()
            ->get();
    }

    public function submitRequest()
    {
        $rules = [
            'requestAssetId' => 'required|exists:assets,id',
            'requestEmployeeId' => 'required|exists:employees,id',
            'requestReason' => 'required|string|max:500',
        ];

        if ($this->isExternalTransfer) {
            if ($this->externalFarm) {
                $rules['externalDepartment'] = 'required';
            }
            if ($this->externalDepartment) {
                $rules['externalFarm'] = 'required';
            }
        }

        $this->validate($rules);

        $asset = Asset::find($this->requestAssetId);
        $employee = Employee::find($this->requestEmployeeId);
        $user = Auth::user();

        TransferRequest::create([
            'asset_id' => $this->requestAssetId,
            'requested_by' => $user->id,
            'requested_by_name' => $user->name,
            'requested_employee_id' => $this->requestEmployeeId,
            'requested_employee_name' => $employee->employee_name,
            'reason' => $this->requestReason,
            'status' => 'Pending Division Head Approval',
            'is_external' => $this->isExternalTransfer,
            'external_farm' => $this->isExternalTransfer ? $this->externalFarm : null,
            'external_department' => $this->isExternalTransfer ? $this->externalDepartment : null,
        ]);

        $this->reset(['requestAssetId', 'requestEmployeeId', 'requestReason', 'showConfirmModal', 'isExternalTransfer', 'externalFarm', 'externalDepartment']);
        $this->loadEmployees();
        $this->loadRequests();
        $this->dispatch('notif', type: 'success', header: 'Success', message: 'Transfer request submitted successfully.');
    }

    public function render()
    {
        return view('livewire.transfer-workspace');
    }
}
