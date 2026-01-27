<?php

namespace App\Jobs;

use App\Models\Asset;
use App\Services\SnipeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SyncAssetToSnipeIT implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     */
    public $timeout = 120;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public $backoff = [30, 60, 120]; // Retry after 30s, 60s, 120s

    /**
     * The asset instance.
     */
    protected $asset;

    /**
     * The action to perform (create, update, or delete).
     */
    protected $action;

    /**
     * Create a new job instance.
     */
    public function __construct(Asset $asset, string $action = 'create')
    {
        $this->asset = $asset;
        $this->action = $action;
    }

    /**
     * Execute the job.
     */
    public function handle(SnipeService $snipeService): void
    {
        // Check if sync is enabled
        $syncEnabled = Cache::remember('snipe_sync_enabled', 3600, function () {
            return \App\Models\User::where('is_admin', true)
                ->where('enable_sync', true)
                ->exists();
        });

        if (!$syncEnabled) {
            Log::info('Snipe-IT Sync Skipped: No admin has enabled sync', [
                'asset_id' => $this->asset->id,
                'ref_id' => $this->asset->ref_id,
                'action' => $this->action
            ]);
            return;
        }

        try {
            $result = match($this->action) {
                'create' => $this->createAsset($snipeService),
                'update' => $this->updateAsset($snipeService),
                'delete' => $this->deleteAsset($snipeService),
                default => throw new \InvalidArgumentException("Invalid action: {$this->action}")
            };

            Log::info('Snipe-IT Sync Success', [
                'asset_id' => $this->asset->id,
                'ref_id' => $this->asset->ref_id,
                'action' => $this->action,
                'result' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Snipe-IT Sync Failed', [
                'asset_id' => $this->asset->id,
                'ref_id' => $this->asset->ref_id,
                'action' => $this->action,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Re-throw to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Create asset in Snipe-IT
     */
    private function createAsset(SnipeService $snipeService): array
    {
        $data = [
            "asset_tag" => $this->asset->ref_id,
            "serial" => $this->asset->model ?? 'N/A',
            "model_id" => 1,
            "status_id" => 2,
            "name" => "",
            "purchase_date" => $this->asset->acquisition_date ? 
                \Carbon\Carbon::parse($this->asset->acquisition_date)->format('Y-m-d') : null,
            "purchase_cost" => $this->asset->item_cost,
            "company_id" => 3,
            "location_id" => 3,
            "rtd_location_id" => 3,
            "notes" => "Synced from Asset Management System on " . now()->format('Y-m-d H:i:s'),
        ];

        Log::info('Creating asset in Snipe-IT', [
            'asset_id' => $this->asset->id,
            'data' => $data
        ]);

        $result = $snipeService->createAsset($data);

        if (isset($result['payload']['id'])) {
            $this->asset->update(['snipe_id' => $result['payload']['id']]);
        }

        return $result;
    }

    /**
     * Update asset in Snipe-IT
     */
    private function updateAsset(SnipeService $snipeService): array
    {
        if (!$this->asset->snipe_id) {
            throw new \Exception('Cannot update: Asset does not have a snipe_id');
        }

        $data = [
            "asset_tag" => $this->asset->ref_id,
            "serial" => $this->asset->model ?? 'N/A',
            "name" => "",
            "purchase_date" => $this->asset->acquisition_date ? 
                \Carbon\Carbon::parse($this->asset->acquisition_date)->format('Y-m-d') : null,
            "purchase_cost" => $this->asset->item_cost,
            "company_id" => 3,
            "location_id" => 3,
            "rtd_location_id" => 3,
            "notes" => "Updated from Asset Management System on " . now()->format('Y-m-d H:i:s'),
        ];

        Log::info('Updating asset in Snipe-IT', [
            'snipe_id' => $this->asset->snipe_id,
            'data' => $data
        ]);

        return $snipeService->updateAsset($this->asset->snipe_id, $data);
    }

    /**
     * Delete asset from Snipe-IT
     */
    private function deleteAsset(SnipeService $snipeService): array
    {
        if (!$this->asset->snipe_id) {
            Log::warning('Cannot delete from Snipe-IT: Asset does not have a snipe_id', [
                'asset_id' => $this->asset->id,
                'ref_id' => $this->asset->ref_id
            ]);
            return ['status' => 'skipped', 'message' => 'No snipe_id found'];
        }

        Log::info('Deleting asset from Snipe-IT', [
            'snipe_id' => $this->asset->snipe_id,
            'ref_id' => $this->asset->ref_id
        ]);

        $result = $snipeService->deleteAsset($this->asset->snipe_id);

        if (isset($result['status']) && $result['status'] === 'success') {
            $this->asset->update(['snipe_id' => null]);
        }

        return $result;
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Snipe-IT Sync Failed Permanently', [
            'asset_id' => $this->asset->id,
            'ref_id' => $this->asset->ref_id,
            'action' => $this->action,
            'error' => $exception->getMessage()
        ]);

        // Optional: Send notification to admin
        // You can implement notification logic here if needed
    }

    /**
     * Get tags for Horizon (if using Laravel Horizon)
     */
    public function tags(): array
    {
        return [
            'snipe-sync',
            'asset:' . $this->asset->id,
            'action:' . $this->action,
            'ref:' . $this->asset->ref_id
        ];
    }
}