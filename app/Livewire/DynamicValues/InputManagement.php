<?php

namespace App\Livewire\DynamicValues;

use Livewire\Component;
use App\Models\DynamicField;
use Illuminate\Support\Facades\Auth;
use Exception;

class InputManagement extends Component
{   
    public $inputType;
    public $fieldName;
    public $items = [];
    
    public $newValue = '';
    
    public $editId = null;
    public $editValue = '';

    public function mount($inputType)
    {
        $this->inputType = $inputType;
        $this->fieldName = ucwords(str_replace('_', ' ', $inputType));
        $this->loadData();
    }

    public function loadData()
    {
        try {
            $this->items = DynamicField::where('field', $this->inputType)
                ->orderBy('value')
                ->get();
        } catch (Exception $e) {
            $this->noreloadNotif('failed', 'Load Error', 'Failed to load data: ' . $e->getMessage());
        }
    }

    public function add()
    {
        try {
            if (!$this->newValue) {
                $this->noreloadNotif('failed', 'Validation Error', 'Value is required.');
                return;
            }

            // Check for duplicates
            $exists = DynamicField::where('field', $this->inputType)
                ->where('value', $this->newValue)
                ->exists();
            
            if ($exists) {
                $this->noreloadNotif('failed', 'Duplicate Entry', 'This value already exists.');
                return;
            }
            
            DynamicField::create([
                'field' => $this->inputType,
                'value' => $this->newValue
            ]);

            $this->audit("Added '{$this->newValue}' to {$this->fieldName}");
            
            $this->newValue = '';
            $this->loadData();
            
            $this->noreloadNotif('success', 'Value Added', 'Value has been successfully added.');
        } catch (Exception $e) {
            $this->noreloadNotif('failed', 'Add Error', 'Failed to add value: ' . $e->getMessage());
        }
    }

    public function startEdit($id)
    {
        try {
            $item = DynamicField::find($id);
            
            if (!$item) {
                $this->noreloadNotif('failed', 'Not Found', 'Value not found.');
                return;
            }
            
            // Check if any assets are using this value
            $assetCount = \App\Models\Asset::where($this->inputType, $item->value)->count();
            
            if ($assetCount > 0) {
                $this->noreloadNotif('failed', 'Cannot Edit', "Cannot edit '{$item->value}'. It is being used by {$assetCount} asset(s).");
                return;
            }
            
            $this->editId = $id;
            $this->editValue = $item->value;
        } catch (Exception $e) {
            $this->noreloadNotif('failed', 'Edit Error', 'Failed to load value: ' . $e->getMessage());
        }
    }

    public function saveEdit()
    {
        try {
            if (!$this->editValue) {
                $this->noreloadNotif('failed', 'Validation Error', 'Value is required.');
                return;
            }

            $item = DynamicField::find($this->editId);
            
            if (!$item) {
                $this->noreloadNotif('failed', 'Not Found', 'Value not found.');
                return;
            }
            
            // Check for duplicates (excluding current value)
            $exists = DynamicField::where('field', $this->inputType)
                ->where('value', $this->editValue)
                ->where('id', '!=', $this->editId)
                ->exists();
            
            if ($exists) {
                $this->noreloadNotif('failed', 'Duplicate Entry', 'This value already exists.');
                return;
            }
            
            $oldValue = $item->value;
            
            $item->update([
                'value' => $this->editValue
            ]);

            $this->audit("Updated {$this->fieldName} from '{$oldValue}' to '{$this->editValue}'");
            
            $this->cancelEdit();
            $this->loadData();
            
            $this->noreloadNotif('success', 'Value Updated', 'Value has been successfully updated.');
        } catch (Exception $e) {
            $this->noreloadNotif('failed', 'Update Error', 'Failed to update value: ' . $e->getMessage());
        }
    }

    public function cancelEdit()
    {
        $this->editId = null;
        $this->editValue = '';
    }

    public function delete($id)
    {
        try {
            $item = DynamicField::find($id);
            
            if (!$item) {
                $this->noreloadNotif('failed', 'Not Found', 'Value not found.');
                return;
            }
            
            // Check if any assets are using this value
            $assetCount = \App\Models\Asset::where($this->inputType, $item->value)->count();
            
            if ($assetCount > 0) {
                $this->noreloadNotif('failed', 'Cannot Delete', "Cannot delete '{$item->value}'. It is being used by {$assetCount} asset(s).");
                return;
            }
            
            $valueToDelete = $item->value;
            
            $item->delete();

            $this->audit("Deleted '{$valueToDelete}' from {$this->fieldName}");
            
            $this->loadData();
            
            $this->noreloadNotif('success', 'Value Deleted', 'Value has been successfully deleted.');
        } catch (Exception $e) {
            $this->noreloadNotif('failed', 'Delete Error', 'Failed to delete value: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.dynamic-values.input-management');
    }

    private function noreloadNotif($type, $header, $message)
    {
        $this->dispatch('notif', type: $type, header: $header, message: $message);
    }

    private function audit($action)
    {
        try {
            \App\Models\AuditTrail::create([
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'action' => $action,
            ]);
        } catch (Exception $e) {
            \Log::error('Audit trail error: ' . $e->getMessage());
        }
    }
}