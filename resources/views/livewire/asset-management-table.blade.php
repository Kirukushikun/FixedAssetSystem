{{-- ============================================================
     Asset Management — index view
     Injected via @yield('content')
     ============================================================ --}}


{{-- Single Livewire root --}}
<div class="h-full flex flex-col">
    @php($currentUser = Auth::user())

    <div class="card content h-full flex flex-col gap-4" style="transform: none !important; will-change: auto;"
        x-data="{
            showModal: false,
            modalTemplate: '',
            targetAsset: '',
            openModal(template) {
                window.dispatchEvent(new CustomEvent('open-modal', { detail: { template, asset: '' } }));
            },
            closeModal() {
                window.dispatchEvent(new CustomEvent('close-modal'));
            }
        }" x-on:keydown.escape.window="closeModal()" x-init="$el.querySelectorAll('.modal-hidden').forEach(el => el.classList.remove('modal-hidden'))">

        {{-- ── Toolbar ── --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-lg font-bold text-[#2d3748]">All Assets</h1>

            <div class="flex items-center gap-2 flex-wrap">

                {{-- Search --}}
                <div
                    class="flex items-center gap-2 border border-gray-200 rounded-lg px-3 py-2 bg-white hover:border-teal-400 transition-colors">
                    <i class="fa-solid fa-magnifying-glass text-gray-400 text-sm"></i>
                    <input class="outline-none text-sm bg-transparent w-40 placeholder-gray-400" type="text"
                        wire:model.live="search" placeholder="Search assets..."
                        title="Search by Reference ID, Brand, Model, Assigned To, etc.">
                </div>

                <button
                    class="flex items-center gap-2 px-4 py-2 bg-[#4fd1c5] hover:bg-teal-500 text-white rounded-lg text-xs font-bold transition-colors disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-[#4fd1c5]"
                    :disabled="!{{ $currentUser?->hasPermission('assets.create') ? 'true' : 'false' }}"
                    @click="{{ $currentUser?->hasPermission('assets.create') ? 'openModal(\'create\')' : 'null' }}"
                    title="{!! $currentUser?->hasPermission('assets.create') ? 'Add New Asset' : 'You do not have permission to add assets' !!}">
                    <i class="fa-solid fa-plus"></i>
                    Add Asset
                </button>


                <a href="{!! $currentUser?->hasPermission('assets.qr') ? '/assetmanagement/qr' : 'javascript:void(0)' !!}"
                    class="flex items-center gap-2 px-4 py-2 bg-indigo-400 hover:bg-indigo-500 text-white rounded-lg text-xs font-bold transition-colors disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-indigo-400"
                    @click="{{ $currentUser?->hasPermission('assets.qr') ? 'true' : 'false' }} || $event.preventDefault()"
                    title="{!! $currentUser?->hasPermission('assets.qr') ? 'QR Code Management' : 'You do not have permission to manage QR codes' !!}">
                    <i class="fa-solid fa-qrcode"></i>
                    QR Codes
                </a>

                <div class="flex items-center gap-1">
                    {{-- Hidden forms — triggered from the import-type choice modal --}}
                    <form id="asset-import-form" action="/assets/import" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" id="asset-import-file" name="file" accept=".xlsx,.xls,.csv" class="hidden">
                    </form>
                    <form id="asset-migration-import-form" action="/assets/migration-import" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" id="asset-migration-import-file" name="file" accept=".xlsx,.xls,.csv" class="hidden">
                    </form>

                    <button type="button" id="asset-import-button"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 hover:text-teal-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="!{{ $currentUser?->hasPermission('assets.import') ? 'true' : 'false' }}"
                        @click="{{ $currentUser?->hasPermission('assets.import') ? 'openModal(\'import-type\')' : 'null' }}"
                        title="{!! $currentUser?->hasPermission('assets.import') ? 'Import Assets' : 'You do not have permission to import assets' !!}">
                        <i class="fa-solid fa-file-import text-sm"></i>
                    </button>

                    <button type="button"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 hover:text-teal-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="!{{ $currentUser?->hasPermission('assets.export') ? 'true' : 'false' }}"
                        @click="{{ $currentUser?->hasPermission('assets.export') ? 'openModal(\'export-type\')' : 'null' }}"
                        title="{!! $currentUser?->hasPermission('assets.export') ? 'Export Assets' : 'You do not have permission to export assets' !!}">
                        <i class="fa-solid fa-file-export text-sm"></i>
                    </button>

                    <button type="button" title="Export Audit Log"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 hover:text-teal-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="!{{ $currentUser?->hasPermission('audit.export') ? 'true' : 'false' }}"
                        @click="{{ $currentUser?->hasPermission('audit.export') ? 'openModal(\'audit-log-filter\')' : 'null' }}"
                        title="{!! $currentUser?->hasPermission('audit.export') ? 'Export Audit Log' : 'You do not have permission to export audit logs' !!}">
                        <i class="fa-solid fa-clipboard-list text-sm"></i>
                    </button>

                    <button type="button" title="Export Repair Log"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 hover:text-teal-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="!{{ $currentUser?->hasPermission('reports.export') ? 'true' : 'false' }}"
                        @click="{{ $currentUser?->hasPermission('reports.export') ? 'openModal(\'repair-log-filter\')' : 'null' }}"
                        title="{!! $currentUser?->hasPermission('reports.export') ? 'Export Repair Log' : 'You do not have permission to export repair logs' !!}">
                        <i class="fa-solid fa-tools text-sm"></i>
                    </button>

                    <div x-data="{ filterOpen: false }" class="relative">
                        <button type="button" title="Filter Assets"
                            class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 hover:text-teal-500 transition-colors"
                            :class="filterOpen ? 'bg-gray-100 text-teal-500' : ''" @click="filterOpen = !filterOpen">
                            <i class="fa-solid fa-sliders text-sm"></i>
                        </button>

                        <div x-show="filterOpen" @click.outside="filterOpen = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1"
                            class="absolute right-0 mt-2 w-80 bg-white border border-gray-200 rounded-xl shadow-xl z-50">
                            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                                <h3 class="text-sm font-bold text-gray-800">Filter Assets</h3>
                                <button @click="filterOpen = false"
                                    class="text-gray-400 hover:text-gray-600 transition-colors">
                                    <i class="fa-solid fa-xmark text-sm"></i>
                                </button>
                            </div>

                            <div class="p-4 space-y-4 max-h-[60vh] overflow-y-auto">

                                <div class="space-y-2">
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Category</p>
                                    <div class="grid grid-cols-2 gap-2">
                                        <select wire:model.live="filterCategoryType"
                                            class="input-group input-group select text-sm rounded-lg border border-gray-200 px-2 py-1.5 outline-none focus:border-teal-400">
                                            <option value="">Type</option>
                                            <option value="IT">IT</option>
                                            <option value="NON-IT">NON-IT</option>
                                        </select>
                                        <select wire:model.live="filterCategory"
                                            class="text-sm rounded-lg border border-gray-200 px-2 py-1.5 outline-none focus:border-teal-400">
                                            <option value="">Category</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->code }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                        <select wire:model.live="filterSubCategory"
                                            class="col-span-2 text-sm rounded-lg border border-gray-200 px-2 py-1.5 outline-none focus:border-teal-400">
                                            <option value="">Sub-category</option>
                                            @foreach ($subCategories as $subCategory)
                                                <option value="{{ $subCategory->name }}">{{ $subCategory->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Assignment
                                    </p>
                                    <div class="grid grid-cols-2 gap-2">
                                        <select wire:model.live="filterFarm"
                                            class="text-sm rounded-lg border border-gray-200 px-2 py-1.5 outline-none focus:border-teal-400">
                                            <option value="">Farm</option>
                                            <option value="BFC">BFC</option>
                                            <option value="BDL">BDL</option>
                                            <option value="PFC">PFC</option>
                                            <option value="RH">RH</option>
                                            <option value="BBGC">BBGC</option>
                                            <option value="HATCHERY">HATCHERY</option>
                                        </select>
                                        <select wire:model.live="filterDepartment"
                                            class="text-sm rounded-lg border border-gray-200 px-2 py-1.5 outline-none focus:border-teal-400">
                                            <option value="">Department</option>
                                            @foreach ($departments as $deptOption)
                                                <option value="{{ $deptOption }}">{{ $deptOption }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Status</p>
                                    <select wire:model.live="filterStatus"
                                        class="w-full text-sm rounded-lg border border-gray-200 px-2 py-1.5 outline-none focus:border-teal-400">
                                        <option value="">All statuses</option>
                                        <option value="Pending Acquisition">Pending Acquisition</option>
                                        <option value="Available">Available</option>
                                        <option value="Issued">Issued</option>
                                        <option value="Transferred">Transferred</option>
                                        <option value="For Transfer">For Transfer</option>
                                        <option value="For Disposal">For Disposal</option>
                                        <option value="Disposed">Disposed</option>
                                        <option value="Lost">Lost</option>
                                    </select>
                                </div>

                                <div class="space-y-2">
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Condition
                                    </p>
                                    <select wire:model.live="filterCondition"
                                        class="w-full text-sm rounded-lg border border-gray-200 px-2 py-1.5 outline-none focus:border-teal-400">
                                        <option value="">All conditions</option>
                                        <option value="Good">Good</option>
                                        <option value="Defective">Defective</option>
                                        <option value="Repair">Repair</option>
                                        <option value="Replace">Replace</option>
                                    </select>
                                </div>

                                <div class="space-y-2">
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Acquisition
                                        Date</p>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <p class="text-xs text-gray-400 mb-1">From</p>
                                            <input type="date" wire:model.live="filterDateFrom"
                                                class="w-full text-sm rounded-lg border border-gray-200 px-2 py-1.5 outline-none focus:border-teal-400">
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-400 mb-1">To</p>
                                            <input type="date" wire:model.live="filterDateTo"
                                                class="w-full text-sm rounded-lg border border-gray-200 px-2 py-1.5 outline-none focus:border-teal-400">
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Cost Range
                                    </p>
                                    <div class="grid grid-cols-2 gap-2">
                                        <input type="number" placeholder="Min" wire:model.live="filterCostMin"
                                            class="text-sm rounded-lg border border-gray-200 px-2 py-1.5 outline-none focus:border-teal-400">
                                        <input type="number" placeholder="Max" wire:model.live="filterCostMax"
                                            class="text-sm rounded-lg border border-gray-200 px-2 py-1.5 outline-none focus:border-teal-400">
                                    </div>
                                </div>

                            </div>

                            <div class="flex items-center justify-between px-4 py-3 border-t border-gray-100">
                                <button wire:click="resetFilters"
                                    class="text-xs font-semibold text-gray-500 hover:text-gray-800 transition-colors">
                                    Reset all
                                </button>
                                <button @click="filterOpen = false"
                                    class="px-4 py-2 bg-teal-500 text-white rounded-lg text-xs font-bold hover:bg-teal-600 transition-colors">
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

                @if ($assets->isEmpty())
                    <!-- Empty state vertically centered -->
                    <div class="flex flex-col items-center justify-center h-full text-gray-400 py-24">
                        <i class="fa-solid fa-boxes-stacked text-4xl"></i>
                        <p class="text-sm font-semibold">No assets found</p>
                        <p class="text-xs">Try adjusting your search or filters</p>
                    </div>
                @else
                    <!-- Table content aligned at top -->
                    <table class="w-full border-collapse">
                        <thead>
                            <tr>
                                <th>Reference ID</th>
                                <th>Category</th>
                                <th>Sub-category</th>
                                <th>Brand</th>
                                <th>Model</th>
                                <th>Status</th>
                                <th>Condition</th>
                                <th>Assigned To</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($assets as $asset)
                                <tr>
                                    <td class="font-mono text-xs text-gray-500">{{ $asset->ref_id }}</td>
                                    <td>
                                        <p class="flex items-center gap-2">
                                            <img src="{{ asset('img/' . $categoryCodeImage[$asset->category]->icon . '.png') }}"
                                                class="w-5 h-5 object-contain" alt="">
                                            <span
                                                class="text-sm font-bold text-gray-700">{{ $categoryCodeImage[$asset->category]->name }}</span>
                                        </p>
                                    </td>
                                    <td>{{ $asset->sub_category }}</td>
                                    <td>{{ $asset->brand }}</td>
                                    <td>{{ $asset->model }}</td>
                                    <td>
                                        <span @class([
                                            'px-3 py-1 rounded-lg text-xs font-semibold text-white',
                                            'bg-[#90CDF4]' => $asset->status === 'Pending Acquisition',
                                            'bg-[#48BB78]' => $asset->status === 'Available',
                                            'bg-[#ECC94B]' => $asset->status === 'Issued',
                                            'bg-[#4299E1]' => $asset->status === 'Transferred',
                                            'bg-[#9F7AEA]' => $asset->status === 'For Transfer',
                                            'bg-[#ED8936]' => $asset->status === 'For Disposal',
                                            'bg-[#2D3748]' => $asset->status === 'Disposed',
                                            'bg-[#F56565]' => $asset->status === 'Lost',
                                            'bg-gray-400' => ! in_array($asset->status, ['Pending Acquisition', 'Available', 'Issued', 'Transferred', 'For Transfer', 'For Disposal', 'Disposed', 'Lost'], true),
                                        ])>
                                            {{ $asset->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <span @class([
                                            'text-xs font-bold uppercase',
                                            'text-green-500' => $asset->condition === 'Good',
                                            'text-amber-500' => $asset->condition === 'Defective',
                                            'text-sky-500' => $asset->condition === 'Repair',
                                            'text-red-500' => $asset->condition === 'Replace',
                                            'text-gray-500' => ! in_array($asset->condition, ['Good', 'Defective', 'Repair', 'Replace'], true),
                                        ])>
                                            {{ $asset->condition }}
                                        </span>
                                    </td>
                                    <td>{{ $asset->assigned_name ?? '—' }}</td>
                                    <td>
                                        <div x-data="{ open: false }" class="relative flex">
                                            <button type="button"
                                                class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-700 transition-colors"
                                                @click="open = !open">
                                                <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
                                            </button>

                                            <div x-show="open" @click.outside="open = false"
                                                x-transition:enter="transition ease-out duration-150"
                                                x-transition:enter-start="opacity-0 scale-95"
                                                x-transition:enter-end="opacity-100 scale-100"
                                                x-transition:leave="transition ease-in duration-100"
                                                x-transition:leave-start="opacity-100 scale-100"
                                                x-transition:leave-end="opacity-0 scale-95"
                                                class="absolute right-0 mt-1 top-full w-36 bg-white border border-gray-100 rounded-xl shadow-lg z-50 overflow-hidden">
                                                <ul class="text-sm text-gray-700 py-1">
                                                    <li>
                                                        <button
                                                            class="w-full text-left px-4 py-2 hover:bg-gray-50 flex items-center gap-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                                            onclick="window.location.href='/assetmanagement/view?targetID={{ encrypt($asset->id) }}'">
                                                            <i class="fa-solid fa-eye text-xs text-gray-400"></i> View
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button
                                                            class="w-full text-left px-4 py-2 hover:bg-gray-50 flex items-center gap-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                                            @if(!$currentUser?->hasPermission('assets.audit')) disabled @endif
                                                            @if($currentUser?->hasPermission('assets.audit')) onclick="window.location.href='/assetmanagement/audit?targetID={{ encrypt($asset->id) }}'" @endif
                                                            title="{!! $currentUser?->hasPermission('assets.audit') ? 'Audit' : 'You do not have permission to audit assets' !!}">
                                                            <i class="fa-solid fa-clipboard-check text-xs text-gray-400"></i> Audit
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button
                                                            class="w-full text-left px-4 py-2 hover:bg-gray-50 flex items-center gap-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                                            @if(!$currentUser?->hasPermission('assets.update') || $asset->status === 'Disposed') disabled @endif
                                                            @if($currentUser?->hasPermission('assets.update') && $asset->status !== 'Disposed') onclick="window.location.href='/assetmanagement/edit?targetID={{ encrypt($asset->id) }}'" @endif
                                                            title="{!! ($currentUser?->hasPermission('assets.update') && $asset->status !== 'Disposed') ? 'Edit' : ($asset->status === 'Disposed' ? 'Cannot edit disposed assets' : 'You do not have permission to edit assets') !!}">
                                                            <i class="fa-solid fa-pen text-xs text-gray-400"></i> Edit
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

            </div>

            <x-pagination :paginator="$assets" />
        </div>
        {{-- end card --}}


        {{-- ================================================================
     MODALS — teleported to <body> so they escape the card's
     stacking context entirely. Fixed positioning works correctly here.
     ================================================================ --}}
        <div x-data="{
            showModal: false,
            modalTemplate: '',
            targetAsset: ''
        }"
            x-on:open-modal.window="
        modalTemplate = $event.detail.template;
        targetAsset   = $event.detail.asset ?? '';
        showModal     = true;
    "
            x-on:close-modal.window="showModal = false; modalTemplate = ''; targetAsset = '';"
            x-on:keydown.escape.window="showModal = false; modalTemplate = ''; targetAsset = '';"
            style="display:contents">
            {{-- Backdrop --}}
            <div x-show="showModal" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/40 z-[70] modal-hidden"
                @click="showModal = false; modalTemplate = ''"></div>

            {{-- Modal panel --}}
            <div x-show="showModal" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="fixed inset-0 z-[80] flex items-center justify-center px-4 pointer-events-none modal-hidden">
                <div
                    class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg pointer-events-auto max-h-[90vh] overflow-y-auto">

                    {{-- Close --}}
                    <button
                        class="absolute right-5 top-5 w-7 h-7 flex items-center justify-center rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-700 transition-colors z-10"
                        @click="showModal = false; modalTemplate = ''; targetAsset = ''">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>

                    {{-- Select Category --}}
                    <div class="p-8" x-show="modalTemplate === 'create'">
                        <h3 class="text-center font-bold text-xl mb-6">Select Category</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4" x-data="{ openCat: null }">
                            @foreach ($categories as $category)
                                <div>
                                    <button class="flex items-center justify-between w-full py-1"
                                        @click.prevent="openCat = openCat === {{ $category->id }} ? null : {{ $category->id }}">
                                        <span
                                            class="flex items-center gap-2 font-bold text-sm text-gray-700 whitespace-nowrap">
                                            <img src="{{ asset('img/' . $category->icon . '.png') }}"
                                                class="w-5 h-5 object-contain" alt="">
                                            {{ $category->name }}
                                        </span>
                                        <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform duration-200"
                                            :style="openCat === {{ $category->id }} ? 'transform:rotate(180deg)' : ''"></i>
                                    </button>
                                    <div x-show="openCat === {{ $category->id }}"
                                        x-transition:enter="transition ease-out duration-150"
                                        x-transition:enter-start="opacity-0 -translate-y-1"
                                        x-transition:enter-end="opacity-100 translate-y-0"
                                        class="ml-7 mt-2 space-y-1">
                                        @foreach ($category->subcategories as $sub)
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

                    {{-- Delete Confirm --}}
                    <div class="p-8" x-show="modalTemplate === 'delete'">
                        <div class="flex flex-col items-center gap-4 text-center">
                            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                                <i class="fa-solid fa-trash text-red-500"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-800 mb-1">Delete Asset</h2>
                                <p class="text-sm text-gray-500">Are you sure you want to delete this asset? You can
                                    restore it later if needed.</p>
                            </div>
                            <div class="flex gap-3 w-full mt-2">
                                <button type="button" @click="showModal = false"
                                    class="flex-1 px-4 py-2 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors">
                                    Cancel
                                </button>
                                <button type="button" @click="showModal = false; $wire.delete(targetAsset)"
                                    class="flex-1 px-4 py-2 bg-red-500 text-white rounded-xl text-sm font-semibold hover:bg-red-600 transition-colors">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Import Type Choice --}}
                    <div class="p-8" x-show="modalTemplate === 'import-type'">
                        <h2 class="text-lg font-bold text-gray-800 mb-1">Import Assets</h2>
                        <p class="text-sm text-gray-400 mb-6">Choose how you want to import assets into the system.</p>
                        <div class="space-y-3">
                            <div class="border border-gray-200 rounded-xl p-4 hover:border-teal-400 hover:bg-teal-50 transition-colors cursor-pointer"
                                 @click="closeModal(); document.getElementById('asset-import-file').click()">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-lg bg-teal-100 flex items-center justify-center shrink-0">
                                        <i class="fa-solid fa-database text-teal-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800 text-sm">System Import</p>
                                        <p class="text-xs text-gray-400 mt-0.5">Restore from a previous system export. Reference IDs and all fields are preserved exactly.</p>
                                    </div>
                                    <i class="fa-solid fa-chevron-right text-gray-300 ml-auto shrink-0"></i>
                                </div>
                            </div>
                            <div class="border border-gray-200 rounded-xl p-4 hover:border-blue-400 hover:bg-blue-50 transition-colors cursor-pointer"
                                 @click="closeModal(); document.getElementById('asset-migration-import-file').click()">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
                                        <i class="fa-solid fa-file-arrow-up text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800 text-sm">Migration Import</p>
                                        <p class="text-xs text-gray-400 mt-0.5">Import from the fill-in migration template. Category names are looked up automatically; Reference IDs are auto-generated.</p>
                                    </div>
                                    <i class="fa-solid fa-chevron-right text-gray-300 ml-auto shrink-0"></i>
                                </div>
                            </div>
                        </div>
                        <button type="button" @click="closeModal()"
                            class="w-full mt-4 px-4 py-2 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                    </div>

                    {{-- Export Type Choice --}}
                    <div class="p-8" x-show="modalTemplate === 'export-type'">
                        <h2 class="text-lg font-bold text-gray-800 mb-1">Export / Download</h2>
                        <p class="text-sm text-gray-400 mb-6">Choose what you want to export or download.</p>
                        <div class="space-y-3">
                            <div class="border border-gray-200 rounded-xl p-4 hover:border-teal-400 hover:bg-teal-50 transition-colors cursor-pointer"
                                 @click="modalTemplate = 'export-filter'">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-lg bg-teal-100 flex items-center justify-center shrink-0">
                                        <i class="fa-solid fa-file-export text-teal-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800 text-sm">System Export</p>
                                        <p class="text-xs text-gray-400 mt-0.5">Full export including Reference IDs and all fields. Use for backup or migrating to another deployment of this system.</p>
                                    </div>
                                    <i class="fa-solid fa-chevron-right text-gray-300 ml-auto shrink-0"></i>
                                </div>
                            </div>
                            <div class="border border-gray-200 rounded-xl p-4 hover:border-blue-400 hover:bg-blue-50 transition-colors cursor-pointer"
                                 @click="closeModal(); window.location.href = '/assets/migration-template'">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
                                        <i class="fa-solid fa-file-arrow-down text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800 text-sm">Migration Template</p>
                                        <p class="text-xs text-gray-400 mt-0.5">Download a blank fill-in form for entering data from an old system. Includes a sample row and clear column instructions.</p>
                                    </div>
                                    <i class="fa-solid fa-chevron-right text-gray-300 ml-auto shrink-0"></i>
                                </div>
                            </div>
                        </div>
                        <button type="button" @click="closeModal()"
                            class="w-full mt-4 px-4 py-2 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                    </div>

                    {{-- Export Assets --}}
                    <div class="p-8" x-show="modalTemplate === 'export-filter'">
                        <h2 class="text-lg font-bold text-gray-800 mb-1">Export Assets</h2>
                        <p class="text-sm text-gray-400 mb-6">Leave filters blank to export all assets.</p>
                        <div class="space-y-4">
                            <div class="input-group">
                                <label>Category Type</label>
                                <select wire:model.live="export_category_type">
                                    <option value="">All</option>
                                    <option value="IT">IT</option>
                                    <option value="NON-IT">NON-IT</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <label>Category</label>
                                <select wire:model.live="export_category">
                                    <option value="">All</option>
                                    @foreach ($export_categories as $cat)
                                        <option value="{{ $cat->code }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="input-group">
                                <label>Sub-category</label>
                                <select wire:model="export_sub_category">
                                    <option value="">All</option>
                                    @foreach ($export_sub_categories ?? [] as $subcat)
                                        <option value="{{ $subcat }}">{{ $subcat }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="input-group">
                                    <label>Farm</label>
                                    <select wire:model="export_farm">
                                        <option value="">All</option>
                                        <option value="BFC">BFC</option>
                                        <option value="BDL">BDL</option>
                                        <option value="PFC">PFC</option>
                                        <option value="RH">RH</option>
                                    </select>
                                </div>
                                <div class="input-group">
                                    <label>Department</label>
                                    <select wire:model="export_department">
                                        <option value="">All</option>
                                        @foreach ($departments ?? [] as $dept)
                                            <option value="{{ $dept }}">{{ $dept }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="input-group">
                                <label>Asset Age</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <p class="text-xs text-gray-400 mb-1">Min years</p>
                                        <input type="number" wire:model="export_age_min" placeholder="0"
                                            min="0">
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400 mb-1">Max years</p>
                                        <input type="number" wire:model="export_age_max" placeholder="Any"
                                            min="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" @click="showModal = false; $wire.clearExportFilters()"
                                class="px-4 py-2 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors">Cancel</button>
                            <button type="button" wire:click="exportWithFilters"
                                class="px-4 py-2 bg-teal-500 text-white rounded-xl text-sm font-bold hover:bg-teal-600 transition-colors">
                                <i class="fa-solid fa-download mr-2"></i>Export
                            </button>
                        </div>
                    </div>

                    {{-- Export Audit Log --}}
                    <div class="p-8" x-show="modalTemplate === 'audit-log-filter'">
                        <h2 class="text-lg font-bold text-gray-800 mb-1">Export Audit Log</h2>
                        <p class="text-sm text-gray-400 mb-6">Filter audit records to export. Leave blank to export
                            all.</p>
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-3">
                                <div class="input-group">
                                    <label>Date From</label>
                                    <input type="date" wire:model="audit_export_date_from">
                                </div>
                                <div class="input-group">
                                    <label>Date To</label>
                                    <input type="date" wire:model="audit_export_date_to">
                                </div>
                            </div>
                            <div class="input-group">
                                <label>Farm</label>
                                <select wire:model="audit_export_farm">
                                    <option value="">All</option>
                                    <option value="BFC">BFC</option>
                                    <option value="BDL">BDL</option>
                                    <option value="PFC">PFC</option>
                                    <option value="RH">RH</option>
                                    <option value="BBGC">BBGC</option>
                                    <option value="HATCHERY">HATCHERY</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" @click="showModal = false"
                                class="px-4 py-2 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors">Cancel</button>
                            <button type="button" @click="showModal = false" wire:click="exportAuditLog"
                                class="px-4 py-2 bg-teal-500 text-white rounded-xl text-sm font-bold hover:bg-teal-600 transition-colors">
                                <i class="fa-solid fa-download mr-2"></i>Export
                            </button>
                        </div>
                    </div>

                    <!-- Repair Log Export Modal -->
                    <div class="p-8" x-show="modalTemplate === 'repair-log-filter'">
                        <h2 class="text-lg font-bold text-gray-800 mb-1">Export Repair Log</h2>
                        <p class="text-sm text-gray-400 mb-6">Filter repair records to export. Leave blank to export
                            all.</p>

                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-3">
                                <div class="input-group">
                                    <label>Date From</label>
                                    <input type="date" wire:model="repair_export_date_from">
                                </div>
                                <div class="input-group">
                                    <label>Date To</label>
                                    <input type="date" wire:model="repair_export_date_to">
                                </div>
                            </div>
                        </div>

                        <div class="input-group mt-4">
                            <label>Type:</label>
                            <select wire:model="repair_export_type">
                                <option value="">All</option>
                                <option value="PMS">PMS (Preventive Maintenance)</option>
                                <option value="Regular Maintenance">Regular Maintenance</option>
                                <option value="Repair">Repair</option>
                            </select>
                        </div>

                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" @click="showModal = false"
                                class="px-4 py-2 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors">Cancel</button>
                            <button type="button" @click="showModal = false" wire:click="exportRepairLog"
                                class="px-4 py-2 bg-teal-500 text-white rounded-xl text-sm font-bold hover:bg-teal-600 transition-colors">
                                <i class="fa-solid fa-download mr-2"></i>Export
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>



        <script>
            (function() {
                const backdrop = document.getElementById('asset-import-backdrop');

                // System import
                const file = document.getElementById('asset-import-file');
                const form = document.getElementById('asset-import-form');
                if (file && form) {
                    file.addEventListener('change', () => {
                        if (file.files.length > 0) {
                            backdrop?.classList.remove('hidden');
                            form.submit();
                        }
                    });
                }

                // Migration import
                const migFile = document.getElementById('asset-migration-import-file');
                const migForm = document.getElementById('asset-migration-import-form');
                if (migFile && migForm) {
                    migFile.addEventListener('change', () => {
                        if (migFile.files.length > 0) {
                            backdrop?.classList.remove('hidden');
                            migForm.submit();
                        }
                    });
                }

                window.addEventListener('pageshow', (e) => {
                    if (e.persisted) backdrop?.classList.add('hidden');
                });
            })();
        </script>

    </div>
