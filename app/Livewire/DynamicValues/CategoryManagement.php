<?php

namespace App\Livewire\DynamicValues;

use Livewire\Component;
use App\Models\Category;
use Exception;

class CategoryManagement extends Component
{
    public $categories = [];
    public $icons = [
        // Electronics & Devices
        'desktop', 'speaker', 'projector', 'microscope',

        // Furniture & Home
        'furniture', 'appliances', 'kitchen', 'broom', 'vehicle',

        // Tools & Equipment
        'tools', 'hammer', 'crane',

        // Vehicles
        'folder'
    ];

    public $newName = '';
    public $newIcon = 'folder';
    public $newCode = '';

    public $editId = null;
    public $editName = '';
    public $editIcon = '';
    public $editCode = '';

    public function mount()
    {
        $this->load();
    }

    public function load()
    {
        try {
            $this->categories = Category::withCount('subcategories')->orderBy('name')->get();
        } catch (Exception $e) {
            $this->noreloadNotif('failed', 'Load Error', 'Failed to load categories: ' . $e->getMessage());
        }
    }

    public function updatedNewName($value)
    {
        $this->newCode = $this->generateCode($value);
    }

    public function updatedEditName($value)
    {
        $this->editCode = $this->generateCode($value);
    }

    private function generateCode($name)
    {
        return strtolower(str_replace(' ', '', $name));
    }

    public function add()
    {
        try {
            if (!$this->newName) {
                $this->noreloadNotif('failed', 'Validation Error', 'Category name is required.');
                return;
            }

            // Check if code already exists
            $exists = Category::where('code', $this->newCode)->exists();
            if ($exists) {
                $this->noreloadNotif('failed', 'Duplicate Code', 'A category with this code already exists.');
                return;
            }

            Category::create([
                'name' => $this->newName,
                'icon' => $this->newIcon,
                'code' => $this->newCode,
            ]);

            $this->newName = '';
            $this->newIcon = 'folder';
            $this->newCode = '';

            $this->load();
            $this->dispatch('subCategoryRefresh');
            
            $this->noreloadNotif('success', 'Category Added', 'Category has been successfully created.');
        } catch (Exception $e) {
            $this->noreloadNotif('failed', 'Add Error', 'Failed to add category: ' . $e->getMessage());
        }
    }

    public function startEdit($id)
    {
        try {
            $cat = Category::find($id);
            
            if (!$cat) {
                $this->noreloadNotif('failed', 'Not Found', 'Category not found.');
                return;
            }
            
            $this->editId = $id;
            $this->editName = $cat->name;
            $this->editIcon = $cat->icon;
            $this->editCode = $cat->code;
        } catch (Exception $e) {
            $this->noreloadNotif('failed', 'Edit Error', 'Failed to load category: ' . $e->getMessage());
        }
    }

    public function saveEdit()
    {
        try {
            if (!$this->editName) {
                $this->noreloadNotif('failed', 'Validation Error', 'Category name is required.');
                return;
            }

            // Check if code already exists (excluding current category)
            $exists = Category::where('code', $this->editCode)
                ->where('id', '!=', $this->editId)
                ->exists();
            
            if ($exists) {
                $this->noreloadNotif('failed', 'Duplicate Code', 'A category with this code already exists.');
                return;
            }

            Category::where('id', $this->editId)->update([
                'name' => $this->editName,
                'icon' => $this->editIcon,
                'code' => $this->editCode,
            ]);

            $this->editId = null;
            $this->editName = '';
            $this->editIcon = '';
            $this->editCode = '';

            $this->load();
            $this->dispatch('subCategoryRefresh');
            
            $this->noreloadNotif('success', 'Category Updated', 'Category has been successfully updated.');
        } catch (Exception $e) {
            $this->noreloadNotif('failed', 'Update Error', 'Failed to update category: ' . $e->getMessage());
        }
    }

    public function cancelEdit()
    {
        $this->editId = null;
        $this->editName = '';
        $this->editIcon = '';
        $this->editCode = '';
    }

    public function delete($id)
    {
        try {
            $category = Category::find($id);
            
            if (!$category) {
                $this->noreloadNotif('failed', 'Not Found', 'Category not found.');
                return;
            }
            
            // Check if any assets are using this category code
            $assetCount = \App\Models\Asset::where('category', $category->code)->count();
            
            if ($assetCount > 0) {
                $this->noreloadNotif('failed', 'Cannot Delete', "Cannot delete '{$category->name}'. It is being used by {$assetCount} asset(s).");
                return;
            }
            
            $category->delete();
            $this->load();
            $this->dispatch('subCategoryRefresh');
            
            $this->noreloadNotif('success', 'Category Deleted', 'Category has been successfully deleted.');
        } catch (Exception $e) {
            $this->noreloadNotif('failed', 'Delete Error', 'Failed to delete category: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.dynamic-values.category-management');
    }

    private function noreloadNotif($type, $header, $message)
    {
        $this->dispatch('notif', type: $type, header: $header, message: $message);
    }

    private function reloadNotif($type, $header, $message)
    {
        session()->flash('notif', [
            'type' => $type,
            'header' => $header,
            'message' => $message
        ]);
    }
}