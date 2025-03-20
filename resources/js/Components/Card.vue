<script setup>
import SpotlightCard from '@/Components/SpotlightCard.vue'

defineProps({
    icon: {
        type: [Object, Function],
        required: true
    },
    title: {
        type: String,
        required: true
    },
    description: {
        type: String,
        required: true
    }
})
</script>

<template>
    <div class="flex items-center px-8">
        <SpotlightCard
            hsl
            :hsl-min="200"
            :hsl-max="280"
            size="300"
            class="mx-auto w-full max-w-sm rounded-[--radius] bg-white/10 shadow-xl shadow-white/2.5 [--radius:theme(borderRadius.2xl)]"
        >
            <div
                class="absolute inset-px rounded-[calc(var(--radius)-1px)] bg-zinc-800"
            ></div>

            <!-- highlight -->
            <div
                class="absolute inset-0 bg-[radial-gradient(40%_128px_at_50%_0%,theme(backgroundColor.white/5%),transparent)]"
            ></div>

            <div class="relative">
                <div class="relative py-20">
                    <div
                        class="absolute inset-0 flex items-center justify-center [mask-image:linear-gradient(to_bottom,white,transparent)]"
                    >
                        <div
                            class="absolute h-96 w-96 rounded-full bg-white/2.5 shadow-xl"
                        ></div>

                        <div
                            class="absolute h-[19rem] w-[19rem] rounded-full border border-white/5 bg-white/2.5 shadow-xl"
                        ></div>

                        <div
                            class="absolute h-56 w-56 rounded-full border border-white/10 bg-white/2.5 shadow-xl"
                        ></div>

                        <div
                            class="absolute h-36 w-36 rounded-full border border-dashed border-white/15 bg-white/2.5 shadow-xl"
                        ></div>
                    </div>

                    <div class="relative flex items-center justify-center">
                        <div
                            class="flex h-16 w-16 rounded-full border border-white/25 bg-white/5 p-3 shadow-xl"
                        >
                            <component
                                :is="icon"
                                class="h-10 w-10 stroke-[1] text-zinc-200"
                            />
                        </div>
                    </div>
                </div>

                <!-- description -->
                <div class="mt-6 px-8 pb-8">
                    <div class="text-lg text-white">{{ title }}</div>

                    <p
                        class="mt-2 text-sm font-light leading-relaxed text-white/75"
                    >
                        {{ description }}
                    </p>
                </div>
            </div>
        </SpotlightCard>
    </div>
</template>
