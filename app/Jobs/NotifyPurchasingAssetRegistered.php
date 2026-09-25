<?php

namespace App\Jobs;

use App\Models\Asset;
use App\Services\PurchasingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NotifyPurchasingAssetRegistered implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     */
    public $timeout = 60;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public $backoff = [30, 60, 120];

    /**
     * The asset instance.
     */
    protected $asset;

    public function __construct(Asset $asset)
    {
        $this->asset = $asset;
    }

    /**
     * Execute the job.
     */
    public function handle(PurchasingService $purchasingService): void
    {
        if (!$this->asset->purchase_reference_id) {
            return;
        }

        try {
            $result = $purchasingService->notifyRegistered(
                $this->asset->purchase_reference_id,
                $this->asset->ref_id,
                $this->asset->serial_no ?: null
            );

            $this->asset->update(['purchasing_synced_at' => now()]);

            Log::info('Purchasing System notified of asset registration', [
                'asset_id' => $this->asset->id,
                'ref_id' => $this->asset->ref_id,
                'purchase_reference_id' => $this->asset->purchase_reference_id,
                'result' => $result,
            ]);
        } catch (\Exception $e) {
            Log::error('Purchasing System registration callback failed', [
                'asset_id' => $this->asset->id,
                'ref_id' => $this->asset->ref_id,
                'purchase_reference_id' => $this->asset->purchase_reference_id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Purchasing System registration callback failed permanently', [
            'asset_id' => $this->asset->id,
            'ref_id' => $this->asset->ref_id,
            'purchase_reference_id' => $this->asset->purchase_reference_id,
            'error' => $exception->getMessage(),
        ]);
    }

    /**
     * Get tags for Horizon (if using Laravel Horizon)
     */
    public function tags(): array
    {
        return [
            'purchasing-callback',
            'asset:' . $this->asset->id,
            'ref:' . $this->asset->purchase_reference_id,
        ];
    }
}
