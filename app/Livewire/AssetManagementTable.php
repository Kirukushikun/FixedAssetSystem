<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Asset;
use Livewire\WithPagination;

class AssetManagementTable extends Component
{   

    use WithPagination;
    
    protected $paginationTheme = 'tailwind';


    public function goToPage($page)
    {
       $this->setPage($page);
    }

    public function delete($targetAsset){
        $asset = Asset::findOrFail($targetAsset);
        if($asset){
            $asset->is_deleted = true;
            $asset->save();            
        }

        $this->dispatch('notif', type: 'success', header: 'Asset Deleted', message: 'Asset has been successfully deleted.');
    }
    
    public function render()
    {   
        $assets = Asset::where('is_deleted', false)
            ->where('is_archived', false)
            ->latest()
            ->paginate(10);
        return view('livewire.assetmanagement-table', compact('assets'));
    }
}