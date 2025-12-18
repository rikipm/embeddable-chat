<script setup lang="ts">
const origin = window.location.origin;
</script>

<template>
    <div class="min-h-screen bg-indigo-500 flex items-center justify-center p-4">
        <div class="w-full max-w-4xl bg-indigo-400 rounded-lg shadow-lg p-8">
            <div class="space-y-4">
                <div class="flex justify-center">
                    <iframe src="/chat" width="500px" height="500px"></iframe>
                </div>
                <p class="text-white">
                    Для добавления чата на свою страницу вставьте следующий HTML код:
                </p>

                <div class="bg-gray-900 rounded-lg overflow-hidden">
                    <pre class="p-4 overflow-x-auto"><code class="language-markup text-white text-sm">&lt;iframe src="{{ origin }}/chat" width="500px" height="500px"&gt;&lt;/iframe&gt;</code></pre>
                </div>
            </div>
        </div>
    </div>
</template>
