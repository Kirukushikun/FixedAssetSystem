<?php

namespace App\Livewire\DynamicValues;

use Livewire\Component;
use App\Models\Category;

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
        $this->categories = Category::orderBy('name')->get();
    }

    public function add()
    {
        if (!$this->newName) return;

        Category::create([
            'name' => $this->newName,
            'icon' => $this->newIcon,
        ]);

        $this->newName = '';
        $this->newIcon = 'folder';

        $this->load();
    }

    public function startEdit($id)
    {
        $cat = Category::find($id);
        $this->editId = $id;
        $this->editName = $cat->name;
        $this->editIcon = $cat->icon;
    }

    public function saveEdit()
    {
        Category::where('id', $this->editId)->update([
            'name' => $this->editName,
            'icon' => $this->editIcon,
        ]);

        $this->editId = null;
        $this->editName = '';
        $this->editIcon = '';

        $this->load();
    }

    public function cancelEdit()
    {
        $this->editId = null;
    }

    public function delete($id)
    {
        Category::where('id', $id)->delete();
        $this->load();
    }

    public function render()
    {
        return view('livewire.dynamic-values.category-management');
    }
}
