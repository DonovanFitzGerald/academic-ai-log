<?php

namespace Tests\Feature\Models;

use App\Models\Chat;
use App\Models\UseLog;
use App\Models\UseLogCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UseLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_mass_assigns_attributes()
    {
        $chat = Chat::factory()->create();

        $useLog = UseLog::create([
            'chat_id' => $chat->id,
            'total_use_cases' => 3,
            'raw_output' => '{"some": "json"}',
            'chat_snapshot' => '1. user: hello',
        ]);

        $this->assertEquals($chat->id, $useLog->chat_id);
        $this->assertEquals(3, $useLog->total_use_cases);
        $this->assertEquals('{"some": "json"}', $useLog->raw_output);
        $this->assertEquals('1. user: hello', $useLog->chat_snapshot);
    }

    public function test_it_has_many_use_cases()
    {
        $useLog = UseLog::factory()->create();
        $useLogCase = UseLogCase::factory()->create(['use_log_id' => $useLog->id]);

        $this->assertTrue($useLog->use_cases->contains($useLogCase));
        $this->assertEquals(1, $useLog->use_cases->count());
        $this->assertInstanceOf(UseLogCase::class, $useLog->use_cases->first());
    }
}
