<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\AuditTrail as AuditModel;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Cache;

class AuditTrail extends Component
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
        $cacheKey = 'audit_trail_' . $this->getPage();

        // Cache the audit trail for 5 minutes (300 seconds)
        $audits = Cache::remember($cacheKey, 300, function () {
            return AuditModel::latest()->paginate(10);
        });

        return view('livewire.audit-trail', compact('audits'));
    }
}