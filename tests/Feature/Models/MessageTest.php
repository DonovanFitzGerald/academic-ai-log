<?php

namespace Tests\Feature\Models;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_mass_assigns_attributes()
    {
        $chat = Chat::factory()->create();

        $message = Message::create([
            'chat_id' => $chat->id,
            'role' => 'user',
            'content' => 'Hello',
            'raw_json' => ['test' => 'value'],
            'sequence' => 1,
            'model' => 'default-model',
        ]);

        $this->assertEquals($chat->id, $message->chat_id);
        $this->assertEquals('user', $message->role);
        $this->assertEquals('Hello', $message->content);
        $this->assertEquals(1, $message->sequence);
        $this->assertEquals('default-model', $message->model);
    }

    public function test_it_casts_raw_json_to_array()
    {
        $chat = Chat::factory()->create();

        $message = Message::create([
            'chat_id' => $chat->id,
            'role' => 'user',
            'content' => 'Hello',
            'sequence' => 1,
            'raw_json' => ['key' => 'value'],
        ]);

        $message->refresh();

        $this->assertIsArray($message->raw_json);
        $this->assertEquals(['key' => 'value'], $message->raw_json);
    }
}
