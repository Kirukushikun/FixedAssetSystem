<?php

namespace App\Livewire;

use App\Models\Asset;
use App\Models\Category;
use App\Models\DisposalRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class DisposalWorkspace extends Component
{
    use WithFileUploads;

    public $tab = 'request';
    public $requestAssetId = '';
    public $reason = '';
    public $attachment;
    public $categoryCodeImage;

    protected $rules = [
        'requestAssetId' => 'required',
        'reason' => 'required|string|max:2000',
        'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
    ];

    public function mount()
    {
        $this->categoryCodeImage = Category::all()->keyBy('code');
    }

    public function setTab($tab)
    {
        $this->tab = $tab;
    }

    public function submitRequest()
    {
        $this->validate();

        try {
            $asset = Asset::findOrFail($this->requestAssetId);

            // Check for existing pending request
            $existing = DisposalRequest::where('asset_id', $asset->id)
                ->whereIn('status', ['Pending VP Approval', 'VP Approved'])
                ->first();

            if ($existing) {
                $this->dispatch('notif', type: 'failed', header: 'Duplicate Request', message: 'This asset already has an active disposal request. Please wait for it to be processed.');
                return;
            }

            $attachmentPath = null;
            $attachmentName = null;

            if ($this->attachment) {
                $attachmentPath = $this->attachment->store('disposal-requests', 'public');
                $attachmentName = $this->attachment->getClientOriginalName();
            }

            DisposalRequest::create([
                'asset_id' => $asset->id,
                'requested_by_user_id' => Auth::id(),
                'requested_by_name' => Auth::user()?->name,
                'requester_farm' => Auth::user()?->farm,
                'requester_department' => Auth::user()?->department,
                'reason' => $this->reason,
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachmentName,
                'status' => 'Pending VP Approval',
            ]);

            $this->reset(['requestAssetId', 'reason', 'attachment']);
            $this->dispatch('notif', type: 'success', header: 'Request Submitted', message: 'Disposal request has been submitted for VP approval.');
        } catch (\Exception $e) {
            Log::error('Disposal request failed', [
                'error' => $e->getMessage(),
                'asset_id' => $this->requestAssetId,
                'user_id' => Auth::id(),
            ]);

            $this->dispatch('notif', type: 'failed', header: 'Request Failed', message: 'Unable to submit disposal request. Please try again.');
        }
    }

    public function approveRequest($requestId)
    {
        try {
            $request = DisposalRequest::with('asset')->findOrFail($requestId);
            $request->update([
                'status' => 'VP Approved',
                'vp_approved_by_user_id' => Auth::id(),
                'vp_approved_by_name' => Auth::user()?->name,
                'vp_approved_at' => now(),
            ]);

            if ($request->asset) {
                $request->asset->update(['status' => 'For Disposal']);
            }

            $this->dispatch('notif', type: 'success', header: 'VP Approval Complete', message: 'Asset is now marked as For Disposal.');
        } catch (\Exception $e) {
            Log::error('Disposal approval failed', ['error' => $e->getMessage(), 'request_id' => $requestId]);
            $this->dispatch('notif', type: 'failed', header: 'Approval Failed', message: 'Unable to approve disposal request.');
        }
    }

    public function markDisposed($requestId)
    {
        try {
            $request = DisposalRequest::with('asset')->findOrFail($requestId);
            $request->update([
                'status' => 'Disposed',
                'accounting_disposed_by_user_id' => Auth::id(),
                'accounting_disposed_by_name' => Auth::user()?->name,
                'disposed_at' => now(),
            ]);

            if ($request->asset) {
                $request->asset->update(['status' => 'Disposed']);
            }

            $this->dispatch('notif', type: 'success', header: 'Disposed', message: 'Asset has been officially marked as disposed.');
        } catch (\Exception $e) {
            Log::error('Mark disposed failed', ['error' => $e->getMessage(), 'request_id' => $requestId]);
            $this->dispatch('notif', type: 'failed', header: 'Update Failed', message: 'Unable to mark asset as disposed.');
        }
    }

    public function render()
    {
        // Get IDs of assets with active disposal requests
        $assetsWithPendingRequests = DisposalRequest::whereIn('status', ['Pending VP Approval', 'VP Approved'])
            ->pluck('asset_id')
            ->toArray();

        $requestableAssets = Asset::where('is_deleted', false)
            ->whereNotIn('status', ['Disposed', 'For Disposal'])
            ->whereNotIn('id', $assetsWithPendingRequests)
            ->orderBy('ref_id')
            ->get();

        $vpRequests = DisposalRequest::with('asset')->where('status', 'Pending VP Approval')->latest()->get();
        $accountingRequests = DisposalRequest::with('asset')->where('status', 'VP Approved')->latest()->get();
        $history = DisposalRequest::with('asset')->latest()->get();

        return view('livewire.disposal-workspace', compact('requestableAssets', 'vpRequests', 'accountingRequests', 'history'));
    }
}
