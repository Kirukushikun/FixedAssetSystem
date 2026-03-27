<div class="h-full flex flex-col gap-4"
     x-data="{
         showRestoreModal: false,
         showDeleteModal: false,
         selectedAssetId: null,
         selectedRefId: ''
     }">

    {{-- ── Toolbar ── --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-lg font-bold text-[#2d3748]">Trash</h1>
    </div>

    {{-- ── Table ── --}}
    <div class="table-container flex-1 flex flex-col min-h-0">
        <div class="flex-1 overflow-y-auto overflow-x-auto minimal-scroll">

            @if($deletedAssets->isEmpty())
                <div class="flex flex-col items-center justify-center h-full gap-3 text-gray-400 py-24">
                    <i class="fa-solid fa-trash text-4xl"></i>
                    <p class="text-sm font-semibold">Trash is empty</p>
                    <p class="text-xs">Deleted assets will appear here</p>
                </div>
            @else
                <table class="w-full border-collapse">
                    <thead>
                        <tr>
                            <th>Ref ID</th>
                            <th>Deleted At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deletedAssets as $item)
                        <tr>
                            <td class="font-mono text-xs text-gray-500">{{ $item->ref_id }}</td>
                            <td class="text-sm text-gray-600">{{ $item->updated_at->format('m/d/Y') }}</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <button
                                        @click="showRestoreModal = true; selectedAssetId = {{ $item->id }}; selectedRefId = '{{ $item->ref_id }}'"
                                        class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-teal-600 border border-teal-200 rounded-lg hover:bg-teal-50 transition-colors"
                                    >
                                        <i class="fa-solid fa-rotate-left text-xs"></i> Restore
                                    </button>
                                    <button
                                        @click="showDeleteModal = true; selectedAssetId = {{ $item->id }}; selectedRefId = '{{ $item->ref_id }}'"
                                        class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-red-500 border border-red-200 rounded-lg hover:bg-red-50 transition-colors"
                                    >
                                        <i class="fa-solid fa-trash text-xs"></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

        </div>

        <x-pagination :paginator="$deletedAssets" />
    </div>

    {{-- ── Restore Modal ── --}}
    <div x-show="showRestoreModal" x-transition.opacity class="fixed inset-0 bg-black/40 z-[70]"></div>
    <div
        x-show="showRestoreModal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 z-[80] flex items-center justify-center px-4"
    >
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg p-8">
            <button
                class="absolute right-5 top-5 w-7 h-7 flex items-center justify-center rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-700 transition-colors"
                @click="showRestoreModal = false"
            >
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>

            <div class="flex flex-col items-center gap-4 text-center">
                <div class="w-12 h-12 rounded-full bg-teal-100 flex items-center justify-center">
                    <i class="fa-solid fa-rotate-left text-teal-500"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800 mb-1">Restore Asset</h2>
                    <p class="text-sm text-gray-500">Are you sure you want to restore asset <strong class="text-gray-800" x-text="selectedRefId"></strong>?</p>
                </div>
                <div class="flex gap-3 w-full mt-2">
                    <button
                        @click="showRestoreModal = false"
                        class="flex-1 px-4 py-2 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors"
                    >
                        Cancel
                    </button>
                    <button
                        @click="$wire.restoreAsset(selectedAssetId); showRestoreModal = false"
                        class="flex-1 px-4 py-2 bg-teal-500 text-white rounded-xl text-sm font-semibold hover:bg-teal-600 transition-colors"
                    >
                        Restore
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Permanent Delete Modal ── --}}
    <div x-show="showDeleteModal" x-transition.opacity class="fixed inset-0 bg-black/40 z-[70]"></div>
    <div
        x-show="showDeleteModal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 z-[80] flex items-center justify-center px-4"
    >
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg p-8">
            <button
                class="absolute right-5 top-5 w-7 h-7 flex items-center justify-center rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-700 transition-colors"
                @click="showDeleteModal = false"
            >
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>

            <div class="flex flex-col items-center gap-4 text-center">
                <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                    <i class="fa-solid fa-trash text-red-500"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800 mb-1">Permanent Delete</h2>
                    <p class="text-sm text-gray-500">Are you sure you want to <span class="text-red-500 font-semibold">permanently delete</span> asset <strong class="text-gray-800" x-text="selectedRefId"></strong>? This action cannot be undone.</p>
                </div>
                <div class="flex gap-3 w-full mt-2">
                    <button
                        @click="showDeleteModal = false"
                        class="flex-1 px-4 py-2 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors"
                    >
                        Cancel
                    </button>
                    <button
                        @click="$wire.permanentDelete(selectedAssetId); showDeleteModal = false"
                        class="flex-1 px-4 py-2 bg-red-500 text-white rounded-xl text-sm font-semibold hover:bg-red-600 transition-colors"
                    >
                        Delete Permanently
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>