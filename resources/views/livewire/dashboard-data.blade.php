<div class="content flex-1 flex flex-col">
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
                        @foreach(['good' => 'GOOD', 'defective' => 'DEFECTIVE', 'repair' => 'REPAIR', 'replace' => 'REPLACE'] as $key => $label)
                            @php
                                $height = $maxCondition > 0 ? ($conditions[$key] / $maxCondition) * 100 : 0;
                            @endphp
                            <div class="relative w-[10%] bg-white rounded-lg flex items-end justify-center text-[10px] pb-2 shadow-md hover:opacity-80 hover:-translate-y-1 transition" 
                                style="height: {{ $height }}%"
                                title="{{ $conditions[$key] }} assets">
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
                    <span class="text-teal-400 font-semibold text-sm">
                        {{ number_format($farm['count']) }} ({{ $farm['percentage'] }}%)
                    </span>
                    <div class="w-full h-1 bg-gray-200 rounded mt-1">
                        <div class="h-1 bg-teal-400 rounded transition-all duration-300" 
                             style="width: {{ $farm['percentage'] }}%"></div>
                    </div>
                </div>
                <button class="bg-teal-400 text-white rounded-md text-xs w-fit py-2 px-4 hover:bg-teal-500 transition">
                    VIEW
                </button>
            </div>
        @endforeach
    </div>
            </div>

            <div class="alert-action flex-1 flex flex-col md:flex-row gap-7">
                <div class="card flex-1">
                    <h1 class="text-lg font-bold mb-5">Alerts</h1>
                    <div class="pl-5 flex flex-col gap-5">
                        <!-- Alert 1 -->
                        <div class="relative flex flex-col gap-2">
                            <!-- icon and line -->
                            <div class="absolute top-1 -left-6 flex flex-col items-center gap-1 h-full">
                                <i class="fa-solid fa-bell text-teal-400 text-md"></i>
                                <div class="w-[3px] rounded-lg flex-1 bg-gray-200"></div>
                            </div>

                            <!-- text content -->
                            <p class="text-sm text-gray-800 font-bold ml-2">5 assets are marked Lost</p>
                            <p class="text-xs text-gray-500 font-semibold ml-2">22 DEC 7:20 PM</p>
                        </div>

                        <!-- Alert 2 -->
                        <div class="relative flex flex-col gap-2">
                            <!-- icon and line -->
                            <div class="absolute top-1 -left-6 flex flex-col items-center gap-1 h-full">
                                <i class="fa-solid fa-bell text-teal-400 text-md"></i>
                                <div class="w-[3px] rounded-lg flex-1 bg-gray-200"></div>
                            </div>

                            <!-- text content -->
                            <p class="text-sm text-gray-800 font-bold ml-2">12 assets are Under Repair for more than 30 days</p>
                            <p class="text-xs text-gray-500 font-semibold ml-2">21 DEC 11:21 PM</p>
                        </div>

                        <!-- Alert 3 -->
                        <div class="relative flex flex-col gap-2">
                            <!-- icon and line -->
                            <div class="absolute top-1 -left-6 flex flex-col items-center gap-1 h-full">
                                <i class="fa-solid fa-bell text-teal-400 text-md"></i>
                                <div class="w-[3px] rounded-lg flex-1 bg-gray-200"></div>
                            </div>

                            <!-- text content -->
                            <p class="text-sm text-gray-800 font-bold ml-2">3 employees have unreturned items</p>
                            <p class="text-xs text-gray-500 font-semibold ml-2">21 DEC 9:28 PM</p>
                        </div>
                    </div>
                </div>
                <div class="card !bg-[#4FD1C5] flex-1 flex flex-col gap-5">
                    <h1 class="text-lg text-white font-bold">Quick Actions</h1>

                    <div class="grid grid-cols-2 gap-4">
                        <button class="bg-white rounded-md p-3 text-sm font-semibold hover:scale-105"><i class="fa-solid fa-plus text-teal-400"></i> Add New Asset</button>
                        <button class="bg-white rounded-md p-3 text-sm font-semibold hover:scale-105"><i class="fa-solid fa-user-plus text-teal-400"></i> Add Employee</button>
                        <button class="bg-white rounded-md p-3 text-sm font-semibold hover:scale-105"><i class="fa-solid fa-file-lines text-teal-400"></i> Generate Report</button>
                        <button class="bg-white rounded-md p-3 text-sm font-semibold hover:scale-105"><i class="fa-solid fa-file-import text-teal-400"></i> Import Assets</button>
                        <button class="bg-white rounded-md p-3 text-sm font-semibold hover:scale-105"><i class="fa-solid fa-file-export text-teal-400"></i> Export Assets</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
