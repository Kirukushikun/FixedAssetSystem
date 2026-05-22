<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Asset;
use App\Models\Employee;
use App\Models\TransferRequest;
use App\Models\Department;
use App\Models\History;
use Illuminate\Support\Facades\Auth;

class TransferWorkspace extends Component
{
    public $transferableAssets = [];
    public $pendingRequests;
    public $approvedRequests;
    public $divisionHeadRequests;
    public $accountingRequests;
    public $departments = [];
    public $defaultTab = 'request';

    public $requestAssetId = null;
    public $requestEmployeeId = null;
    public $requestReason = '';
    public bool $showConfirmModal = false;
    public string $confirmType = '';
    public ?int $confirmId = null;
    public $isExternalTransfer = false;
    public $externalFarm = '';
    public $externalDepartment = '';

    public function mount()
    {
        $user = Auth::user();
        if ($user->hasRole('accounting')) {
            $this->defaultTab = 'accounting';
        }
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
            ->where('farm', Auth::user()->farm)
            ->with(['assignedEmployee:id,employee_name,farm,department'])
            ->get();
    }

    private function loadRequests()
    {
        $user = Auth::user();
        $this->pendingRequests = TransferRequest::where('requested_by', $user->id)
            ->whereIn('status', ['DH Approval', 'For Transfer'])
            ->with(['asset.assignedEmployee', 'requestedEmployee'])
            ->latest()
            ->get();

        $this->approvedRequests = TransferRequest::where('requested_by', $user->id)
            ->where('status', 'Transferred')
            ->with(['asset.assignedEmployee', 'requestedEmployee', 'approvedByUser'])
            ->latest()
            ->get();

        $this->divisionHeadRequests = TransferRequest::where('status', 'DH Approval')
            ->with(['asset.assignedEmployee', 'requestedEmployee', 'requestedByUser'])
            ->latest()
            ->get();

        $this->accountingRequests = TransferRequest::where('status', 'For Transfer')
            ->with(['asset.assignedEmployee', 'requestedEmployee', 'requestedByUser'])
            ->latest()
            ->get();
    }

    public function openConfirm(string $type, ?int $id = null)
    {
        $this->confirmType = $type;
        $this->confirmId = $id;
        $this->showConfirmModal = true;
    }

    public function closeConfirm()
    {
        $this->showConfirmModal = false;
        $this->confirmType = '';
        $this->confirmId = null;
    }

    public function confirmAction()
    {
        if ($this->confirmType === 'request') {
            $this->submitRequest();
            return;
        }

        if ($this->confirmType === 'approve' && $this->confirmId) {
            $this->approveRequest($this->confirmId);
            return;
        }

        if ($this->confirmType === 'complete' && $this->confirmId) {
            $this->completeTransfer($this->confirmId);
        }
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
            ? 'For Transfer'
            : 'DH Approval';

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

        if ($initialStatus === 'For Transfer') {
            $asset?->update(['status' => 'For Transfer']);
        }

        $this->reset(['requestAssetId', 'requestEmployeeId', 'requestReason', 'isExternalTransfer', 'externalFarm', 'externalDepartment']);
        $this->closeConfirm();
        $this->loadTransferableAssets();
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

        if ($request->status === 'DH Approval') {
            $request->update([
                'status' => 'For Transfer',
                'division_head_approved_by_user_id' => Auth::id(),
                'division_head_approved_by_name' => Auth::user()?->name,
                'division_head_approved_at' => now(),
            ]);

            $request->asset?->update(['status' => 'For Transfer']);
            $this->closeConfirm();
            $this->loadTransferableAssets();
            $this->loadRequests();
            $this->dispatch('notif', type: 'success', header: 'Division Head Approval Complete', message: 'Transfer request has been forwarded to Accounting for completion.');
            return;
        }
    }

    public function completeTransfer($requestId)
    {
        if (! Auth::user()?->hasPermission('transfer.complete')) {
            $this->dispatch('notif', type: 'failed', header: 'Access Denied', message: 'You do not have permission to complete transfer requests.');
            return;
        }

        $request = TransferRequest::with(['asset', 'requestedEmployee'])->findOrFail($requestId);

        if ($request->status !== 'For Transfer') {
            $this->dispatch('notif', type: 'failed', header: 'Invalid Status', message: 'Only requests marked For Transfer can be completed.');
            return;
        }

        $request->update([
            'status' => 'Transferred',
            'approved_by' => Auth::id(),
            'approved_by_name' => Auth::user()?->name,
            'approved_at' => now(),
        ]);

        if ($request->asset && $request->requestedEmployee) {
            $request->asset->update([
                'location' => null,
                'assigned_id' => $request->requestedEmployee->id,
                'assigned_name' => $request->requestedEmployee->employee_name,
                'farm' => $request->requestedEmployee->farm,
                'department' => $request->requestedEmployee->department,
                'status' => 'Transferred',
            ]);
        }

        // Record transfer in history
        if ($request->asset) {
            History::create([
                'asset_id' => $request->asset->id,
                'assignee_id' => $request->requestedEmployee->id,
                'assignee_name' => $request->requestedEmployee->employee_name,
                'status' => $request->asset->status,
                'condition' => $request->asset->condition,
                'farm' => $request->requestedEmployee->farm,
                'department' => $request->requestedEmployee->department,
                'location' => $request->requestedEmployee->location,
                'action' => 'Transfer',
            ]);
        }

        $this->closeConfirm();
        $this->loadTransferableAssets();
        $this->loadRequests();
        $this->dispatch('notif', type: 'success', header: 'Transfer Complete', message: 'Asset transfer has been completed.');
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
