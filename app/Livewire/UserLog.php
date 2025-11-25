<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AccessLog;

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
        $userLogs = AccessLog::latest()->paginate(10);
        return view('livewire.user-log', compact('userLogs'));
    }
}
