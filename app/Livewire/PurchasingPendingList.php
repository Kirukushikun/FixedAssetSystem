<?php

namespace App\Livewire;

use App\Models\Asset;
use App\Services\PurchasingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class PurchasingPendingList extends Component
{
    protected $listeners = ['purchasing-modal-opened' => 'open'];

    public array $items = [];
    public bool $loading = false;
    public ?string $error = null;
    public bool $loaded = false;

    /**
     * Fetch the pending list the first time the "From Purchasing System" screen is opened.
     * The component is always present in the Add Asset modal's markup, so fetching is
     * deferred to this listener instead of mount() to avoid an outbound API call on
     * every asset table page load.
     */
    public function open(): void
    {
        if ($this->loaded) {
            return;
        }

        $this->loading = true;
        $this->fetchPending();
    }

    public function retry(): void
    {
        $this->loading = true;
        $this->error = null;
        $this->fetchPending();
    }

    public function selectItem(string $referenceId): void
    {
        if (!Auth::user()?->hasPermission('assets.create')) {
            return;
        }

        $item = collect($this->items)->firstWhere('reference_id', $referenceId);

        if (!$item) {
            $this->error = 'That item is no longer available. Please refresh the list.';
            return;
        }

        Cache::put("purchasing_pending_item:{$referenceId}", $item, now()->addMinutes(30));

        $this->dispatch('purchasing-item-selected', referenceId: $referenceId);
    }

    private function fetchPending(): void
    {
        if (!Auth::user()?->hasPermission('assets.create')) {
            $this->items = [];
            $this->loading = false;
            $this->loaded = true;
            $this->error = 'You do not have permission to create assets.';
            return;
        }

        try {
            $result = app(PurchasingService::class)->getPendingAssets();

            $alreadyImported = Asset::whereNotNull('purchase_reference_id')
                ->pluck('purchase_reference_id')
                ->all();

            $this->items = collect($result['data'] ?? [])
                ->reject(fn ($item) => in_array($item['reference_id'] ?? null, $alreadyImported, true))
                ->values()
                ->all();
        } catch (\Throwable $e) {
            Log::error('Failed to fetch pending items from Purchasing System', [
                'error' => $e->getMessage(),
            ]);

            $this->items = [];
            $this->error = 'Unable to reach the Purchasing System. Please try again later.';
        } finally {
            $this->loading = false;
            $this->loaded = true;
        }
    }

    public function render()
    {
        return view('livewire.purchasing-pending-list');
    }
}
