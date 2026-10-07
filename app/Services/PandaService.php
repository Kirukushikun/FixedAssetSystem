<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PandaService
{
    /**
     * Fetch every employee (active and separated) from PandaSystem-v2.
     */
    public function getEmployees(): array
    {
        $response = Http::withToken(config('services.panda.token'))
            ->acceptJson()
            ->timeout(30)
            ->get(rtrim(config('services.panda.base_uri'), '/') . '/employees');

        $response->throw();

        return $response->json('employees') ?? [];
    }
}
