<div class="card w-full flex flex-col gap-5" x-data="{ showModal: false, modalTemplate: '' }">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-lg font-bold text-gray-800">Categories</h1>
            <p class="text-xs text-gray-400 mt-0.5">Manage asset categories and icons</p>
        </div>
        <span class="text-xs font-semibold bg-teal-50 text-teal-600 px-3 py-1 rounded-full border border-teal-200">
            {{ count($categories) }} entries
        </span>
    </div>

    <!-- Add New -->
    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 flex gap-3 items-center">
        <div  
            @click="modalTemplate = 'icon'; showModal = true"
            class="p-2 border rounded-lg cursor-pointer hover:bg-gray-100 transition flex-shrink-0
                {{ $newIcon ? 'bg-indigo-50 border-indigo-400' : 'border-gray-300 bg-white' }}"
            title="Select Icon"
        >
            <img src="{{ asset('img/' . $newIcon . '.png') }}" class="w-6 h-6 object-contain">
        </div>
        <input 
            type="text" 
            wire:model="newName"
            class="flex-1 py-2 px-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 bg-white"
            placeholder="Add category..."
        >
        <button wire:click="add" 
            class="px-4 py-2 bg-teal-500 hover:bg-teal-600 text-white text-sm font-semibold rounded-lg transition whitespace-nowrap">
            <i class="fa-solid fa-plus mr-1"></i> Add
        </button>
    </div>

    <!-- List -->
    <div class="flex flex-col gap-1 overflow-y-auto pr-1" style="height: 450px;">
        @forelse($categories as $cat)
            <div class="flex items-center px-3 py-2.5 rounded-lg hover:bg-gray-50 border border-transparent hover:border-gray-200 transition group">

                @if($editId === $cat->id)
                    <div class="flex flex-col w-full gap-3">
                        <div class="flex items-center gap-3">
                            <input type="text" wire:model="editName"
                                class="flex-1 border border-teal-400 rounded-lg px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">
                            <div class="flex gap-2">
                                <button wire:click="saveEdit" 
                                    class="px-3 py-1 bg-teal-500 hover:bg-teal-600 text-white text-xs font-semibold rounded-lg transition">Save</button>
                                <button wire:click="cancelEdit" 
                                    class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-semibold rounded-lg transition">Cancel</button>
                            </div>
                        </div>
                        <!-- Icon Picker -->
                        <div class="grid grid-cols-8 gap-2">
                            @foreach($icons as $icon)
                                <div 
                                    wire:click="$set('editIcon', '{{ $icon }}')"  
                                    class="p-2 border rounded-lg cursor-pointer hover:bg-gray-100 transition
                                        {{ $editIcon === $icon ? 'bg-indigo-50 border-indigo-400' : 'border-gray-200' }}"
                                >
                                    <img src="{{ asset('img/' . $icon . '.png') }}" class="w-5 h-5 mx-auto object-contain">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-3 flex-1">
                        <div class="w-8 h-8 flex items-center justify-center bg-gray-100 rounded-lg flex-shrink-0">
                            <img src="{{ asset('img/' . $cat->icon . '.png') }}" class="w-5 h-5 object-contain">
                        </div>
                        <span class="text-sm font-semibold text-gray-700">{{ $cat->name }}</span>
                    </div>
                    <div class="flex gap-1">
                        <button wire:click="startEdit({{ $cat->id }})" 
                            class="px-2 py-1 text-xs font-semibold text-indigo-600 hover:bg-indigo-50 rounded-lg transition">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button wire:click="delete({{ $cat->id }})" 
                            class="px-2 py-1 text-xs font-semibold text-red-500 hover:bg-red-50 rounded-lg transition">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                @endif

            </div>
        @empty
            <div class="flex-1 flex flex-col items-center justify-center text-center gap-2 py-16">
                <i class="fa-solid fa-folder-open text-gray-300 text-4xl"></i>
                <p class="text-gray-400 text-sm">No categories found.</p>
                <p class="text-gray-300 text-xs">Add one above to get started.</p>
            </div>
        @endforelse
    </div>

    <!-- Backdrop -->
    <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-black/30 z-40"></div>

    <!-- Icon Picker Modal -->
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
        <div class="relative bg-white p-8 rounded-xl shadow-lg w-[26rem]">
            <button class="absolute right-5 top-5 text-gray-400 hover:text-gray-800" @click="showModal = false">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <h2 class="text-lg font-bold text-gray-800 mb-1">Select Icon</h2>
            <p class="text-xs text-gray-400 mb-4">Choose an icon for this category</p>
            <div class="grid grid-cols-6 gap-3">
                @foreach($icons as $icon)
                    <div 
                        wire:click="$set('newIcon', '{{ $icon }}')"
                        @click="showModal = false"
                        class="p-3 border rounded-xl cursor-pointer hover:bg-gray-50 transition
                            {{ $newIcon === $icon ? 'bg-indigo-50 border-indigo-400' : 'border-gray-200' }}"
                    >
                        <img src="{{ asset('img/' . $icon . '.png') }}" class="w-8 h-8 mx-auto object-contain">
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>