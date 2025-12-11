<?php

namespace App\Livewire\DynamicValues;

use Livewire\Component;
use App\Models\Category;
use App\Models\SubCategory;
use Exception;

class SubcategoryManagement extends Component
{
    public $categories = [];
    public $subcategories = [];

    // For add new subcategory
    public $newName = '';
    public $newCategoryId = null;
    public $newCategoryType = 'NON-IT'; // default

    // For editing
    public $editId = null;
    public $editName = '';
    public $editCategoryId = null;
    public $editCategoryType = '';

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        try {
            $this->categories = Category::orderBy('name')->get();
            $this->subcategories = SubCategory::with('category')->orderBy('name')->get();
        } catch (Exception $e) {
            $this->noreloadNotif('Failed', 'Load Error', 'Failed to load data: ' . $e->getMessage());
        }
    }

    public function add()
    {
        try {
            if (!$this->newName || !$this->newCategoryId) {
                $this->noreloadNotif('Failed', 'Validation Error', 'Please fill in all required fields.');
                return;
            }

            SubCategory::create([
                'name' => $this->newName,
                'category_id' => $this->newCategoryId,
                'category_type' => $this->newCategoryType,
            ]);

            $this->resetNew();
            $this->loadData();
            
            $this->noreloadNotif('Success', 'Subcategory Added', 'Subcategory has been successfully created.');
        } catch (Exception $e) {
            $this->noreloadNotif('Failed', 'Add Error', 'Failed to add subcategory: ' . $e->getMessage());
        }
    }

    public function resetNew()
    {
        $this->newName = '';
        $this->newCategoryId = null;
        $this->newCategoryType = 'NON-IT';
    }

    public function startEdit($id)
    {
        try {
            $sub = SubCategory::find($id);
            
            if (!$sub) {
                $this->noreloadNotif('Failed', 'Not Found', 'Subcategory not found.');
                return;
            }
            
            $this->editId = $id;
            $this->editName = $sub->name;
            $this->editCategoryId = $sub->category_id;
            $this->editCategoryType = $sub->category_type;
        } catch (Exception $e) {
            $this->noreloadNotif('Failed', 'Edit Error', 'Failed to load subcategory: ' . $e->getMessage());
        }
    }

    public function saveEdit()
    {
        try {
            if (!$this->editName || !$this->editCategoryId) {
                $this->noreloadNotif('Failed', 'Validation Error', 'Please fill in all required fields.');
                return;
            }

            SubCategory::where('id', $this->editId)->update([
                'name' => $this->editName,
                'category_id' => $this->editCategoryId,
                'category_type' => $this->editCategoryType,
            ]);

            $this->resetEdit();
            $this->loadData();
            
            $this->noreloadNotif('Success', 'Subcategory Updated', 'Subcategory has been successfully updated.');
        } catch (Exception $e) {
            $this->noreloadNotif('Failed', 'Update Error', 'Failed to update subcategory: ' . $e->getMessage());
        }
    }

    public function resetEdit()
    {
        $this->editId = null;
        $this->editName = '';
        $this->editCategoryId = null;
        $this->editCategoryType = '';
    }

    public function cancelEdit()
    {
        $this->resetEdit();
    }

    public function delete($id)
    {
        try {
            $subcategory = SubCategory::find($id);
            
            if (!$subcategory) {
                $this->noreloadNotif('Failed', 'Not Found', 'Subcategory not found.');
                return;
            }
            
            $subcategory->delete();
            $this->loadData();
            
            $this->noreloadNotif('Success', 'Subcategory Deleted', 'Subcategory has been successfully deleted.');
        } catch (Exception $e) {
            $this->noreloadNotif('Failed', 'Delete Error', 'Failed to delete subcategory: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.dynamic-values.subcategory-management');
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