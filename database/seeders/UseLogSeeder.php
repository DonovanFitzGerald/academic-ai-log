<?php

namespace Database\Seeders;

use App\Models\Chat;
use App\Models\UseLog;
use App\Models\UseLogCase;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UseLogSeeder extends Seeder
{
    public function run(): void
    {
        $chats = Chat::query()->take(10)->get();

        foreach ($chats as $chat) {
            DB::transaction(function () use ($chat) {
                $caseCount = rand(2, 6);

                $useLog = UseLog::factory()->create([
                    'chat_id' => $chat->id,
                ]);

                $cases = collect();

                for ($i = 1; $i <= $caseCount; $i++) {
                    $cases->push(
                        UseLogCase::factory()->create([
                            'use_log_id' => $useLog->id,
                            'position' => $i,
                        ])
                    );
                }

                $payload = [
                    'total_use_cases' => $caseCount,
                    'use_cases' => $cases->map(fn($c) => [
                        'label' => $c->label,
                        'evidence' => $c->evidence,
                        'input_type' => $c->input_type,
                        'output_type' => $c->output_type,
                        'assistant_role' => $c->assistant_role,
                        'confidence' => $c->confidence,
                    ])->values()->all(),
                    'summary_statement' => $useLog->summary_statement,
                ];

                $useLog->update([
                    'total_use_cases' => $caseCount,
                    'raw_output' => json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
                ]);
            });
        }
    }
}