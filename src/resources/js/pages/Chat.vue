<script setup lang="ts">
import { v4 as uuidv4 } from 'uuid';
import { nextTick, ref, useTemplateRef, watch } from 'vue';

interface Message {
    sender: 'user' | 'assistant';
    text: string;
    time: string;
}

interface APIResponse {
    message: string;
}

const newMessage = ref('');

const messages = ref<Message[]>();

const isBotAnswering = ref(false);

let conversationId: string;

const scrollableAreaDiv = useTemplateRef('scrollable-area-div');

function getTime(): string {
    return new Date().toLocaleTimeString();
}

function newChat(): void {
    messages.value = [{ sender: 'assistant', text: 'Привет! Я робот помощник. Буду рад ответить на ваши вопросы!', time: getTime() }];
    conversationId = uuidv4();
    /*
    В реальном проекте я бы использовал `conversationId = crypto.randomUUID();` вместо отдельного пакета, однако
    1. crypto.randomUUID() доступен только в secure context.
    2. Для secure context требуется HTTPS соединение и следовательно SSL сертификат.
    3. Получать SSL сертификат для данного проекта - это перебор.
     */
}

newChat();

function sendMessage(): void {
    if (newMessage.value.trim() === '') return;

    const time = getTime();
    messages.value.push({
        sender: 'user',
        text: newMessage.value,
        time: time,
    });

    isBotAnswering.value = true;
    fetch('/api/chat', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
        },
        body: JSON.stringify({
            conversation_id: conversationId,
            message: newMessage.value,
        }),
    })
        .then((response) => {
            if (response.ok) {
                return response.json();
            } else {
                throw new Error();
            }
        })
        .then(function (response: APIResponse) {
            messages.value.push({
                sender: 'assistant',
                text: response.message,
                time: time,
            });
        })
        .catch(function () {
            messages.value.push({
                sender: 'assistant',
                text: '(!) SERVER ERROR',
                time: time,
            });
        })
        .finally(function () {
            isBotAnswering.value = false;
        });

    newMessage.value = '';
}

watch(
    messages,
    () => {
        nextTick(() => scrollableAreaDiv.value.scrollTo({ top: scrollableAreaDiv.value.scrollHeight, behavior: 'smooth' }));
    },
    { deep: true },
);
</script>

<template>
    <div class="fixed inset-0 flex h-full w-full flex-col rounded-lg bg-white">
        <div class="flex items-center justify-between rounded-t-lg bg-orange-500 p-3 text-white">
            <h3 class="font-medium">Embeddable chat</h3>
            <button @click="newChat" class="cursor-pointer rounded bg-orange-600 px-5 py-3 text-sm text-white transition-colors hover:bg-orange-700">
                Новый чат
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-3" ref="scrollable-area-div">
            <div v-for="(message, index) in messages" :key="index" class="mb-3">
                <div :class="['rounded-lg p-2 shadow-md', message.sender === 'user' ? 'ml-auto bg-orange-100' : 'mr-auto bg-gray-100', 'max-w-xs']">
                    <p class="text-sm">{{ message.text }}</p>
                    <p class="mt-1 text-xs text-gray-500">{{ message.time }}</p>
                </div>
            </div>
            <div v-if="isBotAnswering" class="mb-4 flex justify-start">
                <div class="rounded-lg bg-white p-3 text-gray-800 shadow-md">
                    <div class="flex space-x-1">
                        <div class="h-2 w-2 animate-bounce rounded-full bg-gray-400" style="animation-delay: 0ms"></div>
                        <div class="h-2 w-2 animate-bounce rounded-full bg-gray-400" style="animation-delay: 150ms"></div>
                        <div class="h-2 w-2 animate-bounce rounded-full bg-gray-400" style="animation-delay: 300ms"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-200 p-3">
            <form class="flex" @submit.prevent="sendMessage">
                <input
                    name="message"
                    v-model="newMessage"
                    type="text"
                    placeholder="Введите сообщение..."
                    class="flex-1 rounded-l-lg border border-gray-300 p-2 text-sm focus:ring-2 focus:ring-orange-500 focus:outline-none"
                    :disabled="isBotAnswering"
                    required
                />
                <button
                    type="submit"
                    class="rounded-r-lg px-4 py-2 text-white"
                    :class="!isBotAnswering ? 'cursor-pointer bg-orange-500 hover:bg-orange-600 focus:outline-none' : 'bg-gray-500'"
                    :disabled="isBotAnswering"
                >
                    Отправить
                </button>
            </form>
        </div>
    </div>
</template>

<style scoped></style>
