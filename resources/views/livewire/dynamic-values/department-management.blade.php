<div class="card flex-1 flex flex-col gap-4">
    <h1 class="text-lg font-bold flex justify-between">Division/Department</h1>
    {{-- Add New --}}
    <div class="flex items-center justify-between gap-3">
        <input 
            type="text" 
            wire:model="newDepartment"
            class="border rounded w-full py-1 px-2 focus:border-indigo-500"
            placeholder="Add department..."
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
        @foreach($departments as $dept)
            <div class="flex items-center justify-between gap-3">

                {{-- If Editing --}}
                @if($editId === $dept->id)
                    <input 
                        type="text" 
                        wire:model="editName"
                        class="border rounded w-full p-1 focus:border-indigo-500"
                    >

                    <div class="flex gap-2">
                        <button wire:click="saveEdit" class="text-green-600">Save</button>
                        <button wire:click="cancelEdit" class="text-gray-500">Cancel</button>
                    </div>

                @else
                    <p class="w-full">{{ $dept->name }}</p>

                    <div class="flex gap-3">
                        <button 
                            wire:click="startEdit({{ $dept->id }})"
                            class="text-indigo-500 hover:scale-105"
                        >
                            Edit
                        </button>

                        <button 
                            wire:click="delete({{ $dept->id }})"
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
