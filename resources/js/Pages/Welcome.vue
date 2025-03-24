<script setup>
import { ref } from 'vue'
import UrlShortener from '@/Components/UrlShortener.vue'
import QrCodeGenerator from '@/Components/QrCodeGenerator.vue'
import ImageOptimizer from '@/Components/ImageOptimizer.vue'
import Navigation from '@/Components/Navigation.vue'
import FAQs from '@/Components/FAQs.vue'
import Card from '@/Components/Card.vue'
import Footer from '@/Components/Footer.vue'
import {
    FingerPrintIcon,
    EyeSlashIcon,
    ClipboardDocumentIcon
} from '@heroicons/vue/24/outline'

const activeTab = ref('URL Shortener')

function handleTabChange(tab) {
    activeTab.value = tab
}
</script>

<template>
    <Navigation @tab-change="handleTabChange" />

    <!-- Update conditional rendering -->
    <UrlShortener v-if="activeTab === 'URL Shortener'" />
    <QrCodeGenerator v-else-if="activeTab === 'QR Code Generator'" />
    <ImageOptimizer v-else-if="activeTab === 'Image Optimizer'" />

    <h1 class="text-white text-center text-2xl font-bold py-24">
        The Services You Can Trust
    </h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-y-16">
        <Card
            :icon="EyeSlashIcon"
            title="Privacy"
            description="We don't store any information about the users. After an hour or as soon as the URL is viewed, it is deleted from the database. QR codes and their associated data are never stored on our servers."
        />

        <Card
            :icon="FingerPrintIcon"
            title="Security"
            description="The shortened URLs are stored in the database using argon2id hashing and AES-256 encryption. Your shorten URLs are safe and secure. QR codes are generated client-side for maximum security and privacy."
        />

        <Card
            :icon="ClipboardDocumentIcon"
            title="Ease"
            description="Simply paste your long URL, generate a shortened URL, and copy it to share wherever you need. Create QR codes instantly with just a few clicks. Quick and convenient!"
        />
    </div>

    <FAQs />

    <Footer />
</template>
