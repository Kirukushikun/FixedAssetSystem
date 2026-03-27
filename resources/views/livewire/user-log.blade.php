<div class="h-full flex flex-col gap-4">

    {{-- ── Toolbar ── --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-lg font-bold text-[#2d3748]">User Logs</h1>

        <div class="flex items-center gap-2">

            {{-- Search --}}
            <div class="flex items-center gap-2 border border-gray-200 rounded-lg px-3 py-2 bg-white hover:border-teal-400 transition-colors">
                <i class="fa-solid fa-magnifying-glass text-gray-400 text-sm"></i>
                <input
                    class="outline-none text-sm bg-transparent w-40 placeholder-gray-400"
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search logs..."
                >
            </div>

            {{-- Filter --}}
            <div x-data="{ filterOpen: false }" class="relative">
                <button
                    type="button"
                    title="Filter Logs"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 hover:text-teal-500 transition-colors"
                    :class="filterOpen ? 'bg-gray-100 text-teal-500' : ''"
                    @click="filterOpen = !filterOpen"
                >
                    <i class="fa-solid fa-sliders text-sm"></i>
                </button>

                <div
                    x-show="filterOpen"
                    @click.outside="filterOpen = false"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-1"
                    class="absolute right-0 mt-2 bg-white border border-gray-200 rounded-xl shadow-xl z-50"
                >
                    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                        <h3 class="text-sm font-bold text-gray-800">Filter</h3>
                        <button @click="filterOpen = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>

                    <div class="p-4 space-y-4">

                        <div class="space-y-2">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Status</p>
                            <select wire:model.live="filterSuccess" class="w-full text-sm rounded-lg border border-gray-200 px-2 py-1.5 outline-none focus:border-teal-400">
                                <option value="">All</option>
                                <option value="1">Success</option>
                                <option value="0">Failed</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Date Range</p>
                            <div class="flex items-center gap-2">
                                <input type="date" wire:model.live="filterDateFrom"
                                    class="flex-1 text-sm rounded-lg border border-gray-200 px-2 py-1.5 outline-none focus:border-teal-400">
                                <span class="text-xs text-gray-400">to</span>
                                <input type="date" wire:model.live="filterDateTo"
                                    class="flex-1 text-sm rounded-lg border border-gray-200 px-2 py-1.5 outline-none focus:border-teal-400">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Time Range</p>
                            <div class="flex items-center gap-2">
                                <input type="time" wire:model.live="filterTimeFrom"
                                    class="flex-1 text-sm rounded-lg border border-gray-200 px-2 py-1.5 outline-none focus:border-teal-400">
                                <span class="text-xs text-gray-400">to</span>
                                <input type="time" wire:model.live="filterTimeTo"
                                    class="flex-1 text-sm rounded-lg border border-gray-200 px-2 py-1.5 outline-none focus:border-teal-400">
                            </div>
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

        </div>
    </div>

    {{-- ── Table ── --}}
    <div class="table-container flex-1 flex flex-col min-h-0">
        <div class="flex-1 overflow-y-auto overflow-x-auto minimal-scroll">

            @if($userLogs->isEmpty())
                <div class="flex flex-col items-center justify-center h-full gap-3 text-gray-400 py-24">
                    <i class="fa-solid fa-arrow-right-to-bracket text-4xl"></i>
                    <p class="text-sm font-semibold">No user log records found</p>
                    <p class="text-xs">Try adjusting your search or filters</p>
                </div>
            @else
                <table class="w-full border-collapse">
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>Status</th>
                            <th>IP Address</th>
                            <th>User Agent</th>
                            <th>Date</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($userLogs as $log)
                        <tr>
                            <td class="text-sm font-semibold">
                                {{ $log->email }}
                                <i class="fa-regular fa-copy cursor-pointer text-gray-400 hover:text-teal-500 transition-colors ml-1"></i>
                            </td>
                            <td>
                                @if($log->success)
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-green-600">
                                        <i class="fa-solid fa-circle-check"></i> Success
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-red-500">
                                        <i class="fa-solid fa-circle-xmark"></i> Failed
                                    </span>
                                @endif
                            </td>
                            <td class="font-mono text-xs text-gray-500">{{ $log->ip_address }}</td>
                            <td class="text-sm text-gray-600 max-w-[200px] truncate" title="{{ $log->user_agent }}">{{ $log->user_agent }}</td>
                            <td class="text-sm text-gray-600">{{ $log->created_at->format('d/m/Y') }}</td>
                            <td class="text-sm text-gray-600">{{ $log->created_at->format('h:i A') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

        </div>

        <x-pagination :paginator="$userLogs" />
    </div>

</div>