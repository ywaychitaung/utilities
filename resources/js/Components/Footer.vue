<template>
    <footer class="text-white pb-16">
        <div class="container mx-auto px-4 text-center">
            <!-- Copyright and Made with Love -->
            <div class="text-gray-400 space-y-4">
                <p>&copy; {{ currentYear }} All Rights Reserved.</p>
                <p>
                    Made with
                    <span class="text-red-500">&hearts;</span> by
                    <a
                        href="https://ywaychitaung.dev"
                        target="_blank"
                        class="hover:text-white"
                    >
                        Yway Chit Aung
                    </a>
                </p>
            </div>
        </div>
    </footer>
</template>

<script>
export default {
    data() {
        return {
            currentYear: new Date().getFullYear() // Dynamically get the current year
        }
    }
}
</script>
