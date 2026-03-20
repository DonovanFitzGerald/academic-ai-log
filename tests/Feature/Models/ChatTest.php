<?php

namespace Tests\Feature\Models;

use App\Models\Chat;
use App\Models\Message;
use App\Models\UseLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_mass_assigns_title()
    {
        $chat = Chat::create(['title' => 'My first chat']);
        $this->assertEquals('My first chat', $chat->title);
    }

    public function test_it_has_many_messages()
    {
        $chat = Chat::factory()->create();
        $message = Message::factory()->create(['chat_id' => $chat->id]);

        $this->assertTrue($chat->messages->contains($message));
        $this->assertEquals(1, $chat->messages->count());
        $this->assertInstanceOf(Message::class, $chat->messages->first());
    }

    public function test_it_has_many_use_logs()
    {
        $chat = Chat::factory()->create();
        $useLog = UseLog::factory()->create(['chat_id' => $chat->id]);

        $this->assertTrue($chat->useLogs->contains($useLog));
        $this->assertEquals(1, $chat->useLogs->count());
        $this->assertInstanceOf(UseLog::class, $chat->useLogs->first());
    }
}
