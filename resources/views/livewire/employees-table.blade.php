{{-- ============================================================
     Employee Management — index view
     ============================================================ --}}

<div class="h-full flex flex-col">

<div
    class="card content h-full flex flex-col gap-4"
    style="transform: none !important; will-change: auto;"
    x-data="{
        openModal(template) {
            window.dispatchEvent(new CustomEvent('open-modal', { detail: { template, employee: '' } }));
        },
        closeModal() {
            window.dispatchEvent(new CustomEvent('close-modal'));
        }
    }"
    x-on:keydown.escape.window="closeModal()"
>

    {{-- ── Toolbar ── --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-lg font-bold text-[#2d3748]">Employee List</h1>

        <div class="flex items-center gap-2 flex-wrap">

            {{-- Search --}}
            <div class="flex items-center gap-2 border border-gray-200 rounded-lg px-3 py-2 bg-white hover:border-teal-400 transition-colors">
                <i class="fa-solid fa-magnifying-glass text-gray-400 text-sm"></i>
                <input
                    class="outline-none text-sm bg-transparent w-40 placeholder-gray-400"
                    type="text"
                    wire:model.live="search"
                    placeholder="Search employee..."
                    title="Search by Employee ID, Name, Position, Farm, Department, etc."
                >
            </div>

            {{-- ADD NEW EMPLOYEE --}}
            <button
                class="flex items-center gap-2 px-4 py-2 bg-[#4fd1c5] hover:bg-teal-500 text-white rounded-lg text-xs font-bold transition-colors"
                @click="openModal('create')"
                title="Add New Employee"
            >
                <i class="fa-solid fa-plus"></i>
                Add Employee
            </button>

            {{-- Icon buttons group --}}
            <div class="flex items-center gap-1">

                {{-- Import --}}
                <form id="employee-import-form" action="/employees/import" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" id="employee-import-file" name="file" accept=".xlsx,.xls,.csv" class="hidden" required>
                    <button
                        type="button"
                        id="employee-import-button"
                        title="Import Employees"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 hover:text-teal-500 transition-colors"
                    >
                        <i class="fa-solid fa-file-import text-sm"></i>
                    </button>
                </form>

                {{-- Export --}}
                <button
                    type="button"
                    title="Export Employees"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 hover:text-teal-500 transition-colors"
                    onclick="window.location.href='/employees/export'"
                >
                    <i class="fa-solid fa-file-export text-sm"></i>
                </button>

                {{-- Filter --}}
                <div x-data="{ filterOpen: false }" class="relative">
                    <button
                        type="button"
                        title="Filter Employees"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 hover:text-teal-500 transition-colors"
                        :class="filterOpen ? 'bg-gray-100 text-teal-500' : ''"
                        @click="filterOpen = !filterOpen"
                    >
                        <i class="fa-solid fa-sliders text-sm"></i>
                    </button>

                    {{-- Filter dropdown --}}
                    <div
                        x-show="filterOpen"
                        @click.outside="filterOpen = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="absolute right-0 mt-2 w-72 bg-white border border-gray-200 rounded-xl shadow-xl z-50"
                    >
                        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                            <h3 class="text-sm font-bold text-gray-800">Filter Employees</h3>
                            <button @click="filterOpen = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                                <i class="fa-solid fa-xmark text-sm"></i>
                            </button>
                        </div>

                        <div class="p-4 space-y-4 max-h-[60vh] overflow-y-auto">

                            <div class="space-y-2">
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Farm</p>
                                <select wire:model.live="filterFarm" class="w-full text-sm rounded-lg border border-gray-200 px-2 py-1.5 outline-none focus:border-teal-400">
                                    <option value="">All Farms</option>
                                    <option value="BFC">BFC</option>
                                    <option value="BDL">BDL</option>
                                    <option value="PFC">PFC</option>
                                    <option value="RH">RH</option>
                                    <option value="BBGC">BBGC</option>
                                    <option value="Hatchery">Hatchery</option>
                                </select>
                            </div>

                            <div class="space-y-2">
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Department</p>
                                <select wire:model.live="filterDepartment" class="w-full text-sm rounded-lg border border-gray-200 px-2 py-1.5 outline-none focus:border-teal-400">
                                    <option value="">All Departments</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->name }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-2">
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Position</p>
                                <select wire:model.live="filterPosition" class="w-full text-sm rounded-lg border border-gray-200 px-2 py-1.5 outline-none focus:border-teal-400">
                                    <option value="">All Positions</option>
                                    @foreach($positions as $pos)
                                        <option value="{{ $pos }}">{{ $pos }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-2">
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Flag</p>
                                <select wire:model.live="filterFlag" class="w-full text-sm rounded-lg border border-gray-200 px-2 py-1.5 outline-none focus:border-teal-400">
                                    <option value="">All Flags</option>
                                    <option value="Under Investigation">Under Investigation</option>
                                    <option value="Pending Clearances">Pending Clearances</option>
                                    <option value="Lost Asset">Lost Asset</option>
                                    <option value="Unreturned Asset">Unreturned Asset</option>
                                    <option value="Damaged Asset">Damaged Asset</option>
                                </select>
                            </div>

                        </div>

                        <div class="flex items-center justify-between px-4 py-3 border-t border-gray-100">
                            <button wire:click="resetFilters" class="text-xs font-semibold text-gray-500 hover:text-gray-800 transition-colors">
                                Reset all
                            </button>
                            <button @click="filterOpen = false" class="px-4 py-2 bg-teal-500 text-white rounded-lg text-xs font-bold hover:bg-teal-600 transition-colors">
                                Done
                            </button>
                        </div>
                    </div>
                </div>

            </div>{{-- end icon group --}}
        </div>
    </div>

    {{-- ── Table ── --}}
    <div class="table-container flex-1 flex flex-col min-h-0">
        <div class="flex-1 overflow-y-auto overflow-x-auto minimal-scroll">
            <table class="h-full">
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Employee Name</th>
                        <th>Position</th>
                        <th>Farm</th>
                        <th>Department/Division</th>
                        <th>Assigned Assets</th>
                        <th>Flags</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $flagColors = [
                            'Under Investigation' => 'text-blue-500',
                            'Pending Clearances'  => 'text-purple-500',
                            'Lost Asset'          => 'text-red-500',
                            'Unreturned Asset'    => 'text-orange-500',
                            'Damaged Asset'       => 'text-yellow-500',
                        ];
                    @endphp

                    @forelse($employees as $employee)
                    <tr>
                        <td class="font-mono text-xs text-gray-500">
                            #{{ $employee->employee_id }}
                            <i class="fa-regular fa-copy cursor-pointer text-gray-400 hover:text-teal-500 transition-colors ml-1"></i>
                        </td>
                        <td class="text-sm font-semibold">{{ $employee->employee_name }}</td>
                        <td class="text-sm text-gray-600">{{ $employee->position }}</td>
                        <td class="text-sm text-gray-600">{{ $employee->farm }}</td>
                        <td class="text-sm text-gray-600">{{ $employee->department }}</td>
                        <td class="text-sm text-gray-600">{{ $employee->assets_count }}</td>
                        <td>
                            @if($employee->flags_count > 0)
                                @php
                                    $displayedFlags = $employee->flags->take(3);
                                    $remainingCount = $employee->flags_count - 3;
                                @endphp
                                <div class="flex gap-2 items-center">
                                    @foreach($displayedFlags as $flag)
                                        <i class="fa-solid fa-flag {{ $flagColors[$flag->flag_type] ?? 'text-gray-500' }}"
                                           title="{{ $flag->flag_type }} - {{ $flag->asset }}"></i>
                                    @endforeach
                                    @if($remainingCount > 0)
                                        <span class="text-xs font-bold text-gray-400">+{{ $remainingCount }}</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-gray-400 text-xs">No flags</span>
                            @endif
                        </td>
                        <td>
                            <div x-data="{ open: false }" class="relative flex justify-center">
                                <button
                                    type="button"
                                    class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-700 transition-colors"
                                    @click="open = !open"
                                >
                                    <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
                                </button>

                                <div
                                    x-show="open"
                                    @click.outside="open = false"
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="absolute right-0 mt-1 top-full w-36 bg-white border border-gray-100 rounded-xl shadow-lg z-50 overflow-hidden"
                                >
                                    <ul class="text-sm text-gray-700 py-1">
                                        <li>
                                            <button
                                                class="w-full text-left px-4 py-2 hover:bg-gray-50 flex items-center gap-2 transition-colors"
                                                onclick="window.location.href='/employees/view?targetID={{ $employee->id }}'"
                                            >
                                                <i class="fa-solid fa-eye text-xs text-gray-400"></i> View
                                            </button>
                                        </li>
                                        <li>
                                            <button
                                                class="w-full text-left px-4 py-2 hover:bg-gray-50 flex items-center gap-2 transition-colors"
                                                @click="open = false; window.dispatchEvent(new CustomEvent('open-modal', { detail: { template: 'edit', employee: {{ $employee->id }} } })); $wire.targetID({{ $employee->id }})"
                                            >
                                                <i class="fa-solid fa-pen text-xs text-gray-400"></i> Edit
                                            </button>
                                        </li>
                                        <li class="border-t border-gray-100">
                                            <button
                                                class="w-full text-left px-4 py-2 hover:bg-red-50 text-red-500 flex items-center gap-2 transition-colors"
                                                @click="open = false; window.dispatchEvent(new CustomEvent('open-modal', { detail: { template: 'delete', employee: {{ $employee->id }} } })); $wire.targetID({{ $employee->id }})"
                                            >
                                                <i class="fa-solid fa-trash text-xs"></i> Delete
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="h-full">
                        <td colspan="8" class="text-center">
                            <div class="flex flex-col items-center justify-center gap-3 text-gray-400 py-24">
                                <i class="fa-solid fa-users text-4xl"></i>
                                <p class="text-sm font-semibold">No employees found</p>
                                <p class="text-xs">Try adjusting your search or filters</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination :paginator="$employees" />
    </div>

    {{-- ── Import loading backdrop ── --}}
    <div
        id="employee-import-backdrop"
        class="hidden fixed inset-0 bg-black/50 z-[60] flex items-center justify-center"
    >
        <div class="bg-white rounded-2xl p-8 shadow-2xl flex flex-col items-center gap-4 min-w-[280px]">
            <div class="animate-spin rounded-full h-10 w-10 border-4 border-gray-200 border-t-teal-500"></div>
            <div class="text-center">
                <h3 class="text-base font-bold text-gray-800 mb-1">Importing Employees</h3>
                <p class="text-sm text-gray-400">Please wait while we process your file...</p>
            </div>
        </div>
    </div>

</div>{{-- end card --}}


{{-- ================================================================
     MODALS
     ================================================================ --}}
<div
    x-data="{
        showModal: false,
        modalTemplate: '',
        targetEmployee: ''
    }"
    x-on:open-modal.window="
        modalTemplate  = $event.detail.template;
        targetEmployee = $event.detail.employee ?? '';
        showModal      = true;
    "
    x-on:close-modal.window="showModal = false; modalTemplate = ''; targetEmployee = '';"
    x-on:keydown.escape.window="showModal = false; modalTemplate = ''; targetEmployee = '';"
    style="display:contents"
>
    {{-- Backdrop --}}
    <div
        x-show="showModal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/40 z-[70]"
        @click="showModal = false; modalTemplate = ''"
    ></div>

    {{-- Modal panel --}}
    <div
        x-show="showModal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 z-[80] flex items-center justify-center px-4 pointer-events-none"
    >
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg pointer-events-auto max-h-[90vh] overflow-y-auto">

            {{-- Close --}}
            <button
                class="absolute right-5 top-5 w-7 h-7 flex items-center justify-center rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-700 transition-colors z-10"
                @click="showModal = false; modalTemplate = ''; targetEmployee = ''; $wire.clear()"
            >
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>

            {{-- Create / Edit --}}
            <div class="p-8" x-show="modalTemplate === 'create' || modalTemplate === 'edit'">
                <h2 class="text-lg font-bold text-gray-800 mb-6" x-text="modalTemplate === 'create' ? 'Add New Employee' : 'Edit Employee Details'"></h2>

                <div class="space-y-4">
                    <div class="input-group">
                        <label>Employee ID</label>
                        <input type="text" wire:model="employee_id">
                    </div>

                    <div class="input-group">
                        <label>Employee Name</label>
                        <input type="text" wire:model="employee_name">
                    </div>

                    <div class="input-group">
                        <label>Position</label>
                        <input type="text" wire:model="position">
                    </div>

                    <div class="input-group">
                        <label>Farm</label>
                        <select wire:model="farm">
                            <option value=""></option>
                            <option value="BFC">BFC</option>
                            <option value="BDL">BDL</option>
                            <option value="PFC">PFC</option>
                            <option value="RH">RH</option>
                            <option value="BBGC">BBGC</option>
                            <option value="Hatchery">Hatchery</option>
                        </select>
                    </div>

                    <div class="input-group">
                        <label>Department/Division</label>
                        <select wire:model="department">
                            <option value=""></option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->name }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button
                        type="button"
                        @click="showModal = false; $wire.clear()"
                        class="px-4 py-2 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        @click="showModal = false; modalTemplate === 'create' ? $wire.submit() : $wire.update()"
                        class="px-4 py-2 bg-teal-500 text-white rounded-xl text-sm font-bold hover:bg-teal-600 transition-colors"
                        x-text="modalTemplate === 'create' ? 'Add Employee' : 'Save Changes'"
                    ></button>
                </div>
            </div>

            {{-- Delete Confirm --}}
            <div class="p-8" x-show="modalTemplate === 'delete'">
                <div class="flex flex-col items-center gap-4 text-center">
                    <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                        <i class="fa-solid fa-trash text-red-500"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-800 mb-1">Delete Employee</h2>
                        <p class="text-sm text-gray-500">Are you sure you want to delete this employee? This action cannot be undone.</p>
                    </div>
                    <div class="flex gap-3 w-full mt-2">
                        <button
                            type="button"
                            @click="showModal = false"
                            class="flex-1 px-4 py-2 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            type="button"
                            @click="showModal = false; $wire.delete()"
                            class="flex-1 px-4 py-2 bg-red-500 text-white rounded-xl text-sm font-semibold hover:bg-red-600 transition-colors"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>{{-- end modal state --}}


{{-- ── Import script ── --}}
<script>
    (function () {
        const btn      = document.getElementById('employee-import-button');
        const file     = document.getElementById('employee-import-file');
        const form     = document.getElementById('employee-import-form');
        const backdrop = document.getElementById('employee-import-backdrop');
        if (!btn || !file || !form || !backdrop) return;
        btn.addEventListener('click', () => file.click());
        file.addEventListener('change', () => {
            if (file.files.length > 0) {
                backdrop.classList.remove('hidden');
                form.submit();
            }
        });
        window.addEventListener('pageshow', (e) => {
            if (e.persisted) backdrop.classList.add('hidden');
        });
    })();
</script>

</div>{{-- end Livewire root --}}