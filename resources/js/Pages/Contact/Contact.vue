<script setup lang="ts">
import { ref } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import Base from "@/Layouts/Base.vue";

const form = ref({
  name: "",
  email: "",
  message: "",
});
</script>

<template>
  <Base>
    <Head title="Contact" />
    <div>
      <p class="text-white">
        {{ $t("messages.The Golden Dragon is easy to find, near the city center, a 5-minute walk behind the central station.") }}
        <br />
        <br />
        {{ $t("messages.Education Boulevard 215, room OG112") }}
        <br />
        {{ $t("messages.5223 DE 's-Hertogenbosch") }}
      </p>
      <br />
      <div class="flex justify-center">
        <img src="/storage/pictures/2020-03-24_1406.png" alt="google maps image" />
      </div>
    </div>
  </Base>
</template>
