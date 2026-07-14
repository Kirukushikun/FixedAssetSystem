<div class="content flex flex-col overflow-x-hidden pr-5" style="padding-bottom: 0 !important;">

    {{-- Alpine store: lives in JS memory, never in the DOM, immune to Livewire morphing --}}
    <script>
        (function () {
            function initDashModal() {
                if (!Alpine.store('dashModal')) {
                    Alpine.store('dashModal', {
                        show: false,
                        template: '',
                        open: function (t) { this.template = t; this.show = true; },
                        close: function () { this.show = false; this.template = ''; }
                    });
                }
            }
            if (window.Alpine) {
                initDashModal();
            } else {
                document.addEventListener('alpine:init', initDashModal);
            }
        })();
    </script>

    @php($dashboardUser = Auth::user())

    <!-- ── Summary Cards ───────────────────────────────────────────────────── -->
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-4 text-[#2d3748]">
        <div class="px-6 py-5 rounded-xl bg-white shadow-sm flex justify-between items-center hover:shadow-lg">
            <div class="card-label">
                <p class="text-sm font-semibold text-gray-400">Total Assets</p>
                <h1 class="text-lg font-bold">{{ $total_assets->count() }}</h1>
            </div>
            <div class="card-icon p-2 rounded-xl bg-[#4FD1C5] text-xl">
                <i class="fa-solid fa-boxes-stacked text-white"></i>
            </div>
        </div>
        <div class="px-5 py-5 rounded-xl bg-white shadow-sm flex justify-between items-center hover:shadow-lg">
            <div class="card-label">
                <p class="text-sm font-semibold text-gray-400">Assigned Assets</p>
                <h1 class="text-lg font-bold">{{ $assigned_assets->count() }}</h1>
            </div>
            <div class="card-icon p-2 rounded-xl bg-[#4FD1C5] text-xl">
                <i class="fa-solid fa-box text-white"></i>
            </div>
        </div>
        <div class="px-5 py-5 rounded-xl bg-white shadow-sm flex justify-between items-center hover:shadow-lg">
            <div class="card-label">
                <p class="text-sm font-semibold text-gray-400">Total Employees</p>
                <h1 class="text-lg font-bold">{{ $total_employees->count() }}</h1>
            </div>
            <div class="card-icon p-2 rounded-xl bg-[#4FD1C5] text-xl">
                <i class="fa-solid fa-users text-white"></i>
            </div>
        </div>
        <div class="px-5 py-5 rounded-xl bg-white shadow-sm flex justify-between items-center hover:shadow-lg">
            <div class="card-label">
                <p class="text-sm font-semibold text-gray-400">Pending Clearances</p>
                <h1 class="text-lg font-bold">{{ $pending_clearances->count() }}</h1>
            </div>
            <div class="card-icon p-2 rounded-xl bg-[#4FD1C5] text-xl">
                <i class="fa-solid fa-file-circle-exclamation text-white"></i>
            </div>
        </div>
    </div>

    <!-- ── Main Cards ───────────────────────────────────────────────────────── -->
    <div class="main-cards mt-5 grid-cols-1 gap-7 lg:grid lg:grid-cols-[calc(35%-10px)_calc(65%-10px)]">

        <!-- Left: Condition Chart + Status Overview -->
        <div class="card flex flex-col gap-5 mb-7 lg:mb-0">
            <div class="graph">
                <div class="flex items-end bg-gradient-to-br from-[#0a0f2c] to-[#111c3d] rounded-xl p-[40px] pb-[65px] font-sans h-72 w-full">
                    <div class="flex flex-col justify-between h-full mr-1 text-xs">
                        <p class="!text-white">{{ $maxCondition }}</p>
                        <p class="!text-white">{{ round($maxCondition * 0.75) }}</p>
                        <p class="!text-white">{{ round($maxCondition * 0.5) }}</p>
                        <p class="!text-white">{{ round($maxCondition * 0.25) }}</p>
                        <p class="!text-white">0</p>
                    </div>
                    <div class="flex items-end justify-around flex-1 h-full">
                        @foreach(['good' => 'GOOD', 'defective' => 'DEFECTIVE', 'repair' => 'REPAIR', 'replace' => 'REPLACE'] as $key => $label)
                            @php($height = $maxCondition > 0 ? ($conditions[$key] / $maxCondition) * 100 : 0)
                            <div class="relative w-[10%] bg-white rounded-lg flex items-end justify-center text-[10px] pb-2 shadow-md hover:opacity-80 hover:-translate-y-1 transition" style="height: {{ $height }}%" title="{{ $conditions[$key] }} assets">
                                <span class="text-gray-800 font-semibold">{{ $conditions[$key] }}</span>
                                <div class="absolute -bottom-[30px] text-white">{{ $label }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <h1 class="text-lg font-bold mt-5">Asset Status Overview</h1>

            <div class="asset-statuses grid grid-cols-3 gap-7 mb-5">
                @foreach([
                    ['key' => 'available',    'label' => 'Available',    'color' => '#38A169'],
                    ['key' => 'issued',        'label' => 'Issued',       'color' => '#ECC94B'],
                    ['key' => 'transferred',   'label' => 'Transferred',  'color' => '#4299E1'],
                    ['key' => 'for_transfer',  'label' => 'For Transfer', 'color' => '#9F7AEA'],
                    ['key' => 'for_disposal',  'label' => 'For Disposal', 'color' => '#ED8936'],
                    ['key' => 'disposed',      'label' => 'Disposed',     'color' => '#4A5568'],
                    ['key' => 'lost',          'label' => 'Lost',         'color' => '#E53E3E'],
                ] as $s)
                <div class="flex flex-col gap-1">
                    <div class="label flex items-center gap-2">
                        <i class="text-lg fa-solid fa-square" style="color: {{ $s['color'] }}"></i>
                        <p class="text-sm text-gray-400">{{ $s['label'] }}</p>
                    </div>
                    <div class="flex flex-col gap-2">
                        <h1 class="text-lg font-bold">{{ number_format($statuses[$s['key']]) }}</h1>
                        <div class="bg-gray-200 w-full h-[4px] rounded-sm">
                            <div class="h-full rounded-sm" style="width: {{ $statusPercentages[$s['key']] }}%; background: {{ $s['color'] }}"></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Right: Farm Distribution + Alerts + Quick Actions -->
        <div class="right-cards flex flex-col gap-7">

            <!-- Farm Distribution -->
            <div class="card">
                <h1 class="text-lg font-bold mb-5">Asset Distribution</h1>
                <div class="grid grid-cols-[1fr_2fr_0.5fr] text-xs uppercase border-b border-gray-200 pb-2">
                    <span class="text-gray-400 font-semibold">Farm</span>
                    <span class="text-gray-400 font-semibold">Assigned Assets</span>
                    <span class="text-gray-400 font-semibold">Action</span>
                </div>
                <div class="divide-y divide-gray-200 max-h-[250px] overflow-y-auto pr-1">
                    @foreach($farmDistribution as $farm)
                    <div class="grid grid-cols-[1fr_2fr_0.5fr] items-center py-4">
                        <span class="text-gray-700 text-sm font-bold">{{ $farm['name'] }}</span>
                        <div class="flex flex-col mr-7 hover:scale-105 transition">
                            <span class="text-teal-400 font-semibold text-sm">{{ number_format($farm['count']) }} ({{ $farm['percentage'] }}%)</span>
                            <div class="w-full h-1 bg-gray-200 rounded mt-1">
                                <div class="h-1 bg-teal-400 rounded transition-all duration-300" style="width: {{ $farm['percentage'] }}%"></div>
                            </div>
                        </div>
                        <button
                            class="bg-teal-400 text-white rounded-md text-xs w-fit py-2 px-4 hover:bg-teal-500 transition"
                            @click="$store.dashModal.open('farm-assets')"
                            wire:click="setFarm('{{ $farm['code'] }}')"
                        >VIEW</button>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="alert-action flex-1 flex flex-col md:flex-row gap-7">

                <!-- Alerts -->
                <div class="card flex-1">
                    <h1 class="text-lg font-bold mb-5">Alerts</h1>
                    <div class="pl-5 flex flex-col gap-5">
                        @forelse($this->alerts as $index => $alert)
                            <div class="relative flex flex-col gap-2">
                                <div class="absolute top-1 -left-6 flex flex-col items-center gap-1 h-full">
                                    <i class="{{ $alert['icon'] }} {{ $alert['color'] }} text-md"></i>
                                    @if(!$loop->last)
                                        <div class="w-[3px] rounded-lg flex-1 bg-gray-200"></div>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-800 font-bold ml-2">{{ $alert['message'] }}</p>
                                <p class="text-xs text-gray-500 font-semibold ml-2">{{ $alert['timestamp']->format('d M g:i A') }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No alerts at this time</p>
                        @endforelse
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card !bg-[#4FD1C5] flex-1 flex flex-col gap-5">
                    <h1 class="text-lg text-white font-bold">Quick Actions</h1>
                    <div class="grid grid-cols-2 gap-4">

                        <button class="bg-white rounded-md p-3 text-sm font-semibold hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                            :disabled="!{{ $dashboardUser?->hasPermission('assets.create') ? 'true' : 'false' }}"
                            @click="{{ $dashboardUser?->hasPermission('assets.create') ? '$store.dashModal.open(\'create\')' : 'null' }}"
                            title="{!! $dashboardUser?->hasPermission('assets.create') ? 'Add New Asset' : 'You do not have permission to add assets' !!}">
                            <i class="fa-solid fa-plus text-teal-400"></i> Add New Asset
                        </button>

                        <button class="bg-white rounded-md p-3 text-sm font-semibold hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                            :disabled="!{{ $dashboardUser?->hasPermission('employees.create') ? 'true' : 'false' }}"
                            @click="{{ $dashboardUser?->hasPermission('employees.create') ? '$store.dashModal.open(\'employee\')' : 'null' }}"
                            title="{!! $dashboardUser?->hasPermission('employees.create') ? 'Add Employee' : 'You do not have permission to add employees' !!}">
                            <i class="fa-solid fa-user-plus text-teal-400"></i> Add Employee
                        </button>

                        {{-- Hidden forms triggered from import-type modal --}}
                        <form id="import-form" action="/assets/import" method="POST" enctype="multipart/form-data" class="hidden">
                            @csrf
                            <input type="file" id="import-file" name="file" accept=".xlsx,.xls,.csv" class="hidden">
                        </form>
                        <form id="migration-import-form" action="/assets/migration-import" method="POST" enctype="multipart/form-data" class="hidden">
                            @csrf
                            <input type="file" id="migration-import-file" name="file" accept=".xlsx,.xls,.csv" class="hidden">
                        </form>

                        {{-- Import loading backdrop --}}
                        <div id="import-loading-backdrop" class="hidden fixed inset-0 bg-black/50 z-[100] flex items-center justify-center">
                            <div class="bg-white rounded-lg p-8 shadow-xl flex flex-col items-center gap-4 min-w-[300px]">
                                <div class="animate-spin rounded-full h-12 w-12 border-4 border-gray-200 border-t-teal-500"></div>
                                <div class="text-center">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-1">Importing Assets</h3>
                                    <p class="text-sm text-gray-500">Please wait while we process your file...</p>
                                </div>
                            </div>
                        </div>
                        <script>
                            (function () {
                                var lb = document.getElementById('import-loading-backdrop');
                                var importFile = document.getElementById('import-file');
                                var importForm = document.getElementById('import-form');
                                if (importFile && importForm) {
                                    importFile.addEventListener('change', function () {
                                        if (importFile.files.length > 0) { lb && lb.classList.remove('hidden'); importForm.submit(); }
                                    });
                                }
                                var migFile = document.getElementById('migration-import-file');
                                var migForm = document.getElementById('migration-import-form');
                                if (migFile && migForm) {
                                    migFile.addEventListener('change', function () {
                                        if (migFile.files.length > 0) { lb && lb.classList.remove('hidden'); migForm.submit(); }
                                    });
                                }
                                window.addEventListener('pageshow', function (e) {
                                    if (e.persisted && lb) lb.classList.add('hidden');
                                });
                            })();
                        </script>

                        <button class="bg-white rounded-md p-3 text-sm font-semibold hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                            :disabled="!{{ $dashboardUser?->hasPermission('assets.import') ? 'true' : 'false' }}"
                            @click="{{ $dashboardUser?->hasPermission('assets.import') ? '$store.dashModal.open(\'import-type\')' : 'null' }}"
                            title="{!! $dashboardUser?->hasPermission('assets.import') ? 'Import Assets' : 'You do not have permission to import assets' !!}">
                            <i class="fa-solid fa-file-import text-teal-400"></i> Import Assets
                        </button>

                        <button class="bg-white rounded-md p-3 text-sm font-semibold hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                            :disabled="!{{ $dashboardUser?->hasPermission('assets.export') ? 'true' : 'false' }}"
                            @click="{{ $dashboardUser?->hasPermission('assets.export') ? '$store.dashModal.open(\'export-type\')' : 'null' }}"
                            title="{!! $dashboardUser?->hasPermission('assets.export') ? 'Export Assets' : 'You do not have permission to export assets' !!}">
                            <i class="fa-solid fa-file-export text-teal-400"></i> Export Assets
                        </button>

                        {{-- Import Employees --}}
                        <form id="employee-import-form" action="/employees/import" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="file" id="import-employee" name="file" accept=".xlsx,.xls,.csv" class="hidden" required>
                            <button type="button" id="import-button-btn"
                                class="w-full bg-white rounded-md p-3 text-sm font-semibold hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                                :disabled="!{{ $dashboardUser?->hasPermission('employees.import') ? 'true' : 'false' }}"
                                @click="{{ $dashboardUser?->hasPermission('employees.import') ? 'document.getElementById(\'import-employee\').click()' : 'null' }}"
                                title="{!! $dashboardUser?->hasPermission('employees.import') ? 'Import Employees' : 'You do not have permission to import employees' !!}">
                                <i class="fa-solid fa-file-import text-teal-400"></i> Import Employees
                            </button>
                        </form>
                        <script>
                            (function () {
                                var empFile = document.getElementById('import-employee');
                                if (empFile) {
                                    empFile.addEventListener('change', function () {
                                        var form = empFile.closest('form');
                                        if (form) form.submit();
                                    });
                                }
                            })();
                        </script>

                        {{-- Export Employees --}}
                        <button class="bg-white rounded-md p-3 text-sm font-semibold hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                            :disabled="!{{ $dashboardUser?->hasPermission('employees.export') ? 'true' : 'false' }}"
                            @click="{{ $dashboardUser?->hasPermission('employees.export') ? 'window.location.href = \'/employees/export\'' : 'null' }}"
                            title="{!! $dashboardUser?->hasPermission('employees.export') ? 'Export Employees' : 'You do not have permission to export employees' !!}">
                            <i class="fa-solid fa-file-export text-teal-400"></i> Export Employees
                        </button>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Modal Backdrop ───────────────────────────────────────────────────── -->
    <div
        x-cloak
        x-show="$store.dashModal && $store.dashModal.show"
        x-transition.opacity
        class="fixed inset-0 bg-black/30 z-40"
    ></div>

    <!-- ── Modal Panel ──────────────────────────────────────────────────────── -->
    <div
        x-cloak
        x-show="$store.dashModal && $store.dashModal.show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
        class="fixed inset-0 flex items-center justify-center z-50"
    >
        <div class="relative bg-white p-10 rounded-lg shadow-lg">

            {{-- Close X --}}
            <div class="absolute right-7 top-7 text-gray-400 cursor-pointer hover:text-gray-800"
                 @click="$store.dashModal.close(); $wire.clear()">
                <i class="fa-solid fa-xmark"></i>
            </div>

            {{-- ── Create: Select Category ── --}}
            <div class="p-8" x-show="$store.dashModal.template === 'create'">
                <h3 class="text-center font-bold text-xl mb-6">Select Category</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4" x-data="{ openCat: null }">
                    @foreach($categories as $category)
                    <div>
                        <button
                            class="flex items-center justify-between w-full py-1"
                            @click.prevent="openCat = openCat === {{ $category->id }} ? null : {{ $category->id }}"
                        >
                            <span class="flex items-center gap-2 font-bold text-sm text-gray-700 whitespace-nowrap">
                                <img src="{{ asset('img/' . $category->icon . '.png') }}" class="w-5 h-5 object-contain" alt="">
                                {{ $category->name }}
                            </span>
                            <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform duration-200"
                               :style="openCat === {{ $category->id }} ? 'transform:rotate(180deg)' : ''"></i>
                        </button>
                        <div
                            x-show="openCat === {{ $category->id }}"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="ml-7 mt-2 space-y-1"
                        >
                            @foreach($category->subcategories as $sub)
                            <a href="{{ url('/assetmanagement/create?category_type=' . $sub->category_type . '&category=' . $category->code . '&sub_category=' . $sub->name) }}"
                               class="flex justify-between items-center text-sm text-gray-500 font-semibold hover:text-teal-500 hover:translate-x-1 transition-all py-0.5">
                                <span>{{ $sub->name }}</span>
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- ── Add Employee ── --}}
            <div class="flex flex-col gap-5 w-[23rem]" x-show="$store.dashModal.template === 'employee'">
                <h2 class="text-xl font-semibold -mb-2">Add New Employee</h2>

                <div class="input-group">
                    <label>Employee ID:</label>
                    <input type="text" wire:model="employee_id" class="border rounded px-2 py-1 w-full" />
                    @error('employee_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="input-group">
                    <label>Employee Name:</label>
                    <input type="text" wire:model="employee_name" class="border rounded px-2 py-1 w-full" />
                    @error('employee_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="input-group">
                    <label>Position:</label>
                    <input type="text" wire:model="position" class="border rounded px-2 py-1 w-full" />
                    @error('position') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="input-group">
                    <label>Farm:</label>
                    <select wire:model="farm">
                        <option value="">Select farm...</option>
                        <option value="BFC">BFC</option>
                        <option value="BDL">BDL</option>
                        <option value="PFC">PFC</option>
                        <option value="RH">RH</option>
                        <option value="BBGC">BBGC</option>
                        <option value="HATCHERY">HATCHERY</option>
                    </select>
                    @error('farm') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="input-group">
                    <label>Department/Division:</label>
                    <select wire:model="department">
                        <option value=""></option>
                        @foreach($departments ?? [] as $dept)
                            <option value="{{ $dept }}">{{ $dept }}</option>
                        @endforeach
                    </select>
                    @error('department') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button"
                            @click="$store.dashModal.close(); $wire.clear()"
                            class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-100">Cancel</button>
                    <button type="button"
                            @click="$store.dashModal.close(); $wire.submit()"
                            class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-800">Confirm</button>
                </div>
            </div>

            {{-- ── Import Type Choice ── --}}
            <div class="flex flex-col gap-4 w-[22rem]" x-show="$store.dashModal.template === 'import-type'">
                <h2 class="text-xl font-semibold">Import Assets</h2>
                <p class="text-sm text-gray-500 -mt-2">Choose how you want to import assets into the system.</p>

                <div class="border border-gray-200 rounded-xl p-4 hover:border-teal-400 hover:bg-teal-50 transition-colors cursor-pointer"
                     @click="$store.dashModal.close(); document.getElementById('import-file').click()">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-teal-100 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-database text-teal-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 text-sm">System Import</p>
                            <p class="text-xs text-gray-400 mt-0.5">Restore from a system export. Reference IDs and all fields preserved exactly.</p>
                        </div>
                        <i class="fa-solid fa-chevron-right text-gray-300 ml-auto shrink-0 text-xs"></i>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-xl p-4 hover:border-blue-400 hover:bg-blue-50 transition-colors cursor-pointer"
                     @click="$store.dashModal.close(); document.getElementById('migration-import-file').click()">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-file-arrow-up text-blue-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 text-sm">Migration Import</p>
                            <p class="text-xs text-gray-400 mt-0.5">Import from the fill-in migration template. Category names and Reference IDs are handled automatically.</p>
                        </div>
                        <i class="fa-solid fa-chevron-right text-gray-300 ml-auto shrink-0 text-xs"></i>
                    </div>
                </div>

                <button type="button" @click="$store.dashModal.close()"
                    class="w-full px-4 py-2 border border-gray-200 rounded-md text-sm font-semibold text-gray-600 hover:bg-gray-100">
                    Cancel
                </button>
            </div>

            {{-- ── Export Type Choice ── --}}
            <div class="flex flex-col gap-4 w-[22rem]" x-show="$store.dashModal.template === 'export-type'">
                <h2 class="text-xl font-semibold">Export / Download</h2>
                <p class="text-sm text-gray-500 -mt-2">Choose what you want to export or download.</p>

                <div class="border border-gray-200 rounded-xl p-4 hover:border-teal-400 hover:bg-teal-50 transition-colors cursor-pointer"
                     @click="$store.dashModal.template = 'export-filter'">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-teal-100 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-file-export text-teal-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 text-sm">System Export</p>
                            <p class="text-xs text-gray-400 mt-0.5">Full export including Reference IDs. Use for backup or migrating to another deployment.</p>
                        </div>
                        <i class="fa-solid fa-chevron-right text-gray-300 ml-auto shrink-0 text-xs"></i>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-xl p-4 hover:border-blue-400 hover:bg-blue-50 transition-colors cursor-pointer"
                     @click="$store.dashModal.close(); window.location.href = '/assets/migration-template'">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-file-arrow-down text-blue-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 text-sm">Migration Template</p>
                            <p class="text-xs text-gray-400 mt-0.5">Download a blank fill-in form for entering data from an old system. Has a sample row showing the correct format.</p>
                        </div>
                        <i class="fa-solid fa-chevron-right text-gray-300 ml-auto shrink-0 text-xs"></i>
                    </div>
                </div>

                <button type="button" @click="$store.dashModal.close()"
                    class="w-full px-4 py-2 border border-gray-200 rounded-md text-sm font-semibold text-gray-600 hover:bg-gray-100">
                    Cancel
                </button>
            </div>

            {{-- ── Export Filter ── --}}
            <div class="flex flex-col gap-5 w-[25rem]" x-show="$store.dashModal.template === 'export-filter'">
                <h2 class="text-xl font-semibold -mb-2">Export Assets - Filters</h2>
                <p class="text-sm text-gray-500">Select filters to customize your export. Leave blank to export all.</p>

                <div class="input-group">
                    <label>Category Type:</label>
                    <select wire:model.live="export_category_type">
                        <option value="">All</option>
                        <option value="IT">IT</option>
                        <option value="NON-IT">NON-IT</option>
                    </select>
                </div>
                <div class="input-group">
                    <label>Category:</label>
                    <select wire:model.live="export_category">
                        <option value="">All</option>
                        @foreach($export_categories as $cat)
                            <option value="{{ $cat->code }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="input-group">
                    <label>Sub-category:</label>
                    <select wire:model="export_sub_category">
                        <option value="">All</option>
                        @foreach($export_sub_categories ?? [] as $subcat)
                            <option value="{{ $subcat }}">{{ $subcat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="input-group">
                    <label>Farm:</label>
                    <select wire:model="export_farm">
                        <option value="">All</option>
                        <option value="BFC">BFC</option>
                        <option value="BDL">BDL</option>
                        <option value="PFC">PFC</option>
                        <option value="RH">RH</option>
                        <option value="BBGC">BBGC</option>
                        <option value="HATCHERY">HATCHERY</option>
                    </select>
                </div>
                <div class="input-group">
                    <label>Department:</label>
                    <select wire:model="export_department">
                        <option value="">All</option>
                        @foreach($departments ?? [] as $dept)
                            <option value="{{ $dept }}">{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="input-group">
                    <label>Asset Age:</label>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-gray-500">Min Years:</label>
                            <input type="number" wire:model="export_age_min" placeholder="0" min="0" class="w-full">
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">Max Years:</label>
                            <input type="number" wire:model="export_age_max" placeholder="Any" min="0" class="w-full">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button"
                            @click="$store.dashModal.close(); $wire.clearExportFilters()"
                            class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-100">Cancel</button>
                    <button type="button" wire:click="exportWithFilters"
                            class="px-4 py-2 bg-teal-500 text-white rounded-md hover:bg-teal-600">
                        <i class="fa-solid fa-download mr-2"></i>Export
                    </button>
                </div>
            </div>

            {{-- ── Farm Assets ── --}}
            <livewire:farm-assets :farmCode="$targetFarm" :key="$targetFarm" />

        </div>
    </div>

</div>
