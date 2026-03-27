<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Asset;
use Illuminate\Support\Facades\Cache;
use App\Models\Category;

class QrManagement extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $search = '';
    public $filterFarm = '';
    public $filterPrinted = '';
    public $filterAffixed = '';
    public $selectedAssets = [];
    public $selectAll = false;

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedAssets = $this->getPageAssetIds();
        } else {
            $this->selectedAssets = [];
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    private function getPageAssetIds()
    {
        return $this->buildQuery()
            ->paginate(15)
            ->pluck('id')
            ->map(fn($id) => (string) $id)
            ->toArray();
    }

    public function togglePrinted($assetId)
    {
        $asset = Asset::findOrFail($assetId);
        $newValue = !$asset->qr_printed;

        // If this asset is in the selection, bulk update all selected
        if (in_array((string) $assetId, array_map('strval', $this->selectedAssets))) {
            Asset::whereIn('id', $this->selectedAssets)
                ->update(['qr_printed' => $newValue]);
        } else {
            $asset->update(['qr_printed' => $newValue]);
        }
    }

    public function toggleAffixed($assetId)
    {
        $asset = Asset::findOrFail($assetId);
        $newValue = !$asset->qr_affixed;

        // If this asset is in the selection, bulk update all selected
        if (in_array((string) $assetId, array_map('strval', $this->selectedAssets))) {
            Asset::whereIn('id', $this->selectedAssets)
                ->update(['qr_affixed' => $newValue]);
        } else {
            $asset->update(['qr_affixed' => $newValue]);
        }
    }

    public function printSelected()
    {
        if (empty($this->selectedAssets)) return;

        $encryptedIds = array_map(fn($id) => encrypt($id), $this->selectedAssets);
        $ids = implode(',', $encryptedIds);

        // Reset selection before redirecting
        $this->selectedAssets = [];
        $this->selectAll = false;

        return redirect('/assetmanagement/qr/print?ids=' . $ids);
    }

    private function buildQuery()
    {
        $query = Asset::select([
            'id', 'ref_id', 'category', 'brand', 'model',
            'farm', 'assigned_name', 'qr_code', 'qr_printed', 'qr_affixed'
        ])
        ->where('is_deleted', false)
        ->where('is_archived', false)
        ->whereNotNull('qr_code');

        if ($this->search) {
            $search = '%' . $this->search . '%';
            $query->where(function($q) use ($search) {
                $q->where('ref_id', 'LIKE', $search)
                  ->orWhere('brand', 'LIKE', $search)
                  ->orWhere('model', 'LIKE', $search);
            });
        }

        $query->when($this->filterFarm, fn($q) => $q->where('farm', $this->filterFarm));
        $query->when($this->filterPrinted !== '', fn($q) => $q->where('qr_printed', $this->filterPrinted));
        $query->when($this->filterAffixed !== '', fn($q) => $q->where('qr_affixed', $this->filterAffixed));

        return $query->orderBy('ref_id');
    }

    public function render()
    {
        $assets = $this->buildQuery()->paginate(15);

        $categoryCodeImage = Cache::remember('categories_by_code', 3600, function () {
            return Category::all()->keyBy('code');
        });

        return view('livewire.qr-management', compact('assets', 'categoryCodeImage'));
    }
}