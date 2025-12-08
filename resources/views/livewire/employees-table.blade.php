<div class="card content flex-1 flex flex-col" x-data="{
    showModal: false,
    modalTemplate: '',
}">
    <div class="table-header flex justify-between items-center">
        <h1 class="text-lg font-bold">Employee List</h1>
        <div class="flex items-center gap-3">
            <div class="border border-2 px-3 py-1 rounded-md border-gray-300">
                    <input class="outline-none text-sm" type="text" wire:model.live="search" placeholder="Search employee...">
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
            </div>
            <button class="px-5 py-2 bg-[#4fd1c5] rounded-lg font-bold text-white text-xs hover:bg-teal-500" @click="showModal = true; modalTemplate = 'create'">ADD NEW EMPLOYEE</button>

            <form id="import-form" action="/employees/import" method="POST" enctype="multipart/form-data">
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

            <i class="fa-solid fa-file-export cursor-pointer" onclick="window.location.href='/employees/export'"></i>

            <i class="fa-solid fa-ellipsis-vertical cursor-pointer"></i>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                    <tr>
                        <th>EMPLOYEE ID</th>
                        <th>EMPLOYEE NAME</th>
                        <th>POSITION</th>
                        <th>FARM</th>
                        <th>DEPARTMENT/DIVISION</th>
                        <th>ASSIGNED ASSETS</th>
                        <th>FLAGS</th>
                        <th>ACTION</th>
                    </tr>
            </thead>
            <tbody>
                @foreach($employees as $employee)
                <tr>
                    <td>#{{$employee->employee_id}} <i class="fa-regular fa-copy cursor-pointer text-gray-400"></i></td>
                    <td>{{$employee->employee_name}}</td>
                    <td>{{$employee->position}}</td>
                    <td>{{$employee->farm}}</td>
                    <td>{{$employee->department}}</td>
                    <td>{{ $employee->assets_count }}</td>
                    <td>
                        @if($employee->flags_count > 0)
                            <div class="flex gap-2 items-center">
                                @php
                                    $displayedFlags = $employee->flags->take(3);
                                    $remainingCount = $employee->flags_count - 3;
                                @endphp
                                
                                @foreach($displayedFlags as $flag)
                                    <i class="fa-solid fa-flag {{ $flagColors[$flag->flag_type] ?? 'text-gray-500' }}" 
                                    title="{{ $flag->flag_type }} - {{ $flag->asset }}"></i>
                                @endforeach
                                
                                @if($remainingCount > 0)
                                    <p class="font-bold text-gray-400">+{{ $remainingCount }}</p>
                                @endif
                            </div>
                        @else
                            <span class="text-gray-400 text-xs">No flags</span>
                        @endif
                    </td>
                    <td x-data="{ open: false }" class="relative">
                        <i class="fa-solid fa-ellipsis-vertical cursor-pointer" @click="open = !open"></i>

                        <!-- Dropdown -->
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-36 bg-white border border-gray-200 rounded-lg shadow-md z-40">
                        <ul class="text-sm text-gray-700">
                            <li>
                                    <button class="w-full text-left px-4 py-2 hover:bg-gray-100" onclick="window.location.href='/employees/view?targetID={{$employee->id}}'">View</button>
                            </li>
                            <li>
                                    <button class="w-full text-left px-4 py-2 hover:bg-gray-100" @click="modalTemplate='edit'; showModal=true; $wire.targetID({{$employee->id}})">Edit</button>
                            </li>
                            <li>
                                    <button class="w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100" @click="modalTemplate='delete'; showModal=true; $wire.targetID({{$employee->id}})">Delete</button>
                            </li>
                        </ul>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <x-pagination :paginator="$employees" />

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

        <div class="relative bg-white p-8 rounded-lg shadow-lg w-[26rem]">
            <!-- Close button -->
            <button type="button" class="absolute right-7 top-7 text-gray-400 hover:text-gray-800" @click="showModal = false; $wire.clear()" aria-label="Close modal">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <!-- Create / Edit Modal -->
            <template x-if="modalTemplate === 'create' || modalTemplate === 'edit'">
                <div class="flex flex-col gap-5">
                    <h2 class="text-xl font-semibold -mb-2" x-text="modalTemplate === 'create' ? 'Add New Employee' : 'Edit Employee Details'"></h2>

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
                            <option value="PFC">PFC</option>
                            <option value="RH">RH</option>
                        </select>
                    </div>

                    <div class="input-group">
                        <label>Department/Division:</label>
                        <select wire:model="department">
                            <option value="">Select Department</option>
                            <option value="FEEDMILL">FEEDMILL</option>
                            <option value="FOC">FOC</option>
                            <option value="GENERAL SERVICES">GENERAL SERVICES</option>
                            <option value="HR">HR</option>
                            <option value="IT &amp; SECURITY">IT &amp; SECURITY</option>
                            <option value="POULTRY">POULTRY</option>
                            <option value="PURCHASING">PURCHASING</option>
                            <option value="SALES &amp; ANALYTICS">SALES &amp; ANALYTICS</option>
                            <option value="SWINE">SWINE</option>
                        </select>
                        <input type="text" wire:model="department" class="border rounded px-2 py-1 w-full" />
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showModal = false; $wire.clear()" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-100">Cancel</button>

                        <button
                            type="button"
                            @click="showModal = false; modalTemplate === 'create' ? $wire.submit() : $wire.update();"
                            class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-800"
                        >
                            <span x-text="modalTemplate === 'create' ? 'Confirm' : 'Update'"></span>
                        </button>
                    </div>
                </div>
            </template>

            <!-- Delete Confirmation Modal -->
            <div class="flex flex-col gap-5" x-show="modalTemplate === 'delete'">
                <h2 class="text-xl font-semibold -mb-2">Delete Modal</h2>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Minima, incidunt!</p>

                <div class="flex justify-end gap-3">
                    <button type="button" @click="showModal = false" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-100">Cancel</button>

                    <button type="button" @click="showModal = false; $wire.delete()" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-800">Confirm</button>
                </div>
            </div>
        </div>
    </div>

</div>
