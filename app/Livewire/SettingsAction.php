<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class SettingsAction extends Component
{
    public $enableSync;
    public $showModal = false;

    public function mount()
    {
        $this->enableSync = Auth::user()->enable_sync ?? false;
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
        $user = Auth::user();
        $user->enable_sync = true;
        $user->save();

        $this->enableSync = true;
        $this->showModal = false;

        session()->flash('message', 'Snipe Sync enabled successfully!');
    }

    public function disableSync()
    {
        $user = Auth::user();
        $user->enable_sync = false;
        $user->save();

        $this->enableSync = false;

        session()->flash('message', 'Snipe Sync disabled successfully!');
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