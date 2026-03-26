{{-- ============================================================
     QR Code Management — index view
     ============================================================ --}}

<div class="h-full flex flex-col">

<div
    class="card content h-full flex flex-col gap-4"
    style="transform: none !important; will-change: auto;"
>

    {{-- ── Toolbar ── --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-lg font-bold text-[#2d3748]">QR Code Management</h1>

        <div class="flex items-center gap-2 flex-wrap">

            {{-- Search --}}
            <div class="flex items-center gap-2 border border-gray-200 rounded-lg px-3 py-2 bg-white hover:border-teal-400 transition-colors">
                <i class="fa-solid fa-magnifying-glass text-gray-400 text-sm"></i>
                <input
                    class="outline-none text-sm bg-transparent w-40 placeholder-gray-400"
                    type="text"
                    wire:model.live="search"
                    placeholder="Search asset..."
                >
            </div>

            {{-- Farm Filter --}}
            <select wire:model.live="filterFarm" class="text-sm rounded-lg border border-gray-200 px-3 py-2 outline-none focus:border-teal-400 bg-white hover:border-teal-400 transition-colors">
                <option value="">All Farms</option>
                <option value="BFC">BFC</option>
                <option value="BDL">BDL</option>
                <option value="PFC">PFC</option>
                <option value="RH">RH</option>
                <option value="BBGC">BBGC</option>
                <option value="Hatchery">Hatchery</option>
            </select>

            {{-- Printed Filter --}}
            <select wire:model.live="filterPrinted" class="text-sm rounded-lg border border-gray-200 px-3 py-2 outline-none focus:border-teal-400 bg-white hover:border-teal-400 transition-colors">
                <option value="">All (Printed)</option>
                <option value="1">Printed</option>
                <option value="0">Not Printed</option>
            </select>

            {{-- Affixed Filter --}}
            <select wire:model.live="filterAffixed" class="text-sm rounded-lg border border-gray-200 px-3 py-2 outline-none focus:border-teal-400 bg-white hover:border-teal-400 transition-colors">
                <option value="">All (Affixed)</option>
                <option value="1">Affixed</option>
                <option value="0">Not Affixed</option>
            </select>

            {{-- Print Selected --}}
            <button
                wire:click="printSelected"
                class="flex items-center gap-2 px-4 py-2 bg-[#4fd1c5] hover:bg-teal-500 text-white rounded-lg text-xs font-bold transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                :disabled="$wire.selectedAssets.length === 0"
                title="Print selected QR codes"
            >
                <i class="fa-solid fa-print"></i>
                Print Selected
            </button>

            {{-- Back --}}
            <a
                href="/assetmanagement"
                class="flex items-center gap-2 px-4 py-2 border border-gray-200 rounded-lg text-xs font-bold text-gray-600 hover:bg-gray-50 transition-colors"
            >
                <i class="fa-solid fa-arrow-left"></i>
                Back
            </a>

        </div>
    </div>

    {{-- ── Table ── --}}
    <div class="table-container flex-1 flex flex-col min-h-0">
        <div class="flex-1 overflow-y-auto overflow-x-auto minimal-scroll">
            <table class="h-full">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" wire:model.live="selectAll">
                        </th>
                        <th>QR Code</th>
                        <th>Reference ID</th>
                        <th>Category</th>
                        <th>Brand</th>
                        <th>Model</th>
                        <th>Farm</th>
                        <th>Assigned To</th>
                        <th>Printed</th>
                        <th>Affixed</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $asset)
                    <tr>
                        <td>
                            <input type="checkbox" wire:model.live="selectedAssets" value="{{ $asset->id }}">
                        </td>
                        <td>
                            @if($asset->qr_code)
                                <img src="{{ asset('storage/' . $asset->qr_code) }}" class="w-10 h-10 rounded" alt="QR Code">
                            @else
                                <span class="text-gray-400 text-xs">No QR</span>
                            @endif
                        </td>
                        <td class="font-mono text-xs text-gray-500">{{ $asset->ref_id }}</td>
                        <td>
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('img/' . $categoryCodeImage[$asset->category]->icon . '.png') }}" class="w-5 h-5 object-contain" alt="">
                                <span class="text-sm font-semibold">{{ $categoryCodeImage[$asset->category]->name ?? '—' }}</span>
                            </div>
                        </td>
                        <td class="text-sm">{{ $asset->brand }}</td>
                        <td class="text-sm">{{ $asset->model }}</td>
                        <td class="text-sm text-gray-600">{{ $asset->farm ?? '—' }}</td>
                        <td class="text-sm text-gray-600">{{ $asset->assigned_name ?? '—' }}</td>
                        <td>
                            <button
                                wire:click="togglePrinted({{ $asset->id }})"
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold transition-colors
                                    {{ $asset->qr_printed
                                        ? 'bg-green-100 text-green-700 hover:bg-green-200'
                                        : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}"
                            >
                                {{ $asset->qr_printed ? 'Printed' : 'Not Printed' }}
                            </button>
                        </td>
                        <td>
                            <button
                                wire:click="toggleAffixed({{ $asset->id }})"
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold transition-colors
                                    {{ $asset->qr_affixed
                                        ? 'bg-blue-100 text-blue-700 hover:bg-blue-200'
                                        : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}"
                            >
                                {{ $asset->qr_affixed ? 'Affixed' : 'Not Affixed' }}
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr class="h-full">
                        <td colspan="10" class="text-center">
                            <div class="flex flex-col items-center justify-center gap-3 text-gray-400 py-24">
                                <i class="fa-solid fa-qrcode text-4xl"></i>
                                <p class="text-sm font-semibold">No assets found</p>
                                <p class="text-xs">Try adjusting your search or filters</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination :paginator="$assets" />
    </div>

</div>{{-- end card --}}

</div>{{-- end Livewire root --}}