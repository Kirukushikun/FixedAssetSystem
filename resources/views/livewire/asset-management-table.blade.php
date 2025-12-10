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
                    <input class="outline-none text-sm" type="text" wire:model.live="search" placeholder="Search asset...">
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
               </div>
               <button class="px-5 py-2 bg-[#4fd1c5] rounded-lg font-bold text-white text-xs hover:bg-teal-500" @click="showModal = true; modalTemplate = 'create'">ADD NEW ASSET</button>
               
               <form id="import-form" action="/assets/import" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" id="import-file" name="file" accept=".xlsx,.xls,.csv" class="hidden" required>
                    <div id="import-button">
                    <i class="fa-solid fa-file-import cursor-pointer"></i>
                    </div>
               </form>

               <script>
                    document.getElementById('import-button').addEventListener('click', () => {
                    document.getElementById('import-file').click();
                    });

                    document.getElementById('import-file').addEventListener('change', () => {
                    document.getElementById('import-form').submit();
                    });
               </script>
               
               <!-- Loading Modal Backdrop -->
               <div 
                    id="import-loading-backdrop"
                    class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center"
               >
                    <div class="bg-white rounded-lg p-8 shadow-xl flex flex-col items-center gap-4 min-w-[300px]">
                         <!-- Spinner -->
                          <div class="animate-spin rounded-full h-12 w-12 border-4 border-gray-200 border-t-teal-500"></div>
                         
                         <!-- Text -->
                         <div class="text-center">
                              <h3 class="text-lg font-semibold text-gray-800 mb-1">Importing Assets</h3>
                              <p class="text-sm text-gray-500">Please wait while we process your file...</p>
                         </div>
                    </div>
               </div>

               <script>
                    const importButton = document.getElementById('import-button');
                    const importFile = document.getElementById('import-file');
                    const importForm = document.getElementById('import-form');
                    const loadingBackdrop = document.getElementById('import-loading-backdrop');

                    // Trigger file input when button is clicked
                    importButton.addEventListener('click', () => {
                         importFile.click();
                    });

                    // Handle file selection and show loading
                    importFile.addEventListener('change', () => {
                         if (importFile.files.length > 0) {
                              // Show loading modal
                              loadingBackdrop.classList.remove('hidden');
                              
                              // Submit the form
                              importForm.submit();
                         }
                    });

                    // Optional: Hide loading if user navigates back (for better UX)
                    window.addEventListener('pageshow', (event) => {
                         if (event.persisted) {
                              loadingBackdrop.classList.add('hidden');
                         }
                    });
               </script>

               <i class="fa-solid fa-file-export cursor-pointer" onclick="window.location.href='/assets/export'"></i>

               <div x-data="{ filterOpen: false }" class="relative">
                    <!-- Toggle Button -->
                    <button @click="filterOpen = !filterOpen">
                         <i class="fa-solid fa-ellipsis-vertical cursor-pointer text-gray-600 hover:text-teal-500 transition"></i>
                    </button>

                    <!-- Dropdown Panel -->
                    <div
                         x-show="filterOpen"
                         @click.outside="filterOpen = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-96 bg-white border-2 border-gray-200 rounded-lg shadow-xl z-50"
                    >
                         <!-- Header -->
                         <div class="flex items-center justify-between p-4 border-b border-gray-200">
                              <h3 class="text-md font-bold text-gray-800">Filter Assets</h3>
                              <button 
                                   @click="filterOpen = false"
                                   class="text-teal-500 hover:text-teal-600 transition"
                              >
                                   <i class="fa-solid fa-chevron-up"></i>
                              </button>
                         </div>

                         <!-- Body -->
                         <div class="p-4 space-y-6 max-h-[28rem] overflow-y-auto">
                              <!-- Category Section -->
                              <div>
                                   <h4 class="text-xs font-semibold text-gray-500 uppercase mb-2">Category</h4>
                                   <div class="input-group !grid !grid-cols-2 !gap-3">
                                        <select wire:model.live="filterCategoryType">
                                             <option value="">Category Type</option>
                                             <option value="IT">IT</option>
                                             <option value="NON-IT">NON-IT</option>
                                        </select>

                                        <select wire:model.live="filterCategory">
                                             <option value="">Category</option>
                                             <option value="it">IT Equipment</option>
                                             <option value="office">Office Furniture</option>
                                             <option value="appliances">Appliances</option>
                                             <option value="audio">Audio Equipment</option>
                                             <option value="tools">Tools & Misc</option>
                                             <option value="kitchen">Kitchen Equipment</option>
                                        </select>

                                        <select wire:model.live="filterSubCategory" class="!col-span-2">
                                             <option value="">Sub-category</option>
                                             @if($filterCategory === 'it')
                                                  <option value="Desktop">Desktop</option>
                                                  <option value="Laptop">Laptop</option>
                                                  <option value="Printer">Printer</option>
                                                  <option value="Router">Router</option>
                                                  <option value="Photocopy Machine">Photocopy Machine</option>
                                                  <option value="Digital Camera">Digital Camera</option>
                                             @elseif($filterCategory === 'office')
                                                  <option value="Table">Table</option>
                                                  <option value="Office Chair">Office Chair</option>
                                                  <option value="Visitors Chair">Visitors Chair</option>
                                                  <option value="Office Table">Office Table</option>
                                                  <option value="Conference Chair">Conference Chair</option>
                                                  <option value="Monoblock Chair">Monoblock Chair</option>
                                             @elseif($filterCategory === 'appliances')
                                                  <option value="Aircon">Aircon</option>
                                                  <option value="Refrigerator">Refrigerator</option>
                                                  <option value="Water Dispenser">Water Dispenser</option>
                                                  <option value="Washing Machine">Washing Machine</option>
                                                  <option value="Wall Fan">Wall Fan</option>
                                             @elseif($filterCategory === 'audio')
                                                  <option value="Amplifier">Amplifier</option>
                                                  <option value="Mixer">Mixer</option>
                                                  <option value="Speaker">Speaker</option>
                                             @elseif($filterCategory === 'tools')
                                                  <option value="Helmet">Helmet</option>
                                                  <option value="Radio">Radio</option>
                                                  <option value="Thermistor Temperature Instrument">Thermistor Temperature Instrument</option>
                                                  <option value="Pipe Wrench">Pipe Wrench</option>
                                                  <option value="Pliers">Pliers</option>
                                                  <option value="Machine">Machine</option>
                                             @elseif($filterCategory === 'kitchen')
                                                  <option value="Cooking Pot">Cooking Pot</option>
                                                  <option value="Repair Chiller">Repair Chiller</option>
                                             @endif
                                        </select>
                                   </div>
                              </div>

                              <!-- Assignment Section -->
                              <div>
                                   <h4 class="text-xs font-semibold text-gray-500 uppercase mb-2">Assignment</h4>
                                   <div class="input-group !grid !grid-cols-2 !gap-3">
                                        <select wire:model.live="filterFarm">
                                             <option value="">Farm</option>
                                             <option value="BFC">BFC</option>
                                             <option value="BDL">BDL</option>
                                             <option value="PFC">PFC</option>
                                             <option value="RH">RH</option>
                                             <!-- Add your farm options here -->
                                        </select>

                                        <select wire:model.live="filterDepartment">
                                             <option value="">Department</option>
                                             <option value="FEEDMILL">Feedmill</option>
                                             <option value="FOC">FOC</option>
                                             <option value="GENERAL SERVICES">General Services</option>
                                             <option value="HR">HR</option>
                                             <option value="IT &amp; SECURITY">IT & Security</option>
                                             <option value="POULTRY">Poultry</option>
                                             <option value="PURCHASING">Purchasing</option>
                                             <option value="SALES &amp; ANALYTICS">Sales & Analytics</option>
                                             <option value="SWINE">Swine</option>
                                             <!-- Add your department options here -->
                                        </select>
                                   </div>
                              </div>

                              <!-- Status Section -->
                              <div class="input-group">
                                   <h4 class="text-xs font-semibold text-gray-500 uppercase mb-2">Status</h4>
                                   <select wire:model.live="filterStatus" class="input-filter w-full">
                                        <option value="">-- Select Status --</option>
                                        <option value="Available">Available</option>
                                        <option value="Issued">Issued</option>
                                        <option value="Transferred">Transferred</option>
                                        <option value="For Disposal">For Disposal</option>
                                        <option value="Disposed">Disposed</option>
                                        <option value="Lost">Lost</option>
                                   </select>
                              </div>

                              <!-- Condition -->
                              <div class="input-group">
                                   <h4 class="text-xs font-semibold text-gray-500 uppercase mb-2">Condition</h4>
                                   <select wire:model.live="filterCondition" class="input-filter w-full">
                                        <option value="">-- Select Condition --</option>
                                        <option value="Good">Good</option>
                                        <option value="Defective">Defective</option>
                                        <option value="Repair">Repair</option>
                                        <option value="Replace">Replace</option>
                                   </select>
                              </div>

                              <!-- Acquisition Date -->
                              <div>
                                   <h4 class="text-xs font-semibold text-gray-500 uppercase mb-2">Acquisition Date</h4>
                                   <div class="grid grid-cols-2 gap-3">
                                        <div class="input-group">
                                             <span class="text-xs text-gray-400">From</span>
                                             <input type="date" wire:model.live="filterDateFrom" class="input-filter">
                                        </div>

                                        <div class="input-group">
                                             <span class="text-xs text-gray-400">To</span>
                                             <input type="date" wire:model.live="filterDateTo" class="input-filter">
                                        </div>
                                   </div>
                              </div>

                              <!-- Cost Range -->
                              <div class="input-group">
                                   <h4 class="text-xs font-semibold text-gray-500 uppercase mb-2">Cost Range</h4>
                                   <div class="grid grid-cols-2 gap-3">
                                        <input type="number" placeholder="Min" wire:model.live="filterCostMin" class="input-filter">
                                        <input type="number" placeholder="Max" wire:model.live="filterCostMax" class="input-filter">
                                   </div>
                              </div>
                         </div>

                         <!-- Footer Buttons -->
                         <div class="flex items-center justify-between p-4 border-t border-gray-200">
                              <button 
                                   wire:click="resetFilters"
                                   class="px-4 py-2 text-xs font-bold text-gray-600 hover:text-gray-800"
                              >
                                   Reset
                              </button>

                              <button 
                                   @click="filterOpen = false"
                                   class="px-5 py-2 bg-teal-500 text-white rounded-lg text-xs font-bold hover:bg-teal-600"
                              >
                                   Apply Filter
                              </button>
                         </div>
                    </div>
               </div>
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
                                        'office' => 'furniture',
                                        'appliances' => 'appliances',
                                        'audio' => 'speaker',
                                        'tools' => 'tools',
                                        'kitchen' => 'kitchen'      
                                   ];
                                   $categoryValue = [
                                        'it' => 'IT Equipment',
                                        'office' => 'Office Furniture',
                                        'appliances' => 'Appliances',
                                        'audio' => 'Audio Equipment',
                                        'tools' => 'Tools & Misc',
                                        'kitchen' => 'Kitchen Equipment'    
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
                                        'Good' => 'text-green-500',
                                        'Defective' => 'text-amber-500',
                                        'Repair' => 'text-sky-500',
                                        'Replace' => 'text-red-500'
                                   ]
                              @endphp 
                              <div class="{{$conditionColor[$asset->condition]}} font-bold uppercase">{{$asset->condition}}</div>
                         </td>
                         <td>{{$asset->assigned_name ?? '--'}}</td>
                         <td x-data="{ open: false }" class="relative">
                              <i class="fa-solid fa-ellipsis-vertical cursor-pointer" @click="open = !open"></i>

                              <!-- Dropdown -->
                              <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-36 bg-white border border-gray-200 rounded-lg shadow-md z-40">
                              <ul class="text-sm text-gray-700">
                                   <li>
                                        <button class="w-full text-left px-4 py-2 hover:bg-gray-100" onclick="window.location.href='/assetmanagement/audit?targetID={{encrypt($asset->id)}}'">Audit</button>
                                   </li>
                                   <li>
                                        <button class="w-full text-left px-4 py-2 hover:bg-gray-100" onclick="window.location.href='/assetmanagement/view?targetID={{encrypt($asset->id)}}'">View</button>
                                   </li>
                                   <li>
                                        <button class="w-full text-left px-4 py-2 hover:bg-gray-100" onclick="window.location.href='/assetmanagement/edit?targetID={{encrypt($asset->id)}}'">Edit</button>
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

     <x-pagination :paginator="$assets" />

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
                                   <template x-for="item in ['Desktop','Laptop','Router']">
                                   <a class="flex justify-between items-center cursor-pointer text-gray-500 font-semibold hover:text-gray-800 hover:translate-x-1" :href="`/assetmanagement/create?category_type=IT&category=it&sub_category=${item}`">
                                        <span x-text="item"></span>
                                        <i class="fa-solid fa-arrow-right"></i>
                                   </a>
                                   </template>
                                   
                                   <template x-for="item in ['Printer','Photocopy Machine','Digital Camera']">
                                   <a class="flex justify-between items-center cursor-pointer text-gray-500 font-semibold hover:text-gray-800 hover:translate-x-1" :href="`/assetmanagement/create?category_type=NON-IT&category=it&sub_category=${item}`">
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
                                   <a class="flex justify-between items-center cursor-pointer text-gray-500 font-semibold hover:text-gray-800 hover:translate-x-1" :href="`/assetmanagement/create?category_type=NON-IT&category=appliances&sub_category=${item}`">
                                        <span x-text="item"></span>
                                        <i class="fa-solid fa-arrow-right"></i>
                                   </a>
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
                                   <a class="flex justify-between items-center cursor-pointer text-gray-500 font-semibold hover:text-gray-800 hover:translate-x-1" :href="`/assetmanagement/create?category_type=NON-IT&category=audio&sub_category=${item}`">
                                        <span x-text="item"></span>
                                        <i class="fa-solid fa-arrow-right"></i>
                                   </a>
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
                                   <a class="flex justify-between items-center cursor-pointer text-gray-500 font-semibold hover:text-gray-800 hover:translate-x-1" :href="`/assetmanagement/create?category_type=NON-IT&category=tools&sub_category=${item}`">
                                        <span x-text="item"></span>
                                        <i class="fa-solid fa-arrow-right"></i>
                                   </a>
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
                                   <a class="flex justify-between items-center cursor-pointer text-gray-500 font-semibold hover:text-gray-800 hover:translate-x-1" :href="`/assetmanagement/create?category_type=NON-IT&category=kitchen&sub_category=${item}`">
                                        <span x-text="item"></span>
                                        <i class="fa-solid fa-arrow-right"></i>
                                   </a>
                                   </template>
                              </div>
                         </div>
                    </div>                    
               </div>
               
               <div class="flex flex-col gap-5 w-[23rem]" x-show="modalTemplate === 'delete'">
                    <h2 class="text-xl font-semibold -mb-2">Delete Asset</h2>
                    <p>Are you sure you want to delete this asset? You can restore it later if needed.</p>

                    <div class="flex justify-end gap-3">
                         <button type="button" @click="showModal = false;" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-100 cursor-pointer">Cancel</button>
                         <button type="button" @click="showModal = false; $wire.delete(targetAsset)" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-800 cursor-pointer">Confirm</button>
                    </div>
               </div>
          </div>

     </div>
</div>