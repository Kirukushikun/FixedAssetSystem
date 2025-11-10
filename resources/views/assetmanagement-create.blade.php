@extends('layouts.app')

@section('content')
<div class="relative overflow-y-auto flex flex-col gap-7">
    <div class="card self-center">
        <i class="fa-solid fa-arrow-left absolute top-8 left-[190px] cursor-pointer hover:-translate-x-1 text-gray-400 hover:text-gray-800 text-xl" onclick="window.history.back()"></i>
        <h1 class="text-lg font-bold">General Information</h1>
        <p class="text-gray-400 text-sm mb-5">Basic details that describe and identify this asset. These values help classify and track the item within the system.</p>
        <div class="grid grid-cols-4 gap-5">
            <div class="input-group">
                <label for="">Reference ID:</label>
                <input type="text">
            </div>
            <div class="input-group">
                @php
                    $categoryValue = [
                        'it' => 'IT Equipment'    
                    ]
                @endphp
                <label for="">Category Type:</label>
                <input type="text" value="{{$categoryValue[$category_type]}}">
            </div>
            <div class="input-group">
                <label for="">Category:</label>
                <input type="text" value="{{$category}}">
            </div>
            <div class="input-group">
                <label for="">Sub-category:</label>
                <input type="text">
            </div>
            <div class="input-group">
                <label for="">Brand:</label>
                <input type="text">
            </div>
            <div class="input-group">
                <label for="">Model:</label>
                <input type="text">
            </div>
            <div class="input-group">
                <label for="">Status:</label>
                <select name="" id="">
                    <option value=""></option>
                    <option value="">Available</option>
                    <option value="">Issued</option>
                    <option value="">Transferred</option>
                    <option value="">For Disposal</option>
                    <option value="">Disposed</option>
                    <option value="">Lost</option>
                </select>
            </div>
            <div class="input-group">
                <label for="">Condition:</label>
                <select name="" id="">
                    <option value=""></option>
                    <option value="">Good</option>
                    <option value="">Repair</option>
                    <option value="">Defective</option>
                    <option value="">Replace</option>
                </select>
            </div>
            <div class="input-group">
                <label for="">Acquisition Date:</label>
                <input type="date">
            </div>
            <div class="input-group">
                <label for="">Item Cost:</label>
                <input type="text">
            </div>
            <div class="input-group">
                <label for="">Depreciated Value:</label>
                <input type="text">
            </div>
            <div class="input-group">
                <label for="">Usable Life:</label>
                <input type="text">
            </div>
        </div>

        <hr class="mt-7 mb-7">

        <h1 class="text-lg font-bold">Technical Details</h1>
        <p class="text-gray-400 text-sm mb-5">Additional specifications used for IT-related assets. These details are helpful for troubleshooting, configuration, and inventory auditing.</p>
        <div class="grid grid-cols-4 gap-5">
            <div class="input-group">
                <label for="">Processor:</label>
                <input type="text">
            </div>
            <div class="input-group">
                <label for="">RAM:</label>
                <select name="" id="">
                    <option value=""></option>
                    <option value="">4GB</option>
                    <option value="">8GB</option>
                    <option value="">16GB</option>
                    <option value="">32GB</option>
                    <option value="">64GB</option>
                </select>
            </div>
            <div class="input-group">
                <label for="">Storage:</label>
                <select name="" id="">
                    <option value=""></option>
                    <option value="">32GB</option>
                    <option value="">64GB</option>
                    <option value="">128GB</option>
                    <option value="">256GB</option>
                    <option value="">512GB</option>
                    <option value="">1TB</option>
                </select>
            </div>
            <div class="input-group">
                <label for="">IP Address:</label>
                <input type="text">
            </div>
            <div class="input-group">
                <label for="">MAC Address:</label>
                <input type="text">
            </div>
            <div class="input-group">
                <label for="">VPN Address:</label>
                <input type="text">
            </div>
            <div class="input-group">
                <label for="">WOL Enabled:</label>
                <select name="" id="">
                    <option value=""></option>
                    <option value="">Yes</option>
                    <option value="">No</option>                    
                </select>
            </div>
        </div>

        <hr class="mt-7 mb-7">

        <h1 class="text-lg font-bold">Assignment Details</h1>
        <p class="text-gray-400 text-sm mb-5">Information on where this asset is currently assigned, including the responsible employee and location.</p>
        <div class="grid grid-cols-3 gap-5">
            <div class="input-group">
                <label for="">Assigned To:</label>
                <select name="" id="">
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
                    <!-- File Name -->
                    <div class="flex-1 bg-gray-50 text-gray-500 px-4 py-2">
                        No file attached
                    </div> 
                </div>
            </div>

            <div class="input-group">
                <label for="">Remarks:</label>
                <textarea name="" id=""></textarea>
            </div>

            <button class="self-end px-5 py-3 bg-[#4fd1c5] rounded-lg font-bold text-white text-xs hover:bg-teal-500" @click="addAsset = true">SAVE</button> 
        </div>

    </div>        
</div>
@endsection