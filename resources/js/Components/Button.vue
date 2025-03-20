<script setup>
import SpotlightButton from '@/Components/SpotlightButton.vue'

defineProps({
    label: {
        type: String,
        required: true
    }
})
</script>

<template>
    <div class="mx-auto flex max-w-xl items-center justify-center">
        <SpotlightButton>
            <button
                type="submit"
                class="relative mt-px bg-gradient-to-b from-white/25 to-white bg-clip-text font-mona text-lg font-medium text-transparent transition-all duration-200"
            >
                {{ label }}
            </button>
        </SpotlightButton>
    </div>
</template>
