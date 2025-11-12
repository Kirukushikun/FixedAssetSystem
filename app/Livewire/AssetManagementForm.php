<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Asset;
use Illuminate\Support\Facades\Log;

class AssetManagementForm extends Component
{   
    public $mode;
    public $showConfirmModal = false;
    public $targetAsset;

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
    
    // TECHNICAL DETAILS ARRAY
    public $technicaldata = [
        'processor'   => '',
        'ram'         => '',
        'storage'     => '',
        'ip_address'  => '',
        'mac_address' => '',
        'vpn_address' => '',
        'wol_enabled' => '',
    ];

    // RULES FOR VALIDATIOn
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
        // Set Mode
        $this->mode = $mode;

        if($mode == 'create'){
            // Prefill initial info for creation
            $this->ref_id = 'FA-' . now()->year . '-' . rand(100, 999);
            $this->category_type = $category_type;
            $this->category = $category;
            $this->sub_category = $sub_category;            
        }else{
            // Prefill inputs of a specific asset on edit or viewing
            $this->targetAsset = Asset::findOrFail($targetID);
            $this->fill([
                'ref_id'            => $this->targetAsset->ref_id,
                'category_type'     => $this->targetAsset->category_type,
                'category'          => $this->targetAsset->category,
                'sub_category'      => $this->targetAsset->sub_category,
                
                'brand' => $this->targetAsset->brand,
                'model' => $this->targetAsset->model,
                'status' => $this->targetAsset->status,
                'condition' => $this->targetAsset->condition,

                'acquisition_date' => $this->targetAsset->acquisition_date,
                'item_cost' => $this->targetAsset->item_cost,
                'depreciated_value' => $this->targetAsset->depreciated_value,
                'usable_life' => $this->targetAsset->usable_life
            ]); 

            //Prefill technical data
            if($this->targetAsset->category_type == 'IT'){
                $this->technicaldata = json_decode($this->targetAsset->technical_data) ?? $this->technicaldata;
            }
            
        }
    }

    // This function will validate before showing modal
    public function trySubmit()
    {
        $this->validate();
        $this->showConfirmModal = true; // show modal only when valid
    }
    
    public function submit(){
        // Final validation upon submit
        $this->validate();


        
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

            'technical_data' => json_encode($this->technicaldata)
        ]);


        $this->redirect('/assetmanagement');
    }

    public function update()
    {
        // Validate input before updating
        $this->validate();

        // Update the asset fields
        $this->targetAsset->update([
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

            'technical_data' => json_encode($this->technicaldata),
        ]);

        $this->redirect('/assetmanagement');
    }
    
    public function render()
    {
        return view('livewire.assetmanagement-form');
    }
}
