<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Asset;
use Illuminate\Support\Facades\Log;

class AssetcreateForm extends Component
{   

    // GENERAL INFORMATION
    public 
        $ref_id, 
        $category_type, 
        $category,
        $sub_category,
        
        $brand, 
        $model, 
        $status,
        $condition,
        
        $acquisition_date, 
        $item_cost, 
        $depreciated_value,
        $usable_life;
    
    // TECHNICAL DETAILS
    public 
        $processor, 
        $ram, 
        $storage,
        $ip_address,
        
        $mac_address, 
        $vpn_address, 
        $wol_enabled;

    protected $rules = [
        'ref_id' => 'required',
        'category_type' => 'required',
        'category' => 'required',
        'sub_category' => 'required',

        'brand' => 'required',
        'model' => 'required',
        'status' => 'required',
        'condition' => 'required',

        'acquisition_date' => 'required',
        'item_cost' => 'nullable',
        'depreciated_value' => 'nullable',
        'usable_life' => 'nullable'
    ];

    public function mount($category_type, $category, $sub_category){
        $this->ref_id = 'FA-' . now()->year . '-' . rand(100, 999);
        $this->category_type = $category_type;
        $this->category = $category;
        $this->sub_category = $sub_category;
    }
    
    public function submit(){
        $technicaldata = [
            'processor'   => $this->processor,
            'ram'         => $this->ram,
            'storage'     => $this->storage,
            'ip_address'  => $this->ip_address,
            'mac_address' => $this->mac_address,
            'vpn_address' => $this->vpn_address,
            'wol_enabled' => $this->wol_enabled,
        ];
        Log::info($technicaldata);
        $this->validate();

        $this->redirect('/assetmanagement');
    }
    
    public function render()
    {
        return view('livewire.assetcreate-form');
    }
}
