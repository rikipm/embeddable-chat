<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChatController\ChatPostRequest;
use App\Services\ChatService\ChatService;

class ChatController extends Controller
{
    public function __construct(
        protected ChatService $chatService,
    ) {}

    public function chat(ChatPostRequest $request)
    {
        $answer = $this->chatService->message($request->conversation_id, $request->message);

        return [
            'message' => $answer,
        ];
    }
}
