@extends('layouts.app')

@section('content')
<div class="card flex items-center justify-between">
    <div>
        <p class="text-sm text-gray-400">#1234</p>
        <h1 class="text-lg font-bold">Employee List</h1>
        <p class="text-sm text-gray-400">Web Developer | BFC | IT & Security</p>
    </div>
    <div class="flex gap-3">
        <button class="px-5 py-2 bg-red-500 rounded-lg font-bold text-white text-xs hover:bg-red-600">DELETE</button>
        <button class="px-5 py-2 bg-blue-500 rounded-lg font-bold text-white text-xs hover:bg-blue-600">EDIT</button>
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
    <button class="px-5 py-2 bg-blue-500 rounded-lg font-bold text-white text-xs hover:bg-blue-600 w-fit">ADD NEW FLAG</button>
</div>

<div class="card content flex-1 flex flex-col">
    <div class="table-header flex justify-between items-center">
        <h1 class="text-lg font-bold">Assigned Asset</h1>
        <div class="flex items-center gap-3">
            <button class="px-5 py-2 bg-[#4fd1c5] rounded-lg font-bold text-white text-xs hover:bg-teal-500">ASSIGN ASSET</button>
            <i class="fa-solid fa-arrow-down-wide-short cursor-pointer"></i>
            <i class="fa-solid fa-ellipsis-vertical cursor-pointer"></i>
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
                <tr>
                    <td>FA-000123</td>
                    <td>
                        <p class="flex items-center gap-2"><img src="/desktop.png" style="width: 25px" alt="" /> <span class="font-bold">IT Equipment</span></p>
                    </td>
                    <td>Desktop</td>
                    <td>Lenove</td>
                    <td>450 G9</td>
                    <td>
                        <div class="px-4 py-1 bg-[#48BB78] text-white w-fit rounded-lg">Available</div>
                    </td>
                    <td>
                        <div class="text-green-500 font-bold">GOOD</div>
                    </td>
                    <td><i class="fa-solid fa-ellipsis-vertical cursor-pointer"></i></td>
                </tr>

                <tr>
                    <td>FA-000123</td>
                    <td>
                        <p class="flex items-center gap-2"><img src="/furniture.png" style="width: 25px" alt="" /> <span class="font-bold">Office Furniture</span></p>
                    </td>
                    <td>Office table</td>
                    <td>Mandaue</td>
                    <td>450 G9</td>
                    <td>
                        <div class="px-4 py-1 bg-[#ECC94B] text-white w-fit rounded-lg">Issued</div>
                    </td>
                    <td>
                        <div class="text-sky-500 font-bold">REPAIR</div>
                    </td>
                    <td><i class="fa-solid fa-ellipsis-vertical cursor-pointer"></i></td>
                </tr>

                <tr>
                    <td>FA-000123</td>
                    <td>
                        <p class="flex items-center gap-2"><img src="/appliances.png" style="width: 25px" alt="" /> <span class="font-bold">Appliances</span></p>
                    </td>
                    <td>Oven</td>
                    <td>Misyubibi</td>
                    <td>450 G9</td>
                    <td>
                        <div class="px-4 py-1 bg-[#4299E1] text-white w-fit rounded-lg">Transferred</div>
                    </td>
                    <td>
                        <div class="text-sky-500 font-bold">REPAIR</div>
                    </td>
                    <td><i class="fa-solid fa-ellipsis-vertical cursor-pointer"></i></td>
                </tr>

                <tr>
                    <td>FA-000123</td>
                    <td>
                        <p class="flex items-center gap-2"><img src="/speaker.png" style="width: 25px" alt="" /> <span class="font-bold">Audio Equipment</span></p>
                    </td>
                    <td>Speaker</td>
                    <td>Edifier</td>
                    <td>450 G9</td>
                    <td>
                        <div class="px-4 py-1 bg-[#ED8936] text-white w-fit rounded-lg">For Disposal</div>
                    </td>
                    <td>
                        <div class="text-amber-500 font-bold">Defective</div>
                    </td>
                    <td><i class="fa-solid fa-ellipsis-vertical cursor-pointer"></i></td>
                </tr>
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

@endsection