<?php

namespace App\Livewire;

use App\Models\Asset;
use App\Models\Category;
use App\Models\DisposalRequest;
use App\Models\History;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;

class DisposalWorkspace extends Component
{
    use WithFileUploads;

    public $tab = 'request';

    // Form fields
    public $requestAssetId = '';
    public $reason = '';
    public $attachment;

    // Modal state — Livewire-owned so re-renders don't wipe it
    public $showConfirmModal = false;
    public $confirmType = '';
    public $confirmId = null;

    public array $selectedDisposals = [];
    public bool  $selectAllDisposals = false;

    public $categoryCodeImage;

    protected $rules = [
        'requestAssetId' => 'required',
        'reason'         => 'required|string|max:2000',
        'attachment'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
    ];

    public function mount()
    {
        $this->categoryCodeImage = Category::all()->keyBy('code');
    }

    public function setTab($tab)
    {
        $this->tab = $tab;
    }

    // ── Modal helpers ──────────────────────────────────────────

    public function openConfirm($type, $id = null)
    {
        $this->confirmType = $type;
        $this->confirmId   = $id;
        $this->showConfirmModal = true;
    }

    public function closeConfirm()
    {
        $this->showConfirmModal = false;
        $this->confirmType = '';
        $this->confirmId   = null;
    }

    public function updatedSelectAllDisposals(bool $value): void
    {
        // Sync selectedDisposals with all VP-approved IDs in the current queue
        if ($value) {
            $user = Auth::user();
            $farmScope = fn($q) => $q->when(!$user->is_admin, fn($q) => $q->where('requester_farm', $user->farm));
            $this->selectedDisposals = DisposalRequest::where('status', 'VP Approved')
                ->tap($farmScope)
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
        } else {
            $this->selectedDisposals = [];
        }
    }

    public function confirmAction()
    {
        match ($this->confirmType) {
            'request'      => $this->submitRequest(),
            'approve'      => $this->approveRequest($this->confirmId),
            'dispose'      => $this->markDisposed($this->confirmId),
            'reject'       => $this->rejectRequest($this->confirmId),
            'bulk_dispose' => $this->bulkDispose(),
            default        => null,
        };

        $this->closeConfirm();
    }

    // ── Actions ────────────────────────────────────────────────

    public function submitRequest()
    {
        if (!Auth::user()?->hasPermission('disposal.request')) {
            $this->dispatch('notif', type: 'failed', header: 'Access Denied', message: 'You do not have permission to request disposal.');
            return;
        }

        $this->validate();

        try {
            $asset = Asset::findOrFail($this->requestAssetId);

            $existing = DisposalRequest::where('asset_id', $asset->id)
                ->whereIn('status', ['Pending Division Head Approval', 'Pending VP Approval', 'VP Approved'])
                ->first();

            if ($existing) {
                $this->dispatch('notif', type: 'failed', header: 'Duplicate Request', message: 'This asset already has an active disposal request.');
                return;
            }

            $attachmentPath = null;
            $attachmentName = null;

            if ($this->attachment) {
                $attachmentPath = $this->attachment->store('disposal-requests', 'public');
                $attachmentName = $this->attachment->getClientOriginalName();
            }

            DisposalRequest::create([
                'asset_id'              => $asset->id,
                'requested_by_user_id'  => Auth::id(),
                'requested_by_name'     => Auth::user()?->name,
                'requester_farm'        => Auth::user()?->farm,
                'requester_department'  => Auth::user()?->department,
                'reason'                => $this->reason,
                'attachment_path'       => $attachmentPath,
                'attachment_name'       => $attachmentName,
                'status'                => Auth::user()->hasRole('accounting')
                                            ? 'Pending VP Approval'
                                            : 'Pending Division Head Approval',
            ]);

            $this->reset(['requestAssetId', 'reason', 'attachment']);

            $approvalStage = Auth::user()->hasRole('accounting') ? 'VP' : 'Division Head';
            $permission    = Auth::user()->hasRole('accounting') ? 'disposal.vp_approve' : 'disposal.approve';
            NotificationService::notifyByPermission(
                $permission,
                'disposal',
                'New Disposal Request',
                "{$asset->ref_id} — {$asset->brand} {$asset->model} submitted by " . Auth::user()->name,
                '/disposal-workspace',
                Auth::id()
            );

            $this->dispatch('notif', type: 'success', header: 'Request Submitted', message: "Disposal request has been submitted for {$approvalStage} approval.");

        } catch (\Exception $e) {
            Log::error('Disposal request failed', [
                'error'    => $e->getMessage(),
                'asset_id' => $this->requestAssetId,
                'user_id'  => Auth::id(),
            ]);
            $this->dispatch('notif', type: 'failed', header: 'Request Failed', message: 'Unable to submit disposal request. Please try again.');
        }
    }

