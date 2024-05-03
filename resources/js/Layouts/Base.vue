<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import Footer from '@/Components/Footer.vue';
</script>

<template>
    <div class="w-screen h-screen bg-red-900 flex items-center justify-center">
        <div class="w-5/6 h-5/6 base-red">
            <div class="text-center text-xl">
                <div>Chinees Indische Specialiteiten</div>
                <div>De Gouden Draak</div>
                <nav>
                    <div class="text-white flex justify-center items-center">
                            <Link class="menu-button"
                            :href="route('menu')"
                            >
                            Menukaart
                        </Link>
                        <Link class="menu-button"
                            :href="route('news')"
                            >
                            Nieuws
                        </Link>
                        <Link class="menu-button"
                            :href="route('contact')"
                            >
                            Contact
                        </Link>
                    </div>
                </nav>
            </div>
            <slot />
            <Footer />
        </div>
    </div>
</template>