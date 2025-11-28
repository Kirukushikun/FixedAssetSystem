<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Asset;

class Trash extends Component
{
    public function render()
    {   
        // Collect Assets marked as deleted
        $deletedAssets = Asset::where('is_deleted', true)->get();

        return view('livewire.trash', compact('deletedAssets'));
    }
}
