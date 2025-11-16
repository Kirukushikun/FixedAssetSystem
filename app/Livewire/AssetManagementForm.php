<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Models\Asset;
use App\Models\Employee;
use App\Models\History;


class AssetManagementForm extends Component
{   
    use WithFileUploads;

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

    // ASSIGNMENT DETAILS
    public $employees = [];
    public $selectedEmployee;
    public $selectedEmployeeName;
    public $farm;
    public $department;
    public $history;

    public $qr_code;
    public $attachment;
    public $attachment_name;
    public $remarks;

    public $newHolder;
    public $newCondition;

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
        'usable_life' => 'nullable',

        'attachment' => 'nullable|file|mimes:pdf|max:5120'
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
                'usable_life' => $this->targetAsset->usable_life,

                'selectedEmployee' => $this->targetAsset->assigned_id,  
                'farm' => $this->targetAsset->farm,
                'department' => $this->targetAsset->department,

                'remarks' => $this->remarks
            ]); 

            $this->qr_code = $this->targetAsset->qr_code;
            $this->attachment = $this->targetAsset->attachment;
            $this->attachment_name = $this->targetAsset->attachment_name;

            //Prefill technical data
            if($this->targetAsset->category_type == 'IT'){
                $this->technicaldata = json_decode($this->targetAsset->technical_data) ?? $this->technicaldata;
            }

            // History
            $this->history = History::where('asset_id', $this->targetAsset->id)->latest()->get();
            
        }

        // Temporary static list. Replace with DB later.
        // $this->employees = [
        //     ['id' => 1, 'employee_name' => 'Chris Bacon', 'farm' => 'BFC', 'department' => 'IT & Security'],
        //     ['id' => 2, 'employee_name' => 'Juan Dela Cruz', 'farm' => 'BDL', 'department' => 'Production'],
        // ];

        $this->employees = Employee::select('id','employee_name','farm','department')->get()->toArray();

    }

    public function updatedSelectedEmployee($value)
    {
        $data = collect($this->employees)->firstWhere('id', $value);

        if ($data) {
            $this->selectedEmployeeName = $data['employee_name'];
            $this->farm = $data['farm'];
            $this->department = $data['department'];
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

        if ($this->attachment) {
            $path = $this->attachment->store('attachment', 'public');
            $originalName = $this->attachment->getClientOriginalName();
        }
        
        $asset = Asset::create([
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

            'assigned_name' => $this->selectedEmployeeName ?? null,
            'assigned_id' => $this->selectedEmployee ?? null,
            'farm' => $this->farm ?? null,
            'department' => $this->department ?? null,

            'attachment' => $path ?? null,
            'attachment_name' => $originalName ?? null
        ]);

        // -------- SAVE QR CODE FILE ----------
        $url = url('/assetmanagement/view?targetID=' . $asset->id);

        $qrFileName = 'qr_' . $asset->id . '.svg';

        QrCode::format('svg')
            ->size(300)
            ->generate($url, storage_path('app/public/qrcodes/' . $qrFileName));

        $asset->update([
            'qr_code' => 'qrcodes/' . $qrFileName
        ]);
        // -------------------------------------

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

    // --- TRANSFER ASSET ---
    public function transferAsset()
    {   
        $assignee = Employee::find($this->newHolder);

        // 1. Save history (old data first)
        History::create([
            'asset_id'      => $this->targetAsset->id,
            'assignee_id'   => $this->targetAsset->assigned_id,
            'assignee_name' => $this->targetAsset->assigned_name,
            'status'        => $this->targetAsset->status,
            'condition'     => $this->newCondition,
            'farm'          => $this->targetAsset->farm,
            'department'    => $this->targetAsset->department,
            'action'        => 'Transfer',
        ]);

        // 2. Update asset (new holder + new condition)
        $this->targetAsset->update([
            'assigned_id'   => $assignee->id,
            'assigned_name' => $assignee->employee_name,
            'farm' => $assignee->farm,
            'department' => $assignee->department,
            'condition' => $this->newCondition,
        ]);
    }

    // --- ASSIGN ASSET ---
    public function assignAsset()
    {   
        $assignee = Employee::find($this->newHolder);

        // 1. Save history
        History::create([
            'asset_id'      => $this->targetAsset->id,
            'assignee_id'   => $assignee->id,
            'assignee_name' => $assignee->employee_name,
            'status'        => $this->targetAsset->status,
            'condition'     => $this->targetAsset->condition,
            'farm'          => $assignee->farm,
            'department'    => $assignee->department,
            'action'        => 'Assign',
        ]);

        // 2. Update asset (assign new holder)
        $this->targetAsset->update([
            'assigned_id'   => $assignee->id,
            'assigned_name' => $assignee->employee_name,
            'farm' => $assignee->farm,
            'department' => $assignee->department,
            'condition' => $this->targetAsset->condition,
        ]);

    }
    
    public function render()
    {   
        return view('livewire.assetmanagement-form');
    }
}
