<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Asset;
use App\Models\Category;
use Illuminate\Support\Facades\Log;

class FarmAssets extends Component
{     
    public $farmCode;
    public $categoryCodeImage;
    public $assets = [];
    
    public function mount($farmCode)
    {   
        $this->categoryCodeImage = Category::all()->keyBy('code');
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
