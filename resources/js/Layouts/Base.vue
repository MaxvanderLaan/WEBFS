<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import Footer from '@/Components/Footer.vue';
import { defineExpose } from 'vue';

const asset = (path: any) => `${import.meta.env.VITE_APP_URL}/storage/${path}`;

defineExpose({ asset });
</script>

<template>
    <div class="w-screen h-screen bg-red-900 flex items-center justify-center">
        <div class="w-5/6 h-5/6 base-red">
            <div class="flex justify-between items-center">
                <img :src="asset('pictures/dragon-large.png')" alt="Dragon" class="big-dragon m-8">
                <div class="flex flex-col">
                    <div class="text-center text-xl">
                        <div class="main-text-sub m-8">
                            Chinees Indische Specialiteiten
                        </div>
                        <div class="main-text m-8">
                            De Gouden Draak
                        </div>
                    </div>
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
                <img :src="asset('pictures/dragon-large.png')" alt="Dragon" class="big-dragon inverted m-8">
            </div>
            <slot />
            <Footer />
        </div>
    </div>
</template>