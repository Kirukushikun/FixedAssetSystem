<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Asset;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class AssetManagementTable extends Component
{   

    use WithPagination;
    
    protected $paginationTheme = 'tailwind';

    public $search = '';

    public function goToPage($page)
    {
       $this->setPage($page);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function delete($targetAsset){
        $asset = Asset::findOrFail($targetAsset);
        if($asset){
            $asset->is_deleted = true;
            $asset->save();            
        }

        $this->audit('Deleted Asset: ' . $asset->ref_id . ' - ' . $asset->category_type . ' / ' . $asset->category . ' / ' . $asset->sub_category);

        $this->dispatch('notif', type: 'success', header: 'Asset Deleted', message: 'Asset has been successfully deleted.');
    }
    
    public function render()
    {   
        $assets = Asset::where('is_deleted', false)
            ->where('is_archived', false)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('ref_id', 'like', '%' . $this->search . '%')
                        ->orWhere('category_type', 'like', '%' . $this->search . '%')
                        ->orWhere('category', 'like', '%' . $this->search . '%')
                        ->orWhere('sub_category', 'like', '%' . $this->search . '%')
                        ->orWhere('brand', 'like', '%' . $this->search . '%')
                        ->orWhere('model', 'like', '%' . $this->search . '%')
                        ->orWhere('status', 'like', '%' . $this->search . '%')
                        ->orWhere('condition', 'like', '%' . $this->search . '%')
                        ->orWhere('item_cost', 'like', '%' . $this->search . '%')
                        ->orWhere('assigned_name', 'like', '%' . $this->search . '%')
                        ->orWhere('assigned_id', 'like', '%' . $this->search . '%')
                        ->orWhere('farm', 'like', '%' . $this->search . '%')
                        ->orWhere('department', 'like', '%' . $this->search . '%')
                        ->orWhere('remarks', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(10);
        return view('livewire.asset-management-table', compact('assets'));
    }

    private function audit($action){
        $user = auth()->user();
        \App\Models\AuditTrail::create([
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'action' => $action,
        ]);
    }
}
