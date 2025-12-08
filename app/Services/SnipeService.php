<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SnipeService
{
    // CREATE
    public function createAsset($data)
    {
        return Http::withToken(env('SNIPE_TOKEN'))
            ->acceptJson()
            ->post(env('SNIPE_URL') . '/hardware', $data)
            ->json();
    }

<<<<<<< HEAD
        // UPDATE
=======
    // UPDATE
>>>>>>> cb7ff4ac48c37cb98a8849666a1d56f7d40561a8
    public function updateAsset($id, $data)
    {
        return Http::withToken(env('SNIPE_TOKEN'))
            ->acceptJson()
            ->put(env('SNIPE_URL') . "/hardware/{$id}", $data)
            ->json();
    }

    // DELETE
    public function deleteAsset($id)
    {
        return Http::withToken(env('SNIPE_TOKEN'))
            ->acceptJson()
            ->delete(env('SNIPE_URL') . "/hardware/{$id}")
            ->json();
    }
}