<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Asset;

class AssetManagementTable extends Component
{   
    public function delete($targetAsset){
        $asset = Asset::findOrFail($targetAsset);
        $asset->is_deleted = true;
        $asset->save();
    }
    
    public function render()
    {   
        $assets = Asset::where('is_deleted', false)
            ->where('is_archived', false)
            ->latest()
            ->get();
        return view('livewire.asset-management-table', compact('assets'));
    }
}
