<template>
    <div class="max-w-6xl mx-auto px-6 pt-20 pb-6 space-y-12">
        <div class="flex items-center justify-center">
            <PhotoIcon class="size-8 text-white" />
            <h1 class="text-2xl font-bold text-white ml-4">Image Optimizer</h1>
        </div>

        <form @submit.prevent="optimizeImage" class="space-y-12">
            <!-- File Upload -->
            <FilePond
                ref="pond"
                :server="{ process: handleFileUpload }"
                label-idle="Drop images here or <span class='filepond--label-action'>Browse</span>"
                accepted-file-types="image/*"
                :max-file-size="10000000"
                :files="files"
                @processfile="handleProcessFile"
                allowMultiple="true"
                class="bg-zinc-800 rounded-lg"
            />

            <!-- Submit Button -->
            <Button
                label="Optimize Images"
                class="w-full"
                :disabled="isLoading || currentFiles.length === 0"
            />
        </form>

        <!-- Results Grid -->
        <div v-if="optimizedImages.length > 0">
            <ul
                role="list"
                class="grid grid-cols-2 gap-x-4 gap-y-8 sm:grid-cols-3 sm:gap-x-6 lg:grid-cols-4 xl:gap-x-8"
            >
                <li
                    v-for="(image, index) in optimizedImages"
                    :key="index"
                    class="relative"
                >
                    <div
                        class="group overflow-hidden rounded-lg bg-zinc-800 focus-within:ring-2 focus-within:ring-blue-500 focus-within:ring-offset-2 focus-within:ring-offset-black"
                    >
                        <img
                            :src="image.preview"
                            :alt="'Optimized Image ' + (index + 1)"
                            class="pointer-events-none aspect-[10/7] w-full object-cover group-hover:opacity-75"
                        />
                        <button
                            type="button"
                            @click="() => downloadImage(index)"
                            class="absolute inset-0 focus:outline-none"
                        >
                            <span class="sr-only"
                                >Download image {{ index + 1 }}</span
                            >
                        </button>
                    </div>
                    <div class="mt-2 space-y-2">
                        <div class="flex justify-between items-center">
                            <p
                                class="block truncate text-sm font-medium text-white"
                            >
                                {{ image.original.name }}
                            </p>
                            <p class="block text-sm font-medium text-red-500">
                                {{ image.originalSize }}
                            </p>
                        </div>
                        <div class="flex justify-between items-center">
                            <p class="block text-sm font-medium text-white/60">
                                Optimized
                            </p>
                            <p class="block text-sm font-medium text-green-500">
                                {{ image.optimizedSize }}
                            </p>
                        </div>
                        <button
                            @click="() => downloadImage(index)"
                            class="flex items-center justify-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/20 rounded-lg transition w-full"
                        >
                            <ArrowDownTrayIcon class="size-5 text-white" />
                            <span class="text-white">Download</span>
                        </button>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { PhotoIcon, ArrowDownTrayIcon } from '@heroicons/vue/24/outline'
import Button from '@/Components/Button.vue'
import vueFilePond from 'vue-filepond'
import FilePondPluginImagePreview from 'filepond-plugin-image-preview'
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type'
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size'
import 'filepond/dist/filepond.min.css'
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css'
import imageCompression from 'browser-image-compression'

// Create FilePond component
const FilePond = vueFilePond(
    FilePondPluginImagePreview,
    FilePondPluginFileValidateType,
    FilePondPluginFileValidateSize
)

const quality = ref(80)
const format = ref('webp')
const width = ref('')
const height = ref('')
const files = ref([])
const isLoading = ref(false)
const optimizedImages = ref([]) // Array to store multiple optimized images
const currentFiles = ref([]) // Array to store multiple files

const handleFileUpload = (fieldName, file, metadata, load, error, progress) => {
    // Add file to current files array
    currentFiles.value.push(file)
    load(file)
}

const optimizeImage = async () => {
    if (currentFiles.value.length === 0) return

    isLoading.value = true

    try {
        // Process each file
        for (const file of currentFiles.value) {
            const originalFormat = file.type.split('/')[1]

            const options = {
                maxSizeMB: 1,
                maxWidthOrHeight: 1920,
                useWebWorker: true,
                initialQuality: 0.7,
                fileType: `image/${originalFormat}`,
                alwaysKeepResolution: true
            }

            // Compress image
            const compressedFile = await imageCompression(file, options)

            // Convert to data URL for preview
            const dataUrl = await new Promise((resolve) => {
                const reader = new FileReader()
                reader.onload = (e) => resolve(e.target.result)
                reader.readAsDataURL(compressedFile)
            })

            // Add to optimized images array
            optimizedImages.value.push({
                original: file,
                optimized: compressedFile,
                preview: dataUrl,
                originalSize: formatBytes(file.size),
                optimizedSize: formatBytes(compressedFile.size),
                format: originalFormat
            })
        }
    } catch (err) {
        console.error('Optimization error:', err)
    }

    isLoading.value = false
}

const downloadImage = (index) => {
    const image = optimizedImages.value[index]
    if (!image) return

    const link = document.createElement('a')
    link.href = image.preview
    link.download =
        image.original.name.replace(/\.[^/.]+$/, '') +
        '_optimized.' +
        image.format
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
}

const handleProcessFile = (error, file) => {
    if (error) {
        console.error('Upload failed:', error)
        return
    }
}

// Clear all images
const clearImages = () => {
    optimizedImages.value = []
    currentFiles.value = []
    files.value = []
}

const formatBytes = (bytes) => {
    if (bytes === 0) return '0 Bytes'
    const k = 1024
    const sizes = ['Bytes', 'KB', 'MB', 'GB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}
</script>
