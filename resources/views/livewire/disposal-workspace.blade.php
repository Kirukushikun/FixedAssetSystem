<div class="flex flex-col gap-5" x-data="{ tab: @entangle('tab') }">

    {{-- Global loading overlay --}}
    <div
        wire:loading.flex
        wire:target="submitRequest,approveRequest,markDisposed,confirmAction"
        class="fixed inset-0 bg-black/30 z-[90] items-center justify-center"
    >
        <div class="bg-white px-5 py-4 rounded-lg shadow-lg flex items-center gap-3 text-sm font-semibold text-gray-700">
            <i class="fa-solid fa-spinner fa-spin text-teal-500"></i>
            <span>Processing request...</span>
        </div>
    </div>

    @php($user = Auth::user())

    <div class="flex flex-col">

        {{-- Tabs --}}
        <div class="flex space-x-0.5">
            @if($user?->hasPermission('disposal.request'))
                <button type="button"
                    class="px-5 py-2 font-medium rounded-t-lg border -mb-px z-10"
                    :class="tab === 'request' ? 'bg-white text-gray-800 border-gray-200 border-b-white' : 'bg-gray-100 text-gray-600 border-transparent hover:bg-gray-200'"
                    @click="tab = 'request'; $wire.setTab('request')">
                    Farm Request
                </button>
            @endif
            @if($user?->hasPermission('disposal.approve'))
                <button type="button"
                    class="px-5 py-2 font-medium rounded-t-lg border -mb-px z-10"
                    :class="tab === 'division_head' ? 'bg-white text-gray-800 border-gray-200 border-b-white' : 'bg-gray-100 text-gray-600 border-transparent hover:bg-gray-200'"
                    @click="tab = 'division_head'; $wire.setTab('division_head')">
                    Division Head Approval
                </button>
            @endif
            @if($user?->hasPermission('disposal.vp_approve'))
            <button type="button"
                class="px-5 py-2 font-medium rounded-t-lg border -mb-px z-10"
                :class="tab === 'approval' ? 'bg-white text-gray-800 border-gray-200 border-b-white' : 'bg-gray-100 text-gray-600 border-transparent hover:bg-gray-200'"
                @click="tab = 'approval'; $wire.setTab('approval')">
                VP Approval
            </button>
            @endif
            @if($user?->hasPermission('disposal.dispose'))
                <button type="button"
                    class="px-5 py-2 font-medium rounded-t-lg border -mb-px z-10"
                    :class="tab === 'accounting' ? 'bg-white text-gray-800 border-gray-200 border-b-white' : 'bg-gray-100 text-gray-600 border-transparent hover:bg-gray-200'"
                    @click="tab = 'accounting'; $wire.setTab('accounting')">
                    Accounting
                </button>
            @endif
            <button type="button"
                class="px-5 py-2 font-medium rounded-t-lg border -mb-px z-10"
                :class="tab === 'history' ? 'bg-white text-gray-800 border-gray-200 border-b-white' : 'bg-gray-100 text-gray-600 border-transparent hover:bg-gray-200'"
                @click="tab = 'history'; $wire.setTab('history')">
                History
            </button>
        </div>

        {{-- Farm Request --}}
        @if($user?->hasPermission('disposal.request'))
        <div class="card rounded-tl-none" x-show="tab === 'request'">
            <div class="flex flex-col gap-4">
                <h2 class="text-lg font-bold">Submit Disposal Request</h2>

                <div class="input-group">
                    <label>Asset</label>
                    <select wire:model="requestAssetId">
                        <option value="">Select asset...</option>
                        @foreach($requestableAssets as $asset)
                            <option value="{{ $asset->id }}">
                                {{ $asset->ref_id }} — {{ $asset->brand }} {{ $asset->model }} ({{ $asset->sub_category }})
                            </option>
                        @endforeach
                    </select>
                    @error('requestAssetId') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="input-group">
                    <label>Reason / Justification</label>
                    <textarea rows="5" wire:model="reason" placeholder="State the reason for disposal request..."></textarea>
                    @error('reason') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Styled file upload (same pattern as service report) --}}
                <div class="flex flex-col gap-2" x-data>
                    <label class="text-[15px] font-semibold">
                        Supporting Attachment
                        @error('attachment') <span class="text-red-500 text-xs ml-2">{{ $message }}</span> @enderror
                    </label>
                    <div class="flex w-full border border-gray-300 rounded-md overflow-hidden text-sm">
                        <button
                            type="button"
                            wire:loading.attr="disabled"
                            wire:target="attachment"
                            class="bg-gray-600 text-white px-4 py-2 hover:bg-gray-500 disabled:opacity-60 disabled:cursor-not-allowed"
                            @click="$refs.disposalFile.click()"
                        >
                            <span wire:loading.remove wire:target="attachment">Choose File</span>
                            <span wire:loading.inline-flex wire:target="attachment" class="items-center gap-2">
                                <i class="fa-solid fa-spinner fa-spin"></i> Uploading...
                            </span>
                        </button>
                        <div class="flex-1 bg-gray-50 text-gray-500 px-4 py-2">
                            <span wire:loading.remove wire:target="attachment">
                                {{ $attachment ? $attachment->getClientOriginalName() : 'No file chosen (PDF, JPG, PNG — max 10MB)' }}
                            </span>
                            <span wire:loading.inline wire:target="attachment">Uploading selected file...</span>
                        </div>
                        <input
                            x-ref="disposalFile"
                            type="file"
                            class="hidden"
                            wire:model="attachment"
                            accept=".pdf,.jpg,.jpeg,.png"
                        >
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="button"
                        wire:click="openConfirm('request')"
                        wire:loading.attr="disabled"
                        wire:target="attachment"
                        class="px-4 py-2 bg-teal-500 text-white rounded-lg text-sm font-bold hover:bg-teal-600 disabled:opacity-60 disabled:cursor-not-allowed"
                        title="Wait for file to finish uploading">
                        Submit Request
                    </button>
                </div>
            </div>
        </div>
        @endif

        {{-- Division Head --}}
        @if($user?->hasPermission('disposal.approve'))
        <div class="card" x-show="tab === 'division_head'">
            <div class="flex flex-col gap-4">
                <h2 class="text-lg font-bold">Division Head Approval Queue</h2>
                @if($divisionHeadRequests->isEmpty())
                    <p class="text-sm text-gray-400">No pending disposal requests.</p>
                @else
                    @foreach($divisionHeadRequests as $request)
                        <div class="border border-gray-200 rounded-xl p-4 flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <a href="/assetmanagement/view?targetID={{ encrypt($request->asset->id) }}"
                                    class="font-bold text-gray-800 hover:text-teal-500 hover:underline transition-colors">
                                    {{ $request->asset->ref_id }} — {{ $request->asset->brand }} {{ $request->asset->model }}
                                </a>
                                <p class="text-sm text-gray-500 mt-1">{{ $request->reason }}</p>
                                <p class="text-xs text-gray-400 mt-1">
                                    Requested by {{ $request->requested_by_name ?: 'System' }} • {{ $request->created_at->format('m/d/Y h:i A') }}
                                </p>
                                @if($request->attachment_path)
                                    <a href="{{ Storage::url($request->attachment_path) }}" target="_blank"
                                        class="inline-block mt-2 text-xs text-blue-500 font-semibold hover:underline">
                                        View attachment
                                    </a>
                                @endif
                            </div>
                            <button type="button" wire:click="openConfirm('approve', {{ $request->id }})"
                                class="px-4 py-2 bg-indigo-500 text-white rounded-lg text-sm font-bold hover:bg-indigo-600 shrink-0">
                                Approve
                            </button>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
        @endif

        {{-- VP Approval --}}
        @if($user?->hasPermission('disposal.vp_approve'))
        <div class="card" x-show="tab === 'approval'">
            <div class="flex flex-col gap-4">
                <h2 class="text-lg font-bold">VP Approval Queue</h2>
                @if($vpRequests->isEmpty())
                    <p class="text-sm text-gray-400">No pending disposal requests.</p>
                @else
                    @foreach($vpRequests as $request)
                        <div class="border border-gray-200 rounded-xl p-4 flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <a href="/assetmanagement/view?targetID={{ encrypt($request->asset->id) }}"
                                    class="font-bold text-gray-800 hover:text-teal-500 hover:underline transition-colors">
                                    {{ $request->asset->ref_id }} — {{ $request->asset->brand }} {{ $request->asset->model }}
                                </a>
                                <p class="text-sm text-gray-500 mt-1">{{ $request->reason }}</p>
                                <p class="text-xs text-gray-400 mt-1">
                                    Requested by {{ $request->requested_by_name ?: 'System' }} • {{ $request->created_at->format('m/d/Y h:i A') }}
                                </p>
                                @if($request->attachment_path)
                                    <a href="{{ Storage::url($request->attachment_path) }}" target="_blank"
                                        class="inline-block mt-2 text-xs text-blue-500 font-semibold hover:underline">
                                        View attachment
                                    </a>
                                @endif
                            </div>
                            <button type="button" wire:click="openConfirm('approve', {{ $request->id }})"
                                class="px-4 py-2 bg-indigo-500 text-white rounded-lg text-sm font-bold hover:bg-indigo-600 shrink-0">
                                Approve
                            </button>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
        @endif

        {{-- Accounting --}}
        @if($user?->hasPermission('disposal.dispose'))
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
                                <th class="border border-gray-300 text-left px-2 py-2">Division Head</th>
                                <th class="border border-gray-300 text-left px-2 py-2">VP Approved</th>
                                <th class="border border-gray-300 text-left px-2 py-2">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($accountingRequests as $request)
                                @php($vpApproved = !empty($request->vp_approved_by_name))
                                <tr>
                                    <td class="border border-gray-300 px-2 py-2 font-mono text-xs">{{ $request->asset->ref_id }}</td>
                                    <td class="border border-gray-300 px-2 py-2">{{ $request->asset->sub_category }}</td>
                                    <td class="border border-gray-300 px-2 py-2">{{ $request->reason }}</td>
                                    <td class="border border-gray-300 px-2 py-2">{{ $request->requested_by_name }}</td>
                                    <td class="border border-gray-300 px-2 py-2">
                                        @if($request->division_head_approved_by_name)
                                            <span class="text-green-600 font-semibold">{{ $request->division_head_approved_by_name }}</span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="border border-gray-300 px-2 py-2">
                                        @if($vpApproved)
                                            <span class="text-green-600 font-semibold">{{ $request->vp_approved_by_name }}</span>
                                        @else
                                            <span class="text-red-400 font-semibold">Pending</span>
                                        @endif
                                    </td>
                                    <td class="border border-gray-300 px-2 py-2">
                                        <button
                                            @if($vpApproved) wire:click="openConfirm('dispose', {{ $request->id }})" @endif
                                            @disabled(!$vpApproved)
                                            title="{{ $vpApproved ? 'Mark as disposed' : 'Waiting for VP approval' }}"
                                            class="px-3 py-1 rounded text-xs font-semibold transition-colors
                                                {{ $vpApproved
                                                    ? 'bg-red-500 text-white hover:bg-red-600 cursor-pointer'
                                                    : 'bg-gray-200 text-gray-400 cursor-not-allowed' }}">
                                            Dispose
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
        @endif

        {{-- History --}}
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
                                        <td>
                                            <a href="/assetmanagement/view?targetID={{ encrypt($request->asset->id) }}"
                                                class="font-semibold hover:text-teal-500 hover:underline transition-colors">
                                                {{ $request->asset?->ref_id }} — {{ $request->asset?->brand }} {{ $request->asset?->model }}
                                            </a>
                                        </td>
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

    {{-- Confirm Modal — Livewire-controlled, immune to re-renders --}}
    @if($showConfirmModal)
        <div class="fixed inset-0 bg-black/40 z-[70]" wire:click="closeConfirm"></div>
        <div class="fixed inset-0 z-[80] flex items-center justify-center px-4 pointer-events-none">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 pointer-events-auto">

                <h2 class="text-lg font-bold text-gray-800 mb-2">
                    @if($confirmType === 'request') Submit Disposal Request
                    @elseif($confirmType === 'dispose') Mark Asset as Disposed
                    @else Approve Disposal Request
                    @endif
                </h2>

                <p class="text-sm text-gray-500 mb-6">
                    @if($confirmType === 'request') Are you sure you want to submit this asset for disposal approval?
                    @elseif($confirmType === 'dispose') This will permanently mark the asset as Disposed. This cannot be undone.
                    @else Are you sure you want to approve this disposal request?
                    @endif
                </p>

                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="closeConfirm"
                        class="px-4 py-2 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="button"
                        wire:click="confirmAction"
                        wire:loading.attr="disabled"
                        wire:target="confirmAction"
                        class="px-4 py-2 bg-teal-500 text-white rounded-xl text-sm font-bold hover:bg-teal-600 disabled:opacity-60 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="confirmAction">Confirm</span>
                        <span wire:loading.inline wire:target="confirmAction">Processing...</span>
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>