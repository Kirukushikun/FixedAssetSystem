<?php

namespace App\Console\Commands;

use App\Jobs\GenerateAssetQrCode;
use App\Models\Asset;
use Illuminate\Console\Command;

class GenerateMissingQrCodes extends Command
{
    protected $signature = 'assets:generate-missing-qr';

    protected $description = 'Queue QR code generation for every asset that does not have one yet';

    public function handle(): int
    {
        $query = Asset::where('is_deleted', false)
            ->where(fn ($q) => $q->whereNull('qr_code')->orWhere('qr_code', ''));

        $total = $query->count();

        if ($total === 0) {
            $this->info('Every asset already has a QR code.');
            return self::SUCCESS;
        }

        $query->chunkById(200, function ($assets) {
            foreach ($assets as $asset) {
                GenerateAssetQrCode::dispatch($asset);
            }
        });

        $this->info("Queued QR generation for {$total} assets. The queue worker will process them in the background.");

        return self::SUCCESS;
    }
}
