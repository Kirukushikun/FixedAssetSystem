<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Asset;
use App\Models\Employee;
use App\Models\Flag;
use Illuminate\Support\Facades\Log;
use Livewire\WithPagination;

class EmployeeView extends Component
{   

    use WithPagination;
    
    protected $paginationTheme = 'tailwind';

    public $employee;

    public $flag_type, $asset;

    public function goToPage($page)
    {
       $this->setPage($page);
    }

    public function mount($targetID){
        $this->employee = Employee::find($targetID);
    }

    public function submitFlag(){
        try {
            Flag::create([
                'target_id' => $this->employee->id,
                'flag_type' => $this->flag_type,
                'asset' => $this->asset
            ]);

            // Clear form
            $this->reset(['flag_type', 'asset']);

            // Use NORELOAD - stays on same page, flags list refreshes
            $this->noreloadNotif('success', 'Flag Added', 'Flag has been successfully added to ' . $this->employee->employee_name . '.');

        } catch (\Exception $e) {
            Log::error('Flag creation failed: ' . $e->getMessage());
            $this->noreloadNotif('failed', 'Flag Creation Failed', 'Unable to create flag. Please try again.');
        }


    }

    public function resolveAllFlags()
    {
        try {
            $flagCount = Flag::where('target_id', $this->employee->id)->count();

            if ($flagCount === 0) {
                $this->noreloadNotif('failed', 'No Flags Found', 'There are no active flags to resolve.');
                return;
            }

            Flag::where('target_id', $this->employee->id)->delete(); // or update status

            // Use NORELOAD - stays on same page, flags list refreshes
            $this->noreloadNotif('success', 'All Flags Resolved', $flagCount . ' flag(s) have been successfully resolved.');

        } catch (\Exception $e) {
            Log::error('Resolve all flags failed: ' . $e->getMessage());
            $this->noreloadNotif('failed', 'Resolution Failed', 'Unable to resolve all flags. Please try again.');
        }
    }

    public function render()
    {   
        $assets = Asset::where('assigned_id', $this->employee->id)->latest()->paginate(10);
        $flags = Flag::where('target_id', $this->employee->id)->get();

        return view('livewire.employee-view', compact('assets', 'flags'));
    }

    private function noreloadNotif($type, $header, $message)
    {
        $this->dispatch('notif', type: $type, header: $header, message: $message);
    }

    private function reloadNotif($type, $header, $message)
    {
        session()->flash('notif', [
            'type' => $type,
            'header' => $header,
            'message' => $message
        ]);
    }
}
