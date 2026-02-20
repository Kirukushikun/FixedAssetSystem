<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Asset;
use App\Models\Audit;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AuditForm extends Component
{   
    use WithFileUploads;

    public $targetAsset;
    public $showConfirmModal = false;
    public $attachment;
    
    // Read-only fields (from asset)
    public $category;
    public $sub_category;
    public $brand;
    public $model;
    public $farm;
    public $location;
    public $description;
    
    // Editable fields
    public $last_audit;
    public $next_audit;
    public $notes;

    public function mount($targetID = null){
        $this->targetAsset = Asset::with('categoryDetails')->findOrFail($targetID);
        
        // Prefill read-only fields from asset
        $this->category = $this->targetAsset->categoryDetails->name ?? '';
        $this->sub_category = $this->targetAsset->sub_category ?? '';
        $this->brand = $this->targetAsset->brand ?? '';
        $this->model = $this->targetAsset->model ?? '';
        $this->farm = $this->targetAsset->farm ?? 'Not assigned';
        $this->location = $this->targetAsset->location ?? 'Not specified';
        $this->description = $this->targetAsset->remarks ?? 'No description';

        // Prefill last audit if there's previous one
        $this->last_audit = Audit::where('asset_id', $targetID)->orderByDesc('audited_at')->first();
    }

    public function trySubmit(){
        $this->validate([
            'next_audit' => 'required|date',
            'notes' => 'nullable|string|max:1000',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
        ]);
        
        $this->showConfirmModal = true;
    }

    public function submit(){
        try{
            $attachmentPath = null;
            $attachmentName = null;
            
            if ($this->attachment) {
                $attachmentPath = $this->attachment->store('attachments', 'public');
                $attachmentName = $this->attachment->getClientOriginalName();
            }

            // Create audit record
            Audit::create([
                'asset_id' => $this->targetAsset->id,
                'farm' => $this->targetAsset->farm ?? 'Not assigned',
                'location' => $this->targetAsset->location ?? 'Not specified',
                'next_audit_date' => $this->next_audit,
                'notes' => $this->notes,
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachmentName,
                'audited_at' => now(),
                'audited_by' => auth()->user()->id,
                'audited_by_name' => auth()->user()->name,
            ]);

            // Update asset's audit dates
            $this->targetAsset->update([
                'last_audit_date' => now(),
                'next_audit_date' => $this->next_audit,
            ]);

            session()->flash('success', 'Asset audited successfully!');
            
            $this->redirect('/assetmanagement');
            session()->flash('notif', [
                'type' => 'success',
                'header' => 'Audit Saved',
                'message' => 'The audit entry has been successfully added to the asset\'s audit records'
            ]);

            Cache::forget('api.assets.index');
            Cache::forget('asset_table_query');
            Cache::forget('trash_deleted_assets');
        } catch (\Exception $e) {
            Log::error('Audit failed: ' . $e->getMessage());
            $this->redirect('/assetmanagement');
            session()->flash('notif', [
                'type' => 'failed',
                'header' => 'Audit Failed',
                'message' => 'Unable to audit asset. Please try again.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.audit-form');
    }
}