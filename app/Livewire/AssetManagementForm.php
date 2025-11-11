<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Asset;
use Illuminate\Support\Facades\Log;

class AssetManagementForm extends Component
{   
    public $showConfirmModal = false;

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

    public function mount($mode, $targetID = null, $category_type = null, $category = null, $sub_category = null){
        if($mode == 'create'){
            $this->ref_id = 'FA-' . now()->year . '-' . rand(100, 999);
            $this->category_type = $category_type;
            $this->category = $category;
            $this->sub_category = $sub_category;            
        }else{
            $targetAsset = Asset::findOrFail($targetID);
            $this->ref_id = $targetAsset->ref_id;
            $this->category_type = $targetAsset->category_type;
            $this->category = $targetAsset->category;
            $this->sub_category = $targetAsset->sub_category;       
        }
    }

    public function trySubmit()
    {
        $this->validate();
        $this->showConfirmModal = true; // show modal only when valid
    }
    
    public function submit(){
        $this->validate();

        $technicaldata = [
            'processor'   => $this->processor,
            'ram'         => $this->ram,
            'storage'     => $this->storage,
            'ip_address'  => $this->ip_address,
            'mac_address' => $this->mac_address,
            'vpn_address' => $this->vpn_address,
            'wol_enabled' => $this->wol_enabled,
        ];
        
        Asset::create([
            'ref_id' => $this->ref_id,
            'category_type' => $this->category_type,
            'category' => $this->category,
            'sub_category' => $this->sub_category,

            'brand' => $this->brand,
            'model' => $this->model,
            'status' => $this->status,
            'condition' => $this->condition,

            'acquisition_date' => $this->acquisition_date,
            'item_cost' => $this->item_cost,
            'depreciated_value' => $this->depreciated_value,
            'usable_life' => $this->usable_life,

            'technical_data' => json_encode($technicaldata)
        ]);


        $this->redirect('/assetmanagement');
    }
    
    public function render()
    {
        return view('livewire.assetmanagement-form');
    }
}
