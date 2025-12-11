<div class="card flex-1 flex flex-col gap-4" x-data="{ showModal: false, modalTemplate: '' }">
    <h1 class="text-lg font-bold">Category Management</h1>

    {{-- Add New --}}
    <div class="flex flex-col gap-3">
        <div class="flex items-center gap-3">
            <div  
                @click="modalTemplate = 'icon'; showModal = true"
                class="px-2 py-1 border rounded cursor-pointer hover:bg-gray-100
                    {{ $newIcon ? 'bg-indigo-100 border-indigo-500' : '' }}"
            >
                <img 
                    src="{{ asset('img/' . $newIcon . '.png') }}" 
                    class="w-6 h-6 mx-auto object-contain"
                >
            </div>
            <input 
                type="text" 
                wire:model="newName"
                class="w-full py-1 px-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-400"
                placeholder="Add category..."
            >

            <button 
                wire:click="add" 
                class="text-indigo-600"
            >
                Add
            </button>
        </div>
    </div>

    <hr>

    {{-- List --}}    
    <div class="flex flex-col gap-4 overflow-y-auto pr-3 minimal-scroll">
        @foreach($categories as $cat)
            <div class="flex items-center justify-between gap-3">

                {{-- Editing --}}
                @if($editId === $cat->id)
                    <div class="flex flex-col w-full gap-3">

                        <div class="flex items-center gap-3">
                            <input 
                                type="text" 
                                wire:model="editName"
                                class="border rounded w-full p-1 focus:border-indigo-600"
                            >

                            <button wire:click="saveEdit" class="text-green-600">Save</button>
                            <button wire:click="cancelEdit" class="text-gray-500">Cancel</button>
                        </div>

                        {{-- Icon Picker --}}
                        <div class="grid grid-cols-6 gap-2">
                            @foreach($icons as $icon)
                                <div 
                                    wire:click="$set('editIcon', '{{ $icon }}')"  
                                    class="p-2 border rounded cursor-pointer hover:bg-gray-100
                                        {{ $editIcon === $icon ? 'bg-indigo-100 border-indigo-500' : '' }}"
                                >
                                    <img 
                                        src="{{ asset('img/' . $icon . '.png') }}" 
                                        class="w-6 h-6 mx-auto object-contain"
                                    >
                                </div>
                            @endforeach
                        </div>
                    </div>

                {{-- Not Editing --}}
                @else
                    <div class="flex items-center gap-2 w-full">
                        <img 
                            src="{{ asset('img/' . $cat->icon . '.png') }}" 
                            class="w-6 h-6"
                        >
                        <p>{{ $cat->name }}</p>
                    </div>

                    <div class="flex gap-3">
                        <button wire:click="startEdit({{ $cat->id }})" class="text-indigo-600">Edit</button>
                        <button wire:click="delete({{ $cat->id }})" class="text-red-500">Delete</button>
                    </div>
                @endif

            </div>
        @endforeach
    </div>

    <!-- Backdrop -->
    <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-black/30 z-40"></div>

    <!-- Modal Container -->
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
            <button class="absolute right-7 top-7 text-gray-400 hover:text-gray-800" @click="showModal = false">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <h2 class="text-xl font-semibold mb-4">Select Icon</h2>

            <!-- Select Modal -->
            <div class="grid grid-cols-6 gap-3">
                @foreach($icons as $icon)
                    <div 
                        wire:click="$set('newIcon', '{{ $icon }}')"
                        @click="showModal = false"
                        class="p-2 border rounded cursor-pointer hover:bg-gray-100
                            {{ $newIcon === $icon ? 'bg-indigo-100 border-indigo-500' : '' }}"
                    >
                        <img 
                            src="{{ asset('img/' . $icon . '.png') }}"
                            class="w-8 h-8 mx-auto object-contain"
                        >
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
