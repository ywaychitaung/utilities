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

            <div class="space-y-4">
                <div class="flex items-center justify-center space-x-8">
                    <label class="inline-flex items-center text-white">
                        <input
                            type="radio"
                            v-model="centerType"
                            value="none"
                            class="form-radio text-blue-600"
                        />
                        <span class="ml-2">No Content</span>
                    </label>
                    <label class="inline-flex items-center text-white">
                        <input
                            type="radio"
                            v-model="centerType"
                            value="text"
                            class="form-radio text-blue-600"
                        />
                        <span class="ml-2">Text</span>
                    </label>
                    <label class="inline-flex items-center text-white">
                        <input
                            type="radio"
                            v-model="centerType"
                            value="logo"
                            class="form-radio text-blue-600"
                        />
                        <span class="ml-2">Logo</span>
                    </label>
                </div>

                <!-- Text Input -->
                <div v-if="centerType === 'text'" class="space-y-2">
                    <input
                        v-model="centerText"
                        type="text"
                        placeholder="Enter text . . ."
                        class="w-full p-2 rounded-lg bg-zinc-800 text-white border border-zinc-700 focus:outline-none focus:border-blue-500"
                    />
                </div>

                <!-- Logo Upload -->
                <div v-if="centerType === 'logo'" class="space-y-2">
                    <FilePond
                        ref="pond"
                        :server="{ process: handleFileUpload }"
                        label-idle="Drop logo here or <span class='filepond--label-action'>Browse</span>"
                        accepted-file-types="image/*"
                        :max-file-size="MAX_FILE_SIZE"
                        :files="files"
                        @processfile="handleProcessFile"
                        class="bg-zinc-800 rounded-lg"
                    />
                </div>
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
import QRCode from 'qrcode'

import vueFilePond from 'vue-filepond'
import FilePondPluginImagePreview from 'filepond-plugin-image-preview'
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type'
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size'
import 'filepond/dist/filepond.min.css'
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css'

// Create FilePond component
const FilePond = vueFilePond(
    FilePondPluginImagePreview,
    FilePondPluginFileValidateType,
    FilePondPluginFileValidateSize
)

const MAX_FILE_SIZE = 30 * 1024 * 1024 // 30MB in bytes

const content = ref('')
const qrCodeGenerated = ref(false)
const isCopied = ref(false)
const isLoading = ref(false)
const qrCodeDataUrl = ref('')
const centerType = ref('none')
const centerText = ref('')
const files = ref([])
const logoDataUrl = ref('')

const handleFileUpload = (
    fieldName,
    file,
    metadata,
    load,
    error,
    progress,
    abort
) => {
    const reader = new FileReader()
    reader.onload = () => {
        logoDataUrl.value = reader.result
        load(reader.result)
    }
    reader.onerror = error
    reader.onprogress = (event) => {
        progress(event.lengthComputable, event.loaded, event.total)
    }
    reader.readAsDataURL(file)
}

const handleProcessFile = (error, file) => {
    if (!error) {
        logoDataUrl.value = file.serverId
    }
}

const generateQrCode = async () => {
    try {
        if (!content.value.trim()) return
        isLoading.value = true

        const options = {
            width: 240,
            margin: 1,
            color: {
                dark: '#000000',
                light: '#ffffff'
            }
        }

        // Create canvas for QR code
        const canvas = document.createElement('canvas')
        const ctx = canvas.getContext('2d')

        // Generate base QR code
        await QRCode.toCanvas(canvas, content.value, options)

        // Add center content if specified
        if (centerType.value === 'text' && centerText.value) {
            const centerX = canvas.width / 2
            const centerY = canvas.height / 2
            ctx.fillStyle = '#ffffff'
            ctx.fillRect(centerX - 40, centerY - 20, 80, 40)
            ctx.fillStyle = '#000000'
            ctx.font = '16px Arial'
            ctx.textAlign = 'center'
            ctx.textBaseline = 'middle'
            ctx.fillText(centerText.value, centerX, centerY)
        } else if (centerType.value === 'logo' && logoDataUrl.value) {
            const img = new Image()
            img.src = logoDataUrl.value
            await new Promise((resolve) => {
                img.onload = () => {
                    const size = canvas.width * 0.2
                    const x = (canvas.width - size) / 2
                    const y = (canvas.height - size) / 2
                    ctx.fillStyle = '#ffffff'
                    ctx.fillRect(x, y, size, size)
                    ctx.drawImage(img, x, y, size, size)
                    resolve()
                }
            })
        }

        qrCodeDataUrl.value = canvas.toDataURL('image/png')
        qrCodeGenerated.value = true
        isCopied.value = false
    } catch (error) {
        console.error('Failed to generate QR code:', error)
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

<style>
.filepond--panel-root {
    background-color: rgb(39 39 42);
    border: 2px dashed rgb(63 63 70);
}
.filepond--drop-label {
    color: #ffffff;
}
</style>