    public function approveRequest($requestId)
    {
        try {
            $request = DisposalRequest::with('asset')->findOrFail($requestId);

            if ($request->status === 'Pending Division Head Approval') {
                if (!Auth::user()?->hasPermission('disposal.approve')) {
                    $this->dispatch('notif', type: 'failed', header: 'Access Denied', message: 'You do not have permission to approve at this stage.');
                    return;
                }
                $request->update([
                    'status'                            => 'Pending VP Approval',
                    'division_head_approved_by_user_id' => Auth::id(),
                    'division_head_approved_by_name'    => Auth::user()?->name,
                    'division_head_approved_at'         => now(),
                ]);
                NotificationService::notifyByPermission(
                    'disposal.vp_approve',
                    'disposal',
                    'Disposal Awaiting VP Approval',
                    "{$request->asset?->ref_id} approved by Division Head — ready for your review.",
                    '/disposal-workspace',
                    Auth::id()
                );
                $this->dispatch('notif', type: 'success', header: 'Approved', message: 'Request forwarded to VP for approval.');

            } elseif ($request->status === 'Pending VP Approval') {
                if (!Auth::user()?->hasPermission('disposal.vp_approve')) {
                    $this->dispatch('notif', type: 'failed', header: 'Access Denied', message: 'You do not have permission to approve at this stage.');
                    return;
                }
                $request->update([
                    'status'                 => 'VP Approved',
                    'vp_approved_by_user_id' => Auth::id(),
                    'vp_approved_by_name'    => Auth::user()?->name,
                    'vp_approved_at'         => now(),
                ]);
                $request->asset?->update(['status' => 'For Disposal']);
                NotificationService::notifyByPermission(
                    'disposal.dispose',
                    'disposal',
                    'Asset Ready for Disposal',
                    "{$request->asset?->ref_id} has been VP-approved and is ready for disposal tagging.",
                    '/disposal-workspace',
                    Auth::id()
                );
                $this->dispatch('notif', type: 'success', header: 'VP Approval Complete', message: 'Asset is now marked as For Disposal.');
            }

        } catch (\Exception $e) {
            Log::error('Disposal approval failed', ['error' => $e->getMessage(), 'request_id' => $requestId]);
            $this->dispatch('notif', type: 'failed', header: 'Approval Failed', message: 'Unable to approve disposal request.');
        }
    }

    public function rejectRequest($requestId)
    {
        if (!Auth::user()?->hasPermission('disposal.approve') && !Auth::user()?->hasPermission('disposal.vp_approve')) {
            $this->dispatch('notif', type: 'failed', header: 'Access Denied', message: 'You do not have permission to reject disposal requests.');
            return;
        }

        try {
            $request = DisposalRequest::with('asset')->findOrFail($requestId);
            $request->update(['status' => 'Rejected']);
            $request->asset?->update(['status' => 'Available']);
            $this->dispatch('notif', type: 'success', header: 'Request Rejected', message: 'Disposal request has been rejected.');
        } catch (\Exception $e) {
            Log::error('Disposal rejection failed', ['error' => $e->getMessage(), 'request_id' => $requestId]);
            $this->dispatch('notif', type: 'failed', header: 'Rejection Failed', message: 'Unable to reject disposal request.');
        }
    }

    public function markDisposed($requestId)
    {
        if (!Auth::user()?->hasPermission('disposal.dispose')) {
            $this->dispatch('notif', type: 'failed', header: 'Access Denied', message: 'You do not have permission to mark assets as disposed.');
            return;
        }

        try {
            $request = DisposalRequest::with('asset')->findOrFail($requestId);

            if ($request->status !== 'VP Approved') {
                $this->dispatch('notif', type: 'failed', header: 'Not Allowed', message: 'This asset can only be disposed after VP approval.');
                return;
            }

            $asset = $request->asset;

            if (!$asset) {
                $this->dispatch('notif', type: 'failed', header: 'Error', message: 'Asset not found.');
                return;
            }

            // Record last assignee in history before clearing
            if ($asset->assigned_id) {
                \App\Models\History::create([
                    'asset_id'      => $asset->id,
                    'assignee_id'   => $asset->assigned_id,
                    'assignee_name' => $asset->assigned_name,
                    'status'        => 'Disposed',
                    'condition'     => 'Defective',
                    'farm'          => $asset->farm,
                    'department'    => $asset->department,
                    'location'      => $asset->location,
                    'action'        => 'Last Assignee Before Disposal',
                ]);
            }

            // Clear assignment and mark asset as disposed
            $asset->update([
                'status'        => 'Disposed',
                'condition'     => 'Defective',
                'assigned_id'   => null,
                'assigned_name' => null,
                'farm'          => null,
                'department'    => null,
                'location'      => null,
            ]);

            // Update the disposal request record only
            $request->update([
                'status'                         => 'Disposed',
                'accounting_disposed_by_user_id' => Auth::id(),
                'accounting_disposed_by_name'    => Auth::user()?->name,
                'disposed_at'                    => now(),
            ]);

            $this->dispatch('notif', type: 'success', header: 'Disposed', message: 'Asset has been officially marked as disposed.');

        } catch (\Exception $e) {
            Log::error('Mark disposed failed', ['error' => $e->getMessage(), 'request_id' => $requestId]);
            $this->dispatch('notif', type: 'failed', header: 'Update Failed', message: 'Unable to mark asset as disposed.');
        }
    }

