<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SnipeService
{
    public function createAsset($data)
    {
        return Http::withToken(env('SNIPE_TOKEN'))
            ->acceptJson()
            ->post(env('SNIPE_URL') . '/hardware', $data)
            ->json();
    }
}