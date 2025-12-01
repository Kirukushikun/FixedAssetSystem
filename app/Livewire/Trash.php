<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Asset;
use Illuminate\Support\Facades\Log;

class Trash extends Component
{
    public function restoreAsset($assetId)
    {
        try {
            $asset = Asset::find($assetId);
            
            if (!$asset) {
                $this->noreloadNotif('failed', 'Asset Not Found', 'Asset not found in system.');
                return;
            }

            // Restore the asset by setting is_deleted to false
            $asset->is_deleted = false;
            $asset->save();

            $this->noreloadNotif('success', 'Asset Restored', 'Asset ' . $asset->ref_id . ' has been restored successfully.');
            Log::info('Asset restored: ' . $asset->ref_id);
            
        } catch (\Exception $e) {
            $this->noreloadNotif('failed', 'Restore Failed', 'Failed to restore asset: ' . $e->getMessage());
            Log::error('Restore asset error: ' . $e->getMessage());
        }
    }

    private function noreloadNotif($type, $header, $message)
    {
        $this->dispatch('notif', type: $type, header: $header, message: $message);
    }

    public function render()
    {   
        // Collect Assets marked as deleted
        $deletedAssets = Asset::where('is_deleted', true)->get();

        return view('livewire.trash', compact('deletedAssets'));
    }
}