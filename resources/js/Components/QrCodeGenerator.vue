<template>
    <div class="max-w-3xl mx-auto px-6 pt-20 pb-6 space-y-12">
        <div class="flex items-center justify-center">
            <QrCodeIcon class="size-8 text-white" />
            <h1 class="text-2xl font-bold text-white ml-4">
                QR Code Generator
            </h1>
        </div>

        <form @submit.prevent="generateQrCode" class="space-y-12">
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
                    v-model="content"
                    placeholder="Enter your text or URL . . ."
                    rows="10"
                    class="relative w-full p-4 text-lg text-gray-300 placeholder-gray-500 bg-transparent border-none focus:outline-none focus:ring-0 focus:border-none transition resize-none"
                    required
                ></textarea>
            </div>

            <!-- Submit Button -->
            <Button
                label="Generate QR Code"
                class="w-full"
                :disabled="isLoading"
            />
        </form>

        <!-- Loading indicator -->
        <div v-if="isLoading" class="flex justify-center">
            <div
                class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-white"
            ></div>
        </div>

        <!-- Display Generated QR Code -->
        <div
            v-if="qrCodeGenerated && !isLoading"
            class="mt-4 w-full max-w-xl mx-auto p-6 bg-gradient-to-r from-zinc-800 to-zinc-900 text-white rounded-lg shadow-md flex flex-col items-center justify-center gap-4 hover:shadow-lg transition"
        >
            <div
                class="w-64 h-64 bg-white p-2 rounded-lg flex items-center justify-center"
            >
                <img
                    v-if="qrCodeDataUrl"
                    :src="qrCodeDataUrl"
                    alt="QR Code"
                    class="w-full h-full object-contain"
                />
            </div>

            <div class="flex gap-4 mt-2">
                <button
                    @click="downloadQrCode"
                    class="flex items-center justify-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/20 rounded-lg transition"
                >
                    <ArrowDownTrayIcon class="size-5 text-white" />
                    <span>Download</span>
                </button>

                <button
                    @click="copyQrCode"
                    class="flex items-center justify-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/20 rounded-lg transition"
                >
                    <component
                        :is="
                            isCopied
                                ? CheckCircleIcon
                                : ClipboardDocumentListIcon
                        "
                        class="size-5 text-white"
                    />
                    <span>{{ isCopied ? 'Copied!' : 'Copy' }}</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Button from '@/Components/Button.vue'
import {
    ClipboardDocumentListIcon,
    CheckCircleIcon,
    QrCodeIcon,
    ArrowDownTrayIcon
} from '@heroicons/vue/24/solid'

const content = ref('') // Stores the text/URL input
const qrCodeGenerated = ref(false) // Tracks if QR code has been generated
const isCopied = ref(false) // Tracks whether the QR code has been copied
const isLoading = ref(false) // Tracks loading state

const qrCodeDataUrl = ref('') // To store the QR code as data URL

// Function to generate QR code
const generateQrCode = async () => {
    try {
        if (!content.value.trim()) {
            return
        }

        isLoading.value = true

        try {
            // Try server-side generation first
            const response = await fetch('/generate-qrcode', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    // Use optional chaining to prevent errors
                    'X-CSRF-TOKEN':
                        document.head
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute('content') || ''
                },
                body: JSON.stringify({ content: content.value })
            })

            if (!response.ok) {
                throw new Error('Server-side generation failed')
            }

            // Get the image data from the response
            const blob = await response.blob()
            qrCodeDataUrl.value = URL.createObjectURL(blob)
        } catch (serverError) {
            console.warn(
                'Server-side QR generation failed, falling back to client-side:',
                serverError
            )

            // Fall back to client-side generation
            qrCodeDataUrl.value = await QRCode.toDataURL(content.value, {
                width: 240,
                margin: 1,
                color: {
                    dark: '#000000',
                    light: '#ffffff'
                }
            })
        }

        qrCodeGenerated.value = true
        isCopied.value = false
    } catch (error) {
        console.error('An error occurred:', error)
    } finally {
        isLoading.value = false
    }
}

// Function to download QR code
const downloadQrCode = () => {
    if (!qrCodeDataUrl.value) return

    const link = document.createElement('a')
    link.href = qrCodeDataUrl.value
    link.download = 'qrcode.png'
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
}

// Function to copy QR code URL to clipboard
const copyQrCode = async () => {
    try {
        await navigator.clipboard.writeText(content.value)
        isCopied.value = true
        setTimeout(() => {
            isCopied.value = false
        }, 5000)
    } catch (error) {
        console.error('Failed to copy content:', error)
    }
}
</script>
