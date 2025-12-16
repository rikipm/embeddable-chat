<?php

namespace Tests\Feature\Controllers\ChatController;

use App\Models\ChatMessage;
use DG\BypassFinals;
use Illuminate\Foundation\Testing\RefreshDatabase;
use MoeMizrak\LaravelOpenrouter\DTO\ResponseData;
use MoeMizrak\LaravelOpenrouter\OpenRouterRequest;
use Tests\TestCase;

class ChatPostTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        BypassFinals::enable();

        $responseData = new ResponseData(
            'fake/id',
            'fake/model',
            'fake/object',
            0,
            choices: [
                [
                    'message' => [
                        'content' => 'Test answer!',
                    ],
                ],
            ]
        );

        $openRouterRequest = app(OpenRouterRequest::class);
        $openRouterRequestMock = mock($openRouterRequest)->makePartial();
        $openRouterRequestMock->shouldReceive('chatRequest')->withAnyArgs()->andReturn($responseData)->once();
        $this->app->instance(OpenRouterRequest::class, $openRouterRequestMock);
    }

    public function test_chat_post()
    {
        $conversationId = uuid_create();
        $message = 'Hello, support!';

        $response = $this->post('/api/chat', [
            'conversation_id' => $conversationId,
            'message' => $message,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['message']);

        $chatMessages = ChatMessage::all()->toArray();

        assert(count($chatMessages) === 2);
        assert($chatMessages[0]['conversation_id'] === $conversationId);
        assert($chatMessages[1]['conversation_id'] === $conversationId);
        assert($chatMessages[0]['message'] === $message);
    }
}
