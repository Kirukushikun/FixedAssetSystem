<div class="flex flex-col gap-5" x-data="{ tab: @entangle('tab'), showConfirm: false, confirmType: '', confirmId: null }">
    <div
        wire:loading.flex
        wire:target="submitRequest,approveRequest,markDisposed"
        class="fixed inset-0 bg-black/30 z-[90] items-center justify-center"
    >
        <div class="bg-white px-5 py-4 rounded-lg shadow-lg flex items-center gap-3 text-sm font-semibold text-gray-700">
            <i class="fa-solid fa-spinner fa-spin text-teal-500"></i>
            <span>Processing request...</span>
        </div>
    </div>
    @php($user = Auth::user())
    <div>
        <h1 class="text-lg font-bold">Disposal Workspace</h1>
        <p class="text-sm text-gray-400 mb-4">Handle farm disposal requests, VP approval, and accounting disposition from one place.</p>
        <div class="flex space-x-1 rounded-t-lg overflow-hidden">
            @if($user?->hasPermission('disposal.request'))
                <button type="button" class="px-5 py-2 font-medium rounded-t-lg border" :class="tab === 'request' ? 'bg-white text-gray-800 border-gray-200 border-b-white' : 'bg-gray-100 text-gray-600 border-transparent hover:bg-gray-200'" @click="tab = 'request'; $wire.setTab('request')">Farm Request</button>
            @endif
            @if($user?->hasPermission('disposal.approve'))
                <button type="button" class="px-5 py-2 font-medium rounded-t-lg border" :class="tab === 'approval' ? 'bg-white text-gray-800 border-gray-200 border-b-white' : 'bg-gray-100 text-gray-600 border-transparent hover:bg-gray-200'" @click="tab = 'approval'; $wire.setTab('approval')">VP Approval</button>
            @endif
            @if($user?->hasPermission('disposal.dispose'))
                <button type="button" class="px-5 py-2 font-medium rounded-t-lg border" :class="tab === 'accounting' ? 'bg-white text-gray-800 border-gray-200 border-b-white' : 'bg-gray-100 text-gray-600 border-transparent hover:bg-gray-200'" @click="tab = 'accounting'; $wire.setTab('accounting')">Accounting</button>
            @endif
            <button type="button" class="px-5 py-2 font-medium rounded-t-lg border" :class="tab === 'history' ? 'bg-white text-gray-800 border-gray-200 border-b-white' : 'bg-gray-100 text-gray-600 border-transparent hover:bg-gray-200'" @click="tab = 'history'; $wire.setTab('history')">History</button>
        </div>
    </div>

    @if($user?->hasPermission('disposal.request'))
    <div class="card" x-show="tab === 'request'">
        <div class="flex flex-col gap-4">
            <h2 class="text-lg font-bold">Submit Disposal Request</h2>
            <div class="input-group">
                <label>Asset</label>
                <select wire:model="requestAssetId">
                    <option value="">Select asset...</option>
                    @foreach($requestableAssets as $asset)
                        <option value="{{ $asset->id }}">{{ $asset->ref_id }} - {{ $asset->brand }} {{ $asset->model }} ({{ $asset->sub_category }})</option>
                    @endforeach
                </select>
            </div>
            <div class="input-group">
                <label>Reason / Justification</label>
                <textarea rows="5" wire:model="reason" placeholder="State the reason for disposal request..."></textarea>
            </div>
            <div class="input-group">
                <label>Supporting Attachment</label>
                <input type="file" wire:model="attachment" accept=".pdf,.jpg,.jpeg,.png">
            </div>
            <div class="flex justify-end">
                <button type="button" @click="showConfirm = true; confirmType = 'request'; confirmId = null"
                    class="px-4 py-2 bg-teal-500 text-white rounded-lg text-sm font-bold hover:bg-teal-600 disabled:opacity-60 disabled:cursor-not-allowed">
                    Submit Request
                </button>
            </div>
        </div>
    </div>
    @endif

    @if($user?->hasPermission('disposal.approve'))
    <div class="card" x-show="tab === 'approval'">
        <div class="flex flex-col gap-4">
            <h2 class="text-lg font-bold">VP Approval Queue</h2>
            @if($vpRequests->isEmpty())
                <p class="text-sm text-gray-400">No pending disposal requests.</p>
            @else
                @foreach($vpRequests as $request)
                    <div class="border border-gray-200 rounded-xl p-4 flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <p class="font-bold text-gray-800">{{ $request->asset?->ref_id }} - {{ $request->asset?->brand }} {{ $request->asset?->model }}</p>
                            <p class="text-sm text-gray-500">{{ $request->reason }}</p>
                            <p class="text-xs text-gray-400 mt-1">Requested by {{ $request->requested_by_name ?: 'System' }} • {{ $request->created_at->format('m/d/Y h:i A') }}</p>
                            @if($request->attachment_path)
                                <a href="{{ Storage::url($request->attachment_path) }}" target="_blank" class="inline-block mt-2 text-xs text-blue-500 font-semibold">View attachment</a>
                            @endif
                        </div>
                        <button type="button" @click="showConfirm = true; confirmType = 'approve'; confirmId = {{ $request->id }}"
                            class="px-4 py-2 bg-indigo-500 text-white rounded-lg text-sm font-bold hover:bg-indigo-600 disabled:opacity-60 disabled:cursor-not-allowed">
                            Approve
                        </button>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
    @endif

    @if($user?->hasPermission('disposal.dispose'))
    <div class="card" x-show="tab === 'accounting'">
        <div class="flex flex-col gap-4">
            <h2 class="text-lg font-bold">Accounting Disposal Queue</h2>
            @if($accountingRequests->isEmpty())
                <p class="text-sm text-gray-400">No VP-approved assets waiting for disposal tagging.</p>
            @else
                @foreach($accountingRequests as $request)
                    <div class="border border-gray-200 rounded-xl p-4 flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <p class="font-bold text-gray-800">{{ $request->asset?->ref_id }} - {{ $request->asset?->brand }} {{ $request->asset?->model }}</p>
                            <p class="text-sm text-gray-500">{{ $request->reason }}</p>
                            <p class="text-xs text-gray-400 mt-1">Approved by {{ $request->vp_approved_by_name ?: 'N/A' }} • {{ optional($request->vp_approved_at)->format('m/d/Y h:i A') }}</p>
                        </div>
                        <button type="button" wire:click="markDisposed({{ $request->id }})" wire:loading.attr="disabled" wire:target="markDisposed({{ $request->id }})"
                            class="px-4 py-2 bg-gray-700 text-white rounded-lg text-sm font-bold hover:bg-gray-800 disabled:opacity-60 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="markDisposed({{ $request->id }})">Mark Disposed</span>
                            <span wire:loading.inline wire:target="markDisposed({{ $request->id }})">Processing...</span>
                        </button>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
    @endif

    <div class="card" x-show="tab === 'history'">
        <div class="flex flex-col gap-4">
            <h2 class="text-lg font-bold">Disposal History</h2>
            @if($history->isEmpty())
                <p class="text-sm text-gray-400">No disposal records yet.</p>
            @else
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Asset</th>
                                <th>Status</th>
                                <th>Requested By</th>
                                <th>VP Approval</th>
                                <th>Disposed At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($history as $request)
                                <tr>
                                    <td>{{ $request->asset?->ref_id }} - {{ $request->asset?->brand }} {{ $request->asset?->model }}</td>
                                    <td>{{ $request->status }}</td>
                                    <td>{{ $request->requested_by_name ?: 'System' }}</td>
                                    <td>{{ $request->vp_approved_by_name ?: '—' }}</td>
                                    <td>{{ optional($request->disposed_at)->format('m/d/Y h:i A') ?: '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
    <div x-show="showConfirm" x-transition.opacity class="fixed inset-0 bg-black/40 z-[70]" @click="showConfirm = false"></div>
    <div x-show="showConfirm" x-transition class="fixed inset-0 z-[80] flex items-center justify-center px-4 pointer-events-none">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 pointer-events-auto">
            <h2 class="text-lg font-bold text-gray-800 mb-2" x-text="confirmType === 'request' ? 'Submit Disposal Request' : 'Approve Disposal Request'"></h2>
            <p class="text-sm text-gray-500 mb-6" x-text="confirmType === 'request' ? 'Are you sure you want to submit this asset for disposal approval?' : 'Are you sure you want to approve this disposal request?'"></p>
            <div class="flex justify-end gap-3">
                <button type="button" @click="showConfirm = false" class="px-4 py-2 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50">Cancel</button>
                <button type="button"
                    @click="confirmType === 'request' ? $wire.submitRequest() : $wire.approveRequest(confirmId); showConfirm = false"
                    class="px-4 py-2 bg-teal-500 text-white rounded-xl text-sm font-bold hover:bg-teal-600">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>
