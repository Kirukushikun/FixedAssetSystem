<div class="card flex-1 flex flex-col gap-4">
    <h1 class="text-lg font-bold flex justify-between">{{ $fieldName }}</h1>
    
    {{-- Add New --}}
    <div class="flex items-center justify-between gap-3">
        <input 
            type="text" 
            wire:model="newValue"
            class="w-full py-1 px-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-400"
            placeholder="Add {{ strtolower($fieldName) }}..."
        >
        <button 
            wire:click="add" 
            class="text-indigo-500 hover:scale-105"
        >
            Add
        </button>
    </div>

    <hr>

    {{-- List --}}
    <div class="flex flex-col gap-4 overflow-y-auto minimal-scroll">
        @foreach($items as $item)
            <div class="flex items-center justify-between gap-3">

                {{-- If Editing --}}
                @if($editId === $item->id)
                    <input 
                        type="text" 
                        wire:model="editValue"
                        class="border rounded w-full p-1 focus:border-indigo-500"
                    >

                    <div class="flex gap-2">
                        <button wire:click="saveEdit" class="text-green-600">Save</button>
                        <button wire:click="cancelEdit" class="text-gray-500">Cancel</button>
                    </div>

                @else
                    <p class="w-full">{{ $item->value }}</p>

                    <div class="flex gap-3">
                        <button 
                            wire:click="startEdit({{ $item->id }})"
                            class="text-indigo-500 hover:scale-105"
                        >
                            Edit
                        </button>

                        <button 
                            wire:click="delete({{ $item->id }})"
                            class="text-red-500 hover:scale-105"
                        >
                            Delete
                        </button>
                    </div>
                @endif

            </div>
        @endforeach        
    </div>

</div>