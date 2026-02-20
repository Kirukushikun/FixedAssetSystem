<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

use App\Models\Asset;
use App\Models\Employee;
use App\Models\History;
use App\Models\Audit;
use App\Models\Category;
use App\Models\Department;
use App\Models\DynamicField;

// Import the new jobs
use App\Jobs\GenerateAssetQrCode;
use App\Jobs\SyncAssetToSnipeIT;

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
    public $categoryCodeImage;
    public $history;
    public $audits;

    public $qr_code;
    public $attachment;
    public $attachment_name;
    public $remarks;

    public $newHolder;
    public $newCondition;

    public $farms = ['BFC', 'BDL', 'PFC', 'RH'];
    public $departments = [];
    public $location;

    // RULES FOR VALIDATION
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

    // Dynamic values 
    public $brands;
    public $processors;
    public $rams;
    public $storages;


    public function mount($mode, $targetID = null, $category_type = null, $category = null, $sub_category = null)
    {
        $this->mode = $mode;

        if($mode == 'create'){
            $this->ref_id = $this->generateNextRefId();
            $this->category_type = $category_type;
            $this->category = $category;
            $this->sub_category = $sub_category;            
        } else {
            $this->loadAssetData($targetID);
        }

        $this->employees = Cache::remember('employees_dropdown', 3600, function() {
            return Employee::select('id', 'employee_name', 'farm', 'department')
                ->orderBy('employee_name')
                ->get()
                ->toArray();
        });

        $this->brands = Cache::remember('brand_list', 3600, function() {
            return DynamicField::where('field', 'brand')->pluck('value')->toArray();
        });

        $this->processors = Cache::remember('processor_list', 3600, function() {
            return DynamicField::where('field', 'processor')->pluck('value')->toArray();
        });

        $this->rams = Cache::remember('ram_list', 3600, function() {
            return DynamicField::where('field', 'RAM')->pluck('value')->toArray();
        });

        $this->storages = Cache::remember('storage_list', 3600, function() {
            return DynamicField::where('field', 'Storage')->pluck('value')->toArray();
        });

        $this->departments = Cache::remember('departments_list', 3600, function() {
            return Department::pluck('name')->toArray();
        });

        $this->categoryCodeImage = Cache::remember('categories_by_code', 3600, function() {
            return Category::all()->keyBy('code');
        });


    }

    /**
     * Generate next reference ID
     */
    private function generateNextRefId(): string
    {
        $year = now()->year;

        $lastRefId = DB::table('assets')
            ->where('ref_id', 'LIKE', "FA-{$year}-%")
            ->orderByRaw('CAST(SUBSTRING(ref_id, 9) AS UNSIGNED) DESC')
            ->value('ref_id');

        if ($lastRefId) {
            $lastNumber = (int) substr($lastRefId, 8);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return 'FA-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Load existing asset data for edit/view mode
     * OPTIMIZED: Uses eager loading to prevent N+1 queries
     */
    private function loadAssetData($targetID): void
    {
        // Eager load all relationships in one query
        $this->targetAsset = Asset::with([
            'history' => function ($query) {
                $query->latest()->limit(50);
            },
            'audits' => function ($query) {
                $query->latest()->limit(50);
            },
            'assignedEmployee:id,employee_name,farm,department',
            'categoryDetails:code,name'
        ])->findOrFail($targetID);
        
        $this->fill([
            'ref_id'            => $this->targetAsset->ref_id,
            'category_type'     => $this->targetAsset->category_type,
            'category'          => $this->targetAsset->category,
            'sub_category'      => $this->targetAsset->sub_category,
            
            'brand' => $this->targetAsset->brand,
            'model' => $this->targetAsset->model,
            'status' => $this->targetAsset->status,
            'condition' => $this->targetAsset->condition,

            // FIX: Format the date properly for the input field
            'acquisition_date' => $this->targetAsset->acquisition_date ? 
                \Carbon\Carbon::parse($this->targetAsset->acquisition_date)->format('Y-m-d') : null,
            
            'item_cost' => $this->targetAsset->item_cost,
            'depreciated_value' => $this->targetAsset->depreciated_value,
            'usable_life' => $this->targetAsset->usable_life,

            'selectedEmployee' => $this->targetAsset->assigned_id,  
            'farm' => $this->targetAsset->farm,
            'department' => $this->targetAsset->department,
            'location' => $this->targetAsset->location,

            'remarks' => $this->remarks
        ]); 

        $this->qr_code = $this->targetAsset->qr_code;
        $this->attachment = $this->targetAsset->attachment;
        $this->attachment_name = $this->targetAsset->attachment_name;

        // Prefill technical data
        if($this->targetAsset->category_type == 'IT'){
            $this->technicaldata = json_decode($this->targetAsset->technical_data, true) ?? $this->technicaldata;
        }

        // Access eager-loaded relationships
        $this->history = $this->targetAsset->history;
        $this->audits = $this->targetAsset->audits;
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

    public function trySubmit()
    {
        $this->validate();
        $this->showConfirmModal = true;
    }
    
    /**
     * OPTIMIZED: QR generation and Snipe-IT sync moved to background jobs
     */
    public function submit()
    {
        DB::beginTransaction();
        
        try {
            $this->validate();

            $path = null;
            $originalName = null;

            if ($this->attachment) {
                $path = $this->attachment->store('attachment', 'public');
                $originalName = $this->attachment->getClientOriginalName();
            }
            
            $asset = Asset::create([
                'ref_id' => $this->ref_id,
                'category_type' => $this->category_type,
                'category' => $this->categoryCodeImage[$this->category]->code,
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
                'location' => $this->location ?? null,

                'attachment' => $path,
                'attachment_name' => $originalName
            ]);

            // Update ref_id to use actual database ID
            $finalRefId = 'FA-' . now()->year . '-' . str_pad($asset->id, 4, '0', STR_PAD_LEFT);
            $asset->update(['ref_id' => $finalRefId]);
            $this->ref_id = $finalRefId;

            // OPTIMIZED: Dispatch QR generation to background queue
            GenerateAssetQrCode::dispatch($asset);

            // OPTIMIZED: Dispatch Snipe-IT sync to background queue
            if ($this->category_type === 'IT') {
                SyncAssetToSnipeIT::dispatch($asset, 'create');
            }

            // Audit Trail
            $this->audit('Created Asset: ' . $this->ref_id . ' - ' . $asset->category_type . ' / ' . $asset->category . ' / ' . $asset->sub_category);

            DB::commit();

            $this->clearAllAssetCaches();
            
            $this->reloadNotif('success', 'Asset Created', 'Asset ' . $this->ref_id . ' has been successfully created. QR code and sync are being processed.');
            $this->redirect('/assetmanagement');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Asset creation failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'ref_id' => $this->ref_id
            ]);
            
            $this->noreloadNotif('failed', 'Create Failed', 'Unable to create asset. Please try again.');
        }
    }

    /**
     * OPTIMIZED: Snipe-IT update moved to background job
     */
    public function update()
    {
        DB::beginTransaction();
        
        try {
            $this->validate();

            // Preserve assignment if not changed
            $assignedId = $this->selectedEmployee ?? $this->targetAsset->assigned_id;
            $assignedName = $this->selectedEmployeeName ?? $this->targetAsset->assigned_name;


            // FIXED: Added 'location' to the update array
            $this->targetAsset->update([
                'ref_id' => $this->ref_id,
                'category_type' => $this->category_type,
                'category' => $this->categoryCodeImage[$this->category]->code,
                'sub_category' => $this->sub_category,

                'brand' => $this->brand,
                'model' => $this->model,
                'status' => $this->status,
                'condition' => $this->condition,

                'acquisition_date' => $this->acquisition_date,
                'item_cost' => $this->item_cost,
                'depreciated_value' => $this->depreciated_value,
                'usable_life' => $this->usable_life,

                // Save assignment details
                'assigned_id' => $assignedId,
                'assigned_name' => $assignedName,
                'farm' => $this->farm,
                'department' => $this->department,
                'location' => $this->location,

                'technical_data' => json_encode($this->technicaldata),
            ]);

            // OPTIMIZED: Dispatch Snipe-IT update to background queue
            if ($this->category_type === 'IT' && $this->targetAsset->snipe_id) {
                SyncAssetToSnipeIT::dispatch($this->targetAsset, 'update');
            }

            // Audit Trail
            $this->audit('Updated Asset: ' . $this->targetAsset->ref_id . ' - ' . $this->targetAsset->category_type . ' / ' . $this->targetAsset->category . ' / ' . $this->targetAsset->sub_category); 

            DB::commit();

            $this->clearAllAssetCaches();

            $this->reloadNotif('success', 'Asset Updated', 'Asset ' . $this->ref_id . ' has been successfully updated.');
            $this->redirect('/assetmanagement');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Asset update failed', [
                'error' => $e->getMessage(),
                'asset_id' => $this->targetAsset->id,
                'user_id' => auth()->id()
            ]);
            
            $this->noreloadNotif('failed', 'Update Failed', 'Unable to update asset. Please try again.');
        }
    }

    /**
     * OPTIMIZED: Uses eager loading when refreshing history
     */
    public function transferAsset()
    {   
        DB::beginTransaction();
        
        try {
            $assignee = Employee::find($this->newHolder);

            if (!$assignee) {
                $this->noreloadNotif('failed', 'Transfer Failed', 'Selected employee not found.');
                return;
            }

            // Save history
            History::create([
                'asset_id'      => $this->targetAsset->id,
                'assignee_id'   => $assignee->employee_id,
                'assignee_name' => $assignee->employee_name,
                'status'        => $this->targetAsset->status,
                'condition'     => $this->newCondition,
                'farm'          => $assignee->farm,
                'department'    => $assignee->department,
                'location'      => $this->location,
                'action'        => 'Transfer',
            ]);

            // Update asset
            $this->targetAsset->update([
                'assigned_id'   => $assignee->id,
                'assigned_name' => $assignee->employee_name,
                'farm' => $assignee->farm,
                'department' => $assignee->department,
                'location' => $this->location,
                'condition' => $this->newCondition,
            ]);

            DB::commit();

            // Refresh history after transfer using relationship
            $this->targetAsset->load(['history' => function ($query) {
                $query->latest()->limit(50);
            }]);
            $this->history = $this->targetAsset->history;

            $this->clearAllAssetCaches();

            $this->noreloadNotif('success', 'Asset Transferred', 'Asset has been successfully transferred to ' . $assignee->employee_name . '.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Asset transfer failed', [
                'error' => $e->getMessage(),
                'asset_id' => $this->targetAsset->id,
                'new_holder' => $this->newHolder,
                'user_id' => auth()->id()
            ]);
            
            $this->noreloadNotif('failed', 'Transfer Failed', 'Unable to transfer asset. Please try again.');
        }
    }

    /**
     * OPTIMIZED: Uses eager loading when refreshing history
     */
    public function assignAsset()
    {   
        try {
            $assignee = Employee::find($this->newHolder);

            if (!$assignee) {
                $this->noreloadNotif('failed', 'Assignment Failed', 'Selected employee not found.');
                return;
            }

            // Update form fields to preview the assignment (NOT saved to DB yet)
            $this->selectedEmployee = $assignee->id;
            $this->selectedEmployeeName = $assignee->employee_name;
            $this->targetAsset->assigned_name = $assignee->employee_name;
            $this->farm = $assignee->farm;
            $this->department = $assignee->department;
            
            // Reset the modal field
            $this->reset(['newHolder']);

            $this->clearAllAssetCaches();
            
        } catch (\Exception $e) {
            Log::error('Asset assignment preview failed', [
                'error' => $e->getMessage(),
                'asset_id' => $this->targetAsset->id ?? null,
                'new_holder' => $this->newHolder,
                'user_id' => auth()->id()
            ]);
            
            $this->noreloadNotif('failed', 'Assignment Failed', 'Unable to preview asset assignment. Please try again.');
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
        \App\Models\AuditTrail::create([
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'action' => $action,
        ]);
    }

    public function clearAllAssetCaches()
    {
        Cache::forget('api.assets.index');
        Cache::forget('asset_table_query');
        Cache::forget('trash_deleted_assets');
        Cache::forget('employees_dropdown');
        Cache::forget('departments_list');
        Cache::forget('categories_by_code');
    }
}