<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import Border from '@/Layouts/Border.vue';
import Footer from '@/Components/Footer.vue';
import LocaleSwitcher from '@/Components/LocaleSwitcher.vue';
import { defineExpose } from 'vue';

const asset = (path: any) => `/storage/${path}`;

defineExpose({ asset });
</script>

<template>
    <Border>
        <LocaleSwitcher />
        <div class="flex flex-col justify-between">
            <div class="flex xl:flex-row items-center justify-center">
                <img :src="asset('pictures/dragon-large.png')" alt="Dragon" class="big-dragon m-4 order-1 xl:order-1">
                <img :src="asset('pictures/dragon-large.png')" alt="Dragon" class="big-dragon inverted m-4 order-2 xl:order-3">
                <div class="flex flex-col order-3 xl:order-2">
                    <div class="text-center text-xl">
                        <div class="main-text-sub m-4">
                            {{ $t('messages.Chinese Indonesian Specialties') }}
                        </div>
                        <div class="main-text m-4">
                            {{ $t('messages.The Golden Dragon') }}
                        </div>
                    </div>
                    <nav>
                        <div class="text-white flex justify-center items-center">
                            <Link class="menu-button" :href="route('menu')">
                                {{ $t('messages.Menu') }}
                            </Link>
                            <Link class="menu-button" :href="route('news')">
                                {{ $t('messages.News') }}
                            </Link>
                            <Link class="menu-button" :href="route('contact')">
                                {{ $t('messages.Contact') }}
                            </Link>
                            <Link class="menu-button" :href="route('takeaway')">
                                {{ $t('Takeaway') }}
                            </Link>
                        </div>
                    </nav>
                </div>
            </div>
            <slot />
            <Footer />
        </div>
    </Border>
</template>