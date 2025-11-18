<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\AuditTrail as AuditModel;
use Livewire\WithPagination;

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
        $audits = AuditModel::latest()->paginate(10);
        return view('livewire.audit-trail', compact('audits'));
    }
}
