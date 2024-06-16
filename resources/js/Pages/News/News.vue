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
    <Head title="News" />
    <p class="text-white">
      {{ $t("messages.Due to the Corona crisis, De Gouden Draak is currently only partially open. The restaurant section is closed.") }}
      <br>
      {{ $t("messages.You can still pick up your favorite dishes.") }}
    </p>
  </Base>
</template>
