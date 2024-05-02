<script setup lang="ts">
import Footer from '@/Components/Footer.vue';
</script>

<template>
    <div class="w-screen h-screen bg-red-900 flex items-center justify-center">
        <div class="w-5/6 h-5/6 base-red">
            <slot />
            <Footer />
        </div>
    </div>
</template>