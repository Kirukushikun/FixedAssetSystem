<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Asset;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class Trash extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function restoreAsset($assetId)
    {
        try {
            $asset = Asset::find($assetId);

            if (!$asset) {
                $this->noreloadNotif('failed', 'Asset Not Found', 'Asset not found in system.');
                return;
            }

            $asset->is_deleted = false;
            $asset->save();

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
            $asset->delete();

            $this->clearAllAssetCaches();
            $this->noreloadNotif('success', 'Asset Deleted', 'Asset ' . $refId . ' has been permanently deleted.');
            Log::info('Asset permanently deleted: ' . $refId);

        } catch (\Exception $e) {
            $this->noreloadNotif('failed', 'Delete Failed', 'Failed to permanently delete asset: ' . $e->getMessage());
            Log::error('Permanent delete asset error: ' . $e->getMessage());
        }
    }

    private function clearAllAssetCaches(): void
    {
        Cache::forget('trash_deleted_assets');
        Cache::forget('asset_table_query');
        Cache::forget('api.assets.index');
    }

    private function noreloadNotif($type, $header, $message): void
    {
        $this->dispatch('notif', type: $type, header: $header, message: $message);
    }

    public function render()
    {
        $deletedAssets = Asset::where('is_deleted', true)
            ->when($this->search, fn($q) =>
                $q->where('ref_id', 'like', "%{$this->search}%")
            )
            ->latest('updated_at')
            ->paginate(10);

        return view('livewire.trash', compact('deletedAssets'));
    }
}