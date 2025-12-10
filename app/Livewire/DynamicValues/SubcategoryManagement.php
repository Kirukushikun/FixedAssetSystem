<?php

namespace App\Livewire\DynamicValues;

use Livewire\Component;
use App\Models\Category;
use App\Models\Subcategory;

class SubcategoryManagement extends Component
{
    public $categories = [];
    public $subcategories = [];

    // For add new subcategory
    public $newName = '';
    public $newCategoryId = null;
    public $newCategoryType = 'IT'; // default

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
        $this->categories = Category::orderBy('name')->get();
        $this->subcategories = Subcategory::with('category')->orderBy('name')->get();
    }

    public function add()
    {
        if (!$this->newName || !$this->newCategoryId) return;

        Subcategory::create([
            'name' => $this->newName,
            'category_id' => $this->newCategoryId,
            'category_type' => $this->newCategoryType,
        ]);

        $this->resetNew();
        $this->loadData();
    }

    public function resetNew()
    {
        $this->newName = '';
        $this->newCategoryId = null;
        $this->newCategoryType = 'IT';
    }

    public function startEdit($id)
    {
        $sub = Subcategory::find($id);
        $this->editId = $id;
        $this->editName = $sub->name;
        $this->editCategoryId = $sub->category_id;
        $this->editCategoryType = $sub->category_type;
    }

    public function saveEdit()
    {
        Subcategory::where('id', $this->editId)->update([
            'name' => $this->editName,
            'category_id' => $this->editCategoryId,
            'category_type' => $this->editCategoryType,
        ]);

        $this->resetEdit();
        $this->loadData();
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
        Subcategory::where('id', $id)->delete();
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.dynamic-values.subcategory-management');
    }
}
