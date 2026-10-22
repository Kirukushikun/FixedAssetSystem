<?php

namespace App\Jobs;

use App\Imports\AssetMigrationImport;
use App\Models\Employee;
use App\Models\SystemNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Runs a confirmed migration import outside the web request, so large files
 * are not cut off by the web server / Cloudflare timeout. All-or-nothing:
 * a failure rolls back every row, so the file can simply be uploaded again.
 */
class RunAssetMigrationImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    // Never retry: a retry while the first attempt is still running would import twice.
    public $tries = 1;

    // Must stay below the queue's retry_after (REDIS_QUEUE_RETRY_AFTER).
    public $timeout = 600;

    /**
     * @param array<string, int>  $matches              name as written in the file => confirmed employees.id
     * @param array<string, ?int> $subCategoryMappings  sub-category as written in the file => subcategories.id, null = skip
     */
    public function __construct(
        private string $path,
        private string $fileName,
        private array $matches,
        private bool $linkAvailable,
        private int $userId,
        private array $subCategoryMappings = [],
    ) {}

    public function handle(): void
    {
        $employees = Employee::whereIn('id', array_values($this->matches))->get()->keyBy('id');
        $matches = collect($this->matches)
            ->map(fn ($id) => $employees->get($id))
            ->filter()
            ->all();

        $import = new AssetMigrationImport($matches, $this->linkAvailable, $this->subCategoryMappings);

        DB::transaction(fn () => Excel::import($import, $this->path, 'local'));

        Storage::disk('local')->delete($this->path);
        Cache::flush();

        $this->notify(
            'Migration import complete',
            "{$this->fileName}: {$import->createdCount} assets added ({$import->linkedCount} linked to employees), "
                . "{$import->skippedCount} skipped (duplicates or missing farm).",
        );
    }

    public function failed(\Throwable $e): void
    {
        Log::error('Asset migration import failed', ['file' => $this->fileName, 'error' => $e->getMessage()]);

        Storage::disk('local')->delete($this->path);

        $this->notify(
            'Migration import failed',
            "{$this->fileName} was not imported. No assets were added, so you can fix the file and upload it again.",
        );
    }

    private function notify(string $title, string $message): void
    {
        SystemNotification::create([
            'user_id' => $this->userId,
            'type'    => 'import',
            'title'   => $title,
            'message' => $message,
            'url'     => '/assetmanagement',
        ]);
    }
}
