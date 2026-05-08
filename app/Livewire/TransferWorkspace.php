<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Asset;
use App\Models\Employee;
use App\Models\TransferRequest;
use Illuminate\Support\Facades\Auth;

class TransferWorkspace extends Component
{
    public $transferableAssets = [];
    public $pendingRequests;
    public $approvedRequests;

    public $requestAssetId = null;
    public $requestEmployeeId = null;
    public $requestReason = '';
    public $showConfirmModal = false;

    public function mount()
    {
        $this->loadTransferableAssets();
        $this->loadRequests();
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
            ->where('status', 'pending')
            ->with(['asset', 'requestedEmployee'])
            ->latest()
            ->get();

        $this->approvedRequests = TransferRequest::where('requested_by', $user->id)
            ->where('status', 'approved')
            ->with(['asset', 'requestedEmployee', 'approvedByUser'])
            ->latest()
            ->get();
    }

    public function submitRequest()
    {
        $this->validate([
            'requestAssetId' => 'required|exists:assets,id',
            'requestEmployeeId' => 'required|exists:employees,id',
            'requestReason' => 'required|string|max:500',
        ]);

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
            'status' => 'pending',
        ]);

        $this->reset(['requestAssetId', 'requestEmployeeId', 'requestReason', 'showConfirmModal']);
        $this->loadRequests();
        $this->dispatch('notif', type: 'success', header: 'Success', message: 'Transfer request submitted successfully.');
    }

    public function render()
    {
        return view('livewire.transfer-workspace');
    }
}
