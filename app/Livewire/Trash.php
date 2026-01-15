<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Asset;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

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

            // Clear all relevant caches after restore
            $this->clearAllAssetCaches();

            $this->noreloadNotif('success', 'Asset Restored', 'Asset ' . $asset->ref_id . ' has been restored successfully.');
            Log::info('Asset restored: ' . $asset->ref_id);

            
        } catch (\Exception $e) {
            $this->noreloadNotif('failed', 'Restore Failed', 'Failed to restore asset: ' . $e->getMessage());
            Log::error('Restore asset error: ' . $e->getMessage());
        }
    }

    public function permanentDelete($assetId)
    {
        try {
            $asset = Asset::find($assetId);
            
            if (!$asset) {
                $this->noreloadNotif('failed', 'Asset Not Found', 'Asset not found in system.');
                return;
            }

            $refId = $asset->ref_id;
            
            // Permanently delete the asset from database
            $asset->delete();

            // Clear all relevant caches after permanent deletion
            $this->clearAllAssetCaches();

            $this->noreloadNotif('success', 'Asset Deleted', 'Asset ' . $refId . ' has been permanently deleted.');
            Log::info('Asset permanently deleted: ' . $refId);

            
        } catch (\Exception $e) {
            $this->noreloadNotif('failed', 'Delete Failed', 'Failed to permanently delete asset: ' . $e->getMessage());
            Log::error('Permanent delete asset error: ' . $e->getMessage());
        }
    }

    private function clearAllAssetCaches()
    {
        // Clear trash cache
        Cache::forget('trash_deleted_assets');
        
        // Clear asset table caches
        Cache::forget('asset_table_query');
        
        // Clear API caches
        Cache::forget('api.assets.index');
    }

    private function noreloadNotif($type, $header, $message)
    {
        $this->dispatch('notif', type: $type, header: $header, message: $message);
    }

    public function render()
    {   
        // Cache deleted assets for 10 minutes
        $deletedAssets = Cache::remember('trash_deleted_assets', 600, function () {
            return Asset::where('is_deleted', true)->get();
        });

        return view('livewire.trash', compact('deletedAssets'));
    }
}