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

// UPDATE
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