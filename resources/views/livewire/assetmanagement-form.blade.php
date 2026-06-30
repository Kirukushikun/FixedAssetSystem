<div class="relative overflow-y-auto flex flex-col gap-7"
     x-data="{
         showModal: @entangle('showConfirmModal'),
         modalTemplate: '',
     }"    
 >
     <div
         wire:loading.flex
         wire:target="attachment,trySubmit,submit,update,assignAsset,addRepairRecord,resetChanges"
         class="fixed inset-0 bg-black/30 z-[90] items-center justify-center"
     >
         <div class="bg-white px-5 py-4 rounded-lg shadow-lg flex items-center gap-3 text-sm font-semibold text-gray-700">
             <i class="fa-solid fa-spinner fa-spin text-teal-500"></i>
             <span>Processing request...</span>
         </div>
     </div>

     <div class="card self-center relative">
         
         <i class="fa-solid fa-arrow-left absolute top-8 -left-[50px] cursor-pointer hover:-translate-x-1 text-gray-400 hover:text-gray-800 text-xl" onclick="window.history.back()"></i>
         <h1 class="text-lg font-bold">General Information</h1>
         <p class="text-gray-400 text-sm mb-10">Basic details that describe and identify this asset. These values help classify and track the item within the system.</p>

        <!-- <img  src="{{asset('img/QR-Code.png')}}" width="120" alt=""> -->

        @if($mode != 'create')
            <div class=" inline-block group">

                <!-- QR -->
                <img src="{{ asset('storage/' . $qr_code) }}" 
                    class="w-[90px] absolute top-[20px] right-[25px]">

                <!-- Overlay -->
                <div class="absolute top-[20px] right-[25px] w-[90px] h-[90px] 
                        bg-black/40 rounded flex items-center justify-center gap-3">
                <!-- View -->
                <span @click="modalTemplate = 'qr', showModal = true" class="cursor-pointer">
                    <i class="fa-solid fa-eye text-white text-xl"></i>
                </span>
                <!-- Download -->
                <a href="{{ asset('storage/' . $qr_code) }}" download>
                    <i class="fa-solid fa-download text-white text-xl"></i>
                </a>
            </div>

            </div>
        @endif

        <div class="grid grid-cols-4 gap-5">
            <div class="input-group">
                <label for="ref_id">Reference ID: </label>
                <input type="text" id="ref_id" value="{{$ref_id}}" readonly>
            </div>
            <div class="input-group">
                <label for="category_type">Category Type:</label>
                <input type="text" id="category_type" value="{{$category_type}}" readonly>
            </div>
            <div class="input-group">
                <label for="category">Category:</label>
                <input type="text" id="category" value="{{$categoryCodeImage[$category]->name}}" readonly>
            </div>
            <div class="input-group">
                <label for="sub_category">Sub-category:</label>
                <input type="text" id="sub_category" value="{{$sub_category}}" readonly>
            </div>

            @if($category_type !== 'IT')
            <div class="input-group">
                <label for="serial_no">Serial No: @error('serial_no')<span>This field is required</span>@enderror</label>
                <input type="text" id="serial_no" class="{{ $errors->has('serial_no') ? '!border-red-400' : '' }}" wire:model="serial_no" {{$mode == 'view' ? 'readonly' : ''}}>
            </div>
            @endif
            <div class="input-group">
                <label for="brand">Brand: @error('brand')<span>This field is required</span>@enderror</label>
                <select id="brand" class="{{ $errors->has('brand') ? '!border-red-400' : '' }}" wire:model="brand" {{$mode == 'view' ? 'disabled' : ''}}>
                    <option value=""></option>
                    @foreach($brands as $brandOption)
                        <option value="{{ $brandOption }}">{{ $brandOption }}</option>
                    @endforeach
                </select>
            </div>
            <div class="input-group">
                <label for="model">Model: @error('model')<span>This field is required</span>@enderror</label>
                <input type="text" id="model" class="{{ $errors->has('model') ? '!border-red-400' : '' }}" wire:model="model" {{$mode == 'view' ? 'readonly' : ''}}>
            </div>
            <div class="input-group">
                <label for="status">Status: @error('status')<span>This field is required</span>@enderror</label>
                <select id="status" class="{{ $errors->has('status') ? '!border-red-400' : '' }}" wire:model="status" {{$mode == 'view' ? 'disabled' : ''}}>
                    <option value=""></option>
                    <option value="Available">Available</option>
                    <option value="Issued">Issued</option>
                    <option value="Transferred">Transferred</option>
                    <option value="For Disposal">For Disposal</option>
                    @if($mode == 'view' || $status === 'Disposed')
                        <option value="Disposed">Disposed</option>
                    @endif
                    <option value="Lost">Lost</option>
                </select>
            </div>
            <div class="input-group">
                <label for="condition">Condition: @error('condition')<span>This field is required</span>@enderror</label>
                <select id="condition" class="{{ $errors->has('condition') ? '!border-red-400' : '' }}" wire:model="condition" {{$mode == 'view' ? 'disabled' : ''}}>
                    <option value=""></option>
                    <option value="Good">Good</option>
                    <option value="Repair">Repair</option>
                    <option value="Defective">Defective</option>
                    <option value="Replace">Replace</option>
                </select>
            </div>

            <div class="input-group">
                <label for="acquisition_date">Acquisition Date: @error('acquisition_date')<span>This field is required</span>@enderror</label>
                <input type="date" id="acquisition_date" class="{{ $errors->has('acquisition_date') ? '!border-red-400' : '' }}" wire:model="acquisition_date" {{$mode == 'view' ? 'readonly' : ''}}>
            </div>
            <div class="input-group">
                <label for="item_cost">Item Cost:</label>
                <input type="text" id="item_cost" wire:model="item_cost" {{$mode == 'view' ? 'readonly' : ''}}>
            </div>
            <div class="input-group">
                <label for="depreciated_value">Depreciated Value:</label>
                <input type="text" id="depreciated_value" wire:model="depreciated_value" {{$mode == 'view' ? 'readonly' : ''}}>
            </div>
            <div class="input-group">
                <label for="usable_life">Usable Life:</label>
                <input type="text" id="usable_life" wire:model="usable_life" {{$mode == 'view' ? 'readonly' : ''}}>
            </div>

            @if($mode == 'view' && $acquisition_date)
                <div class="input-group">
                    <label for="age">Age:</label>
                    <input type="text" id="age" value="{{ \Carbon\Carbon::parse($acquisition_date)->diffForHumans(null, true) }}" readonly>
                </div>
            @endif

        </div>
        
        @if($category_type == 'IT')
            <hr class="mt-7 mb-7">

            <h1 class="text-lg font-bold">Technical Details</h1>
            <p class="text-gray-400 text-sm mb-5">Additional specifications used for IT-related assets. These details are helpful for troubleshooting, configuration, and inventory auditing.</p>
            <div class="grid grid-cols-4 gap-5">
                <div class="input-group">
                    <label for="serial">Serial No:</label>
                    <input type="text" id="serial" wire:model="technicaldata.serial" {{$mode == 'view' ? 'readonly' : ''}}>
                </div>
                <div class="input-group">
                    <label for="processor">Processor:</label>
                    <select id="processor" wire:model="technicaldata.processor" {{$mode == 'view' ? 'disabled' : ''}}>
                        <option value=""></option>
                        @foreach($processors as $processorOption)
                            <option value="{{ $processorOption }}">{{ $processorOption }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="input-group">
                    <label for="ram">RAM:</label>
                    <select id="ram" wire:model="technicaldata.ram" {{$mode == 'view' ? 'disabled' : ''}}>
                        <option value=""></option>
                        @foreach($rams as $ramOption)
                            <option value="{{ $ramOption }}">{{ $ramOption }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="input-group">
                    <label for="storage">Storage:</label>
                    <select id="storage" wire:model="technicaldata.storage" {{$mode == 'view' ? 'disabled' : ''}}>
                        <option value=""></option>
                        @foreach($storages as $storageOption)
                            <option value="{{ $storageOption }}">{{ $storageOption }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="input-group">
                    <label for="ip_address">IP Address:</label>
                    <input type="text" id="ip_address" wire:model="technicaldata.ip_address" {{$mode == 'view' ? 'readonly' : ''}}>
                </div>
                <div class="input-group">
                    <label for="mac_address">MAC Address:</label>
                    <input type="text" id="mac_address" wire:model="technicaldata.mac_address" {{$mode == 'view' ? 'readonly' : ''}}>
                </div>
                <div class="input-group">
                    <label for="vpn_address">VPN Address:</label>
                    <input type="text" id="vpn_address" wire:model="technicaldata.vpn_address" {{$mode == 'view' ? 'readonly' : ''}}>
                </div>
                <div class="input-group">
                    <label for="wol_enabled">WOL Enabled:</label>
                    <select id="wol_enabled" wire:model="technicaldata.wol_enabled" {{$mode == 'view' ? 'disabled' : ''}}>
                        <option value=""></option>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>                    
                    </select>
                </div>
            </div>
        @endif
        <hr class="mt-7 mb-7">

        <h1 class="text-lg font-bold">Assignment Details</h1>
        <p class="text-gray-400 text-sm mb-5">Information on where this asset is currently assigned, including the responsible employee and location.</p>
        <div class="grid grid-cols-4 gap-5">
            <!-- EMPLOYEE SELECT - FIXED: Added selected attribute to preserve value after re-render -->
            <div class="input-group">
                <label>Assigned To:</label>
                @if($mode == 'edit' || $mode == 'view' && $targetAsset->assigned_name)
                    <input type="text" value="{{$targetAsset->assigned_name}}" readonly>
                @else 
                    <select wire:model.live="selectedEmployee" {{ $mode == 'view' ? 'disabled' : '' }}>
                        <option value="">Select</option>
                        @foreach ($employees as $emp)
                            <option value="{{ $emp['id'] }}" {{ $selectedEmployee == $emp['id'] ? 'selected' : '' }}>
                                {{ $emp['employee_name'] }}
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>

            <!-- FARM -->
            <div class="input-group">
                <label>Farm:</label>
                @if($selectedEmployee || $mode == 'view' || $mode == 'edit')
                    <input type="text" wire:model="farm" readonly>
                @else
                    <select wire:model="farm" {{ $mode == 'view' ? 'disabled' : '' }}>
                        <option value="">Select Farm</option>
                        @foreach($farms as $farmOption)
                            <option value="{{ $farmOption }}">{{ $farmOption }}</option>
                        @endforeach
                    </select>
                @endif
            </div>

            <!-- DEPARTMENT -->
            <div class="input-group">
                <label>Department/Division:</label>
                @if($selectedEmployee || $mode == 'view' || $mode == 'edit')
                    <input type="text" wire:model="department" readonly>
                @else
                    <select wire:model="department" {{ $mode == 'view' ? 'disabled' : '' }}>
                        <option value="">Select Department</option>
                        @foreach($departments as $deptOption)
                            <option value="{{ $deptOption }}">{{ $deptOption }}</option>
                        @endforeach
                    </select>
                @endif
            </div>

            <!-- LOCATION -->
            <div class="input-group">
                <label>Location:</label>
                @if($mode == 'view')
                    <input type="text" wire:model="location" readonly>
                @else
                    <input type="text" wire:model="location">
                @endif
            </div>
        </div>

        <div class="flex flex-col gap-5 mt-5"> 
            @if($mode == 'create')
                <div class="file-group flex flex-col gap-2">
                    <label for="attachment" class="text-[15px] font-semibold relative">
                        Attachment(s):
                        @error('attachment')
                            <span class="absolute bg-white text-red-600 right-0 bottom-[-20px] text-xs p-1">
                                {{ $message }}
                            </span>
                        @enderror
                    </label>

                    <!-- Same layout container -->
                    <div class="flex w-full border border-gray-400 rounded-md overflow-hidden text-sm relative">

                        <!-- Clickable Upload Button -->
                        <button
                            type="button"
                            wire:loading.attr="disabled"
                            wire:target="attachment"
                            class="bg-gray-600 text-white px-4 py-2 cursor-pointer hover:bg-gray-500 disabled:opacity-60 disabled:cursor-not-allowed"
                            @click="$refs.attachment.click()"
                        >
                            <span wire:loading.remove wire:target="attachment">Upload File</span>
                            <span wire:loading.inline-flex wire:target="attachment" class="items-center gap-2">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                                <span>Uploading...</span>
                            </span>
                        </button>

                        <!-- Filename or placeholder -->
                        <div class="flex-1 bg-gray-50 text-gray-500 px-4 py-2">
                            <span wire:loading.remove wire:target="attachment">{{ $attachment ? $attachment->getClientOriginalName() : 'No file attached' }}</span>
                            <span wire:loading.inline wire:target="attachment">Uploading selected file...</span>
                        </div>

                        <!-- Hidden Real Input -->
                        <input 
                            x-ref="attachment"
                            type="file"
                            class="hidden"
                            wire:model="attachment"
                            accept="application/pdf"
                        >
                    </div>
                </div>
            @else 
                <div class="file-group flex flex-col gap-2">
                    <label class="text-[15px] font-semibold">Attachment(s):</label>

                    <div class="flex w-full border border-gray-400 rounded-md overflow-hidden text-sm">
                        @if($attachment)
                            <a href="{{ Storage::url($attachment) }}" 
                            target="_blank" 
                            class="bg-gray-600 text-white px-4 py-2 cursor-pointer hover:bg-gray-500"
                            >
                                View File
                            </a>

                            <div class="flex-1 bg-gray-50 text-gray-500 px-4 py-2">
                                {{ $attachment_name }}
                            </div>

                        @else 
                            <div class="bg-gray-600 text-white px-4 py-2 cursor-pointer hover:bg-gray-500" disabled>
                                View File
                            </div>
                            
                            <div class="flex-1 bg-gray-50 text-gray-500 px-4 py-2">
                                No file attached
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <div class="input-group">
                <label for="">Remarks:</label>
                <textarea name="" id="" wire:model="remarks"></textarea>
            </div>
            
            @if($mode != 'create')
                <div class="input-group">
                    <label class="block mb-2 font-medium">Assignment History:</label>
                    @if($history->isNotEmpty())
                        <table class="w-full border border-gray-300 border-collapse text-sm">
                            <thead>
                                <tr class="bg-gray-50 text-gray-500">
                                    <th class="border border-gray-300 text-left px-2 py-2">Assignee</th>
                                    <th class="border border-gray-300 text-left px-2 py-2">Status</th>
                                    <th class="border border-gray-300 text-left px-2 py-2">Condition</th>
                                    <th class="border border-gray-300 text-left px-2 py-2">Farm</th>
                                    <th class="border border-gray-300 text-left px-2 py-2">Department</th>
                                    <th class="border border-gray-300 text-left px-2 py-2">Action</th>
                                    <th class="border border-gray-300 text-left px-2 py-2">Transaction Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($history as $asset)
                                    <tr>
                                        <td class="border border-gray-300 px-2 py-2">(#{{$asset->assignee_id ?? '—'}}) {{$asset->assignee_name ?? '—'}}</td>
                                        <td class="border border-gray-300 px-2 py-2">{{$asset->status}}</td>
                                        <td class="border border-gray-300 px-2 py-2">{{$asset->condition}}</td>
                                        <td class="border border-gray-300 px-2 py-2">{{$asset->farm ?? '—'}}</td>
                                        <td class="border border-gray-300 px-2 py-2">{{$asset->department ?? '—'}}</td>
                                        <td class="border border-gray-300 px-2 py-2">{{$asset->action}}</td>
                                        <td class="border border-gray-300 px-2 py-2">{{$asset->updated_at->format('m/d/Y')}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-gray-400 text-sm">This asset do not have history yet.</p>
                    @endif
                </div>

                <div class="input-group">
                    <label class="block mb-2 font-medium">Audit History:</label>
                    @if($audits->isNotEmpty())
                        <table class="w-full border border-gray-300 border-collapse text-sm">
                            <thead>
                                <tr class="bg-gray-50 text-gray-500">
                                    <th class="border border-gray-300 text-left px-2 py-2">Date</th>
                                    <th class="border border-gray-300 text-left px-2 py-2">Auditor</th>
                                    <!-- <th class="border border-gray-300 text-left px-2 py-2">Result</th> -->
                                    <th class="border border-gray-300 text-left px-2 py-2">Finding</th>
                                    <th class="border border-gray-300 text-left px-2 py-2">Notes</th>
                                    <th class="border border-gray-300 text-left px-2 py-2">Attachment(s)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($audits as $audit)
                                    <tr>
                                        <td class="border border-gray-300 px-2 py-2">{{$audit->audited_at->format('m/d/Y')}}</td>
                                        <td class="border border-gray-300 px-2 py-2">{{$audit->audited_by_name}}</td>
                                        <td class="border border-gray-300 px-2 py-2">{{$audit->finding ?? 'No specific finding'}}</td>
                                        <td class="border border-gray-300 px-2 py-2">{{$audit->notes ?? 'No notes were added for this audit.'}}</td>
                                        <td class="border border-gray-300 px-2 py-2">
                                            {{$audit->attachment_name ?? 'No files attached'}} 
                                            @if($audit->attachment_path)
                                                <a href="{{ Storage::url($audit->attachment_path) }}" target="_blank" class="ml-1 px-2 py-1 bg-blue-400 rounded-md font-bold text-white text-xs hover:bg-blue-500">
                                                    View
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else 
                        <p class="text-gray-400 text-sm">This asset has not been audited yet.</p>
                    @endif
                </div>

                <div class="input-group">
                    <label class="block mb-2 font-medium">Repair & Maintenance History:</label>
                    @if($repairs->isNotEmpty())
                        <table class="w-full border border-gray-300 border-collapse text-sm">
                            <thead>
                                <tr class="bg-gray-50 text-gray-500">
                                    <th class="border border-gray-300 text-left px-2 py-2">Date</th>
                                    <th class="border border-gray-300 text-left px-2 py-2">Type</th>
                                    <th class="border border-gray-300 text-left px-2 py-2">Cost</th>
                                    <th class="border border-gray-300 text-left px-2 py-2">Notes</th>
                                    <th class="border border-gray-300 text-left px-2 py-2">Source</th>
                                    <th class="border border-gray-300 text-left px-2 py-2">Report</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($repairs as $repair)
                                    <tr>
                                        <td class="border border-gray-300 px-2 py-2">{{ \Carbon\Carbon::parse($repair->date)->format('m/d/Y') }}</td>
                                        <td class="border border-gray-300 px-2 py-2">{{ $repair->type }}</td>
                                        <td class="border border-gray-300 px-2 py-2">{{ $repair->cost ? '₱' . number_format($repair->cost, 2) : '—' }}</td>
                                        <td class="border border-gray-300 px-2 py-2">{{ $repair->notes ?? '—' }}</td>
                                        <td class="border border-gray-300 px-2 py-2">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold
                                                {{ ($repair->source ?? 'Internal') === 'Internal' 
                                                    ? 'bg-teal-100 text-teal-700' 
                                                    : 'bg-orange-100 text-orange-700' }}">
                                                <i class="fa-solid {{ ($repair->source ?? 'Internal') === 'Internal' ? 'fa-building' : 'fa-truck' }} text-[10px]"></i>
                                                {{ $repair->source ?? 'Internal' }}
                                            </span>
                                        </td>     
                                        <td class="border border-gray-300 px-2 py-2">
                                            @if($repair->service_report_path)
                                                <button wire:click="openServiceReport({{ $repair->id }}, 'view')"
                                                    class="px-2 py-1 bg-blue-400 text-white rounded text-xs font-semibold hover:bg-blue-500">
                                                    <i class="fa-solid fa-eye text-[10px] mr-1"></i>View
                                                </button>
                                            @else
                                                <button wire:click="openServiceReport({{ $repair->id }}, 'upload')"
                                                    class="px-2 py-1 bg-orange-400 text-white rounded text-xs font-semibold hover:bg-orange-500">
                                                    <i class="fa-solid fa-upload text-[10px] mr-1"></i>Upload
                                                </button>
                                            @endif
                                        </td>                               
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-gray-400 text-sm">No repair or maintenance records found for this asset.</p>
                    @endif
                </div>

                @if($activeInvestigation || $status === 'Lost')
                <div class="input-group">
                    <label class="block mb-2 font-medium flex items-center gap-2">
                        <span class="inline-block w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                        Lost Asset Investigation
                    </label>
                    @if($activeInvestigation)
                        <div class="rounded-xl border border-red-200 bg-red-50 p-4 flex flex-col gap-3">
                            <div class="flex items-start justify-between gap-4 flex-wrap">
                                <div>
                                    <p class="text-sm font-semibold text-red-700">Status: Under Investigation</p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        Opened by {{ $activeInvestigation->opened_by_name ?? '—' }}
                                        on {{ $activeInvestigation->created_at->format('M d, Y h:i A') }}
                                    </p>
                                    @if($activeInvestigation->notes)
                                        <p class="text-xs text-gray-600 mt-1 italic">{{ $activeInvestigation->notes }}</p>
                                    @endif
                                </div>
                                @php($invUser = Auth::user())
                                @if($invUser?->hasPermission('assets.edit'))
                                <div class="flex gap-2 shrink-0">
                                    <button type="button" wire:click="openResolveModal('found')"
                                        class="px-3 py-1.5 bg-teal-500 text-white text-xs font-bold rounded-lg hover:bg-teal-600">
                                        Mark as Found
                                    </button>
                                    <button type="button" wire:click="openResolveModal('written_off')"
                                        class="px-3 py-1.5 bg-gray-700 text-white text-xs font-bold rounded-lg hover:bg-gray-800">
                                        Write Off
                                    </button>
                                </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <p class="text-xs text-gray-400">Asset is marked as Lost. Save to automatically open an investigation record.</p>
                    @endif
                </div>
                @endif

                <div class="input-group">
                    <label class="block mb-2 font-medium">Disposal Record:</label>
                    @if($latestDisposalRequest)
                        <table class="w-full border border-gray-300 border-collapse text-sm">
                            <thead>
                                <tr class="bg-gray-50 text-gray-500">
                                    <th class="border border-gray-300 text-left px-2 py-2">Reason</th>
                                    <th class="border border-gray-300 text-left px-2 py-2">Approval Date</th>
                                    <th class="border border-gray-300 text-right px-2 py-2">Form</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="border border-gray-300 px-2 py-2">{{ $latestDisposalRequest->reason }}</td>
                                    <td class="border border-gray-300 px-2 py-2">{{ optional($latestDisposalRequest->vp_approved_at)->format('m/d/Y h:i A') ?: 'Pending approval' }}</td>
                                    <td class="border border-gray-300 px-2 py-2 text-right">
                                        <a href="{{ route('assets.disposal-form', $targetAsset->id) }}" class="text-teal-600 font-semibold hover:underline">
                                            View Disposal Form
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    @else
                        <p class="text-gray-400 text-sm">No disposal request has been recorded for this asset.</p>
                    @endif
                </div>
            @endif

            <div class="self-end flex gap-3">
                @if($mode == 'edit')
                    @php($assetFormUser = Auth::user())
                    @if(!$targetAsset->assigned_id)
                        @if($assetFormUser?->hasRole('purchasing'))
                            <button class="px-5 py-3 bg-blue-400 rounded-lg font-bold text-white text-xs hover:bg-blue-500"
                                @click="modalTemplate = 'assign', showModal = true">ASSIGN ASSET</button>
                        @endif
                    @endif
                @endif
                @if($mode == 'edit' || $mode == 'view')
                    @php($assetFormUser = Auth::user())
                    @if($mode == 'edit' || ($mode == 'view' && $assetFormUser?->hasRole('sme')))
                        @if(!$assetFormUser?->hasRole('accounting'))
                            <button class="px-5 py-3 bg-orange-400 rounded-lg font-bold text-white text-xs hover:bg-orange-500 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-orange-400"
                                :disabled="!{{ $assetFormUser?->hasPermission('assets.repair') ? 'true' : 'false' }}"
                                @click="{{ $assetFormUser?->hasPermission('assets.repair') ? 'modalTemplate = \'repair\'; showModal = true' : 'null' }}"
                                title="{!! $assetFormUser?->hasPermission('assets.repair') ? 'Add Repair Record' : 'You do not have permission to add repair records' !!}">
                                ADD REPAIR RECORD
                            </button>
                        @endif
                    @endif
                @endif 
                @if($mode != 'view')
                    @if($mode == 'edit')
                        <button class="px-5 py-3 border border-2 border-gray-300 rounded-lg font-bold text-gray-600 text-xs hover:bg-gray-200 disabled:opacity-60 disabled:cursor-not-allowed" 
                            wire:click="resetChanges()"
                            wire:loading.attr="disabled"
                            wire:target="resetChanges">
                            <span wire:loading.remove wire:target="resetChanges">RESET CHANGES</span>
                            <span wire:loading.inline wire:target="resetChanges">RESETTING...</span>
                        </button>
                    @endif
                    <button class="px-5 py-3 bg-[#4fd1c5] rounded-lg font-bold text-white text-xs hover:bg-teal-500 disabled:opacity-60 disabled:cursor-not-allowed" 
                        wire:click="trySubmit()" @click="modalTemplate = 'submit'"
                        wire:loading.attr="disabled"
                        wire:target="trySubmit,submit,update">
                        <span wire:loading.remove wire:target="trySubmit,submit,update">SAVE</span>
                        <span wire:loading.inline wire:target="trySubmit,submit,update">PROCESSING...</span>
                    </button> 
                @endif
            </div>
        </div>

    </div>    

    
    <!-- Backdrop -->
    <div 
        x-show="showModal"
        x-transition.opacity
        class="fixed inset-0 bg-black/30 z-40"
    ></div>

    <!-- Modal -->
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
        <div class="relative bg-white p-10 rounded-lg shadow-lg">
            <div class="absolute right-7 top-7 text-gray-400 cursor-pointer hover:text-gray-800" @click="showModal = false"><i class="fa-solid fa-xmark"></i></div>
            
            <!-- SUBMIT MODAL -->
            <div class="flex flex-col gap-5 w-[23rem]" x-show="modalTemplate === 'submit'">
                <h2 class="text-xl font-semibold -mb-2">Save Asset</h2>
                <p>Do you want to save this asset? Make sure all required details are correct.</p>

                <div class="flex justify-end gap-3">
                    <button type="button" @click="showModal = false;" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-100 cursor-pointer">Cancel</button>
                    @if($mode == 'create')
                        <button type="button" @click="showModal = false; $wire.submit()" wire:loading.attr="disabled" wire:target="submit" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-800 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="submit">Confirm</span>
                            <span wire:loading.inline wire:target="submit">Saving...</span>
                        </button>
                    @else 
                        <button type="button" @click="showModal = false; $wire.update()" wire:loading.attr="disabled" wire:target="update" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-800 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="update">Confirm</span>
                            <span wire:loading.inline wire:target="update">Saving...</span>
                        </button>
                    @endif
                </div>
            </div>
            
            @if($mode != 'create')
                <div class="flex flex-col gap-4 w-[26rem]" x-show="modalTemplate === 'assign'">
                    <div>
                        <h2 class="text-xl font-semibold">Assign Asset</h2>
                        <p class="text-sm text-gray-400 mt-1">Assign this asset to an employee. Changes apply only after saving.</p>
                    </div>

                    <hr>

                    <!-- New Holder -->
                    <div class="input-group">
                        <label class="text-xs text-gray-500 uppercase font-semibold">Assign To</label>
                        <select wire:model.live="newHolder">
                            <option value="">Select employee...</option>
                            @foreach ($employees as $emp)
                                <option value="{{ $emp['id'] }}">{{ $emp['employee_name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Employee Reference -->
                    <div class="bg-gray-50 rounded-lg p-3 flex flex-col gap-3 border border-gray-200">
                        <p class="text-xs font-semibold text-gray-400 uppercase">Employee Reference</p>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="input-group">
                                <label class="text-xs text-gray-500">Farm</label>
                                <input type="text" wire:model="transferFarm" readonly 
                                    class="bg-white text-gray-600" placeholder="—">
                            </div>
                            <div class="input-group">
                                <label class="text-xs text-gray-500">Department</label>
                                <input type="text" wire:model="transferDepartment" readonly 
                                    class="bg-white text-gray-600" placeholder="—">
                            </div>
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="input-group">
                        <label class="text-xs text-gray-500 uppercase font-semibold">Location</label>
                        <input type="text" wire:model="newLocation" placeholder="Enter location">
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="showModal = false" 
                            class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-100 text-sm">Cancel</button>
                        <button type="button" @click="showModal = false; $wire.assignAsset()" wire:loading.attr="disabled" wire:target="assignAsset"
                            class="px-4 py-2 bg-gray-700 text-white rounded-md hover:bg-gray-800 text-sm font-semibold disabled:opacity-60 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="assignAsset">Confirm Assignment</span>
                            <span wire:loading.inline wire:target="assignAsset">Processing...</span>
                        </button>
                    </div>
                </div>

                <div class="flex flex-col gap-4 w-[26rem]" x-show="modalTemplate === 'repair'">
                    <div>
                        <h2 class="text-xl font-semibold">Add Repair / Maintenance Record</h2>
                        <p class="text-sm text-gray-400 mt-1">Log a repair or maintenance activity for this asset.</p>
                    </div>

                    <hr>

                    <div class="input-group">
                        <label class="text-xs text-gray-500 uppercase font-semibold">Date</label>
                        <input type="date" wire:model="repair_date">
                    </div>

                    <div class="input-group">
                        <label class="text-xs text-gray-500 uppercase font-semibold">Type</label>
                        <select wire:model="repair_type">
                            <option value="">Select type...</option>
                            <option value="PMS">PMS (Preventive Maintenance)</option>
                            <option value="Regular Maintenance">Regular Maintenance</option>
                            <option value="Repair">Repair</option>
                        </select>
                    </div>

                    <div class="input-group">
                        <label class="text-xs text-gray-500 uppercase font-semibold">Cost</label>
                        <input type="number" wire:model="repair_cost" placeholder="0.00" min="0">
                    </div>

                    <div class="input-group">
                        <label class="text-xs text-gray-500 uppercase font-semibold">Notes</label>
                        <textarea wire:model="repair_notes" rows="3" placeholder="Describe the repair or maintenance done..."></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="showModal = false" 
                            class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-100 text-sm">Cancel</button>
                        <button type="button" @click="showModal = false; $wire.addRepairRecord()" wire:loading.attr="disabled" wire:target="addRepairRecord"
                            class="px-4 py-2 bg-orange-400 text-white rounded-md hover:bg-orange-500 text-sm font-semibold disabled:opacity-60 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="addRepairRecord">Save Record</span>
                            <span wire:loading.inline wire:target="addRepairRecord">Saving...</span>
                        </button>
                    </div>
                </div>

                <div class="flex flex-col gap-5 items-center" x-show="modalTemplate === 'qr'">
                    <h2 class="text-xl font-semibold self-start">QR Code — {{ $ref_id }}</h2>
                    <img src="{{ asset('storage/' . $qr_code) }}" class="w-64 h-64">
                    <a href="{{ asset('storage/' . $qr_code) }}" download 
                    class="w-full text-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-800 cursor-pointer text-sm font-bold">
                        DOWNLOAD
                    </a>
                </div>
            @endif
            
        </div>

    </div>

    {{-- Service Report Modal --}}
@if($showServiceReportModal)
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black/30 z-[90]" wire:click="closeServiceReport"></div>

    {{-- Panel --}}
    <div class="fixed inset-0 z-[100] flex items-center justify-center px-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-8 flex flex-col gap-5">

            {{-- Close --}}
            <button class="absolute right-5 top-5 text-gray-400 hover:text-gray-700"
                wire:click="closeServiceReport">
                <i class="fa-solid fa-xmark"></i>
            </button>

            @if($serviceReportMode === 'upload')
                <div>
                    <h2 class="text-lg font-semibold">Upload Service Report</h2>
                    <p class="text-sm text-gray-400 mt-1">Attach the completed service report for this repair entry.</p>
                </div>

                <hr>

                <div class="input-group">
                    <label class="text-xs text-gray-500 uppercase font-semibold">Remarks / Additional Notes</label>
                    <textarea wire:model="serviceReportRemarks" rows="3"
                        placeholder="Any notes from the technician or findings..."></textarea>
                    @error('serviceReportRemarks')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-xs text-gray-500 uppercase font-semibold">
                        Service Report File
                        @error('serviceReportFile')
                            <span class="text-red-500 ml-1 normal-case">{{ $message }}</span>
                        @enderror
                    </label>
                    <div class="flex w-full border border-gray-300 rounded-md overflow-hidden text-sm">
                        <button type="button"
                            wire:loading.attr="disabled"
                            wire:target="serviceReportFile"
                            class="bg-gray-600 text-white px-4 py-2 hover:bg-gray-500 disabled:opacity-60"
                            @click="$refs.srFile.click()">
                            <span wire:loading.remove wire:target="serviceReportFile">Choose File</span>
                            <span wire:loading.inline-flex wire:target="serviceReportFile"
                                class="items-center gap-2">
                                <i class="fa-solid fa-spinner fa-spin"></i> Uploading...
                            </span>
                        </button>
                        <div class="flex-1 bg-gray-50 text-gray-500 px-4 py-2">
                            {{ $serviceReportFile ? $serviceReportFile->getClientOriginalName() : 'No file chosen (PDF, JPG, PNG — max 5MB)' }}
                        </div>
                        <input x-ref="srFile" type="file" class="hidden"
                            wire:model="serviceReportFile"
                            accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" wire:click="closeServiceReport"
                        class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-100 text-sm">
                        Cancel
                    </button>
                    <button type="button" wire:click="submitServiceReport"
                        wire:loading.attr="disabled"
                        wire:target="submitServiceReport"
                        class="px-4 py-2 bg-orange-400 text-white rounded-md hover:bg-orange-500 text-sm font-semibold disabled:opacity-60">
                        <span wire:loading.remove wire:target="submitServiceReport">Save Report</span>
                        <span wire:loading.inline wire:target="submitServiceReport">Saving...</span>
                    </button>
                </div>

            @elseif($serviceReportMode === 'view' && $viewingRepair)
                <div>
                    <h2 class="text-lg font-semibold">Service Report</h2>
                    <p class="text-sm text-gray-400 mt-1">
                        {{ \Carbon\Carbon::parse($viewingRepair->date)->format('m/d/Y') }}
                        — {{ $viewingRepair->type }}
                    </p>
                </div>

                <hr>

                <div class="flex flex-col gap-1">
                    <p class="text-xs text-gray-400 uppercase font-semibold">Remarks</p>
                    <p class="text-sm text-gray-700">
                        {{ $viewingRepair->service_report_remarks ?: 'No remarks provided.' }}
                    </p>
                </div>

                <div class="flex flex-col gap-1">
                    <p class="text-xs text-gray-400 uppercase font-semibold">Attached File</p>
                    <div class="flex w-full border border-gray-300 rounded-md overflow-hidden text-sm">
                        <a href="{{ Storage::url($viewingRepair->service_report_path) }}"
                            target="_blank"
                            class="bg-gray-600 text-white px-4 py-2 hover:bg-gray-500">
                            View File
                        </a>
                        <div class="flex-1 bg-gray-50 text-gray-500 px-4 py-2">
                            {{ $viewingRepair->service_report_name }}
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="button" wire:click="closeServiceReport"
                        class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-100 text-sm">
                        Close
                    </button>
                </div>
            @endif

        </div>
    </div>
@endif

{{-- Resolve Investigation Modal --}}
@if($showResolveModal)
    <div class="fixed inset-0 bg-black/40 z-[70]" wire:click="closeResolveModal"></div>
    <div class="fixed inset-0 z-[80] flex items-center justify-center px-4 pointer-events-none">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 pointer-events-auto flex flex-col gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0
                    {{ $resolveAction === 'found' ? 'bg-teal-100' : 'bg-gray-700' }}">
                    <i class="fa-solid {{ $resolveAction === 'found' ? 'fa-magnifying-glass text-teal-600' : 'fa-ban text-white' }} text-sm"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800">
                        {{ $resolveAction === 'found' ? 'Mark Asset as Found' : 'Write Off Asset' }}
                    </h2>
                    <p class="text-xs text-gray-400">
                        {{ $resolveAction === 'found'
                            ? 'Asset status will be reset to Available.'
                            : 'Asset will be permanently marked as Disposed.' }}
                    </p>
                </div>
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="text-sm font-semibold text-gray-700">Resolution Notes <span class="text-gray-400 font-normal">(optional)</span></label>
                <textarea wire:model="investigationNotes" rows="3"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-400 focus:border-transparent"
                    placeholder="Add any notes about the resolution..."></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" wire:click="closeResolveModal"
                    class="px-4 py-2 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="button"
                    wire:click="resolveInvestigation"
                    wire:loading.attr="disabled"
                    wire:target="resolveInvestigation"
                    class="px-4 py-2 rounded-xl text-sm font-bold text-white disabled:opacity-60
                        {{ $resolveAction === 'found' ? 'bg-teal-500 hover:bg-teal-600' : 'bg-gray-700 hover:bg-gray-800' }}">
                    <span wire:loading.remove wire:target="resolveInvestigation">
                        {{ $resolveAction === 'found' ? 'Confirm Found' : 'Confirm Write-Off' }}
                    </span>
                    <span wire:loading wire:target="resolveInvestigation">
                        <i class="fa-solid fa-spinner fa-spin mr-1"></i>Saving...
                    </span>
                </button>
            </div>
        </div>
    </div>
@endif
</div>