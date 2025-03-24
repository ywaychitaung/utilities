<script setup>
import { ref } from 'vue'

const activeTab = ref('URL Shortener')
const tabs = ['URL Shortener', 'QR Code Generator', 'Image Optimizer']

const emit = defineEmits(['tabChange'])

function setActive(tab) {
    activeTab.value = tab
    emit('tabChange', tab)
}
</script>

<template>
    <div class="flex justify-center mb-8 pt-16">
        <div class="relative mx-auto rounded-2xl bg-white/5 p-2 w-[600px]">
            <div class="relative">
                <!-- Animated background for active tab -->
                <div
                    class="absolute inset-y-0 h-full rounded-lg bg-white/10 transition-all duration-300"
                    :style="{
                        width: `${100 / tabs.length}%`,
                        transform: `translateX(${
                            tabs.indexOf(activeTab) * 100
                        }%)`
                    }"
                />

                <!-- Navigation tabs -->
                <ul class="relative flex items-center justify-center">
                    <li
                        v-for="(tab, index) in tabs"
                        :key="index"
                        class="flex-1 text-center"
                    >
                        <button
                            :class="[
                                activeTab === tab
                                    ? 'text-white'
                                    : 'text-white/60 hover:text-white'
                            ]"
                            class="inline-flex w-full justify-center items-center px-4 py-3 text-center text-base font-medium transition"
                            @click.prevent="setActive(tab)"
                        >
                            {{ tab }}
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
