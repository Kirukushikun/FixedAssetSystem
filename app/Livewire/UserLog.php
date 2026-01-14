<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AccessLog;
use Illuminate\Support\Facades\Cache;

class UserLog extends Component
{   
    use WithPagination;
    
    protected $paginationTheme = 'tailwind';


    public function goToPage($page)
    {
       $this->setPage($page);
    }

    public function render()
    {   
        // Create unique cache key based on current page
        $cacheKey = 'user_log_' . $this->getPage();

        // Cache the user logs for 5 minutes (300 seconds)
        $userLogs = Cache::remember($cacheKey, 300, function () {
            return AccessLog::latest()->paginate(10);
        });

        return view('livewire.user-log', compact('userLogs'));
    }
}