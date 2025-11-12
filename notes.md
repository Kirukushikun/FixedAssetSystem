# 🧩 Starter Kit – Modal Confirmation with Livewire Basic CRUD


## Overview
This modal system combines Alpine.js flexibility and Livewire backend interaction.
It allows you to handle multiple modal types — Create, Edit, and Delete — in a single reusable container, making it ideal for CRUD operations.

## 1. Static Multi-Purpose Modal (No Livewire)
A front-end–only version to visualize how the modal system works before backend integration.

```html
<!-- Static Multi-Purpose Modal -->
<div x-data="{ showModal: false, modalTemplate: '' }" class="relative">

    <!-- Example Buttons -->
    <div class="flex gap-3">
        <button @click="showModal = true; modalTemplate = 'create'" class="px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-800">Create</button>
        <button @click="showModal = true; modalTemplate = 'delete'" class="px-4 py-2 bg-red-700 text-white rounded hover:bg-red-800">Delete</button>
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

            <!-- Create Modal -->
            <div class="flex flex-col gap-5" x-show="modalTemplate === 'create'">
                <h2 class="text-xl font-semibold -mb-2">Create Modal</h2>

                <div>
                    <label>Input Field 1</label>
                    <input type="text" class="border rounded w-full p-1" />
                </div>

                <div>
                    <label>Input Field 2</label>
                    <input type="text" class="border rounded w-full p-1" />
                </div>

                <div class="flex justify-end gap-3">
                    <button @click="showModal = false" class="px-4 py-2 border rounded hover:bg-gray-100">Cancel</button>
                    <button @click="showModal = false" class="px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-800">Confirm</button>
                </div>
            </div>

            <!-- Delete Confirmation -->
            <div class="flex flex-col gap-5" x-show="modalTemplate === 'delete'">
                <h2 class="text-xl font-semibold -mb-2">Delete Modal</h2>
                <p>Are you sure you want to delete this item?</p>

                <div class="flex justify-end gap-3">
                    <button @click="showModal = false" class="px-4 py-2 border rounded hover:bg-gray-100">Cancel</button>
                    <button @click="showModal = false" class="px-4 py-2 bg-red-700 text-white rounded hover:bg-red-800">Confirm</button>
                </div>
            </div>
        </div>
    </div>
</div>
```

### 💡 How It Works
- modalTemplate switches content dynamically between modal types.
- Single modal container keeps your markup minimal.
- Great for scaling toward more complex actions (e.g. Livewire calls).

## 2. Livewire CRUD Integration
Here’s the real-world version with Livewire handling **Create**, **Edit**, and **Delete** actions.
We’ll keep the fields minimal: firstname, lastname, and age.

### 🔹 Livewire Component
```php
<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Employee;

class EmployeeCrud extends Component
{
    public $target;
    public $firstname, $lastname, $age;

    protected $rules = [
        'firstname' => 'required',
        'lastname' => 'required',
        'age' => 'required|numeric|min:1',
    ];

    // Load employee data into form inputs
    public function targetID($id)
    {
        $this->target = $id;
        $employee = Employee::find($id);

        $this->firstname = $employee->firstname;
        $this->lastname  = $employee->lastname;
        $this->age       = $employee->age;
    }

    // Create new employee
    public function submit()
    {
        $this->validate();

        Employee::create([
            'firstname' => $this->firstname,
            'lastname'  => $this->lastname,
            'age'       => $this->age,
        ]);

        $this->clear();
    }

    // Update existing employee
    public function update()
    {
        $employee = Employee::find($this->target);

        $employee->update([
            'firstname' => $this->firstname,
            'lastname'  => $this->lastname,
            'age'       => $this->age,
        ]);

        $this->clear();
    }

    // Delete employee
    public function delete()
    {
        Employee::find($this->target)?->delete();
        $this->clear();
    }

    // Reset form fields
    public function clear()
    {
        $this->reset(['target', 'firstname', 'lastname', 'age']);
    }

    // Render view with employee list
    public function render()
    {
        return view('livewire.employee-crud', [
            'employees' => Employee::latest()->get(),
        ]);
    }
}
```

### 🔹 Livewire View
```html
<div class="card content flex-1 flex flex-col" x-data="{ showModal: false, modalTemplate: '' }">

    <!-- Action Buttons -->
    <div class="flex gap-3 mb-4">
        <button @click="showModal = true; modalTemplate = 'create'" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Add New</button>
        <button @click="modalTemplate='edit'; showModal=true; $wire.targetID(1)" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Edit</button>
        <button @click="modalTemplate='delete'; showModal=true; $wire.targetID(1)" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Delete</button>
    </div>

    <!-- Backdrop -->
    <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-black/30 z-40"></div>

    <!-- Modal -->
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

            <!-- Create / Edit Modal -->
            <template x-if="modalTemplate === 'create' || modalTemplate === 'edit'">
                <div class="flex flex-col gap-5">
                    <h2 class="text-xl font-semibold" x-text="modalTemplate === 'create' ? 'Create Employee' : 'Edit Employee'"></h2>

                    <div>
                        <label>First Name</label>
                        <input type="text" wire:model="firstname" class="border rounded w-full p-1" />
                    </div>

                    <div>
                        <label>Last Name</label>
                        <input type="text" wire:model="lastname" class="border rounded w-full p-1" />
                    </div>

                    <div>
                        <label>Age</label>
                        <input type="number" wire:model="age" class="border rounded w-full p-1" />
                    </div>

                    <div class="flex justify-end gap-3">
                        <button @click="showModal = false; $wire.clear()" class="px-4 py-2 border rounded hover:bg-gray-100">Cancel</button>
                        <button
                            @click="showModal = false; modalTemplate === 'create' ? $wire.submit() : $wire.update();"
                            class="px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-800"
                        >
                            <span x-text="modalTemplate === 'create' ? 'Confirm' : 'Update'"></span>
                        </button>
                    </div>
                </div>
            </template>

            <!-- Delete Confirmation -->
            <div class="flex flex-col gap-5" x-show="modalTemplate === 'delete'">
                <h2 class="text-xl font-semibold -mb-2">Delete Employee</h2>
                <p>Are you sure you want to delete this employee?</p>

                <div class="flex justify-end gap-3">
                    <button @click="showModal = false" class="px-4 py-2 border rounded hover:bg-gray-100">Cancel</button>
                    <button @click="showModal = false; $wire.delete()" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Confirm</button>
                </div>
            </div>
        </div>
    </div>
</div>
```
### 💡 How It Works
- Public properties (`$target`, `$firstname`, `$lastname`, `$age`) are automatically synced between component and view via `wire:model`
- **targetID()** - Loads an employee's data into the form fields when Edit/Delete button is clicked
- **submit()** - Validates input and creates a new employee record in the database
- **update()** - Finds the targeted employee and updates their information
- **delete()** - Removes the targeted employee from the database
- **clear()** - Resets all form fields back to empty state
- **render()** - Fetches all employees (newest first) and passes them to the view for display
- **Alpine.js in view** handles modal visibility and switching between create/edit/delete templates
- **$wire** magic property allows Alpine to call Livewire methods directly from the frontend