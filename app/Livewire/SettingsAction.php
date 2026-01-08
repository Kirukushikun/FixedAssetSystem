<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class SettingsAction extends Component
{
    public $enableSync;
    public $showModal = false;

    public function mount()
    {
        // Check if ANY admin has sync enabled (system-wide check)
        $this->enableSync = User::where('is_admin', true)
            ->where('enable_sync', true)
            ->exists();
    }

    public function toggleSync()
    {
        if (!$this->enableSync) {
            // Show confirmation modal when enabling
            $this->showModal = true;
        } else {
            // Disable directly without confirmation
            $this->disableSync();
        }
    }

    public function confirmEnable()
    {
        // Enable sync for ALL admins (unified setting)
        User::where('is_admin', true)->update([
            'enable_sync' => true
        ]);

        $this->enableSync = true;
        $this->showModal = false;

        cache()->forget('snipe_sync_enabled');

        session()->flash('message', 'Snipe Sync enabled successfully for all admins!');
    }

    public function disableSync()
    {
        // Disable sync for ALL admins (unified setting)
        User::where('is_admin', true)->update([
            'enable_sync' => false
        ]);

        $this->enableSync = false;

        cache()->forget('snipe_sync_enabled');

        session()->flash('message', 'Snipe Sync disabled successfully for all admins!');
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.settings-action');
    }
}