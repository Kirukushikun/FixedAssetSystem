<div class="card flex-1 flex flex-col gap-4">
    <h1 class="text-lg font-bold">Subcategory Management</h1>

    {{-- Add New Subcategory --}}
    <div class="flex gap-3 items-center">
        <select wire:model="newCategoryType" class="border rounded px-2 py-1 w-1/6">
            <option value="IT">IT</option>
            <option value="NON-IT">NON-IT</option>
        </select>
        <select wire:model="newCategoryId" class="border rounded px-2 py-1 w-1/3">
            <option value="">Select Category</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
        </select>
        <input 
            type="text" 
            wire:model="newName" 
            placeholder="Add subcategory..."
            class="border rounded px-2 py-1 w-1/3 focus:border-indigo-600"
        >
        <button wire:click="add" class="text-indigo-600">Add</button>
    </div>

    <hr>

    {{-- Subcategory List --}}
    <div class="flex flex-col gap-4 overflow-y-auto pr-3 minimal-scroll">
        @foreach($subcategories as $sub)
            <div class="flex items-center justify-between gap-3">

                {{-- Edit Mode --}}
                @if($editId === $sub->id)
                    <input 
                        type="text" 
                        wire:model="editName" 
                        class="border rounded px-2 py-1 w-1/3 focus:border-indigo-600"
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
                        <button wire:click="startEdit({{ $sub->id }})" class="text-indigo-600">Edit</button>
                        <button wire:click="delete({{ $sub->id }})" class="text-red-500">Delete</button>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
