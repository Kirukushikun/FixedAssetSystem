<div class="card flex-1 flex flex-col gap-4">
    <h1 class="text-lg font-bold flex justify-between">Division/Department</h1>
    {{-- Add New --}}
    <div class="flex items-center justify-between gap-3">
        <input 
            type="text" 
            wire:model="newDepartment"
            class="w-full py-1 px-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-400"
            placeholder="Add department..."
            title="Enter Department Name"
        >
        <button 
            wire:click="add" 
            class="text-indigo-500 hover:scale-105"
            title="Add New Department"
        >
            Add
        </button>
    </div>

    <hr>

    {{-- List --}}
    <div class="flex flex-col gap-4 overflow-y-auto minimal-scroll">
        @forelse($departments as $dept)
            <div class="flex items-center justify-between gap-3">

                {{-- If Editing --}}
                @if($editId === $dept->id)
                    <input 
                        type="text" 
                        wire:model="editName"
                        class="border rounded w-full p-1 focus:border-indigo-500"
                    >

                    <div class="flex gap-2">
                        <button wire:click="saveEdit" class="text-green-600" title="Save Department">Save</button>
                        <button wire:click="cancelEdit" class="text-gray-500" title="Cancel Editing">Cancel</button>
                    </div>

                @else
                    <p class="w-full">{{ $dept->name }}</p>

                    <div class="flex gap-3">
                        <button 
                            wire:click="startEdit({{ $dept->id }})"
                            class="text-indigo-500 hover:scale-105"
                            title="Edit Department"
                        >
                            Edit
                        </button>

                        <button 
                            wire:click="delete({{ $dept->id }})"
                            class="text-red-500 hover:scale-105"
                            title="Delete Department"
                        >
                            Delete
                        </button>
                    </div>
                @endif

            </div>
        @empty
            <div class="flex-1 flex items-center justify-center">
                <p class="text-gray-500 italic">No departments found.</p>
            </div>
        @endforelse        
    </div>

</div>
