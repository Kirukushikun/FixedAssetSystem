<div class="card w-full flex flex-col gap-5">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-lg font-bold text-gray-800">Subcategory Management</h1>
            <p class="text-xs text-gray-400 mt-0.5">Manage subcategories across all asset categories</p>
        </div>
        <span class="text-xs font-semibold bg-teal-50 text-teal-600 px-3 py-1 rounded-full border border-teal-200">
            {{ count($subcategories) }} entries
        </span>
    </div>

    <!-- Add New Subcategory -->
    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 flex flex-col gap-3">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Add New Subcategory</p>
        <div class="flex gap-3 items-center">
            <select wire:model="newCategoryType" 
                class="py-2 px-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 w-1/3 bg-white">
                <option value="NON-IT">NON-IT</option>
                <option value="IT">IT</option>
            </select>

            <select wire:model="newCategoryId" 
                class="py-2 px-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 w-1/3 bg-white">
                <option value="">Select Category</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>

            <input 
                type="text" 
                wire:model="newName" 
                placeholder="Subcategory name..."
                class="flex-1 py-2 px-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 bg-white w-2/5"
            >

            <button wire:click="add" 
                class="px-4 py-2 bg-teal-500 hover:bg-teal-600 text-white text-sm font-semibold rounded-lg transition whitespace-nowrap">
                <i class="fa-solid fa-plus mr-1"></i> Add
            </button>
        </div>
    </div>

    <!-- Table Header -->
    <div class="flex text-xs font-semibold text-gray-400 uppercase tracking-wide border-b border-gray-200 pb-2 px-3">
        <span class="w-48">Subcategory</span>
        <span class="flex-1">Category</span>
        <span class="w-48 text-center">Type</span>
        <span class="w-20 text-right">Actions</span>
    </div>

    <!-- Subcategory List -->
    <div class="flex flex-col gap-1 overflow-y-auto pr-1" style="height: 300px;">
        @forelse($subcategories as $sub)
            <div class="flex items-center px-3 py-2.5 rounded-lg hover:bg-gray-50 border border-transparent hover:border-gray-200 transition group">

                @if($editId === $sub->id)
                    <div class="flex items-center gap-2 w-full">
                        <input type="text" wire:model="editName"
                            class="flex-1 border border-teal-400 rounded-lg px-2 py-1 text-sm focus:outline-none">
                        <select wire:model="editCategoryId"
                            class="border border-gray-300 rounded-lg px-2 py-1 text-sm focus:outline-none w-36">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <select wire:model="editCategoryType"
                            class="border border-gray-300 rounded-lg px-2 py-1 text-sm focus:outline-none w-24">
                            <option value="IT">IT</option>
                            <option value="NON-IT">NON-IT</option>
                        </select>
                        <button wire:click="saveEdit"
                            class="px-3 py-1 bg-teal-500 hover:bg-teal-600 text-white text-xs font-semibold rounded-lg transition whitespace-nowrap">Save</button>
                        <button wire:click="cancelEdit"
                            class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-semibold rounded-lg transition whitespace-nowrap">Cancel</button>
                    </div>

                @else
                    <span class="w-48 text-sm font-semibold text-gray-700">{{ $sub->name }}</span>
                    <span class="flex-1 text-sm text-gray-500">{{ $sub->category->name ?? '—' }}</span>
                    <span class="w-48 text-center">
                        <span class="px-2 py-0.5 rounded-full text-white text-xs font-semibold"
                            style="{{ $sub->category_type === 'IT' ? 'background-color: #6486f1;' : 'background-color: #fb923c;' }}">
                            {{ $sub->category_type }}
                        </span>
                    </span>
                    <div class="w-20 flex gap-1 justify-end">
                        <button wire:click="startEdit({{ $sub->id }})"
                            class="px-2 py-2 text-xs font-semibold text-indigo-600 hover:bg-indigo-50 rounded-lg transition">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button wire:click="delete({{ $sub->id }})"
                            class="px-2 py-2 text-xs font-semibold text-red-500 hover:bg-red-50 rounded-lg transition">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                @endif

            </div>
        @empty
            <div class="flex-1 flex flex-col items-center justify-center text-center gap-2 py-16">
                <i class="fa-solid fa-folder-open text-gray-300 text-4xl"></i>
                <p class="text-gray-400 text-sm">No subcategories found.</p>
                <p class="text-gray-300 text-xs">Add one above to get started.</p>
            </div>
        @endforelse
    </div>
</div>