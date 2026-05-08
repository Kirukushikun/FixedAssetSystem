<div class="flex flex-col gap-5" x-data="{ tab: 'request', showConfirmModal: false }">
    @php($user = Auth::user())

    {{-- Tabs + Card unified block --}}
    <div class="flex flex-col">

        {{-- Tab buttons --}}
        <div class="flex space-x-0.5">
            <button type="button"
                class="px-5 py-2 font-medium rounded-t-lg border -mb-px z-10"
                :class="tab === 'request' ? 'bg-white text-gray-800 border-gray-200 border-b-white' : 'bg-gray-100 text-gray-600 border-transparent hover:bg-gray-200'"
                @click="tab = 'request'">
                Request Transfer
            </button>
            <button type="button"
                class="px-5 py-2 font-medium rounded-t-lg border -mb-px z-10"
                :class="tab === 'pending' ? 'bg-white text-gray-800 border-gray-200 border-b-white' : 'bg-gray-100 text-gray-600 border-transparent hover:bg-gray-200'"
                @click="tab = 'pending'">
                Pending
            </button>
            <button type="button"
                class="px-5 py-2 font-medium rounded-t-lg border -mb-px z-10"
                :class="tab === 'history' ? 'bg-white text-gray-800 border-gray-200 border-b-white' : 'bg-gray-100 text-gray-600 border-transparent hover:bg-gray-200'"
                @click="tab = 'history'">
                History
            </button>
        </div>

        {{-- Card panels --}}
        <div class="card rounded-tl-none" x-show="tab === 'request'">
            <div class="flex flex-col gap-4">
                <h2 class="text-lg font-bold">Submit Transfer Request</h2>
                <div class="input-group">
                    <label>Asset</label>
                    <select wire:model="requestAssetId">
                        <option value="">Select asset...</option>
                        @foreach($transferableAssets as $asset)
                            <option value="{{ $asset->id }}">{{ $asset->ref_id }} - {{ $asset->brand }} {{ $asset->model }} (Assigned to: {{ $asset->assignedEmployee->employee_name }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="input-group">
                    <label>Transfer To</label>
                    <select wire:model="requestEmployeeId">
                        <option value="">Select employee...</option>
                        @foreach($transferableAssets as $asset)
                            @if($requestAssetId == $asset->id)
                                <option value="{{ $asset->assignedEmployee->id }}">{{ $asset->assignedEmployee->employee_name }} (Current: {{ $asset->assignedEmployee->employee_name }})</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="input-group">
                    <label>Reason / Justification</label>
                    <textarea rows="5" wire:model="requestReason" placeholder="State the reason for transfer request..."></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="button" @click="showConfirmModal = true"
                        class="px-4 py-2 bg-teal-500 text-white rounded-lg text-sm font-bold hover:bg-teal-600">
                        Submit Request
                    </button>
                </div>
            </div>
        </div>

        <div class="card" x-show="tab === 'pending'">
            <div class="flex flex-col gap-4">
                <h2 class="text-lg font-bold">Pending Requests</h2>
                @if($pendingRequests->isEmpty())
                    <p class="text-sm text-gray-400">No pending transfer requests.</p>
                @else
                    @foreach($pendingRequests as $request)
                        <div class="border border-gray-200 rounded-xl p-4 flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <p class="font-bold text-gray-800">{{ $request->asset->ref_id }} - {{ $request->asset->brand }} {{ $request->asset->model }}</p>
                                <p class="text-sm text-gray-500">{{ $request->reason }}</p>
                                <p class="text-xs text-gray-400 mt-1">Transfer to: {{ $request->requested_employee_name }} • {{ $request->created_at->format('m/d/Y h:i A') }}</p>
                            </div>
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">Pending</span>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="card" x-show="tab === 'history'">
            <div class="flex flex-col gap-4">
                <h2 class="text-lg font-bold">Transfer History</h2>
                @if($approvedRequests->isEmpty())
                    <p class="text-sm text-gray-400">No approved transfer requests yet.</p>
                @else
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Asset</th>
                                    <th>Status</th>
                                    <th>Requested By</th>
                                    <th>Transfer To</th>
                                    <th>Approved By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($approvedRequests as $request)
                                    <tr>
                                        <td>{{ $request->asset->ref_id }} - {{ $request->asset->brand }} {{ $request->asset->model }}</td>
                                        <td>{{ $request->status }}</td>
                                        <td>{{ $request->requested_by_name }}</td>
                                        <td>{{ $request->requested_employee_name }}</td>
                                        <td>{{ $request->approved_by_name ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

    </div>{{-- end flex-col --}}

    {{-- Confirm Modal --}}
    <div x-cloak x-show="showConfirmModal" x-transition.opacity class="fixed inset-0 bg-black/40 z-[70]" @click="showConfirmModal = false"></div>
    <div x-cloak x-show="showConfirmModal" x-transition class="fixed inset-0 z-[80] flex items-center justify-center px-4 pointer-events-none">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 pointer-events-auto">
            <h2 class="text-lg font-bold text-gray-800 mb-2">Submit Transfer Request</h2>
            <p class="text-sm text-gray-500 mb-6">Are you sure you want to submit this transfer request to Accounting?</p>
            <div class="flex justify-end gap-3">
                <button type="button" @click="showConfirmModal = false" class="px-4 py-2 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50">Cancel</button>
                <button type="button"
                    @click="submitRequest(); showConfirmModal = false"
                    class="px-4 py-2 bg-teal-500 text-white rounded-xl text-sm font-bold hover:bg-teal-600">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>