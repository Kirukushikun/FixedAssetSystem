<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Asset;
use Illuminate\Support\Facades\Log;

class FarmAssets extends Component
{     
    public $farmCode;
    public $assets = [];

    public function mount($farmCode)
    {
        $this->farmCode = $farmCode;
        $this->loadAssets();
    }

    public function loadAssets()
    {
        // Simulate loading or fetch real data
        $this->assets = Asset::where('farm', $this->farmCode)->get();
        
        // Dispatch event to hide loading
        $this->dispatch('dataLoaded');
    }

    public function render()
    {
        return view('livewire.farm-assets');
    }
}
