<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SnipeService
{
    // CREATE
    public function createAsset($data)
    {
        return Http::withToken(config('services.snipe.token'))
            ->acceptJson()
            ->post(config('services.snipe.url') . '/hardware', $data)
            ->json();
    }

    // UPDATE
    public function updateAsset($id, $data)
    {
        return Http::withToken(config('services.snipe.token'))
            ->acceptJson()
            ->put(config('services.snipe.url') . "/hardware/{$id}", $data)
            ->json();
    }

    // DELETE
    public function deleteAsset($id)
    {
        return Http::withToken(config('services.snipe.token'))
            ->acceptJson()
            ->delete(config('services.snipe.url') . "/hardware/{$id}")
            ->json();
    }
}