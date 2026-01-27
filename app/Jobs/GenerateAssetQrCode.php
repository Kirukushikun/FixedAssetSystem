<?php

namespace App\Jobs;

use App\Models\Asset;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GenerateAssetQrCode implements ShouldQueue
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
    public $backoff = [10, 30, 60]; // Retry after 10s, 30s, 60s

    /**
     * The asset instance.
     */
    protected $asset;

    /**
     * Create a new job instance.
     */
    public function __construct(Asset $asset)
    {
        $this->asset = $asset;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $url = url('/assetmanagement/view?targetID=' . $this->asset->id);
            $qrFileName = 'qr_' . $this->asset->id . '.svg';
            $qrPath = storage_path('app/public/qrcodes/' . $qrFileName);

            // Ensure directory exists
            $directory = dirname($qrPath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            // Generate QR code
            QrCode::format('svg')
                ->size(300)
                ->generate($url, $qrPath);

            // Update asset with QR code path
            $this->asset->update(['qr_code' => 'qrcodes/' . $qrFileName]);

            Log::info('QR Code generated successfully', [
                'asset_id' => $this->asset->id,
                'ref_id' => $this->asset->ref_id,
                'qr_path' => $qrFileName
            ]);

        } catch (\Exception $e) {
            Log::error('QR Code generation failed', [
                'asset_id' => $this->asset->id,
                'ref_id' => $this->asset->ref_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Re-throw to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('QR Code generation failed permanently', [
            'asset_id' => $this->asset->id,
            'ref_id' => $this->asset->ref_id,
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
            'qr-generation',
            'asset:' . $this->asset->id,
            'ref:' . $this->asset->ref_id
        ];
    }
}