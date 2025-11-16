<div class="card content flex-1 flex flex-col" x-data="{
     showModal: false,
     modalTemplate: '',
     openCategory: 'it',
     targetAsset: ''
}">
     <div class="table-header flex justify-between items-center">
          <h1 class="text-lg font-bold">All Assets</h1>
          <div class="flex items-center gap-3">
               <div class="border border-2 px-3 py-1 rounded-md border-gray-300">
                    <input class="outline-none text-sm" type="text">
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
               </div>
               <button class="px-5 py-2 bg-[#4fd1c5] rounded-lg font-bold text-white text-xs hover:bg-teal-500" @click="showModal = true; modalTemplate = 'create'">ADD NEW ASSET</button>
               <i class="fa-solid fa-file-import cursor-pointer"></i>
               <i class="fa-solid fa-file-export cursor-pointer"></i>
               <i class="fa-solid fa-ellipsis-vertical cursor-pointer"></i>
          </div>
     </div>

     <div class="table-container">
          <table>
               <thead>
                    <tr>
                         <th>REFERENCE ID</th>
                         <th>CATEGORY</th>
                         <th>SUB-CATEGORY</th>
                         <th>BRAND</th>
                         <th>MODEL</th>
                         <th>STATUS</th>
                         <th>CONDITION</th>
                         <th>ASSIGNED TO</th>
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
                         <td>{{$asset->assigned_name ?? '--'}}</td>
                         <td x-data="{ open: false }" class="relative">
                              <i class="fa-solid fa-ellipsis-vertical cursor-pointer" @click="open = !open"></i>

                              <!-- Dropdown -->
                              <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-36 bg-white border border-gray-200 rounded-lg shadow-md z-40">
                              <ul class="text-sm text-gray-700">
                                   <!-- <li>
                                        <button class="w-full text-left px-4 py-2 hover:bg-gray-100">Clone</button>
                                   </li> -->
                                   <li>
                                        <button class="w-full text-left px-4 py-2 hover:bg-gray-100" onclick="window.location.href='/assetmanagement/view?targetID={{$asset->id}}'">View</button>
                                   </li>
                                   <li>
                                        <button class="w-full text-left px-4 py-2 hover:bg-gray-100" onclick="window.location.href='/assetmanagement/edit?targetID={{$asset->id}}'">Edit</button>
                                   </li>
                                   <li>
                                        <button class="w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100" @click="showModal = true; modalTemplate = 'delete'; targetAsset = {{$asset->id}}">Delete</button>
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
               
               <div class="w-[20rem]" x-show="modalTemplate === 'create'">
                    <h3 class="text-center font-bold text-xl mb-6">Select Category</h3>

                    <!-- Category List -->
                    <div class="space-y-3">
                         <!-- IT Equipment -->
                         <div>
                              <button 
                                   class="flex items-center justify-between w-full font-semibold"
                                   @click="openCategory === 'it' ? openCategory = '' : openCategory = 'it'"
                              >
                                   <span class="flex items-center gap-2 font-bold"><img src="{{asset('img/desktop.png')}}" style="width: 23px;" alt="">IT Equipment</span>
                                   <i class="fa-solid fa-chevron-down transition-transform duration-200"
                                   :class="openCategory === 'it' ? 'rotate-180' : ''"></i>
                              </button>

                              <div x-show="openCategory === 'it'" x-transition class="ml-8 mt-2 space-y-1 text-sm text-gray-600">
                                   <template x-for="item in ['Desktop','Laptop','Printer','Router','Photocopy Machine','Digital Camera']">
                                   <a class="flex justify-between items-center cursor-pointer text-gray-500 font-semibold hover:text-gray-800 hover:translate-x-1" :href="`/assetmanagement/create?category_type=IT&category=it&sub_category=${item}`">
                                        <span x-text="item"></span>
                                        <i class="fa-solid fa-arrow-right"></i>
                                   </a>
                                   </template>
                              </div>
                         </div>

                         <!-- Office Furniture -->
                         <div>
                              <button 
                                   class="flex items-center justify-between w-full font-semibold"
                                   @click="openCategory === 'office' ? openCategory = '' : openCategory = 'office'"
                              >
                                   <span class="flex items-center gap-2 font-bold"><img src="{{asset('img/furniture.png')}}" style="width: 23px;" alt="">Office Furniture</span>
                                   <i class="fa-solid fa-chevron-down transition-transform duration-200"
                                   :class="openCategory === 'office' ? 'rotate-180' : ''"></i>
                              </button>

                              <div x-show="openCategory === 'office'" x-transition class="ml-8 mt-2 space-y-1 text-sm text-gray-600">
                                   <template x-for="item in ['Table','Office Chair','Office Table','Conference Table','Monoblock Chair']">
                                   <a class="flex justify-between items-center cursor-pointer text-gray-500 font-semibold hover:text-gray-800 hover:translate-x-1" :href="`/assetmanagement/create?category_type=NON-IT&category=office&sub_category=${item}`">
                                        <span x-text="item"></span>
                                        <i class="fa-solid fa-arrow-right"></i>
                                   </a>
                                   </template>
                              </div>
                         </div>

                         <!-- Appliances -->
                         <div>
                              <button 
                                   class="flex items-center justify-between w-full font-semibold"
                                   @click="openCategory === 'appliances' ? openCategory = '' : openCategory = 'appliances'"
                              >
                                   <span class="flex items-center gap-2 font-bold"><img src="{{asset('img/appliances.png')}}" style="width: 23px;" alt="">Appliances</span>
                                   <i class="fa-solid fa-chevron-down transition-transform duration-200"
                                   :class="openCategory === 'appliances' ? 'rotate-180' : ''"></i>
                              </button>

                              <div x-show="openCategory === 'appliances'" x-transition class="ml-8 mt-2 space-y-1 text-sm text-gray-600">
                                   <template x-for="item in ['Aircon','Refrigerator','Water Dispenser','Waching Machine','Wall Fan']">
                                   <div class="flex justify-between items-center cursor-pointer text-gray-500 font-semibold hover:text-gray-800 hover:translate-x-1">
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
                                   <span class="flex items-center gap-2 font-bold"><img src="{{asset('img/speaker.png')}}" style="width: 23px;" alt="">Audio Equipment</span>
                                   <i class="fa-solid fa-chevron-down transition-transform duration-200"
                                   :class="openCategory === 'audio' ? 'rotate-180' : ''"></i>
                              </button>

                              <div x-show="openCategory === 'audio'" x-transition class="ml-8 mt-2 space-y-1 text-sm text-gray-600">
                                   <template x-for="item in ['Amplifier','Mixer','Speaker']">
                                   <div class="flex justify-between items-center cursor-pointer text-gray-500 font-semibold hover:text-gray-800 hover:translate-x-1">
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
                                   <span class="flex items-center gap-2 font-bold"><img src="{{asset('img/tools.png')}}" style="width: 23px;" alt="">Tools & Misc</span>
                                   <i class="fa-solid fa-chevron-down transition-transform duration-200"
                                   :class="openCategory === 'tools' ? 'rotate-180' : ''"></i>
                              </button>

                              <div x-show="openCategory === 'tools'" x-transition class="ml-8 mt-2 space-y-1 text-sm text-gray-600">
                                   <template x-for="item in ['Helmet','Radio','Thermistor Temperature','Pipe Wrench','Pliers','Machine']">
                                   <div class="flex justify-between items-center cursor-pointer text-gray-500 font-semibold hover:text-gray-800 hover:translate-x-1">
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
                                   <span class="flex items-center gap-2 font-bold"><img src="{{asset('img/kitchen.png')}}" style="width: 23px;" alt="">Kitchen Equipment</span>
                                   <i class="fa-solid fa-chevron-down transition-transform duration-200"
                                   :class="openCategory === 'kitchen' ? 'rotate-180' : ''"></i>
                              </button>

                              <div x-show="openCategory === 'kitchen'" x-transition class="ml-8 mt-2 space-y-1 text-sm text-gray-600">
                                   <template x-for="item in ['Cooking Pot','Repair Chiller']">
                                   <div class="flex justify-between items-center cursor-pointer text-gray-500 font-semibold hover:text-gray-800 hover:translate-x-1">
                                        <span x-text="item"></span>
                                        <i class="fa-solid fa-arrow-right"></i>
                                   </div>
                                   </template>
                              </div>
                         </div>
                    </div>                    
               </div>
               
               <div class="flex flex-col gap-5 w-[23rem]" x-show="modalTemplate === 'delete'">
                    <h2 class="text-xl font-semibold -mb-2">Delete Modal</h2>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Minima, incidunt! asdadasd</p>

                    <div class="flex justify-end gap-3">
                         <button type="button" @click="showModal = false;" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-100 cursor-pointer">Cancel</button>
                         <button type="button" @click="showModal = false; $wire.delete(targetAsset)" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-800 cursor-pointer">Confirm</button>
                    </div>
               </div>
          </div>

     </div>
</div>