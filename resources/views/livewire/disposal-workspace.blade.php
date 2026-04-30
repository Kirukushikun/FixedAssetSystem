<div class="flex flex-col gap-5" x-data="{ tab: @entangle('tab') }">
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
    <div class="card flex flex-col gap-4">
        <div>
            <h1 class="text-lg font-bold">Disposal Workspace</h1>
            <p class="text-sm text-gray-400">Handle farm disposal requests, VP approval, and accounting disposition from one place.</p>
        </div>

        <div class="flex flex-wrap gap-2">
            <button type="button" class="px-4 py-2 rounded-lg text-sm font-bold" :class="tab === 'request' ? 'bg-teal-500 text-white' : 'bg-gray-100 text-gray-600'" @click="tab = 'request'; $wire.setTab('request')">Farm Request</button>
            <button type="button" class="px-4 py-2 rounded-lg text-sm font-bold" :class="tab === 'approval' ? 'bg-teal-500 text-white' : 'bg-gray-100 text-gray-600'" @click="tab = 'approval'; $wire.setTab('approval')">VP Approval</button>
            <button type="button" class="px-4 py-2 rounded-lg text-sm font-bold" :class="tab === 'accounting' ? 'bg-teal-500 text-white' : 'bg-gray-100 text-gray-600'" @click="tab = 'accounting'; $wire.setTab('accounting')">Accounting</button>
            <button type="button" class="px-4 py-2 rounded-lg text-sm font-bold" :class="tab === 'history' ? 'bg-teal-500 text-white' : 'bg-gray-100 text-gray-600'" @click="tab = 'history'; $wire.setTab('history')">History</button>
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
                <button type="button" wire:click="submitRequest" wire:loading.attr="disabled" wire:target="submitRequest"
                    class="px-4 py-2 bg-teal-500 text-white rounded-lg text-sm font-bold hover:bg-teal-600 disabled:opacity-60 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="submitRequest">Submit Request</span>
                    <span wire:loading.inline wire:target="submitRequest">Submitting...</span>
                </button>
            </div>
        </div>
    </div>

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
                        <button type="button" wire:click="approveRequest({{ $request->id }})" wire:loading.attr="disabled" wire:target="approveRequest({{ $request->id }})"
                            class="px-4 py-2 bg-indigo-500 text-white rounded-lg text-sm font-bold hover:bg-indigo-600 disabled:opacity-60 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="approveRequest({{ $request->id }})">Approve</span>
                            <span wire:loading.inline wire:target="approveRequest({{ $request->id }})">Approving...</span>
                        </button>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

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
</div>
