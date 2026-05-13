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
    public $divisionHeadRequests;
    public $vpRequests;
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
        $this->departments = Department::all();
    }

    private function employeeQuery()
    {
        $user = Auth::user();

        $query = Employee::where('is_deleted', false);

        if (! $this->isExternalTransfer) {
            return $query->where('farm', $user->farm);
        }

        return $query
            ->when($this->externalFarm, fn ($employeeQuery) => $employeeQuery->where('farm', $this->externalFarm))
            ->when($this->externalDepartment, fn ($employeeQuery) => $employeeQuery->where('department', $this->externalDepartment));
    }

    public function updatedIsExternalTransfer()
    {
        $this->requestEmployeeId = null;
        $this->externalFarm = '';
        $this->externalDepartment = '';
    }

    public function updatedExternalFarm()
    {
        $this->requestEmployeeId = null;
        $this->externalDepartment = '';
    }

    public function updatedExternalDepartment()
    {
        $this->requestEmployeeId = null;
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

        $this->divisionHeadRequests = TransferRequest::where('status', 'Pending Division Head Approval')
            ->with(['asset', 'requestedEmployee', 'requestedByUser'])
            ->latest()
            ->get();

        $this->vpRequests = TransferRequest::where('status', 'Pending VP Approval')
            ->with(['asset', 'requestedEmployee', 'requestedByUser'])
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
            $rules['externalFarm'] = 'required';
            $rules['externalDepartment'] = 'required';
        }

        $this->validate($rules);

        $asset = Asset::find($this->requestAssetId);
        $employee = $this->employeeQuery()
            ->where('id', $this->requestEmployeeId)
            ->first();

        if (! $employee) {
            $this->requestEmployeeId = null;
            $this->dispatch('notif', type: 'failed', header: 'Invalid Transfer To', message: 'Please select an employee from the current farm and department filter.');
            return;
        }

        $user = Auth::user();
        $initialStatus = $user?->hasRole('division_head')
            ? 'Pending VP Approval'
            : 'Pending Division Head Approval';

        TransferRequest::create([
            'asset_id' => $this->requestAssetId,
            'requested_by' => $user->id,
            'requested_by_name' => $user->name,
            'requested_employee_id' => $this->requestEmployeeId,
            'requested_employee_name' => $employee->employee_name,
            'reason' => $this->requestReason,
            'status' => $initialStatus,
            'is_external' => $this->isExternalTransfer,
            'external_farm' => $this->isExternalTransfer ? $this->externalFarm : null,
            'external_department' => $this->isExternalTransfer ? $this->externalDepartment : null,
        ]);

        $this->reset(['requestAssetId', 'requestEmployeeId', 'requestReason', 'showConfirmModal', 'isExternalTransfer', 'externalFarm', 'externalDepartment']);
        $this->loadRequests();
        $this->dispatch('notif', type: 'success', header: 'Success', message: 'Transfer request submitted successfully.');
    }

    public function approveRequest($requestId)
    {
        if (! Auth::user()?->hasPermission('transfer.approve')) {
            $this->dispatch('notif', type: 'failed', header: 'Access Denied', message: 'You do not have permission to approve transfer requests.');
            return;
        }

        $request = TransferRequest::with(['asset', 'requestedEmployee'])->findOrFail($requestId);

        if ($request->status === 'Pending Division Head Approval') {
            $request->update([
                'status' => 'Pending VP Approval',
                'division_head_approved_by_user_id' => Auth::id(),
                'division_head_approved_by_name' => Auth::user()?->name,
                'division_head_approved_at' => now(),
            ]);

            $this->loadRequests();
            $this->dispatch('notif', type: 'success', header: 'Division Head Approval Complete', message: 'Transfer request has been forwarded to VP for approval.');
            return;
        }

        if ($request->status === 'Pending VP Approval') {
            $request->update([
                'status' => 'Approved',
                'approved_by' => Auth::id(),
                'approved_by_name' => Auth::user()?->name,
                'approved_at' => now(),
            ]);

            if ($request->asset && $request->requestedEmployee) {
                $request->asset->update([
                    'assigned_id' => $request->requestedEmployee->id,
                    'assigned_name' => $request->requestedEmployee->employee_name,
                    'farm' => $request->requestedEmployee->farm,
                    'department' => $request->requestedEmployee->department,
                    'status' => 'Transferred',
                ]);
            }

            $this->loadTransferableAssets();
            $this->loadRequests();
            $this->dispatch('notif', type: 'success', header: 'VP Approval Complete', message: 'Asset transfer has been approved.');
        }
    }

    public function render()
    {
        return view('livewire.transfer-workspace', [
            'employees' => $this->employeeQuery()
                ->orderBy('employee_name')
                ->get(),
        ]);
    }
}
