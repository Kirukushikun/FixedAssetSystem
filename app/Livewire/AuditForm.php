<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Asset;
use App\Models\Audit;

class AuditForm extends Component
{   
    use WithFileUploads;

    public $targetAsset;
    public $showConfirmModal = false;
    public $attachment;
    
    // Form fields
    public $location;
    public $next_audit;
    public $notes;

    public function mount($targetID = null){
        $this->targetAsset = Asset::findOrFail($targetID);
        // Prefill location if asset has one
        $this->location = $this->targetAsset->farm ?? '';
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
            'audited_by' => auth()->id(),
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
            'type' => 'Success',
            'header' => 'Success',
            'message' => 'Success'
        ]);
    }

    public function render()
    {
        return view('livewire.audit-form');
    }
}