<?php

namespace App\Services;

use App\Models\Chat;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class AssistantService
{
    public function call(Chat $chat): array
    {
        $contents = $this->buildContentsFromChat($chat, includeAssistant: true);

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite-preview:generateContent';

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'x-goog-api-key' => config('services.google.api_key'),
        ])->post($url, [
                    'contents' => $contents,
                ]);

        if ($response->failed()) {
            throw new RuntimeException('Assistant request failed: ' . $response->body());
        }

        return $response->json();
    }

    public function createUseLog(Chat $chat): array
    {
        $promptPath = resource_path('prompts/AI_LOG_FORMATTER.md');
        $systemPrompt = file_get_contents($promptPath);

        if (!is_string($systemPrompt) || trim($systemPrompt) === '') {
            throw new RuntimeException("AI log prompt file missing/empty: {$promptPath}");
        }

        $contents = $this->buildContentsFromChat($chat, includeAssistant: true);

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite-preview:generateContent';

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'x-goog-api-key' => config('services.google.api_key'),
        ])->post($url, [
                    'system_instruction' => [
                        'parts' => [
                            ['text' => $systemPrompt],
                        ],
                    ],
                    'contents' => $contents,
                ]);

        if ($response->failed()) {
            throw new RuntimeException('Use log request failed: ' . $response->body());
        }

        return $response->json();
    }

    private function buildContentsFromChat(Chat $chat, bool $includeAssistant): array
    {
        $query = $chat->messages()->orderBy('sequence')->get(['role', 'content']);

        $messages = $includeAssistant
            ? $query
            : $query->where('role', 'user');

        return $messages
            ->map(function ($message) {
                return [
                    'role' => $message->role === 'assistant' ? 'model' : 'user',
                    'parts' => [
                        ['text' => $message->content],
                    ],
                ];
            })
            ->values()
            ->all();
    }
}