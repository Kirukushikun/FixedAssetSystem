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
    
    // Form fields
    public $location;
    public $last_audit;
    public $next_audit;
    public $notes;

    public function mount($targetID = null){
        $this->targetAsset = Asset::findOrFail($targetID);
        // Prefill location if asset has one
        $this->location = $this->targetAsset->farm ?? '';

        // Prefill last audit if theres previous one
        $this->last_audit = Audit::where('asset_id', $targetID)->orderByDesc('audited_at')->first();
    }

    public function trySubmit(){
        $this->validate([
            'location' => 'required',
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
                'location' => $this->location,
                'next_audit_date' => $this->next_audit,
                'notes' => $this->notes,
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachmentName,
                'audited_at' => now(),
                'audited_by' => 61,
            ]);

            // Update asset location
            $this->targetAsset->update([
                'location' => $this->location,
                'last_audit_date' => now(),
                'next_audit_date' => $this->next_audit,
            ]);

            session()->flash('success', 'Asset audited successfully!');
            
            $this->redirect('/assetmanagement');
            session()->flash('notif', [
                'type' => 'success',
                'header' => 'Audit Saved',
                'message' => 'The audit entry has been successfully added to the asset’s audit records'
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