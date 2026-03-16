<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\UseLog;
use App\Services\AssistantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UseLogController extends Controller
{
    public function store(Request $request, Chat $chat, AssistantService $assistantService)
    {
        $useLogData = $assistantService->createUseLog($chat);

        $chatSnapshot = $chat->messages()
            ->orderBy('sequence')
            ->get(['role', 'content', 'sequence'])
            ->map(fn($message) => "{$message->sequence}. {$message->role}: {$message->content}")
            ->implode("\n");

        $useLog = DB::transaction(function () use ($chat, $useLogData, $chatSnapshot) {
            return UseLog::create([
                'chat_id' => $chat->id,
                'total_use_cases' => $useLogData['total_use_cases'],
                'raw_output' => json_encode($useLogData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
                'chat_snapshot' => $chatSnapshot,
            ]);
        });

        return response()->json([
            'useLog' => $useLog,
            'parsed' => $useLogData,
        ]);
    }
}