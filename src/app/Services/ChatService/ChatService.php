<?php

namespace App\Services\ChatService;

use App\Enums\SenderEnum;
use App\Models\ChatMessage;
use Illuminate\Support\Collection;
use LaravelOpenRouter;
use MoeMizrak\LaravelOpenrouter\DTO\ChatData;
use MoeMizrak\LaravelOpenrouter\DTO\MessageData;
use MoeMizrak\LaravelOpenrouter\Types\RoleType;

class ChatService
{
    public function __construct(
        protected string $model = 'nousresearch/hermes-3-llama-3.1-405b:free',
        protected int $maxTokens = 100,
    ) {}

    public function message(string $conversationId, string $message): string
    {
        ChatMessage::create([
            'conversation_id' => $conversationId,
            'sender' => SenderEnum::USER,
            'message' => $message,
        ]);

        $chatMessages = ChatMessage::whereConversationId($conversationId)
            ->orderBy('created_at', 'asc')
            ->get();

        $answerMessage = $this->generateNextMessage($chatMessages);

        ChatMessage::create([
            'conversation_id' => $conversationId,
            'sender' => SenderEnum::ASSISTANT,
            'message' => $answerMessage,
        ]);

        return $answerMessage;
    }

    /**
     * @param  Collection<ChatMessage>  $chatMessages
     */
    public function generateNextMessage(Collection $chatMessages): mixed
    {
        /** @var MessageData[] $messagesData */
        $messagesData[] = new MessageData(
            'Ты - бот поддержи для нашего сайта', // Сюда пихайте всю информацию которую должен знать бот
            RoleType::SYSTEM,
        );

        $messagesData[] = new MessageData(
            'Привет! Я робот помощник. Буду рад ответить на ваши вопросы!',
            RoleType::ASSISTANT,
        );

        foreach ($chatMessages as $previousMessage) {
            $messagesData[] = new MessageData(
                $previousMessage->message,
                $previousMessage->sender === 'user' ? 'user' : 'assistant', // Нужно для конвертации между внутренним энумом и форматом OpenAI
            );
        }

        $chatData = new ChatData(
            messages: $messagesData,
            model: $this->model,
            max_tokens: $this->maxTokens,
        );

        return LaravelOpenRouter::chatRequest($chatData)->choices[0]['message']['content'];
    }
}
