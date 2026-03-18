<?php

namespace Database\Factories;

use App\Models\Chat;
use App\Models\UseLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class UseLogFactory extends Factory
{
    protected $model = UseLog::class;

    public function definition(): array
    {
        $summary = $this->faker->sentence(14);

        // Placeholder; in the seeder we will overwrite with consistent JSON
        $payload = [
            'total_use_cases' => 0,
            'use_cases' => [],
            'summary_statement' => $summary,
        ];

        return [
            'chat_id' => Chat::factory(),
            'total_use_cases' => 0,
            'summary_statement' => $summary,
            'raw_output' => json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            'chat_snapshot' => null,
        ];
    }
}