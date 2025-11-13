<div class="flex flex-1 flex-col gap-5" x-data="{
    showModal: false,
    modalTemplate: '',
}">
    <div class="card flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-400">#{{$employee->employee_id}}</p>
            <h1 class="text-lg font-bold">{{$employee->employee_name}}</h1>
            <p class="text-sm text-gray-400">{{$employee->position}} | {{$employee->farm}} | {{$employee->department}}</p>
        </div>
        <div class="flex gap-3">
            <button class="px-5 py-2 bg-red-500 rounded-lg font-bold text-white text-xs hover:bg-red-600" @click="showModal = true; modalTemplate = 'delete'">DELETE</button>
            <button class="px-5 py-2 bg-blue-500 rounded-lg font-bold text-white text-xs hover:bg-blue-600" @click="showModal = true; modalTemplate = 'create'">EDIT</button>
        </div>
    </div>

    <div class="card flex flex-col gap-4">
        <div class="flex items-center justify-between">
            <h1 class="text-lg font-bold">Active Flags</h1>
            <i class="fa-solid fa-pen-to-square cursor-pointer text-gray-400"></i>
        </div>

        <div class="grid grid-cols-3 gap-3 text-sm">
            <p><i class="fa-solid fa-flag text-[#4299E1]"></i> Under Investigation - Laptop HP ProBook (FA-00045)</p>
            <p><i class="fa-solid fa-flag text-[#C075F9]"></i> Pending Clearances - Router TP-Link (FA-00032)</p>
            <p><i class="fa-solid fa-flag text-[#F56565]"></i> Lost Asset - Printer Canon G3010 (FA-00025)</p>
            <p><i class="fa-solid fa-flag text-[#ED8936]"></i> Unreturned Asset - Edifier 400</p>
            <p><i class="fa-solid fa-flag text-[#ECC94B]"></i> Damaged Asset - N-VISIOn 23.8 Inch Gaming Monitor</p>
        </div>
        <button class="px-5 py-2 bg-blue-500 rounded-lg font-bold text-white text-xs hover:bg-blue-600 w-fit" @click="showModal = true; modalTemplate = 'flag'">ADD NEW FLAG</button>
    </div>

    <div class="card content flex-1 flex flex-col">
        <div class="table-header flex justify-between items-center">
            <h1 class="text-lg font-bold">Assigned Assets</h1>
            <!-- <div class="flex items-center gap-3">
                <button class="px-5 py-2 bg-[#4fd1c5] rounded-lg font-bold text-white text-xs hover:bg-teal-500">ASSIGN ASSET</button>
                <i class="fa-solid fa-arrow-down-wide-short cursor-pointer"></i>
                <i class="fa-solid fa-ellipsis-vertical cursor-pointer"></i>
            </div> -->
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
                                @php
                                    $categoryImg = [
                                            'it' => 'desktop',
                                            'office' => 'furniture'     
                                    ];
                                    $categoryValue = [
                                            'it' => 'IT Equipment',
                                            'office' => 'Office Furniture'    
                                    ];
                                @endphp
                                <p class="flex items-center gap-2"><img src="{{ asset('img/' . $categoryImg[$asset->category] . '.png') }}" style="width: 25px" alt="" /> <span class="font-bold">{{$categoryValue[$asset->category]}}</span></p>
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
                            <td x-data="{ open: false }" class="relative">
                                <i class="fa-solid fa-ellipsis-vertical cursor-pointer" @click="open = !open"></i>

                                <!-- Dropdown -->
                                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-36 bg-white border border-gray-200 rounded-lg shadow-md z-40">
                                <ul class="text-sm text-gray-700">
                                    <li>
                                            <button class="w-full text-left px-4 py-2 hover:bg-gray-100">Clone</button>
                                    </li>
                                    <li>
                                            <button class="w-full text-left px-4 py-2 hover:bg-gray-100">View</button>
                                    </li>
                                    <li>
                                            <button class="w-full text-left px-4 py-2 hover:bg-gray-100">Edit</button>
                                    </li>
                                    <li>
                                            <button class="w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100">Delete</button>
                                    </li>
                                </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pagination-container flex items-center justify-end gap-3 mt-auto">
            <div class="text-xs text-gray-400">Showing 1 to 10 of 50 results</div>

            <!-- Previous Button -->
            <button class="px-2 py-2 rounded-md hover:scale-110 cursor-pointer bg-teal-100 text-sm">
                <i class="fa-solid fa-caret-left text-teal-500"></i>
            </button>

            <!-- Page Numbers -->
            <button class="bg-teal-400 text-white px-4 py-2 rounded-md hover:scale-110 cursor-pointer text-sm">1</button>
            <button class="bg-teal-100 text-teal-500 px-4 py-2 rounded-md hover:scale-110 cursor-pointer text-sm">2</button>
            <button class="bg-teal-100 text-teal-500 px-4 py-2 rounded-md hover:scale-110 cursor-pointer text-sm">3</button>
            <button class="bg-teal-100 text-teal-500 px-4 py-2 rounded-md hover:scale-110 cursor-pointer text-sm">4</button>
            <button class="bg-teal-100 text-teal-500 px-4 py-2 rounded-md hover:scale-110 cursor-pointer text-sm">5</button>

            <!-- Next Button -->
            <button class="px-2 py-2 rounded-md hover:scale-110 cursor-pointer bg-teal-100 text-sm">
                <i class="fa-solid fa-caret-right text-teal-500"></i>
            </button>
        </div>
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
                    <select name="" id="">
                        <option value="">Select type:</option>
                        <option value="Under Investigation">Under Investigation</option>
                        <option value="Unreturned Asset">Pending Clearances</option>
                        <option value="Pending Clearances">Pending Clearances</option>
                        <option value="Damaged Asset">Damaged Asset</option>
                        <option value="Lost Asset">Lost Asset</option>
                    </select>
                </div>

                <div class="input-group">
                    <label>Asset</label>
                    <select name="" id="">
                        <option value="">Select Asset:</option>
                        @foreach($assets as $asset)
                            <option value="{{$asset->brand}} {{$asset->model}}">{{$asset->brand}} {{$asset->model}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-3">
                    <button @click="showModal = false" class="px-4 py-2 border rounded hover:bg-gray-100">Cancel</button>
                    <button @click="showModal = false" class="px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-800">Confirm</button>
                </div>
            </div>

            <!-- Delete Confirmation -->
            <div class="flex flex-col gap-5" x-show="modalTemplate === 'delete'">
                <h2 class="text-xl font-semibold -mb-2">Delete Modal</h2>
                <p>Are you sure you want to delete this item?</p>

                <div class="flex justify-end gap-3">
                    <button @click="showModal = false" class="px-4 py-2 border rounded hover:bg-gray-100">Cancel</button>
                    <button @click="showModal = false" class="px-4 py-2 bg-red-700 text-white rounded hover:bg-red-800">Confirm</button>
                </div>
            </div>
        </div>
    </div>
</div>
