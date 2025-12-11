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

    public $editId = null;
    public $editName = '';
    public $editIcon = '';

    public function mount()
    {
        $this->load();
    }

    public function load()
    {
        try {
            $this->categories = Category::orderBy('name')->get();
        } catch (Exception $e) {
            $this->noreloadNotif('Failed', 'Load Error', 'Failed to load categories: ' . $e->getMessage());
        }
    }

    public function add()
    {
        try {
            if (!$this->newName) {
                $this->noreloadNotif('Failed', 'Validation Error', 'Category name is required.');
                return;
            }

            Category::create([
                'name' => $this->newName,
                'icon' => $this->newIcon,
            ]);

            $this->newName = '';
            $this->newIcon = 'folder';

            $this->load();
            
            $this->noreloadNotif('Success', 'Category Added', 'Category has been successfully created.');
        } catch (Exception $e) {
            $this->noreloadNotif('Failed', 'Add Error', 'Failed to add category: ' . $e->getMessage());
        }
    }

    public function startEdit($id)
    {
        try {
            $cat = Category::find($id);
            
            if (!$cat) {
                $this->noreloadNotif('Failed', 'Not Found', 'Category not found.');
                return;
            }
            
            $this->editId = $id;
            $this->editName = $cat->name;
            $this->editIcon = $cat->icon;
        } catch (Exception $e) {
            $this->noreloadNotif('Failed', 'Edit Error', 'Failed to load category: ' . $e->getMessage());
        }
    }

    public function saveEdit()
    {
        try {
            if (!$this->editName) {
                $this->noreloadNotif('Failed', 'Validation Error', 'Category name is required.');
                return;
            }

            Category::where('id', $this->editId)->update([
                'name' => $this->editName,
                'icon' => $this->editIcon,
            ]);

            $this->editId = null;
            $this->editName = '';
            $this->editIcon = '';

            $this->load();
            
            $this->noreloadNotif('Success', 'Category Updated', 'Category has been successfully updated.');
        } catch (Exception $e) {
            $this->noreloadNotif('Failed', 'Update Error', 'Failed to update category: ' . $e->getMessage());
        }
    }

    public function cancelEdit()
    {
        $this->editId = null;
        $this->editName = '';
        $this->editIcon = '';
    }

    public function delete($id)
    {
        try {
            $category = Category::find($id);
            
            if (!$category) {
                $this->noreloadNotif('Failed', 'Not Found', 'Category not found.');
                return;
            }
            
            $category->delete();
            $this->load();
            
            $this->noreloadNotif('Success', 'Category Deleted', 'Category has been successfully deleted.');
        } catch (Exception $e) {
            $this->noreloadNotif('Failed', 'Delete Error', 'Failed to delete category: ' . $e->getMessage());
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