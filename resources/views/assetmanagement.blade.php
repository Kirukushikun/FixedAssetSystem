@extends('layouts.app')

@section('content')
<div class="card content flex-1 flex flex-col" x-data="{
     addAsset: false,
     openCategory: 'it'
}">
     <div class="table-header flex justify-between items-center">
     <h1 class="text-lg font-bold">All Assets</h1>
     <div class="flex items-center gap-3">
          <div class="border border-2 px-3 py-1 rounded-md ">
               <input class="outline-none text-sm" type="text">
               <i class="fa-solid fa-magnifying-glass text-sm"></i>
          </div>
          <button class="px-5 py-2 bg-[#4fd1c5] rounded-lg font-bold text-white text-xs hover:bg-teal-500" @click="addAsset = true">ADD NEW ASSET</button>
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
                    <th>ASSIGNED TO</th>
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
                    <td>John Reyes</td>
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
                    <td>John Reyes</td>
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
                    <td>John Reyes</td>
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
                         <div class="text-amber-500 font-bold">DEFECTIVE</div>
                    </td>
                    <td>John Reyes</td>
                    <td><i class="fa-solid fa-ellipsis-vertical cursor-pointer"></i></td>
               </tr>

               <tr>
                    <td>FA-000123</td>
                    <td>
                         <p class="flex items-center gap-2"><img src="/tools.png" style="width: 25px" alt="" /> <span class="font-bold">Tools & Misc</span></p>
                    </td>
                    <td>Tool Kit</td>
                    <td>Mikasa</td>
                    <td>450 G9</td>
                    <td>
                         <div class="px-4 py-1 bg-[#2D3748] text-white w-fit rounded-lg">Disposed</div>
                    </td>
                    <td>
                         <div class="text-red-500 font-bold">REPLACE</div>
                    </td>
                    <td>John Reyes</td>
                    <td><i class="fa-solid fa-ellipsis-vertical cursor-pointer"></i></td>
               </tr>

               <tr>
                    <td>FA-000123</td>
                    <td>
                         <p class="flex items-center gap-2"><img src="/kitchen.png" style="width: 25px" alt="" /> <span class="font-bold">Kitchen Equipment</span></p>
                    </td>
                    <td>Frying Pan</td>
                    <td>Aginamotor</td>
                    <td>450 G9</td>
                    <td>
                         <div class="px-4 py-1 bg-[#F56565] text-white w-fit rounded-lg">Lost</div>
                    </td>
                    <td>
                         <div class="text-red-500 font-bold">REPLACE</div>
                    </td>
                    <td>John Reyes</td>
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

     <!-- Backdrop -->
     <div 
     x-show="addAsset"
     x-transition.opacity
     class="fixed inset-0 bg-black/30 z-40"
     ></div>

     <!-- Modal -->
     <div 
     x-show="addAsset"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 scale-90"
     x-transition:enter-end="opacity-100 scale-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 scale-100"
     x-transition:leave-end="opacity-0 scale-90"
     class="fixed inset-0 flex items-center justify-center z-50"
     >
     <div class="relative bg-white p-10 rounded-lg shadow-lg w-[23rem]">
          <div class="absolute right-7 top-7 text-gray-400 cursor-pointer hover:text-gray-800" @click="addAsset = false"><i class="fa-solid fa-xmark"></i></div>

          <!-- Header -->
          <h2 class="text-center font-bold text-lg mb-1">Select</h2>
          <h3 class="text-center font-bold text-xl mb-6">Category Type</h3>

          <!-- Category List -->
          <div class="space-y-3">
               <!-- IT Equipment -->
               <div>
                    <button 
                         class="flex items-center justify-between w-full font-semibold"
                         @click="openCategory === 'it' ? openCategory = '' : openCategory = 'it'"
                    >
                         <span class="flex items-center gap-2 font-bold"><img src="desktop.png" style="width: 23px;" alt="">IT Equipment</span>
                         <i class="fa-solid fa-chevron-down transition-transform duration-200"
                         :class="openCategory === 'it' ? 'rotate-180' : ''"></i>
                    </button>

                    <div x-show="openCategory === 'it'" x-transition class="ml-8 mt-2 space-y-1 text-sm text-gray-600">
                         <template x-for="item in ['Desktop','Laptop','Printer','Router','Photocopy Machine','Digital Camera']">
                         <div class="flex justify-between items-center cursor-pointer text-gray-500 hover:text-gray-800 hover:translate-x-1" onclick="window.location.href='asset-creation.html'">
                              <span x-text="item"></span>
                              <i class="fa-solid fa-arrow-right"></i>
                         </div>
                         </template>
                    </div>
               </div>

               <!-- Office Furniture -->
               <div>
                    <button 
                         class="flex items-center justify-between w-full font-semibold"
                         @click="openCategory === 'office' ? openCategory = '' : openCategory = 'office'"
                    >
                         <span class="flex items-center gap-2 font-bold"><img src="furniture.png" style="width: 23px;" alt="">Office Furniture</span>
                         <i class="fa-solid fa-chevron-down transition-transform duration-200"
                         :class="openCategory === 'office' ? 'rotate-180' : ''"></i>
                    </button>

                    <div x-show="openCategory === 'office'" x-transition class="ml-8 mt-2 space-y-1 text-sm text-gray-600">
                         <template x-for="item in ['Table','Office Chair','Office Table','Conference Table','Monoblock Chair']">
                         <div class="flex justify-between items-center cursor-pointer text-gray-500 hover:text-gray-800 hover:translate-x-1">
                              <span x-text="item"></span>
                              <i class="fa-solid fa-arrow-right"></i>
                         </div>
                         </template>
                    </div>
               </div>

               <!-- Appliances -->
               <div>
                    <button 
                         class="flex items-center justify-between w-full font-semibold"
                         @click="openCategory === 'appliances' ? openCategory = '' : openCategory = 'appliances'"
                    >
                         <span class="flex items-center gap-2 font-bold"><img src="appliances.png" style="width: 23px;" alt="">Appliances</span>
                         <i class="fa-solid fa-chevron-down transition-transform duration-200"
                         :class="openCategory === 'appliances' ? 'rotate-180' : ''"></i>
                    </button>

                    <div x-show="openCategory === 'appliances'" x-transition class="ml-8 mt-2 space-y-1 text-sm text-gray-600">
                         <template x-for="item in ['Aircon','Refrigerator','Water Dispenser','Waching Machine','Wall Fan']">
                         <div class="flex justify-between items-center cursor-pointer text-gray-500 hover:text-gray-800 hover:translate-x-1">
                              <span x-text="item"></span>
                              <i class="fa-solid fa-arrow-right"></i>
                         </div>
                         </template>
                    </div>
               </div>

               <!-- Audio Equipment -->
               <div>
                    <button 
                         class="flex items-center justify-between w-full font-semibold"
                         @click="openCategory === 'audio' ? openCategory = '' : openCategory = 'audio'"
                    >
                         <span class="flex items-center gap-2 font-bold"><img src="speaker.png" style="width: 23px;" alt="">Audio Equipment</span>
                         <i class="fa-solid fa-chevron-down transition-transform duration-200"
                         :class="openCategory === 'audio' ? 'rotate-180' : ''"></i>
                    </button>

                    <div x-show="openCategory === 'audio'" x-transition class="ml-8 mt-2 space-y-1 text-sm text-gray-600">
                         <template x-for="item in ['Amplifier','Mixer','Speaker']">
                         <div class="flex justify-between items-center cursor-pointer text-gray-500 hover:text-gray-800 hover:translate-x-1">
                              <span x-text="item"></span>
                              <i class="fa-solid fa-arrow-right"></i>
                         </div>
                         </template>
                    </div>
               </div>

               <!-- Tools & Misc -->
               <div>
                    <button 
                         class="flex items-center justify-between w-full font-semibold"
                         @click="openCategory === 'tools' ? openCategory = '' : openCategory = 'tools'"
                    >
                         <span class="flex items-center gap-2 font-bold"><img src="tools.png" style="width: 23px;" alt="">Tools & Misc</span>
                         <i class="fa-solid fa-chevron-down transition-transform duration-200"
                         :class="openCategory === 'tools' ? 'rotate-180' : ''"></i>
                    </button>

                    <div x-show="openCategory === 'tools'" x-transition class="ml-8 mt-2 space-y-1 text-sm text-gray-600">
                         <template x-for="item in ['Helmet','Radio','Thermistor Temperature','Pipe Wrench','Pliers','Machine']">
                         <div class="flex justify-between items-center cursor-pointer text-gray-500 hover:text-gray-800 hover:translate-x-1">
                              <span x-text="item"></span>
                              <i class="fa-solid fa-arrow-right"></i>
                         </div>
                         </template>
                    </div>
               </div>

               <!-- Kitchen Equipment -->
               <div>
                    <button 
                         class="flex items-center justify-between w-full font-semibold"
                         @click="openCategory === 'kitchen' ? openCategory = '' : openCategory = 'kitchen'"
                    >
                         <span class="flex items-center gap-2 font-bold"><img src="kitchen.png" style="width: 23px;" alt="">Kitchen Equipment</span>
                         <i class="fa-solid fa-chevron-down transition-transform duration-200"
                         :class="openCategory === 'kitchen' ? 'rotate-180' : ''"></i>
                    </button>

                    <div x-show="openCategory === 'kitchen'" x-transition class="ml-8 mt-2 space-y-1 text-sm text-gray-600">
                         <template x-for="item in ['Cooking Pot','Repair Chiller']">
                         <div class="flex justify-between items-center cursor-pointer text-gray-500 hover:text-gray-800 hover:translate-x-1">
                              <span x-text="item"></span>
                              <i class="fa-solid fa-arrow-right"></i>
                         </div>
                         </template>
                    </div>
               </div>
          </div>
     </div>
     </div>
</div>
@endsection