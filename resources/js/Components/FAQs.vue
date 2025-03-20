<script setup>
import { XMarkIcon } from '@heroicons/vue/20/solid'
import DetailsGroup from '@/Components/DetailsGroup.vue'
import DetailsItem from '@/Components/DetailsItem.vue'
import DetailsContent from '@/Components/DetailsContent.vue'

const items = [
    {
        title: 'Is my data safe?',
        content:
            'Yes, we don’t store any URLs in the database for more than one hour. No one can access to your URLs unless you share the shorten URLs. The URLs are deleted after an hour or as soon as they’re viewed. We don’t store any URLs in the database for more than an hour. We are committed to transparency and privacy. No data, no worries!'
    },
    {
        title: 'How secure is the shortened URL?',
        content:
            'The shortened URLs are stored in the database using argon2id hashing and the original URL using AES-256 encryption. Both argon2id and AES-256 are award winning secure algorithms.'
    },
    {
        title: 'How many URLs can I shorten?',
        content:
            'You can shorten as many URLs as you want. There are no restrictions on the number of URLs you can shorten.'
    },
    {
        title: 'Can I customize, delete or track the shortened URLs?',
        content:
            'No, the system does not provide any customization, delete or track for the shortened URLs. We cannot track who created the URL or what the original URL and the shorten URL are. This is to ensure the privacy and anonymity of the users. Anonymity is our top priority.'
    },
    {
        title: 'How long is the shortened URL valid?',
        content:
            'The shortened URLs are valid for an hour or as soon as they’re viewed. After an hour or as soon as the URL is viewed, it is deleted from the database.'
    },
    {
        title: 'Is my QR code data stored on your servers?',
        content:
            'No, all QR codes are generated entirely on your device. We never receive, process, or store any of the data you use to create QR codes. Your information remains private and never leaves your browser.'
    },
    {
        title: 'What types of content can I encode in a QR code?',
        content:
            'You can encode any text or URL in a QR code. This includes website addresses, plain text, contact information, or any other text-based data you want to share.'
    },
    {
        title: 'How do I share my generated QR code?',
        content:
            'After generating a QR code, you can download it as a PNG image file or copy the original text. You can then share the image through email, messaging apps, social media, or print it for physical distribution.'
    }
]
</script>

<template>
    <h1 class="text-white text-center mt-24 text-2xl font-bold">
        Frequently Asked Questions
    </h1>
    <div class="mx-auto flex max-w-2xl py-24">
        <DetailsGroup class="space-y-5">
            <DetailsItem
                v-for="(item, index) in items"
                :key="index"
                v-slot="{ isActive, toggle }"
                class="group rounded-2xl border border-white/10 bg-white/5 transition duration-500 hover:bg-white/[0.075]"
            >
                <div
                    class="flex cursor-pointer items-center p-4"
                    @click="toggle"
                >
                    <div
                        class="text-white/75 transition group-hover:text-white"
                    >
                        {{ item.title }}
                    </div>
                    <div class="relative ml-auto">
                        <XMarkIcon
                            :class="{
                                'rotate-180': isActive,
                                'rotate-45': !isActive
                            }"
                            class="h-6 w-6 text-white/50 transition-transform duration-500"
                        />
                    </div>
                </div>
                <DetailsContent
                    class="overflow-hidden px-4 transition-all duration-500 will-change-[height]"
                >
                    <p
                        class="pb-4 font-light leading-relaxed tracking-wide text-white/75"
                    >
                        {{ item.content }}
                    </p>
                </DetailsContent>
            </DetailsItem>
        </DetailsGroup>
    </div>
</template>
