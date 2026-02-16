<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Asset;
use App\Models\Employee;
use App\Models\Flag;
use App\Models\Category;
use Illuminate\Support\Facades\Log;
use Livewire\WithPagination;
use App\Models\History;

class EmployeeView extends Component
{   

    use WithPagination;
    
    protected $paginationTheme = 'tailwind';

    public $employee;

    public $flag_type, $asset, $selectedFlagId;

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

    public function resolveFlag($flagId)
    {
        try {
            $flag = Flag::find($flagId);

            if (!$flag) {
                $this->noreloadNotif('failed', 'Flag Not Found', 'The flag could not be found.');
                return;
            }

            $flag->delete();

            $this->noreloadNotif('success', 'Flag Resolved', 'The flag has been successfully resolved.');

        } catch (\Exception $e) {
            Log::error('Resolve flag failed: ' . $e->getMessage());
            $this->noreloadNotif('failed', 'Resolution Failed', 'Unable to resolve flag. Please try again.');
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

            Flag::where('target_id', $this->employee->id)->delete();

            $this->noreloadNotif('success', 'All Flags Resolved', $flagCount . ' flag(s) have been successfully resolved.');

        } catch (\Exception $e) {
            Log::error('Resolve all flags failed: ' . $e->getMessage());
            $this->noreloadNotif('failed', 'Resolution Failed', 'Unable to resolve all flags. Please try again.');
        }
    }

    public function unassignAsset($assetId)
    {
        try {
            $asset = Asset::find($assetId);

            if (!$asset) {
                $this->noreloadNotif('failed', 'Asset Not Found', 'The asset could not be found.');
                return;
            }

            // Save to history before unassigning
            History::create([
                'asset_id'      => $asset->id,
                'assignee_id'   => $this->employee->employee_id,
                'assignee_name' => $this->employee->employee_name,
                'status'        => $asset->status,
                'condition'     => $asset->condition,
                'farm'          => $asset->farm,
                'department'    => $asset->department,
                'location'      => $asset->location,
                'action'        => 'Unassigned',
            ]);

            // Clear assignment
            $asset->update([
                'assigned_id'   => null,
                'assigned_name' => null,
                'farm'          => null,
                'department'    => null,
                'location'      => null,
                'status'        => 'Available', // Change status back to Available
            ]);

            $this->noreloadNotif('success', 'Asset Unassigned', 'Asset ' . $asset->ref_id . ' has been successfully unassigned.');

        } catch (\Exception $e) {
            Log::error('Unassign asset failed: ' . $e->getMessage());
            $this->noreloadNotif('failed', 'Unassign Failed', 'Unable to unassign asset. Please try again.');
        }
    }

    public function unassignAllAssets()
    {
        try {
            $assets = Asset::where('assigned_id', $this->employee->id)->get();

            if ($assets->isEmpty()) {
                $this->noreloadNotif('failed', 'No Assets Found', 'This employee has no assigned assets.');
                return;
            }

            $count = 0;

            foreach ($assets as $asset) {
                // Save to history
                History::create([
                    'asset_id'      => $asset->id,
                    'assignee_id'   => $this->employee->employee_id,
                    'assignee_name' => $this->employee->employee_name,
                    'status'        => $asset->status,
                    'condition'     => $asset->condition,
                    'farm'          => $asset->farm,
                    'department'    => $asset->department,
                    'location'      => $asset->location,
                    'action'        => 'Unassigned (Bulk)',
                ]);

                // Clear assignment
                $asset->update([
                    'assigned_id'   => null,
                    'assigned_name' => null,
                    'farm'          => null,
                    'department'    => null,
                    'location'      => null,
                    'status'        => 'Available',
                ]);

                $count++;
            }

            $this->noreloadNotif('success', 'All Assets Unassigned', $count . ' asset(s) have been successfully unassigned from ' . $this->employee->employee_name . '.');

        } catch (\Exception $e) {
            Log::error('Unassign all assets failed: ' . $e->getMessage());
            $this->noreloadNotif('failed', 'Unassign Failed', 'Unable to unassign assets. Please try again.');
        }
    }

    public function render()
    {   
        $assets = Asset::where('is_deleted', false)->where('assigned_id', $this->employee->id)->latest()->paginate(10);
        $flags = Flag::where('target_id', $this->employee->id)->get();

        // Get categories as array with code as key
        $categoryCodeImage = Category::all()->keyBy('code');

        return view('livewire.employee-view', compact('assets', 'flags', 'categoryCodeImage'));
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