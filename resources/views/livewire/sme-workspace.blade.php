<div class="flex flex-col gap-5" x-data="{ showConfirm: false, confirmAssetId: null }">
    <div
        wire:loading.flex
        wire:target="saveReview"
        class="fixed inset-0 bg-black/30 z-[90] items-center justify-center"
    >
        <div class="bg-white px-5 py-4 rounded-lg shadow-lg flex items-center gap-3 text-sm font-semibold text-gray-700">
            <i class="fa-solid fa-spinner fa-spin text-teal-500"></i>
            <span>Saving review...</span>
        </div>
    </div>

    @php($user = Auth::user())
    <div class="card flex flex-col gap-4">
        <div>
            <h1 class="text-lg font-bold">Subject Matter Expert Workspace</h1>
            <p class="text-sm text-gray-400">Select an employee, review assigned assets, add condition insights, and flag directly when needed.</p>
            <p class="text-xs text-amber-500 font-semibold mt-1"><i class="fa-solid fa-triangle-exclamation mr-1"></i>This workspace is intended for employee clearance purposes only.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="input-group">
                <label>Filter by Farm</label>
                <select wire:model.live="filterFarm">
                    <option value="">All farms</option>
                    @foreach($farms as $farm)
                        <option value="{{ $farm }}">{{ $farm }}</option>
                    @endforeach
                </select>
            </div>

            <div class="input-group">
                <label>Filter by Department</label>
                <select wire:model.live="filterDepartment">
                    <option value="">All departments</option>
                    @foreach($departments as $department)
                        <option value="{{ $department }}">{{ $department }}</option>
                    @endforeach
                </select>
            </div>

            <div class="input-group">
                <label>Select Employee</label>
                <select wire:model.live="employeeId">
                    <option value="">Choose an employee...</option>
                    @foreach($filteredEmployees as $employee)
                        <option value="{{ $employee['id'] }}">{{ $employee['employee_name'] }} ({{ $employee['employee_id'] }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        @if($selectedEmployee)
            <div class="rounded-2xl border border-teal-100 bg-gradient-to-br from-teal-50 to-white p-5">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-teal-500 text-white flex items-center justify-center text-lg font-bold">
                            {{ strtoupper(substr($selectedEmployee->employee_name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-xs text-teal-600 font-bold uppercase tracking-wide">Selected Employee</p>
                            <h2 class="text-lg font-bold text-gray-800">{{ $selectedEmployee->employee_name }}</h2>
                            <p class="text-sm text-gray-500">Employee #{{ $selectedEmployee->employee_id }}</p>
                        </div>
                    </div>
                    <span class="text-xs font-semibold px-3 py-1 rounded-full bg-white border border-teal-100 text-teal-600">
                        {{ $assets->count() }} assigned asset(s)
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-5 text-sm">
                    <div class="bg-white border border-gray-100 rounded-xl p-3">
                        <p class="text-xs uppercase font-semibold text-gray-400">Farm</p>
                        <p class="font-bold text-gray-700">{{ $selectedEmployee->farm ?: '—' }}</p>
                    </div>
                    <div class="bg-white border border-gray-100 rounded-xl p-3">
                        <p class="text-xs uppercase font-semibold text-gray-400">Department</p>
                        <p class="font-bold text-gray-700">{{ $selectedEmployee->department ?: '—' }}</p>
                    </div>
                    <div class="bg-white border border-gray-100 rounded-xl p-3">
                        <p class="text-xs uppercase font-semibold text-gray-400">Review Scope</p>
                        <p class="font-bold text-gray-700">Assigned assets</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if($selectedEmployee)
        <div class="card flex flex-col gap-5">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold">Assigned Assets for Review</h2>
                <span class="text-sm text-gray-400">{{ $assets->count() }} asset(s)</span>
            </div>

            @if($assets->isEmpty())
                <p class="text-sm text-gray-400">This employee currently has no assigned assets.</p>
            @else
                <div class="flex flex-col gap-4">
                    @foreach($assets as $asset)
                        <div class="border border-gray-200 rounded-xl p-5 flex flex-col gap-4">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-xs text-gray-400">{{ $asset->ref_id }}</p>
                                    <h3 class="font-bold text-gray-800">{{ $asset->brand }} {{ $asset->model }}</h3>
                                    <p class="text-sm text-gray-500">{{ $asset->sub_category }} | {{ $asset->status }} | Current condition: {{ $asset->condition }}</p>
                                </div>
                                <span class="text-xs font-semibold px-3 py-1 rounded-lg bg-teal-50 text-teal-600">SME Review</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="input-group">
                                    <label>Condition Note</label>
                                    <select wire:model="assetReviews.{{ $asset->id }}.condition_note" :disabled="!{{ $user?->hasPermission('sme.review') ? 'true' : 'false' }}">
                                        <option value="">Select note...</option>
                                        <option value="Good">Good</option>
                                        <option value="Repair">Repair</option>
                                        <option value="Defective">Defective</option>
                                        <option value="Replace">Replace</option>
                                        <option value="For Disposal">For Disposal</option>
                                        <option value="Lost">Lost</option>
                                    </select>
                                </div>

                                <div class="input-group">
                                    <label>Recommended Flag</label>
                                    <select wire:model="assetReviews.{{ $asset->id }}.recommended_flag" :disabled="!{{ $user?->hasPermission('sme.review') ? 'true' : 'false' }}">
                                        <option value="">No direct flag</option>
                                        <option value="Pending Clearances">Pending Clearances</option>
                                        <option value="Damaged Asset">Damaged Asset</option>
                                        <option value="Lost Asset">Lost Asset</option>
                                        <option value="Under Investigation">Under Investigation</option>
                                        <option value="Unreturned Asset">Unreturned Asset</option>
                                    </select>
                                </div>
                            </div>

                            <div class="input-group">
                                <label>SME Remarks</label>
                                <textarea rows="4" wire:model="assetReviews.{{ $asset->id }}.remarks" placeholder="Add observations, technical findings, or clearance notes..." :disabled="!{{ $user?->hasPermission('sme.review') ? 'true' : 'false' }}"></textarea>
                            </div>

                            <div class="flex items-center justify-between gap-4 flex-wrap">
                                <label class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600">
                                    <input type="checkbox" wire:model="assetReviews.{{ $asset->id }}.flagged_employee" :disabled="!{{ $user?->hasPermission('sme.review') ? 'true' : 'false' }}">
                                    Directly flag employee based on this review
                                </label>

                                @if($user?->hasPermission('sme.review'))
                                    <button type="button" @click="showConfirm = true; confirmAssetId = {{ $asset->id }}" class="px-4 py-2 bg-teal-500 text-white rounded-lg text-sm font-bold hover:bg-teal-600 transition-colors">
                                        <i class="fa-solid fa-floppy-disk mr-2"></i>Save Review
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    {{-- Confirm Modal --}}
    <div x-cloak x-show="showConfirm" x-transition.opacity class="fixed inset-0 bg-black/40 z-[70]" @click="showConfirm = false"></div>
    <div x-cloak x-show="showConfirm" x-transition class="fixed inset-0 z-[80] flex items-center justify-center px-4 pointer-events-none">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 pointer-events-auto">
            <h2 class="text-lg font-bold text-gray-800 mb-2">Save SME Review</h2>
            <p class="text-sm text-gray-500 mb-6">Are you sure you want to save this review? If the flag option is checked, a flag record will also be created for this employee.</p>
            <div class="flex justify-end gap-3">
                <button type="button" @click="showConfirm = false" class="px-4 py-2 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50">Cancel</button>
                <button type="button"
                    @click="$wire.saveReview(confirmAssetId); showConfirm = false"
                    class="px-4 py-2 bg-teal-500 text-white rounded-xl text-sm font-bold hover:bg-teal-600">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>
