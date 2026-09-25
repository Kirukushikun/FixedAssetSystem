<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PurchasingService
{
    /**
     * Fetch the list of PR'd items that are awaiting Fixed Asset creation.
     */
    public function getPendingAssets(): array
    {
        $response = Http::withToken(config('services.purchasing.token'))
            ->acceptJson()
            ->timeout(15)
            ->post(config('services.purchasing.base_uri') . '/fixed-assets/pending');

        $response->throw();

        return $response->json();
    }

    /**
     * Check connectivity/auth against the Purchasing System.
     */
    public function healthCheck(): bool
    {
        try {
            $response = Http::withToken(config('services.purchasing.token'))
                ->acceptJson()
                ->timeout(10)
                ->post(config('services.purchasing.base_uri') . '/health');

            return $response->successful();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Notify the Purchasing System that a pending item has been registered as a Fixed Asset.
     */
    public function notifyRegistered(string $referenceId, string $famsAssetId, ?string $serialNumber): array
    {
        $response = Http::withToken(config('services.purchasing.token'))
            ->acceptJson()
            ->timeout(15)
            ->post(config('services.purchasing.base_uri') . '/fixed-assets/callbacks/registered', [
                'items' => [
                    [
                        'reference_id' => $referenceId,
                        'fams_asset_id' => $famsAssetId,
                        'serial_number' => $serialNumber,
                    ],
                ],
            ]);

        $response->throw();

        return $response->json();
    }
}
