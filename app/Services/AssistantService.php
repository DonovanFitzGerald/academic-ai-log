<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class AssistantService
{
    public function call(string $content): array
    {
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3-flash-preview:generateContent';

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'x-goog-api-key' => config('services.google.api_key'),
        ])->post($url, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $content],
                            ],
                        ],
                    ],
                ]);

        if ($response->failed()) {
            throw new RuntimeException('Assistant request failed: ' . $response->body());
        }

        return $response->json();
    }
}