<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Models\Asset;
use App\Models\Employee;
use App\Models\History;
use App\Models\Audit;
use App\Services\SnipeService;

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
        'serial'   => '',
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
    public $audits;

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

            // Audits
            $this->audits = Audit::where('asset_id', $this->targetAsset->id)->latest()->get();
            
        }

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
        try{
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

            
            // 3. Check if IT category → Sync to Snipe-IT
            // if ($this->category_type === 'IT') {
            //     $this->syncToSnipeIT($asset);
            // }

            // Audit Trail
            $this->audit('Created Asset: ' . $asset->ref_id . ' - ' . $asset->category_type . ' / ' . $asset->category . ' / ' . $asset->sub_category);

            // Use RELOAD notification because we're redirecting
            $this->reloadNotif('success', 'Asset Created', 'Asset ' . $this->ref_id . ' has been successfully created.');
            $this->redirect('/assetmanagement');

        } catch (\Exception $e) {
            Log::error('Asset creation failed: ' . $e->getMessage());
            // Use NORELOAD notification to show error without redirect
            $this->noreloadNotif('failed', 'Create Failed', 'Unable to create asset. Please try again.');
        }
    }

    public function update()
    {
        try {
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

            // Audit Trail
            $this->audit('Updated Asset: ' . $this->targetAsset->ref_id . ' - ' . $this->targetAsset->category_type . ' / ' . $this->targetAsset->category . ' / ' . $this->targetAsset->sub_category); 
            

            // Use RELOAD notification because we're redirecting
            $this->reloadNotif('success', 'Asset Updated', 'Asset ' . $this->ref_id . ' has been successfully updated.');
            $this->redirect('/assetmanagement');

        } catch (\Exception $e) {
            Log::error('Asset update failed: ' . $e->getMessage());
            // Use NORELOAD notification to show error without redirect
            $this->noreloadNotif('failed', 'Update Failed', 'Unable to update asset. Please try again.');
        }
    }

    // --- TRANSFER ASSET ---
    public function transferAsset()
    {   
        try {
            $assignee = Employee::find($this->newHolder);

            if (!$assignee) {
                $this->noreloadNotif('failed', 'Transfer Failed', 'Selected employee not found.');
                return;
            }

            // 1. Save history (old data first)
            History::create([
                'asset_id'      => $this->targetAsset->id,
                'assignee_id'   => $assignee->employee_id,
                'assignee_name' => $assignee->employee_name,
                'status'        => $this->targetAsset->status,
                'condition'     => $this->newCondition,
                'farm'          => $assignee->farm,
                'department'    => $assignee->department,
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

            // Refresh history after transfer
            $this->history = History::where('asset_id', $this->targetAsset->id)->latest()->get();

            // Use NORELOAD notification - stays on same page to see updated data
            $this->noreloadNotif('success', 'Asset Transferred', 'Asset has been successfully transferred to ' . $assignee->employee_name . '.');

        } catch (\Exception $e) {
            Log::error('Asset transfer failed: ' . $e->getMessage());
            $this->noreloadNotif('failed', 'Transfer Failed', 'Unable to transfer asset. Please try again.');
        }
    }

    // --- ASSIGN ASSET ---
    public function assignAsset()
    {   
        try {
            $assignee = Employee::find($this->newHolder);

            if (!$assignee) {
                $this->noreloadNotif('failed', 'Assignment Failed', 'Selected employee not found.');
                return;
            }

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

            $this->reset(['newHolder']);

            // Refresh history after assignment
            $this->history = History::where('asset_id', $this->targetAsset->id)->latest()->get();

            // Use NORELOAD notification - stays on same page to see updated data
            $this->noreloadNotif('success', 'Asset Assigned', 'Asset has been successfully assigned to ' . $assignee->employee_name . '.');

        } catch (\Exception $e) {
            Log::error('Asset assignment failed: ' . $e->getMessage());
            $this->noreloadNotif('failed', 'Assignment Failed', 'Unable to assign asset. Please try again.');
        }
    }
    
    public function render()
    {   
        return view('livewire.assetmanagement-form');
    }

    private function noreloadNotif($type, $header, $message){
        $this->dispatch('notif', type: $type, header: $header, message: $message);
    }

    private function reloadNotif($type, $header, $message){
        session()->flash('notif', [
            'type' => $type,
            'header' => $header,
            'message' => $message
        ]);
    }

    private function audit($action){
        $user = auth()->user();
        \App\Models\AuditTrail::create([
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'action' => $action,
        ]);
    }

    public function syncToSnipeIT($asset)
    {
        $data = [
            "name" => $asset->brand . ' ' . $asset->model,
            "asset_tag" => $asset->ref_id,
            "serial" => $asset->model ?? null,
            "model_id" => $asset->id,     
            "status_id" => 2,   
            "name" => $asset->brand . ' ' . $asset->model,
            "purchase_date" => $asset->acquisition_date,
            "purchase_cost" => $asset->item_cost,
        ];

        $result = app(\App\Services\SnipeService::class)->createAsset($data);
        
        $targetAsset = Asset::find($asset->id);    

        $targetAsset->update([
            'snipe_id' => $result['payload']['id'] ?? null,
        ]);

        Log::info('Snipe-IT Sync Result:', $result);
    }

    public function updateToSnipeIT($asset)
    {
        $data = [
            "asset_tag" => $asset->ref_id,
            "serial" => $asset->model,
            "name" => $asset->brand . ' ' . $asset->model,
            "purchase_date" => $asset->acquisition_date,
            "purchase_cost" => $asset->item_cost,
        ];

        $result = app(\App\Services\SnipeService::class)
            ->updateAsset($asset->snipe_id, $data);

        Log::info('Snipe-IT Update Result:', $result);
    }

    public function deleteFromSnipeIT($asset)
    {
        $result = app(\App\Services\SnipeService::class)
            ->deleteAsset($asset->snipe_id);

        Log::info('Snipe-IT Delete Result:', $result);
    }
    
}