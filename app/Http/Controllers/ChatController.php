<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function store(Request $request)
    {
        $chat = Chat::create([
            'title' => 'New chat',
        ]);

        return redirect()->route('chats.show', $chat)->setStatusCode(303);
    }

    public function show(Chat $chat)
    {
        return inertia('chats/show', [
            'chat' => $chat,
            'messages' => $chat->messages()
                ->orderBy('sequence')
                ->get(),
            'useLog' => $chat->useLogs()
                ->with(['use_cases' => fn($row) => $row->orderBy('position')])
                ->latest('created_at')
                ->first(),
        ]);
    }

    public function destroy(Chat $chat)
    {
        $chat->delete();
    }
}