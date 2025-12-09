<div class="table-container flex-1 flex flex-col min-h-0 overflow-y-auto flex-1" 
     x-data="{ 
         showRestoreModal: false, 
         showDeleteModal: false, 
         selectedAssetId: null, 
         selectedRefId: '' 
     }">
    <table class="w-full">
        <thead>
            <tr>
                <th>Ref ID</th>
                <th>Deleted At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($deletedAssets as $item)
            <tr>
                <td>{{$item->ref_id}}</td>
                <td>{{$item->updated_at->format('m/d/Y')}}</td>
                <td>
                    <div class="flex gap-2">
                        <button 
                            @click="showRestoreModal = true; selectedAssetId = {{ $item->id }}; selectedRefId = '{{ $item->ref_id }}'"
                            class="bg-white border border-2 text-gray-500 rounded-md text-xs py-2 px-4 hover:bg-gray-500 hover:text-white transition"
                        >
                            RESTORE
                        </button>
                        <button 
                            @click="showDeleteModal = true; selectedAssetId = {{ $item->id }}; selectedRefId = '{{ $item->ref_id }}'"
                            class="bg-white border border-2 border-red-500 text-red-500 rounded-md text-xs py-2 px-4 hover:bg-red-500 hover:text-white transition"
                        >
                            DELETE
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="px-4 py-8 text-center text-gray-500">
                    No deleted assets found
                </td>
            </tr>
            @endforelse            
        </tbody>
    </table>

    <!-- Backdrop for Restore Modal -->
    <div x-show="showRestoreModal" x-transition.opacity class="fixed inset-0 bg-black/30 z-40"></div>

    <!-- Restore Modal Container -->
    <div
        x-show="showRestoreModal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
        class="fixed inset-0 flex items-center justify-center z-50"
    >
        <div class="relative bg-white p-8 rounded-lg shadow-lg w-[26rem]">
            <button class="absolute right-7 top-7 text-gray-400 hover:text-gray-800" @click="showRestoreModal = false">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <div class="flex flex-col gap-5">
                <h2 class="text-xl font-semibold -mb-2">Restore Asset</h2>
                <p>Are you sure you want to restore asset <strong x-text="selectedRefId"></strong>?</p>

                <div class="flex justify-end gap-3">
                    <button @click="showRestoreModal = false" class="px-4 py-2 border rounded hover:bg-gray-100">Cancel</button>
                    <button 
                        @click="$wire.restoreAsset(selectedAssetId); showRestoreModal = false" 
                        class="px-4 py-2 bg-teal-600 text-white rounded hover:bg-teal-700"
                    >
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Backdrop for Delete Modal -->
    <div x-show="showDeleteModal" x-transition.opacity class="fixed inset-0 bg-black/30 z-40"></div>

    <!-- Permanent Delete Modal Container -->
    <div
        x-show="showDeleteModal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
        class="fixed inset-0 flex items-center justify-center z-50"
    >
        <div class="relative bg-white p-8 rounded-lg shadow-lg w-[26rem]">
            <button class="absolute right-7 top-7 text-gray-400 hover:text-gray-800" @click="showDeleteModal = false">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <div class="flex flex-col gap-5">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fa-solid fa-trash text-red-600 text-xl"></i>
                    </div>
                    <h2 class="text-xl font-semibold">Permanent Delete</h2>
                </div>
                <p>Are you sure you want to <strong class="text-red-600">permanently delete</strong> asset <strong x-text="selectedRefId"></strong>? This action cannot be undone.</p>

                <div class="flex justify-end gap-3">
                    <button @click="showDeleteModal = false" class="px-4 py-2 border rounded hover:bg-gray-100">Cancel</button>
                    <button 
                        @click="$wire.permanentDelete(selectedAssetId); showDeleteModal = false" 
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                    >
                        Delete Permanently
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>