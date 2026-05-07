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
            <button type="button" class="px-5 py-2 font-medium rounded-t-lg border disabled:opacity-50 disabled:cursor-not-allowed"
                :class="tab === 'request' ? 'bg-white text-gray-800 border-gray-200 border-b-white' : 'bg-gray-100 text-gray-600 border-transparent hover:bg-gray-200'"
                :disabled="!{{ $user?->hasPermission('disposal.request') ? 'true' : 'false' }}"
                @click="{{ $user?->hasPermission('disposal.request') ? 'tab = \'request\'; $wire.setTab(\'request\')' : 'null' }}"
                title="{!! $user?->hasPermission('disposal.request') ? 'Farm Request' : 'You do not have permission to submit disposal requests' !!}">
                Farm Request
            </button>
            <button type="button" class="px-5 py-2 font-medium rounded-t-lg border disabled:opacity-50 disabled:cursor-not-allowed"
                :class="tab === 'approval' ? 'bg-white text-gray-800 border-gray-200 border-b-white' : 'bg-gray-100 text-gray-600 border-transparent hover:bg-gray-200'"
                :disabled="!{{ $user?->hasPermission('disposal.approve') ? 'true' : 'false' }}"
                @click="{{ $user?->hasPermission('disposal.approve') ? 'tab = \'approval\'; $wire.setTab(\'approval\')' : 'null' }}"
                title="{!! $user?->hasPermission('disposal.approve') ? 'VP Approval' : 'You do not have permission to approve disposal requests' !!}">
                VP Approval
            </button>
            <button type="button" class="px-5 py-2 font-medium rounded-t-lg border disabled:opacity-50 disabled:cursor-not-allowed"
                :class="tab === 'accounting' ? 'bg-white text-gray-800 border-gray-200 border-b-white' : 'bg-gray-100 text-gray-600 border-transparent hover:bg-gray-200'"
                :disabled="!{{ $user?->hasPermission('disposal.dispose') ? 'true' : 'false' }}"
                @click="{{ $user?->hasPermission('disposal.dispose') ? 'tab = \'accounting\'; $wire.setTab(\'accounting\')' : 'null' }}"
                title="{!! $user?->hasPermission('disposal.dispose') ? 'Accounting' : 'You do not have permission to dispose assets' !!}">
                Accounting
            </button>
            <button type="button" class="px-5 py-2 font-medium rounded-t-lg border"
                :class="tab === 'history' ? 'bg-white text-gray-800 border-gray-200 border-b-white' : 'bg-gray-100 text-gray-600 border-transparent hover:bg-gray-200'"
                @click="tab = 'history'; $wire.setTab('history')">History</button>
        </div>
    </div>

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
            @endif
        </div>
    </div>

    <div class="card" x-show="tab === 'accounting'">
        <div class="flex flex-col gap-4">
            <h2 class="text-lg font-bold">Accounting Disposal Queue</h2>
            @if($accountingRequests->isEmpty())
                <p class="text-sm text-gray-400">No VP-approved assets waiting for disposal tagging.</p>
            @else
                <table class="w-full border border-gray-300 border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500">
                            <th class="border border-gray-300 text-left px-2 py-2">Asset</th>
                            <th class="border border-gray-300 text-left px-2 py-2">Category</th>
                            <th class="border border-gray-300 text-left px-2 py-2">Reason</th>
                            <th class="border border-gray-300 text-left px-2 py-2">Requested By</th>
                            <th class="border border-gray-300 text-left px-2 py-2">Approved By</th>
                            <th class="border border-gray-300 text-left px-2 py-2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($accountingRequests as $request)
                            <tr>
                                <td class="border border-gray-300 px-2 py-2">{{ $request->asset->ref_id }}</td>
                                <td class="border border-gray-300 px-2 py-2">{{ $request->asset->sub_category }}</td>
                                <td class="border border-gray-300 px-2 py-2">{{ $request->reason }}</td>
                                <td class="border border-gray-300 px-2 py-2">{{ $request->requested_by_name }}</td>
                                <td class="border border-gray-300 px-2 py-2">{{ $request->approved_by_name }}</td>
                                <td class="border border-gray-300 px-2 py-2">
                                    <button wire:click="disposeAsset({{ $request->asset->id }})" class="px-3 py-1 bg-red-500 text-white rounded text-xs hover:bg-red-600">Dispose</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

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
