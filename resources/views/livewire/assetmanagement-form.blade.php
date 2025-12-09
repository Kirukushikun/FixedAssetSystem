<div class="relative overflow-y-auto flex flex-col gap-7"
    x-data="{
        showModal: @entangle('showConfirmModal'),
        modalTemplate: '',
    }"    
>
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
                            bg-black/40 rounded flex items-center justify-center 
                            opacity-0 group-hover:opacity-100 transition">
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
                @php
                    $categoryValue = [
                        'it' => 'IT Equipment',
                        'office' => 'Office Furniture',
                        'appliances' => 'Appliances',
                        'audio' => 'Audio Equipment',
                        'tools' => 'Tools & Misc',
                        'kitchen' => 'Kitchen Equipment'
                    ]
                @endphp
                <label for="category">Category:</label>
                <input type="text" id="category" value="{{$categoryValue[$category]}}" readonly>
            </div>
            <div class="input-group">
                <label for="sub_category">Sub-category:</label>
                <input type="text" id="sub_category" value="{{$sub_category}}" readonly>
            </div>

            <div class="input-group">
                <label for="brand">Brand: @error('brand')<span>This field is required</span>@enderror</label>
                <input type="text" id="brand" class="{{ $errors->has('brand') ? '!border-red-400' : '' }}" wire:model="brand" {{$mode == 'view' ? 'readonly' : ''}}>
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
                    <option value="Disposed">Disposed</option>
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
                    <input type="text" id="processor" wire:model="technicaldata.processor" {{$mode == 'view' ? 'readonly' : ''}}>
                </div>
                <div class="input-group">
                    <label for="ram">RAM:</label>
                    <select id="ram" wire:model="technicaldata.ram" {{$mode == 'view' ? 'disabled' : ''}}>
                        <option value=""></option>
                        <option value="4GB">4GB</option>
                        <option value="8GB">8GB</option>
                        <option value="16GB">16GB</option>
                        <option value="32GB">32GB</option>
                        <option value="64GB">64GB</option>
                    </select>
                </div>
                <div class="input-group">
                    <label for="storage">Storage:</label>
                    <select id="storage" wire:model="technicaldata.storage" {{$mode == 'view' ? 'disabled' : ''}}>
                        <option value=""></option>
                        <option value="32GB">32GB</option>
                        <option value="64GB">64GB</option>
                        <option value="128GB">128GB</option>
                        <option value="256GB">256GB</option>
                        <option value="512GB">512GB</option>
                        <option value="1TB">1TB</option>
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
        <div class="grid grid-cols-3 gap-5">
            <!-- EMPLOYEE SELECT -->
            <div class="input-group">
                <label>Assigned To:</label>
                @if($mode == 'view' && $targetAsset->assigned_name)
                    <input type="text" value="{{$targetAsset->assigned_name}}" readonly>
                @else 
                    <select wire:model.live="selectedEmployee" {{ $mode == 'view' || $mode == 'edit' ? 'disabled' : '' }}>
                        <option value="">Select</option>

                        @foreach ($employees as $emp)
                            <option value="{{ $emp['id'] }}">{{ $emp['employee_name'] }}</option>
                        @endforeach
                    </select>
                @endif
            </div>

            <!-- FARM -->
            <div class="input-group">
                <label>Farm:</label>
                <input type="text" wire:model="farm" readonly>
            </div>

            <!-- DEPARTMENT -->
            <div class="input-group">
                <label>Department/Division:</label>
                <input type="text" wire:model="department" readonly>
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
                        <div 
                            class="bg-gray-600 text-white px-4 py-2 cursor-pointer hover:bg-gray-500"
                            @click="$refs.attachment.click()"
                        >
                            Upload File
                        </div>

                        <!-- Filename or placeholder -->
                        <div class="flex-1 bg-gray-50 text-gray-500 px-4 py-2">
                            {{ $attachment ? $attachment->getClientOriginalName() : 'No file attached' }}
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
                                    <th class="border border-gray-300 text-left px-2 py-2">Date Issued</th>
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
                                    <th class="border border-gray-300 text-left px-2 py-2">Notes</th>
                                    <th class="border border-gray-300 text-left px-2 py-2">Attachment(s)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($audits as $audit)
                                    <tr>
                                        <td class="border border-gray-300 px-2 py-2">{{$audit->audited_at->format('m/d/Y')}}</td>
                                        <td class="border border-gray-300 px-2 py-2">{{$audit->audited_by}}</td>
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
            @endif

            <div class="self-end flex gap-3">
                @if($mode == 'edit')
                    @if(!$targetAsset->assigned_id)
                        <button class="px-5 py-3 bg-blue-400 rounded-lg font-bold text-white text-xs hover:bg-blue-500" @click="modalTemplate = 'assign', showModal = true">ASSIGN ASSET</button> 
                    @else 
                        <button class="px-5 py-3 bg-blue-400 rounded-lg font-bold text-white text-xs hover:bg-blue-500" @click="modalTemplate = 'transfer', showModal = true">TRANSFER ASSET</button> 
                    @endif
                @endif 
                @if($mode != 'view')
                    <!-- <button class="px-5 py-3 border border-2 border-gray-300 rounded-lg font-bold text-gray-600 text-xs hover:bg-gray-200" wire:click="submit">RESET</button> -->
                    <button class="px-5 py-3 bg-[#4fd1c5] rounded-lg font-bold text-white text-xs hover:bg-teal-500" wire:click="trySubmit()" @click="modalTemplate = 'submit'">SAVE</button> 
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
                        <button type="button" @click="showModal = false; $wire.submit()" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-800 cursor-pointer">Confirm</button>
                    @else 
                        <button type="button" @click="showModal = false; $wire.update()" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-800 cursor-pointer">Confirm</button>
                    @endif
                </div>
            </div>
            
            @if($mode != 'create')
                <!-- SUBMIT MODAL -->
                <div class="flex flex-col gap-5 w-[23rem]" x-show="modalTemplate === 'transfer'">
                    <h2 class="text-xl font-semibold -mb-2">Transfer Asset</h2>
                    <p>Update the current holder of this asset. This action will create a transfer record and move the accountability to the selected employee.</p>

                    <div class="input-group">
                        <label for="">Current Holder: </label>
                        <input type="text" value="{{$targetAsset->assigned_name ?? 'No current holder'}}" readonly>
                    </div>

                    <div class="input-group">
                        <label for="">New Holder: </label>
                        <select name="" id="" wire:model="newHolder">
                            <option value=""></option>
                            
                            @foreach ($employees as $emp)
                                @if($emp['id'] !=  $targetAsset->assigned_id)
                                    <option value="{{ $emp['id'] }}">{{ $emp['employee_name'] }}</option>
                                @endif
                                
                            @endforeach
                        </select>
                    </div>

                    <div class="input-group">
                        <label for="">Condition: </label>
                        <select name="" id="" wire:model="newCondition">
                            <option value=""></option>
                            <option value="Good">Good</option>
                            <option value="Repair">Repair</option>
                            <option value="Defective">Defective</option>
                            <option value="Replace">Replace</option>
                        </select>
                    </div>

                    <button type="button" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-800 cursor-pointer" @click="showModal = false; $wire.transferAsset()">Confirm</button>
                </div>

                <div class="flex flex-col gap-5 w-[23rem]" x-show="modalTemplate === 'assign'">
                    <h2 class="text-xl font-semibold -mb-2">Assign Asset</h2>
                    <p>Assign the current holder of this asset. This action will assign a record and move the accountability to the selected employee.</p>

                    <div class="input-group">
                        <label for="">New Holder: </label>
                        <select name="" id="" wire:model="newHolder">
                            <option value=""></option>
                            
                            @foreach ($employees as $emp)
                                <option value="{{ $emp['id'] }}">{{ $emp['employee_name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="button" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-800 cursor-pointer" @click="showModal = false; $wire.assignAsset()">Confirm</button>
                </div>
            @endif
        </div>

    </div>
</div>