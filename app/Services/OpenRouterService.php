<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenRouterService
{
    public function analyze(string $systemPrompt, string $userPrompt): string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.openrouter.api_key'),
            'HTTP-Referer'  => config('app.url'),
            'X-Title'       => config('app.name'),
        ])
        ->timeout(60)
        ->post('https://openrouter.ai/api/v1/chat/completions', [
            'model'    => config('services.openrouter.model', 'meta-llama/llama-3.3-70b-instruct'),
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user',   'content' => $userPrompt],
            ],
            'max_tokens' => 800,
        ]);

        if ($response->failed()) {
            throw new \RuntimeException('OpenRouter request failed: ' . $response->body());
        }

        return $response->json('choices.0.message.content') ?? '';
    }
}
