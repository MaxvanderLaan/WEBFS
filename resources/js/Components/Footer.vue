<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <footer class="py-16 text-center text-yellow-500">
        <Link 
        :href="route('contact')"
        >
        {{ $t("messages.To Contact")}}
        </Link>
    </footer>
</template>