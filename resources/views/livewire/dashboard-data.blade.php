<div
    class="content flex-1 flex flex-col"
    x-data="{
        showModal: false,
        modalTemplate: '',
        openCategory: 'it',
        targetAsset: '',
    }"
>
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-4 text-[#2d3748]">
        <div class="px-6 py-5 rounded-xl bg-white shadow-sm flex justify-between items-center hover:-translate-y-2 hover:shadow-lg">
            <div class="card-label">
                <p class="text-sm font-semibold text-gray-400">Total Assets</p>
                <h1 class="text-lg font-bold">{{$total_assets->count()}}</h1>
            </div>
            <div class="card-icon p-2 rounded-xl bg-[#4FD1C5] text-xl">
                <i class="fa-solid fa-boxes-stacked text-white"></i>
            </div>
        </div>
        <div class="px-5 py-5 rounded-xl bg-white shadow-sm flex justify-between items-center hover:-translate-y-2 hover:shadow-lg">
            <div class="card-label">
                <p class="text-sm font-semibold text-gray-400">Assigned Assets</p>
                <h1 class="text-lg font-bold">{{$assigned_assets->count()}}</h1>
            </div>
            <div class="card-icon p-2 rounded-xl bg-[#4FD1C5] text-xl">
                <i class="fa-solid fa-box text-white"></i>
            </div>
        </div>
        <div class="px-5 py-5 rounded-xl bg-white shadow-sm flex justify-between items-center hover:-translate-y-2 hover:shadow-lg">
            <div class="card-label">
                <p class="text-sm font-semibold text-gray-400">Total Employees</p>
                <h1 class="text-lg font-bold">{{$total_employees->count()}}</h1>
            </div>
            <div class="card-icon p-2 rounded-xl bg-[#4FD1C5] text-xl">
                <i class="fa-solid fa-users text-white"></i>
            </div>
        </div>
        <div class="px-5 py-5 rounded-xl bg-white shadow-sm flex justify-between items-center hover:-translate-y-2 hover:shadow-lg">
            <div class="card-label">
                <p class="text-sm font-semibold text-gray-400">Pending Clearances</p>
                <h1 class="text-lg font-bold">0</h1>
            </div>
            <div class="card-icon p-2 rounded-xl bg-[#4FD1C5] text-xl">
                <i class="fa-solid fa-file-circle-exclamation text-white"></i>
            </div>
        </div>
    </div>

    <br />

    <!-- Main Cards -->
    <div class="main-cards h-full grid-cols-1 lg:grid-cols-[calc(35%-10px)_calc(65%-10px)]">
        <div class="card flex flex-col gap-5">
            <!-- Condition Chart -->
            <div class="graph">
                <div class="flex items-end bg-gradient-to-br from-[#0a0f2c] to-[#111c3d] rounded-xl p-[40px] pb-[65px] font-sans h-72 w-full">
                    <!-- Y-Axis -->
                    <div class="flex flex-col justify-between h-full mr-1 text-xs">
                        <p class="!text-white">{{ $maxCondition }}</p>
                        <p class="!text-white">{{ round($maxCondition * 0.75) }}</p>
                        <p class="!text-white">{{ round($maxCondition * 0.5) }}</p>
                        <p class="!text-white">{{ round($maxCondition * 0.25) }}</p>
                        <p class="!text-white">0</p>
                    </div>

                    <!-- Chart Bars -->
                    <div class="flex items-end justify-around flex-1 h-full">
                        @foreach(['good' => 'GOOD', 'defective' => 'DEFECTIVE', 'repair' => 'REPAIR', 'replace' => 'REPLACE'] as $key => $label) @php $height = $maxCondition > 0 ? ($conditions[$key] / $maxCondition) * 100 : 0; @endphp
                        <div class="relative w-[10%] bg-white rounded-lg flex items-end justify-center text-[10px] pb-2 shadow-md hover:opacity-80 hover:-translate-y-1 transition" style="height: {{ $height }}%" title="{{ $conditions[$key] }} assets">
                            <span class="text-gray-800 font-semibold">{{ $conditions[$key] }}</span>
                            <div class="absolute -bottom-[30px] text-white">{{ $label }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <h1 class="text-lg font-bold mt-5">Asset Status Overview</h1>

            <!-- Asset Status Overview -->
            <div class="asset-statuses grid grid-cols-2 gap-7 mb-5">
                <!-- Available -->
                <div class="flex flex-col gap-1">
                    <div class="label flex items-center gap-2">
                        <i class="text-lg fa-solid fa-square text-[#38A169]"></i>
                        <p class="text-sm text-gray-400">Available</p>
                    </div>
                    <div class="flex flex-col gap-2">
                        <h1 class="text-lg font-bold">{{ number_format($statuses['available']) }}</h1>
                        <div class="bg-gray-200 w-full h-[4px] rounded-sm">
                            <div class="h-full bg-[#38A169] rounded-sm" style="width: {{ $statusPercentages['available'] }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Issued -->
                <div class="flex flex-col gap-1">
                    <div class="label flex items-center gap-2">
                        <i class="text-lg fa-solid fa-square text-[#ECC94B]"></i>
                        <p class="text-sm text-gray-400">Issued</p>
                    </div>
                    <div class="flex flex-col gap-2">
                        <h1 class="text-lg font-bold">{{ number_format($statuses['issued']) }}</h1>
                        <div class="bg-gray-200 w-full h-[4px] rounded-sm">
                            <div class="h-full bg-[#ECC94B] rounded-sm" style="width: {{ $statusPercentages['issued'] }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Transferred -->
                <div class="flex flex-col gap-1">
                    <div class="label flex items-center gap-2">
                        <i class="text-lg fa-solid fa-square text-[#4299E1]"></i>
                        <p class="text-sm text-gray-400">Transferred</p>
                    </div>
                    <div class="flex flex-col gap-2">
                        <h1 class="text-lg font-bold">{{ number_format($statuses['transferred']) }}</h1>
                        <div class="bg-gray-200 w-full h-[4px] rounded-sm">
                            <div class="h-full bg-[#4299E1] rounded-sm" style="width: {{ $statusPercentages['transferred'] }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- For Disposal -->
                <div class="flex flex-col gap-1">
                    <div class="label flex items-center gap-2">
                        <i class="text-lg fa-solid fa-square text-[#ED8936]"></i>
                        <p class="text-sm text-gray-400">For Disposal</p>
                    </div>
                    <div class="flex flex-col gap-2">
                        <h1 class="text-lg font-bold">{{ number_format($statuses['for_disposal']) }}</h1>
                        <div class="bg-gray-200 w-full h-[4px] rounded-sm">
                            <div class="h-full bg-[#ED8936] rounded-sm" style="width: {{ $statusPercentages['for_disposal'] }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Disposed -->
                <div class="flex flex-col gap-1">
                    <div class="label flex items-center gap-2">
                        <i class="text-lg fa-solid fa-square text-[#4A5568]"></i>
                        <p class="text-sm text-gray-400">Disposed</p>
                    </div>
                    <div class="flex flex-col gap-2">
                        <h1 class="text-lg font-bold">{{ number_format($statuses['disposed']) }}</h1>
                        <div class="bg-gray-200 w-full h-[4px] rounded-sm">
                            <div class="h-full bg-[#4A5568] rounded-sm" style="width: {{ $statusPercentages['disposed'] }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Lost -->
                <div class="flex flex-col gap-1">
                    <div class="label flex items-center gap-2">
                        <i class="text-lg fa-solid fa-square text-[#E53E3E]"></i>
                        <p class="text-sm text-gray-400">Lost</p>
                    </div>
                    <div class="flex flex-col gap-2">
                        <h1 class="text-lg font-bold">{{ number_format($statuses['lost']) }}</h1>
                        <div class="bg-gray-200 w-full h-[4px] rounded-sm">
                            <div class="h-full bg-[#E53E3E] rounded-sm" style="width: {{ $statusPercentages['lost'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="right-cards flex flex-col gap-7">
            <div class="card">
                <h1 class="text-lg font-bold mb-5">Asset Distribution</h1>
                <div class="grid grid-cols-[1fr_2fr_0.5fr] text-xs uppercase border-b border-gray-200 pb-2">
                    <span class="text-gray-400 font-semibold">Farm</span>
                    <span class="text-gray-400 font-semibold">Assigned Assets</span>
                    <span class="text-gray-400 font-semibold">Action</span>
                </div>

                <div class="divide-y divide-gray-200">
                    @foreach($farmDistribution as $farm)
                    <div class="grid grid-cols-[1fr_2fr_0.5fr] items-center py-4">
                        <span class="text-gray-700 text-sm font-bold">{{ $farm['name'] }}</span>
                        <div class="flex flex-col mr-7 hover:scale-105 transition">
                            <span class="text-teal-400 font-semibold text-sm"> {{ number_format($farm['count']) }} ({{ $farm['percentage'] }}%) </span>
                            <div class="w-full h-1 bg-gray-200 rounded mt-1">
                                <div class="h-1 bg-teal-400 rounded transition-all duration-300" style="width: {{ $farm['percentage'] }}%"></div>
                            </div>
                        </div>
                        <button class="bg-teal-400 text-white rounded-md text-xs w-fit py-2 px-4 hover:bg-teal-500 transition" @click="showModal = true; modalTemplate = 'farm-assets';" wire:click="setFarm('{{ $farm['code'] }}')">VIEW</button>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="alert-action flex-1 flex flex-col md:flex-row gap-7">
                <div class="card flex-1">
                    <h1 class="text-lg font-bold mb-5">Alerts</h1>
                    <div class="pl-5 flex flex-col gap-5">
                        @forelse($this->alerts as $index => $alert)
                            <div class="relative flex flex-col gap-2">
                                <!-- icon and line -->
                                <div class="absolute top-1 -left-6 flex flex-col items-center gap-1 h-full">
                                    <i class="{{ $alert['icon'] }} {{ $alert['color'] }} text-md"></i>
                                    @if(!$loop->last)
                                        <div class="w-[3px] rounded-lg flex-1 bg-gray-200"></div>
                                    @endif
                                </div>

                                <!-- text content -->
                                <p class="text-sm text-gray-800 font-bold ml-2">{{ $alert['message'] }}</p>
                                <p class="text-xs text-gray-500 font-semibold ml-2">
                                    {{ $alert['timestamp']->format('d M g:i A') }}
                                </p>
                            </div>
                        @empty
                            <div class="">
                                <p class="text-sm text-gray-500">No alerts at this time</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="card !bg-[#4FD1C5] flex-1 flex flex-col gap-5">
                    <h1 class="text-lg text-white font-bold">Quick Actions</h1>

                    <div class="grid grid-cols-2 gap-4">
                        <button class="bg-white rounded-md p-3 text-sm font-semibold hover:scale-105" @click="showModal = true; modalTemplate = 'create'"><i class="fa-solid fa-plus text-teal-400"></i> Add New Asset</button>
                        <button class="bg-white rounded-md p-3 text-sm font-semibold hover:scale-105" @click="showModal = true; modalTemplate = 'employee'"><i class="fa-solid fa-user-plus text-teal-400"></i> Add Employee</button>
                        <!-- <button class="bg-white rounded-md p-3 text-sm font-semibold hover:scale-105"><i class="fa-solid fa-file-import text-teal-400"></i> Import Assets</button> -->
                        
                        <form id="import-form" action="/employees/import" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="file" id="import-file" name="file" accept=".xlsx,.xls,.csv" class="hidden" required>
                            <button id="import-button" class="w-full bg-white rounded-md p-3 text-sm font-semibold hover:scale-105"><i class="fa-solid fa-file-import text-teal-400"></i> Import Assets</button>
                        </form>

                        <script>
                            document.getElementById('import-button').addEventListener('click', () => {
                                document.getElementById('import-file').click();
                            });

                            document.getElementById('import-file').addEventListener('change', () => {
                                document.getElementById('import-form').submit();
                            });
                        </script>

                        <button class="bg-white rounded-md p-3 text-sm font-semibold hover:scale-105" onclick="window.location.href='/employees/export'"><i class="fa-solid fa-file-export text-teal-400"></i> Export Assets</button>

                        <form id="import-form" action="/employees/import" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="file" id="import-employee" name="file" accept=".xlsx,.xls,.csv" class="hidden" required>
                            <button id="import-button-btn" class="w-full bg-white rounded-md p-3 text-sm font-semibold hover:scale-105"><i class="fa-solid fa-file-import text-teal-400"></i> Import Employees</button>
                        </form>

                        <script>
                            document.getElementById('import-button-btn').addEventListener('click', () => {
                                document.getElementById('import-employee').click();
                            });

                            document.getElementById('import-employee').addEventListener('change', () => {
                                document.getElementById('import-form').submit();
                            });
                        </script>

                        <button class="bg-white rounded-md p-3 text-sm font-semibold hover:scale-105" onclick="window.location.href='/employees/export'"><i class="fa-solid fa-file-export text-teal-400"></i> Export Employees</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


        <!-- Backdrop -->
        <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-black/30 z-40"></div>

        <!-- Modal -->
        <div x-show="showModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90" class="fixed inset-0 flex items-center justify-center z-50">
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

                <div class="flex flex-col gap-5 w-[23rem]" x-show="modalTemplate === 'employee'">
                    <h2 class="text-xl font-semibold -mb-2">Add New Employee</h2>

                    <div class="input-group">
                        <label>Employee ID:</label>
                        <input type="text" wire:model="employee_id" class="border rounded px-2 py-1 w-full" />
                    </div>

                    <div class="input-group">
                        <label>Employee Name:</label>
                        <input type="text" wire:model="employee_name" class="border rounded px-2 py-1 w-full" />
                    </div>

                    <div class="input-group">
                        <label>Position:</label>
                        <input type="text" wire:model="position" class="border rounded px-2 py-1 w-full" />
                    </div>

                    <div class="input-group">
                        <label>Farm:</label>
                        <select wire:model="farm">
                            <option value=""></option>
                            <option value="BFC">BFC</option>
                            <option value="BDL">BDL</option>
                            <option value="BFC">BFC</option>
                            <option value="RH">RH</option>
                        </select>
                    </div>

                    <div class="input-group">
                        <label>Department/Division:</label>
                        <input type="text" wire:model="department" class="border rounded px-2 py-1 w-full" />
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showModal = false; $wire.clear()" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-100">Cancel</button>

                        <button
                            type="button"
                            @click="showModal = false; modalTemplate === 'create'; $wire.submit();"
                            class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-800"
                        >
                            Confirm
                        </button>
                    </div>
                </div>

                <livewire:farm-assets :farmCode="$targetFarm" :key="$targetFarm"/>
            </div>
        </div>

</div>
