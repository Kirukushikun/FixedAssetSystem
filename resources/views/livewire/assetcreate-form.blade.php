<div class="relative overflow-y-auto flex flex-col gap-7">
    <div class="card self-center">
        <i class="fa-solid fa-arrow-left absolute top-8 left-[190px] cursor-pointer hover:-translate-x-1 text-gray-400 hover:text-gray-800 text-xl" onclick="window.history.back()"></i>
        <h1 class="text-lg font-bold">General Information</h1>
        <p class="text-gray-400 text-sm mb-5">Basic details that describe and identify this asset. These values help classify and track the item within the system.</p>
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
                        'office' => 'Office Furniture'    
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
                <input type="text" id="brand" class="{{ $errors->has('brand') ? '!border-red-400' : '' }}" wire:model="brand">
            </div>
            <div class="input-group">
                <label for="model">Model: @error('model')<span>This field is required</span>@enderror</label>
                <input type="text" id="model" class="{{ $errors->has('model') ? '!border-red-400' : '' }}" wire:model="model">
            </div>
            <div class="input-group">
                <label for="status">Status: @error('status')<span>This field is required</span>@enderror</label>
                <select id="status" class="{{ $errors->has('status') ? '!border-red-400' : '' }}" wire:model="status">
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
                <select id="condition" class="{{ $errors->has('condition') ? '!border-red-400' : '' }}" wire:model="condition">
                    <option value=""></option>
                    <option value="Good">Good</option>
                    <option value="Repair">Repair</option>
                    <option value="Defective">Defective</option>
                    <option value="Replace">Replace</option>
                </select>
            </div>

            <div class="input-group">
                <label for="acquisition_date">Acquisition Date: @error('acquisition_date')<span>This field is required</span>@enderror</label>
                <input type="date" id="acquisition_date" class="{{ $errors->has('acquisition_date') ? '!border-red-400' : '' }}" wire:model="acquisition_date">
            </div>
            <div class="input-group">
                <label for="item_cost">Item Cost:</label>
                <input type="text" id="item_cost" wire:model="item_cost">
            </div>
            <div class="input-group">
                <label for="depreciated_value">Depreciated Value:</label>
                <input type="text" id="depreciated_value" wire:model="depreciated_value">
            </div>
            <div class="input-group">
                <label for="usable_life">Usable Life:</label>
                <input type="text" id="usable_life" wire:model="usable_life">
            </div>
        </div>
        
        @if($category_type == 'IT')
        <hr class="mt-7 mb-7">

        <h1 class="text-lg font-bold">Technical Details</h1>
        <p class="text-gray-400 text-sm mb-5">Additional specifications used for IT-related assets. These details are helpful for troubleshooting, configuration, and inventory auditing.</p>
        <div class="grid grid-cols-4 gap-5">
            <div class="input-group">
                <label for="processor">Processor:</label>
                <input type="text" id="processor" wire:model="processor">
            </div>
            <div class="input-group">
                <label for="ram">RAM:</label>
                <select id="ram" wire:model="ram">
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
                <select id="storage" wire:model="storage">
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
                <input type="text" id="ip_address" wire:model="ip_address">
            </div>
            <div class="input-group">
                <label for="mac_address">MAC Address:</label>
                <input type="text" id="mac_address" wire:model="mac_address">
            </div>
            <div class="input-group">
                <label for="vpn_address">VPN Address:</label>
                <input type="text" id="vpn_address" wire:model="vpn_address">
            </div>
            <div class="input-group">
                <label for="wol_enabled">WOL Enabled:</label>
                <select id="wol_enabled" wire:model="wol_enabled">
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
            <div class="input-group">
                <label for="">Assigned To:</label>
                <select id="">
                    <option value=""></option>
                    <option value="">Chris Bacon</option>                    
                </select>

            </div>
            <div class="input-group">
                <label for="">Farm:</label>
                <input type="text" readonly>
            </div>
            <div class="input-group">
                <label for="">Department/Division:</label>
                <input type="text" readonly>
            </div>
        </div>
        <div class="flex flex-col gap-5 mt-5"> 
            <div class="file-group flex flex-col gap-2">
                <label for="" class="text-[15px] font-semibold">Attachment(s):</label>
                <div class="flex w-full border border-gray-400 rounded-md overflow-hidden text-sm">
                    <div target="_blank" type="button" class="bg-gray-600 text-white px-4 py-2 cursor-pointer hover:bg-gray-500" disabled>
                        Upload File
                    </div>
                    <div class="flex-1 bg-gray-50 text-gray-500 px-4 py-2">
                        No file attached
                    </div> 
                </div>
            </div>

            <div class="input-group">
                <label for="">Remarks:</label>
                <textarea name="" id=""></textarea>
            </div>
            
            <div class="self-end flex gap-3">
                <button class="px-5 py-3 border border-2 border-gray-300 rounded-lg font-bold text-gray-600 text-xs hover:bg-gray-200" wire:click="submit">RESET</button>
                <button class="px-5 py-3 bg-[#4fd1c5] rounded-lg font-bold text-white text-xs hover:bg-teal-500" wire:click="submit">SAVE</button> 
            </div>
            
        </div>

    </div>        
</div>