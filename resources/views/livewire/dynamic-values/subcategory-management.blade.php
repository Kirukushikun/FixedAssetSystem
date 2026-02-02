<div class="card flex-1 flex flex-col gap-4">
    <h1 class="text-lg font-bold">Subcategory Management</h1>

    {{-- Add New Subcategory --}}
    <div class="flex gap-3 items-center">
        <!-- Select -->
        <select wire:model="newCategoryType" class="w-full py-1 px-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-400 w-1/6" title="Select Category Type">
            <option value="NON-IT">NON-IT</option>
            <option value="IT">IT</option>
        </select>
        <!-- Select -->
        <select wire:model="newCategoryId" class="w-full py-1 px-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-400 w-1/3" title="Select Category">
            <option value="">Select Category</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
        </select>
        <!-- Input -->
        <input 
            type="text" 
            wire:model="newName" 
            placeholder="Add subcategory..."
            class="w-full py-1 px-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-400"
            title="Enter Subcategory Name"
        >
        <button wire:click="add" class="text-indigo-600" title="Add Subcategory">Add</button>
    </div>

    <hr>

    {{-- Subcategory List --}}
    <div class="flex flex-col gap-4 overflow-y-auto pr-3 minimal-scroll " style="height: 400px;">
        @forelse($subcategories as $sub)
            <div class="flex items-center justify-between gap-3">

                {{-- Edit Mode --}}
                @if($editId === $sub->id)
                    <input 
                        type="text" 
                        wire:model="editName" 
                        class="border rounded px-2 py-1 w-1/3 focus:border-indigo-600"
                        title="Edit Subcategory Name"
                    >

                    <select wire:model="editCategoryId" class="border rounded px-2 py-1 w-1/3">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>

                    <select wire:model="editCategoryType" class="border rounded px-2 py-1 w-1/6">
                        <option value="IT">IT</option>
                        <option value="NON-IT">NON-IT</option>
                    </select>

                    <div class="flex gap-2">
                        <button wire:click="saveEdit" class="text-green-600">Save</button>
                        <button wire:click="cancelEdit" class="text-gray-500">Cancel</button>
                    </div>

                @else
                    <div class="flex gap-3 w-2/3">
                        <p>{{ $sub->name }}</p>
                        <span class="italic text-sm text-gray-500">({{ $sub->category->name ?? 'No category' }})</span>
                    </div>

                    <div class="w-1/6 text-center">
                        <span 
                            class="px-2 py-0.5 rounded text-white text-xs"
                            style="background-color: {{ $sub->category_type === 'IT' ? '#4F46E5' : '#D14343' }}"
                        >
                            {{ $sub->category_type }}
                        </span>
                    </div>

                    <div class="flex gap-3">
                        <button wire:click="startEdit({{ $sub->id }})" class="text-indigo-600" title="Edit Subcategory">Edit</button>
                        <button wire:click="delete({{ $sub->id }})" class="text-red-500" title="Delete Subcategory">Delete</button>
                    </div>
                @endif
            </div>
        @empty
            <div class="flex-1 flex items-center justify-center">
                <p class="text-gray-500 italic">No subcategories found.</p>
            </div>
        @endforelse
    </div>
</div>
