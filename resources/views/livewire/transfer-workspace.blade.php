<div class="flex flex-col gap-5" x-data="{ tab: @entangle('defaultTab').live, isExternalTransfer: @entangle('isExternalTransfer').live }">
    @php($user = Auth::user())

    {{-- Tabs + Card unified block --}}
    <div class="flex flex-col">

        {{-- Tab buttons --}}
        <div class="flex space-x-0.5">
            @if(!$user?->hasRole('accounting'))
            <button type="button"
                class="px-5 py-2 font-medium rounded-t-lg border -mb-px z-10"
                :class="tab === 'request' ? 'bg-white text-gray-800 border-gray-200 border-b-white' : 'bg-gray-100 text-gray-600 border-transparent hover:bg-gray-200'"
                @click="tab = 'request'">
                Request Transfer
            </button>
            @endif
            @if(!$user?->hasRole('accounting'))
            <button type="button"
                class="px-5 py-2 font-medium rounded-t-lg border -mb-px z-10"
                :class="tab === 'pending' ? 'bg-white text-gray-800 border-gray-200 border-b-white' : 'bg-gray-100 text-gray-600 border-transparent hover:bg-gray-200'"
                @click="tab = 'pending'">
                Pending
            </button>
            @endif
            @if($user?->hasPermission('transfer.approve'))
                <button type="button"
                    class="px-5 py-2 font-medium rounded-t-lg border -mb-px z-10"
                    :class="tab === 'division_head' ? 'bg-white text-gray-800 border-gray-200 border-b-white' : 'bg-gray-100 text-gray-600 border-transparent hover:bg-gray-200'"
                    @click="tab = 'division_head'">
                    DH Approval
                </button>
            @endif
            @if($user?->hasPermission('transfer.complete'))
                <button type="button"
                    class="px-5 py-2 font-medium rounded-t-lg border -mb-px z-10"
                    :class="tab === 'accounting' ? 'bg-white text-gray-800 border-gray-200 border-b-white' : 'bg-gray-100 text-gray-600 border-transparent hover:bg-gray-200'"
                    @click="tab = 'accounting'">
                    Accounting
                </button>
            @endif
        </div>

        {{-- Card panels --}}
        <div class="card !rounded-tl-none" x-show="tab === 'request'">
            <div class="flex flex-col gap-4">
                <h2 class="text-lg font-bold">Submit Transfer Request</h2>
                <div class="input-group">
                    <label>Asset</label>
                    <select wire:model.live="requestAssetId">
                        <option value="">Select asset...</option>
                        @foreach($transferableAssets as $asset)
                            <option value="{{ $asset->id }}">{{ $asset->ref_id }} - {{ $asset->brand }} {{ $asset->model }} (Assigned to: {{ $asset->assignedEmployee->employee_name }})</option>
                        @endforeach
                    </select>
                    @error('requestAssetId') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="externalTransfer" wire:model.live="isExternalTransfer" class="w-4 h-4 text-teal-500 rounded border-gray-300 focus:ring-teal-500">
                    <label for="externalTransfer" class="text-sm font-medium text-gray-700">External Transfer (Outside department/farm)</label>
                </div>
                <div x-show="isExternalTransfer" class="grid grid-cols-2 gap-4">
                    <div class="input-group">
                        <label>Farm</label>
                        <select wire:model.live="externalFarm">
                            <option value="">Select farm...</option>
                            <option value="BFC">BFC</option>
                            <option value="BDL">BDL</option>
                            <option value="PFC">PFC</option>
                            <option value="RH">RH</option>
                            <option value="BBGC">BBGC</option>
                            <option value="HATCHERY">HATCHERY</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>Department</label>
                        <select wire:model.live="externalDepartment">
                            <option value="">Select department...</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->name }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                        @error('externalDepartment') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                @error('externalFarm') <p class="text-xs text-red-500 -mt-2">{{ $message }}</p> @enderror
                <div class="input-group">
                    <label>Transfer To</label>
                    <select wire:model.live="requestEmployeeId">
                        <option value="">Select employee...</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->employee_name }} ({{ $employee->farm }} - {{ $employee->department }})</option>
                        @endforeach
                    </select>
                    @error('requestEmployeeId') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="input-group">
                    <label>Reason / Justification</label>
                    <textarea rows="5" wire:model.live="requestReason" placeholder="State the reason for transfer request..."></textarea>
                    @error('requestReason') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex justify-end">
                    <button type="button" wire:click="openConfirm('request')"
                        class="px-4 py-2 bg-teal-500 text-white rounded-lg text-sm font-bold hover:bg-teal-600">
                        Submit Request
                    </button>
                </div>
            </div>
        </div>

        @if(!$user?->hasRole('accounting'))
        <div class="card !rounded-tl-none" x-show="tab === 'pending'">
            <div class="flex flex-col gap-4">
                <h2 class="text-lg font-bold">Pending Requests</h2>
                @if($pendingRequests->isEmpty())
                    <p class="text-sm text-gray-400">No pending transfer requests.</p>
                @else
                    @foreach($pendingRequests as $request)
                        <div class="border border-gray-200 rounded-xl p-4 flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <a href="/assetmanagement/view?targetID={{ encrypt($request->asset->id) }}"
                                    class="font-bold text-gray-800 hover:text-teal-500 hover:underline transition-colors">
                                        {{ $request->asset->ref_id }} - {{ $request->asset->brand }} {{ $request->asset->model }}
                                </a>
                                <p class="text-sm text-gray-500">{{ $request->reason }}</p>
                                <p class="text-xs text-gray-400 mt-1">
                                    From: {{ $request->asset->assignedEmployee->employee_name ?? '—' }} ({{ $request->asset->assignedEmployee->farm ?? '—' }} - {{ $request->asset->assignedEmployee->department ?? '—' }})
                                </p>
                                <p class="text-xs text-gray-400 mt-1">
                                    To: {{ $request->requested_employee_name }} ({{ $request->requestedEmployee->farm ?? '—' }} - {{ $request->requestedEmployee->department ?? '—' }})
                                </p>
                                <p class="text-xs text-gray-400 mt-1">{{ $request->created_at->format('m/d/Y h:i A') }}</p>
                            </div>
                            @if($request->status === 'Rejected')
                                <span class="px-3 py-1 bg-red-100 text-red-600 rounded-full text-xs font-semibold shrink-0">Rejected</span>
                            @else
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold shrink-0">Pending</span>
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
        @endif

        @if($user?->hasPermission('transfer.approve'))
        <div class="card !rounded-tl-none" x-show="tab === 'division_head'">
            <div class="flex flex-col gap-4">
                <h2 class="text-lg font-bold">DH Approval Queue</h2>
                @if($divisionHeadRequests->isEmpty())
                    <p class="text-sm text-gray-400">No pending transfer requests.</p>
                @else
                    @foreach($divisionHeadRequests as $request)
                        <div class="border border-gray-200 rounded-xl p-4 flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <a href="/assetmanagement/view?targetID={{ encrypt($request->asset->id) }}"
                                    class="font-bold text-gray-800 hover:text-teal-500 hover:underline transition-colors">
                                        {{ $request->asset->ref_id }} - {{ $request->asset->brand }} {{ $request->asset->model }}
                                </a>
                                <p class="text-sm text-gray-500">{{ $request->reason }}</p>
                                <p class="text-xs text-gray-400 mt-1">
                                    From: {{ $request->asset?->assignedEmployee->employee_name ?? '—' }} ({{ $request->asset?->assignedEmployee->farm ?? '—' }} - {{ $request->asset?->assignedEmployee->department ?? '—' }})
                                </p>
                                <p class="text-xs text-gray-400 mt-1">
                                    To: {{ $request->requested_employee_name }} ({{ $request->requestedEmployee->farm ?? '—' }} - {{ $request->requestedEmployee->department ?? '—' }})
                                </p>
                                <p class="text-xs text-gray-400 mt-1">Requested by {{ $request->requested_by_name }} • {{ $request->created_at->format('m/d/Y h:i A') }}</p>
                            </div>
                            <div class="flex flex-col gap-2 shrink-0">
                                <button type="button" wire:click="openConfirm('approve', {{ $request->id }})"
                                    class="px-4 py-2 bg-indigo-500 text-white rounded-lg text-sm font-bold hover:bg-indigo-600">
                                    Approve
                                </button>
                                <button type="button" wire:click="openConfirm('reject', {{ $request->id }})"
                                    class="px-4 py-2 bg-red-100 text-red-600 rounded-lg text-sm font-bold hover:bg-red-200">
                                    Reject
                                </button>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        @endif

        @if($user?->hasPermission('transfer.complete'))
        <div class="card !rounded-tl-none" x-show="tab === 'accounting'">
            <div class="flex flex-col gap-4">
                <h2 class="text-lg font-bold">Accounting Transfer Queue</h2>
                @if($accountingRequests->isEmpty())
                    <p class="text-sm text-gray-400">No pending transfer requests.</p>
                @else
                    @foreach($accountingRequests as $request)
                        <div class="border border-gray-200 rounded-xl p-4 flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <a href="/assetmanagement/view?targetID={{ encrypt($request->asset->id) }}"
                                    class="font-bold text-gray-800 hover:text-teal-500 hover:underline transition-colors">
                                        {{ $request->asset->ref_id }} - {{ $request->asset->brand }} {{ $request->asset->model }}
                                </a>
                                <p class="text-sm text-gray-500">{{ $request->reason }}</p>
                                <p class="text-xs text-gray-400 mt-1">
                                    From: {{ $request->asset?->assignedEmployee->employee_name ?? '—' }} ({{ $request->asset?->assignedEmployee->farm ?? '—' }} - {{ $request->asset?->assignedEmployee->department ?? '—' }})
                                </p>
                                <p class="text-xs text-gray-400 mt-1">
                                    To: {{ $request->requested_employee_name }} ({{ $request->requestedEmployee->farm ?? '—' }} - {{ $request->requestedEmployee->department ?? '—' }})
                                </p>
                                <p class="text-xs text-gray-400 mt-1">Requested by {{ $request->requested_by_name }} • Division Head: {{ $request->division_head_approved_by_name ?: '—' }}</p>
                            </div>
                            <button type="button" wire:click="openConfirm('complete', {{ $request->id }})"
                                class="px-4 py-2 bg-teal-500 text-white rounded-lg text-sm font-bold hover:bg-teal-600">
                                Complete Transfer
                            </button>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
        @endif


    </div>{{-- end flex-col --}}

    <div @if(! $showConfirmModal) style="display:none" @endif class="fixed inset-0 bg-black/40 z-[70]" wire:click="closeConfirm"></div>
    <div @if(! $showConfirmModal) style="display:none" @endif class="fixed inset-0 z-[80] flex items-center justify-center px-4 pointer-events-none">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 pointer-events-auto">
            <h2 class="text-lg font-bold text-gray-800 mb-2">
                @if($confirmType === 'request') Submit Transfer Request
                @elseif($confirmType === 'complete') Complete Transfer
                @elseif($confirmType === 'reject') Reject Transfer Request
                @else Approve Transfer Request
                @endif
            </h2>
            <p class="text-sm text-gray-500 mb-6">
                @if($confirmType === 'request') Are you sure you want to submit this transfer request?
                @elseif($confirmType === 'complete') Are you sure you want to complete this transfer and reassign the asset?
                @elseif($confirmType === 'reject') Are you sure you want to reject this request? The asset will remain with its current holder.
                @else Are you sure you want to approve this transfer request?
                @endif
            </p>
            <div class="flex justify-end gap-3">
                <button type="button" wire:click="closeConfirm" class="px-4 py-2 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50">Cancel</button>
                <button type="button"
                    wire:click="confirmAction"
                    wire:loading.attr="disabled"
                    wire:target="confirmAction"
                    class="px-4 py-2 bg-teal-500 text-white rounded-xl text-sm font-bold hover:bg-teal-600">
                    <span wire:loading.remove wire:target="confirmAction">Confirm</span>
                    <span wire:loading wire:target="confirmAction">Processing...</span>
                </button>
            </div>
        </div>
    </div>
</div>