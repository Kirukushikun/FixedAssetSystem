<div class="card flex-1 flex flex-col gap-5">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-lg font-bold text-gray-800">{{ $fieldName }}</h1>
            <p class="text-xs text-gray-400 mt-0.5">Manage {{ strtolower($fieldName) }} options</p>
        </div>
        <span class="text-xs font-semibold bg-teal-50 text-teal-600 px-3 py-1 rounded-full border border-teal-200">
            {{ count($items) }} entries
        </span>
    </div>

    <!-- Add New -->
    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 flex gap-3">
        <input 
            type="text" 
            wire:model="newValue"
            class="flex-1 py-2 px-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 bg-white"
            placeholder="Add {{ strtolower($fieldName) }}..."
        >
        <button wire:click="add" 
            class="px-4 py-2 bg-teal-500 hover:bg-teal-600 text-white text-sm font-semibold rounded-lg transition whitespace-nowrap">
            <i class="fa-solid fa-plus mr-1"></i> Add
        </button>
    </div>

    <!-- List -->
    <div class="flex flex-col gap-1 overflow-y-auto pr-1" style="height: 360px;">
        @forelse($items as $item)
            <div class="flex items-center px-3 py-2.5 rounded-lg hover:bg-gray-50 border border-transparent hover:border-gray-200 transition group">

                @if($editId === $item->id)
                    <input type="text" wire:model="editValue"
                        class="flex-1 border border-teal-400 rounded-lg px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300 mr-3">
                    <div class="flex gap-2">
                        <button wire:click="saveEdit" 
                            class="px-3 py-1 bg-teal-500 hover:bg-teal-600 text-white text-xs font-semibold rounded-lg transition">Save</button>
                        <button wire:click="cancelEdit" 
                            class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-semibold rounded-lg transition">Cancel</button>
                    </div>
                @else
                    <span class="flex-1 text-sm font-semibold text-gray-700">{{ $item->value }}</span>
                    <div class="flex gap-1">
                        <button wire:click="startEdit({{ $item->id }})" 
                            class="px-2 py-1 text-xs font-semibold text-indigo-600 hover:bg-indigo-50 rounded-lg transition">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button wire:click="delete({{ $item->id }})" 
                            class="px-2 py-1 text-xs font-semibold text-red-500 hover:bg-red-50 rounded-lg transition">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                @endif

            </div>
        @empty
            <div class="flex-1 flex flex-col items-center justify-center text-center gap-2 py-16">
                <i class="fa-solid fa-box-open text-gray-300 text-4xl"></i>
                <p class="text-gray-400 text-sm">No {{ strtolower($fieldName) }} found.</p>
                <p class="text-gray-300 text-xs">Add one above to get started.</p>
            </div>
        @endforelse
    </div>
</div>