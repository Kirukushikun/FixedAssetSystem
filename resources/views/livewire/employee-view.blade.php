<div class="flex flex-1 flex-col gap-5" x-data="{
    showModal: false,
    modalTemplate: '',
    selectedFlagId: null,
    selectedAssetId: null,
}">
    @php($employeeViewUser = Auth::user())
    <div class="card flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-400">#{{$employee->employee_id}}</p>
            <h1 class="text-lg font-bold">{{$employee->employee_name}}</h1>
            <p class="text-sm text-gray-400">{{$employee->position}} | {{$employee->farm}} | {{$employee->department}}</p>
        </div>
    </div>

    <div class="card flex flex-col gap-4">
        <div class="flex items-center justify-between">
            <h1 class="text-lg font-bold">SME Insights</h1>
            <span class="text-xs font-semibold px-3 py-1 rounded-lg bg-teal-50 text-teal-600">Visible to HR</span>
        </div>

        @if($smeReviews->isEmpty())
            <p class="text-sm text-gray-400">No SME reviews have been recorded for this employee yet.</p>
        @else
            <div class="flex flex-col gap-3 text-sm">
                @foreach($smeReviews as $review)
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="font-bold text-gray-800">{{ $review->asset?->brand }} {{ $review->asset?->model }}</p>
                                <p class="text-xs text-gray-500">{{ $review->asset?->ref_id ?? 'Asset record unavailable' }} • {{ $review->asset?->sub_category ?? 'N/A' }}</p>
                            </div>
                            <p class="text-xs text-gray-400">{{ $review->created_at->format('m/d/Y h:i A') }}</p>
                        </div>
                        <div class="mt-3 grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <p class="text-xs uppercase font-semibold text-gray-400">Condition Note</p>
                                <p class="font-semibold text-gray-700">{{ $review->condition_note ?: '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase font-semibold text-gray-400">Recommended Flag</p>
                                <p class="font-semibold text-gray-700">{{ $review->recommended_flag ?: '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase font-semibold text-gray-400">Reviewed By</p>
                                <p class="font-semibold text-gray-700">{{ $review->reviewed_by_name ?: 'System' }}</p>
                            </div>
                        </div>
                        <div class="mt-3">
                            <p class="text-xs uppercase font-semibold text-gray-400">Remarks</p>
                            <p class="text-gray-700">{{ $review->remarks ?: 'No remarks provided.' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="card flex flex-col gap-4">
        <div class="flex items-center justify-between">
            <h1 class="text-lg font-bold">Active Flags</h1>
            <i class="fa-solid fa-pen-to-square cursor-pointer text-gray-400"></i>
        </div>

        @php
            $flagColors = [
                'Under Investigation' => 'text-blue-500',
                'Pending Clearances' => 'text-purple-500',
                'Lost Asset' => 'text-red-500',
                'Unreturned Asset' => 'text-orange-500',
                'Damaged Asset' => 'text-yellow-500',
            ];
        @endphp

        <!-- Flexible flag list -->
        <div class="flex flex-col gap-3 text-sm">
            @forelse($flags as $flag)
                <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-flag {{ $flagColors[$flag->flag_type] ?? 'text-gray-500' }}"></i>
                        <span>{{ $flag->flag_type }} - {{ $flag->asset }}@if(!empty($flag->source)) <span class="text-xs text-gray-400">({{ $flag->source }})</span>@endif</span>
                    </div>
                    @if($employeeViewUser?->hasPermission('employees.update'))
                        <button 
                            class="px-3 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600 transition disabled:opacity-50 disabled:cursor-not-allowed"
                            @click="showModal = true; modalTemplate = 'resolve'; selectedFlagId = {{ $flag->id }}"
                        >
                            <i class="fa-solid fa-check mr-1"></i>RESOLVE
                        </button>
                    @else
                        <button 
                            class="px-3 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600 transition disabled:opacity-50 disabled:cursor-not-allowed"
                            disabled
                            title="You do not have permission to resolve flags"
                        >
                            <i class="fa-solid fa-check mr-1"></i>RESOLVE
                        </button>
                    @endif
                </div>
            @empty
                <div class="text-gray-400">
                    No flags yet
                </div>
            @endforelse
        </div>

        <button class="px-5 py-2 bg-blue-500 rounded-lg font-bold text-white text-xs hover:bg-blue-600 w-fit disabled:opacity-50 disabled:cursor-not-allowed"
            @disabled="{!! $employeeViewUser?->hasPermission('employees.update') ? '' : 'disabled' !!}"
            @click="$employeeViewUser?->hasPermission('employees.update') ? showModal = true, modalTemplate = 'flag' : null"
            title="{!! $employeeViewUser?->hasPermission('employees.update') ? 'Add New Flag' : 'You do not have permission to add flags' !!}">
            ADD NEW FLAG
        </button>
                @if($flags->isNotEmpty())
                    <button class="px-5 py-2 bg-green-600 rounded-lg font-bold text-white text-xs hover:bg-green-700 w-fit"
                        @click="showModal = true; modalTemplate = 'resolveAll'">
                        MARK ALL AS RESOLVED
                    </button>
                @endif
            </div>
        @endif
    </div>

    <div class="card content flex-1 flex flex-col">
        <div class="table-header flex justify-between items-center">
            <h1 class="text-lg font-bold">Assigned Assets</h1>
            <div class="flex items-center gap-3">
                <button
                    @disabled="{!! ($assets->total() > 0 && $employeeViewUser?->hasPermission('employees.update')) ? '' : 'disabled' !!}"
                    @click="($assets->total() > 0 && $employeeViewUser?->hasPermission('employees.update')) ? showModal = true, modalTemplate = 'unassignAll' : null"
                    class="px-5 py-2 bg-orange-500 rounded-lg font-bold text-white text-xs hover:bg-orange-600 disabled:opacity-50 disabled:cursor-not-allowed"
                    title="{!! ($assets->total() > 0 && $employeeViewUser?->hasPermission('employees.update')) ? 'Unassign All' : ($assets->total() === 0 ? 'No assets to unassign' : 'You do not have permission to unassign assets') !!}">
                    <i class="fa-solid fa-users-slash mr-1"></i>UNASSIGN ALL
                </button>
                <button class="px-5 py-2 bg-gray-200 rounded-lg font-bold text-gray-700 text-xs hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed"
                    @disabled="{!! $employeeViewUser?->hasPermission('forms.view') ? '' : 'disabled' !!}"
                    @click="$employeeViewUser?->hasPermission('forms.view') ? window.location.href='/employees/forms?targetID={{ $employee->id }}' : null"
                    title="{!! $employeeViewUser?->hasPermission('forms.view') ? 'Form Library' : 'You do not have permission to view forms' !!}">
                    <i class="fa-solid fa-folder-open mr-1"></i>FORM LIBRARY
                </button>
                <button class="px-5 py-2 bg-blue-500 rounded-lg font-bold text-white text-xs hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed"
                    @disabled="{!! $employeeViewUser?->hasPermission('forms.accountability') ? '' : 'disabled' !!}"
                    @click="$employeeViewUser?->hasPermission('forms.accountability') ? window.location.href='/accountability-form?targetID={{$employee->id}}' : null"
                    title="{!! $employeeViewUser?->hasPermission('forms.accountability') ? 'Generate Accountability Form' : 'You do not have permission to generate accountability forms' !!}">
                    <i class="fa-solid fa-file-lines mr-1"></i>GENERATE ACCOUNTABILITY FORM
                </button>
                <button class="px-5 py-2 bg-indigo-500 rounded-lg font-bold text-white text-xs hover:bg-indigo-600 disabled:opacity-50 disabled:cursor-not-allowed"
                    @disabled="{!! $employeeViewUser?->hasPermission('forms.transfer') ? '' : 'disabled' !!}"
                    @click="$employeeViewUser?->hasPermission('forms.transfer') ? window.location.href='/transfer-form?targetID={{ $employee->id }}' : null"
                    title="{!! $employeeViewUser?->hasPermission('forms.transfer') ? 'Generate Transfer Form' : 'You do not have permission to generate transfer forms' !!}">
                    <i class="fa-solid fa-right-left mr-1"></i>GENERATE TRANSFER FORM
                </button>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>REFERENCE ID</th>
                        <th>CATEGORY TYPE</th>
                        <th>CATEGORY</th>
                        <th>BRAND</th>
                        <th>MODEL</th>
                        <th>STATUS</th>
                        <th>CONDITION</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assets as $asset)
                        <tr>
                            <td>{{$asset->ref_id}}</td>
                            <td>
                                <p class="flex items-center gap-2"><img src="{{ asset('img/' . $categoryCodeImage[$asset->category]->icon . '.png') }}" style="width: 25px" alt="" /> <span class="font-bold">{{$categoryCodeImage[$asset->category]->name}}</span></p>
                            </td>
                            <td>{{$asset->sub_category}}</td>
                            <td>{{$asset->brand}}</td>
                            <td>{{$asset->model}}</td>
                            <td>
                                @php 
                                    $statusColor = [
                                            'Available' => 'bg-[#48BB78]',
                                            'Issued' => 'bg-[#ECC94B]',
                                            'Transferred' => 'bg-[#4299E1]',
                                            'For Disposal' => 'bg-[#ED8936]',
                                            'Disposed' => 'bg-[#2D3748]',
                                            'Lost' => 'bg-[#F56565]'
                                    ]
                                @endphp 
                                <div class="px-4 py-1 {{$statusColor[$asset->status]}} text-white w-fit rounded-lg">{{$asset->status}}</div>
                            </td>
                            <td> 
                                @php 
                                    $conditionColor = [
                                            'Good' => 'green',
                                            'Defective' => 'amber',
                                            'Repair' => 'sky',
                                            'Replace' => 'red'
                                    ]
                                @endphp 
                                <div class="text-{{$conditionColor[$asset->condition]}}-500 font-bold uppercase">{{$asset->condition}}</div>
                            </td>
                            <td>
                                <button 
                                    @click="showModal = true; modalTemplate = 'unassign'; selectedAssetId = {{ $asset->id }}"
                                    class="px-3 py-1 bg-orange-500 text-white text-xs rounded hover:bg-orange-600 transition disabled:opacity-50 disabled:cursor-not-allowed"
                                    @disabled="{!! $employeeViewUser?->hasPermission('employees.update') ? '' : 'disabled' !!}"
                                    title="{!! $employeeViewUser?->hasPermission('employees.update') ? 'Unassign' : 'You do not have permission to unassign assets' !!}">
                                    Unassign
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <x-pagination :paginator="$assets" />
    </div>

    <!-- Backdrop -->
    <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-black/30 z-40"></div>

    <!-- Modal Container -->
    <div
        x-show="showModal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
        class="fixed inset-0 flex items-center justify-center z-50"
    >
        <div class="relative bg-white p-8 rounded-lg shadow-lg w-[26rem]">
            <button class="absolute right-7 top-7 text-gray-400 hover:text-gray-800" @click="showModal = false">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <!-- Create Modal -->
            <div class="flex flex-col gap-5" x-show="modalTemplate === 'flag'">
                <h2 class="text-xl font-semibold -mb-2">Flag Employee</h2>

                <div class="input-group">
                    <label>Type of Flag:</label>
                    <select name="" id="" wire:model="flag_type">
                        <option value="">Select type:</option>
                        <option value="Under Investigation">Under Investigation</option>
                        <option value="Unreturned Asset">Unreturned Asset</option>
                        <option value="Pending Clearances">Pending Clearances</option>
                        <option value="Damaged Asset">Damaged Asset</option>
                        <option value="Lost Asset">Lost Asset</option>
                    </select>
                </div>

                <div class="input-group">
                    <label>Asset</label>
                    <select name="" id="" wire:model="asset">
                        <option value="">Select Asset:</option>
                        @foreach($assets as $asset)
                            <!-- UPDATED: Now shows sub_category for more context -->
                            <option value="{{$asset->brand}} {{$asset->model}} ({{$asset->sub_category}})">
                                {{$asset->sub_category}} - {{$asset->brand}} {{$asset->model}}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-3">
                    <button @click="showModal = false" class="px-4 py-2 border rounded hover:bg-gray-100">Cancel</button>
                    <button @click="showModal = false; $wire.call('submitFlag')" 
                            class="px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-800">
                        Confirm
                    </button>
                </div>
            </div>

            <!-- Resolve Single Flag Confirmation -->
            <div class="flex flex-col gap-5" x-show="modalTemplate === 'resolve'">
                <h2 class="text-xl font-semibold -mb-2">Resolve Flag</h2>
                <p>Are you sure you want to resolve this flag?</p>

                <div class="flex justify-end gap-3">
                    <button @click="showModal = false" class="px-4 py-2 border rounded hover:bg-gray-100">
                        Cancel
                    </button>
                    <button @click="showModal = false; $wire.call('resolveFlag', selectedFlagId)" 
                            class="px-4 py-2 bg-red-700 text-white rounded hover:bg-red-800">
                        Confirm
                    </button>
                </div>
            </div>

            <!-- Resolve All Flags Confirmation -->
            <div class="flex flex-col gap-5" x-show="modalTemplate === 'resolveAll'">
                <h2 class="text-xl font-semibold -mb-2">Resolve All Flags</h2>
                <p>Are you sure you want to mark all flags as resolved? This action will resolve {{ count($flags) }} flag(s).</p>

                <div class="flex justify-end gap-3">
                    <button @click="showModal = false" class="px-4 py-2 border rounded hover:bg-gray-100">
                        Cancel
                    </button>
                    <button @click="showModal = false; $wire.call('resolveAllFlags')" 
                            class="px-4 py-2 bg-green-700 text-white rounded hover:bg-green-800">
                        Confirm
                    </button>
                </div>
            </div>

            <!-- Unassign Single Asset Confirmation -->
            <div class="flex flex-col gap-5" x-show="modalTemplate === 'unassign'">
                <h2 class="text-xl font-semibold -mb-2">Unassign Asset</h2>
                <p>Are you sure you want to unassign this asset from <strong>{{ $employee->employee_name }}</strong>?</p>
                <p class="text-sm text-gray-500">The asset will be marked as "Available" and removed from this employee's accountability.</p>

                <div class="flex justify-end gap-3">
                    <button @click="showModal = false" class="px-4 py-2 border rounded hover:bg-gray-100">
                        Cancel
                    </button>
                    <button @click="showModal = false; $wire.call('unassignAsset', selectedAssetId)" 
                            class="px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700">
                        Confirm Unassign
                    </button>
                </div>
            </div>

            <!-- Unassign All Assets Confirmation -->
            <div class="flex flex-col gap-5" x-show="modalTemplate === 'unassignAll'">
                <h2 class="text-xl font-semibold -mb-2">Unassign All Assets</h2>
                <p>Are you sure you want to unassign <strong>ALL {{ $assets->total() }} asset(s)</strong> from <strong>{{ $employee->employee_name }}</strong>?</p>
                <p class="text-sm text-gray-500">All assets will be marked as "Available" and history records will be created for tracking.</p>
                
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3">
                    <p class="text-sm text-yellow-700">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                        <strong>Warning:</strong> This action will unassign all assets at once.
                    </p>
                </div>

                <div class="flex justify-end gap-3">
                    <button @click="showModal = false" class="px-4 py-2 border rounded hover:bg-gray-100">
                        Cancel
                    </button>
                    <button @click="showModal = false; $wire.call('unassignAllAssets')" 
                            class="px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700">
                        Confirm Unassign All
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>