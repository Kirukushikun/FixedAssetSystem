<div class="card content flex-1 flex flex-col">
    <div class="table-header flex justify-between items-center">
        <h1 class="text-lg font-bold">QR Code Management</h1>
        <div class="flex items-center gap-3">

            <!-- Search -->
            <div class="border border-2 px-3 py-1 rounded-md border-gray-300">
                <input class="outline-none text-sm" type="text" wire:model.live="search" placeholder="Search asset...">
                <i class="fa-solid fa-magnifying-glass text-sm"></i>
            </div>

            <!-- Farm Filter -->
            <select wire:model.live="filterFarm" class="text-sm border border-gray-300 rounded-md px-3 py-1">
                <option value="">All Farms</option>
                <option value="BFC">BFC</option>
                <option value="BDL">BDL</option>
                <option value="PFC">PFC</option>
                <option value="RH">RH</option>
                <option value="BBGC">BBGC</option>
                <option value="Hatchery">Hatchery</option>
            </select>

            <!-- Printed Filter -->
            <select wire:model.live="filterPrinted" class="text-sm border border-gray-300 rounded-md px-3 py-1">
                <option value="">All (Printed)</option>
                <option value="1">Printed</option>
                <option value="0">Not Printed</option>
            </select>

            <!-- Affixed Filter -->
            <select wire:model.live="filterAffixed" class="text-sm border border-gray-300 rounded-md px-3 py-1">
                <option value="">All (Affixed)</option>
                <option value="1">Affixed</option>
                <option value="0">Not Affixed</option>
            </select>

            <!-- Print Selected Button -->
            <button 
                wire:click="printSelected"
                class="px-5 py-2 bg-[#4fd1c5] rounded-lg font-bold text-white text-xs hover:bg-teal-500 disabled:opacity-50"
                :disabled="$wire.selectedAssets.length === 0"
                title="Print selected QR codes"
            >
                <i class="fa-solid fa-print mr-2"></i>PRINT SELECTED
            </button>

            <!-- Back -->
            <a href="/assetmanagement" class="px-5 py-2 border-2 border-gray-300 rounded-lg font-bold text-gray-600 text-xs hover:bg-gray-100">
                BACK
            </a>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>
                        <input type="checkbox" wire:model.live="selectAll">
                    </th>
                    <th>QR CODE</th>
                    <th>REFERENCE ID</th>
                    <th>CATEGORY</th>
                    <th>BRAND</th>
                    <th>MODEL</th>
                    <th>FARM</th>
                    <th>ASSIGNED TO</th>
                    <th>PRINTED</th>
                    <th>AFFIXED</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assets as $asset)
                <tr>
                    <td>
                        <input type="checkbox" wire:model.live="selectedAssets" value="{{ $asset->id }}">
                    </td>
                    <td>
                        @if($asset->qr_code)
                            <img src="{{ asset('storage/' . $asset->qr_code) }}" class="w-10 h-10">
                        @else
                            <span class="text-gray-400 text-xs">No QR</span>
                        @endif
                    </td>
                    <td class="font-bold">{{ $asset->ref_id }}</td>
                    <td>{{ $categoryCodeImage[$asset->category]->name ?? '—' }}</td>
                    <td>{{ $asset->brand }}</td>
                    <td>{{ $asset->model }}</td>
                    <td>{{ $asset->farm ?? '—' }}</td>
                    <td>{{ $asset->assigned_name ?? '—' }}</td>
                    <td>
                        <button
                            wire:click="togglePrinted({{ $asset->id }})"
                            class="px-3 py-1 rounded-full text-xs font-bold transition
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
                            class="px-3 py-1 rounded-full text-xs font-bold transition
                                {{ $asset->qr_affixed 
                                    ? 'bg-blue-100 text-blue-700 hover:bg-blue-200' 
                                    : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}"
                        >
                            {{ $asset->qr_affixed ? 'Affixed' : 'Not Affixed' }}
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <x-pagination :paginator="$assets" />
</div>