    public function bulkDispose(): void
    {
        if (!Auth::user()?->hasPermission('disposal.dispose')) {
            $this->dispatch('notif', type: 'failed', header: 'Access Denied', message: 'You do not have permission to mark assets as disposed.');
            return;
        }

        if (empty($this->selectedDisposals)) {
            $this->dispatch('notif', type: 'failed', header: 'Nothing Selected', message: 'Please select at least one request to dispose.');
            return;
        }

        $count = 0;

        foreach ($this->selectedDisposals as $requestId) {
            try {
                $request = DisposalRequest::with('asset')->find($requestId);

                if (!$request || $request->status !== 'VP Approved') continue;

                $asset = $request->asset;

                if ($asset?->assigned_id) {
                    \App\Models\History::create([
                        'asset_id'      => $asset->id,
                        'assignee_id'   => $asset->assigned_id,
                        'assignee_name' => $asset->assigned_name,
                        'status'        => 'Disposed',
                        'condition'     => 'Defective',
                        'farm'          => $asset->farm,
                        'department'    => $asset->department,
                        'location'      => $asset->location,
                        'action'        => 'Last Assignee Before Disposal',
                    ]);
                }

                $asset?->update([
                    'status'        => 'Disposed',
                    'condition'     => 'Defective',
                    'assigned_id'   => null,
                    'assigned_name' => null,
                    'farm'          => null,
                    'department'    => null,
                    'location'      => null,
                ]);

                $request->update([
                    'status'                         => 'Disposed',
                    'accounting_disposed_by_user_id' => Auth::id(),
                    'accounting_disposed_by_name'    => Auth::user()?->name,
                    'disposed_at'                    => now(),
                ]);

                $count++;
            } catch (\Exception $e) {
                Log::error('Bulk dispose item failed', ['request_id' => $requestId, 'error' => $e->getMessage()]);
            }
        }

        $this->selectedDisposals = [];
        $this->selectAllDisposals = false;

        $this->dispatch('notif', type: 'success', header: 'Bulk Dispose Complete', message: "{$count} asset(s) have been marked as disposed.");
    }

    public function render()
    {
        $assetsWithActiveRequests = DisposalRequest::whereIn('status', [
            'Pending Division Head Approval',
            'Pending VP Approval',
            'VP Approved',
        ])->pluck('asset_id')->toArray();

        $requestableAssets = Asset::where('is_deleted', false)
            ->whereNotIn('status', ['Disposed', 'For Disposal'])
            ->whereNotIn('id', $assetsWithActiveRequests)
            ->where('farm', Auth::user()->farm)           // NEW
            // ->where('department', Auth::user()->department) // NEW
            ->orderBy('ref_id')
            ->get();

        $user = Auth::user();
        $farmScope = fn($q) => $q->when(!$user->is_admin, fn($q) => $q->where('requester_farm', $user->farm));

        $divisionHeadRequests = DisposalRequest::with('asset')
            ->where('status', 'Pending Division Head Approval')
            ->tap($farmScope)->latest()->get();

        $vpRequests = DisposalRequest::with('asset')
            ->where('status', 'Pending VP Approval')
            ->tap($farmScope)->latest()->get();

        $accountingRequests = DisposalRequest::with('asset')
            ->where('status', 'VP Approved')
            ->tap($farmScope)->latest()->get();

        $history = DisposalRequest::with('asset')
            ->tap($farmScope)->latest()->get();

        return view('livewire.disposal-workspace', compact(
            'requestableAssets',
            'divisionHeadRequests',
            'vpRequests',
            'accountingRequests',
            'history'
        ));
    }
}