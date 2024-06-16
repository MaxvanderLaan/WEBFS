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
		<div class="base-red">
        <LocaleSwitcher />
            <div class="flex justify-between">
                <div class="flex items-center">
                    <img :src="asset('pictures/dragon-small.png')" alt="Dragon" class="small-dragon">
                    <div class="chinese-text">
                        {{ $t('messages.The Golden Dragon') }}
                    </div>
                        <img :src="asset('pictures/dragon-small.png')" alt="Dragon" class="small-dragon inverted">
                    </div>

                    <div class="scroll-container flex">
                        <div class="scroll-text">
                       {{ $t('messages.Welcome to the golden dragon, Tablet edition!') }}
                        </div>
                    </div>

                    <div class="flex items-center">
                        <img :src="asset('pictures/dragon-small.png')" alt="Dragon" class="small-dragon">
                        <div class="chinese-text">
                           {{ $t('messages.The Golden Dragon') }}
                        </div>
                        <img :src="asset('pictures/dragon-small.png')" alt="Dragon" class="small-dragon inverted">
                    </div>
                </div>
                <slot />
                <Footer />
		</div>
	</Border>
</template>