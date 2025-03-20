<template>
    <div class="max-w-3xl mx-auto px-6 pt-20 pb-6 space-y-12">
        <div class="flex items-center justify-center">
            <LinkIcon class="size-8 text-white" />
            <h1 class="text-2xl font-bold text-white ml-4">URL Shortener</h1>
        </div>

        <form @submit.prevent="shortenUrl" class="space-y-12">
            <div
                class="group relative w-full max-w-3xl mx-auto overflow-hidden rounded-xl bg-zinc-800 transition"
            >
                <!-- Animated Border Effect -->
                <div
                    class="absolute inset-0 flex items-center [container-type:inline-size]"
                >
                    <div
                        class="absolute h-[100cqw] w-[100cqw] animate-spin bg-[conic-gradient(from_0_at_50%_50%,rgba(255,255,255,0.5)_0deg,transparent_60deg,transparent_300deg,rgba(255,255,255,0.5)_360deg)] opacity-0 transition duration-300 group-hover:opacity-100"
                    ></div>
                </div>

                <!-- Inner Border -->
                <div
                    class="absolute inset-0.5 rounded-xl bg-gradient-to-r from-zinc-800 to-zinc-900"
                ></div>

                <!-- Textarea -->
                <textarea
                    v-model="originalUrl"
                    placeholder="Enter your URL . . ."
                    rows="10"
                    class="relative w-full p-4 text-lg text-gray-300 placeholder-gray-500 bg-transparent border-none focus:outline-none focus:ring-0 focus:border-none transition resize-none"
                    required
                ></textarea>
            </div>

            <!-- Submit Button -->
            <Button label="Shorten URL" class="w-full" />
        </form>

        <!-- Display Shortened URL -->
        <div
            v-if="shortUrl"
            @click="copyToClipboard"
            class="mt-4 w-full max-w-xl mx-auto p-4 bg-gradient-to-r from-zinc-800 to-zinc-900 text-white text-lg rounded-lg shadow-md flex items-center justify-between cursor-pointer hover:shadow-lg transition"
        >
            <p class="truncate">
                <a
                    :href="shortUrl"
                    target="_blank"
                    class="hover:underline hover:text-sky-500"
                >
                    {{ shortUrl }}
                </a>
            </p>
            <!-- Clipboard and Check Icon with Bounce Effect -->
            <div
                class="flex items-center justify-center p-2 rounded-full border-2 border-white/50 hover:border-white transition"
            >
                <component
                    :is="isCopied ? CheckCircleIcon : ClipboardDocumentListIcon"
                    :class="[
                        'size-6 text-white',
                        isBouncing ? 'animate-bounce' : ''
                    ]"
                />
            </div>
        </div>
    </div>
</template>

<script>
import { ref } from 'vue'
import axios from 'axios'
import Button from '@/Components/Button.vue'
import {
    ClipboardDocumentListIcon,
    CheckCircleIcon,
    LinkIcon
} from '@heroicons/vue/24/solid'

export default {
    setup() {
        const originalUrl = ref('') // Stores the original URL input
        const shortUrl = ref('') // Stores the shortened URL
        const isCopied = ref(false) // Tracks whether the URL has been copied

        // Function to shorten the URL
        const shortenUrl = async () => {
            try {
                const response = await axios.post('/shorten', {
                    original_url: originalUrl.value
                })
                shortUrl.value = response.data.short_url // Update shortened URL
                isCopied.value = false // Reset icon to Clipboard when new URL is shortened
            } catch (error) {
                console.error('An error occurred:', error)
            }
        }

        // Function to copy the shortened URL to the clipboard
        const copyToClipboard = async () => {
            try {
                await navigator.clipboard.writeText(shortUrl.value) // Copy URL
                isCopied.value = true // Change icon to CheckCircle
                // Reset icon back to Clipboard after 2 seconds
                setTimeout(() => {
                    isCopied.value = false
                }, 5000)
            } catch (error) {
                console.error('Failed to copy URL:', error)
            }
        }

        return {
            originalUrl,
            shortUrl,
            shortenUrl,
            copyToClipboard,
            isCopied,
            ClipboardDocumentListIcon,
            CheckCircleIcon
        }
    },

    components: {
        Button,
        LinkIcon
    }
}
</script>
