<script setup lang="ts">
import { defineProps, ref, onMounted, computed } from 'vue';
import Tablet from '@/Layouts/Tablet.vue';

const props = defineProps({
  saleId: Number,
  tableId: Number,
  success: String,
  recentOrderTime: String,
  timesOrdered: Number,
});

const countdown = ref('10:00');
const buttonDisabled = ref(true);

const navigateToCreateOrder = () => {
  window.location.href = route('tablet.create', { saleId: props.saleId });
};

const navigateToCheckout = () => {
  window.location.href = route('tablet.checkout', { saleId: props.saleId });
};

const calculateRemainingTime = () => {
  const now = new Date();
  const recentOrderDate = new Date(props.recentOrderTime);
  const tenMinutes = 10 * 60 * 1000;
  const endTime = new Date(recentOrderDate.getTime() + tenMinutes);
  const timeDifference = endTime - now;

  if (timeDifference > 0) {
    const minutes = Math.floor(timeDifference / (60 * 1000));
    const seconds = Math.floor((timeDifference % (60 * 1000)) / 1000);
    countdown.value = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
  } else {
    countdown.value = '0:00';
    clearInterval(intervalId);
  }
};

const updateButtonDisabledState = () => {
  const now = new Date();
  const recentOrderDate = new Date(props.recentOrderTime);
  const tenMinutes = 10 * 60 * 1000;
  const endTime = new Date(recentOrderDate.getTime() + tenMinutes);
  const timeDifference = endTime - now;

  buttonDisabled.value = timeDifference > 0 || props.timesOrdered >= 5;
};

let intervalId;

onMounted(() => {
  calculateRemainingTime();
  updateButtonDisabledState();
  intervalId = setInterval(() => {
    calculateRemainingTime();
    updateButtonDisabledState();
  }, 1000);
});
</script>

<template>
  <Tablet>
    <div>
      <div v-if="props.success" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 relative" role="alert">
        <p>{{ props.success }}</p>
      </div>
      {{ $t('messages.Welcome to the Golden Dragon') }}
      <br>
      {{ $t('messages.Have a nice stay and enjoy your meal.') }}
      <br>
      {{ $t('messages.You are allowed to order up to 5 times with a period of 10 minutes in between') }}
      <br>
       <br>
      {{ $t('messages.You are seated at table nr: ') }} {{ props.tableId }}
      <br>
      {{ $t('messages.Times ordered: ') }} {{ props.timesOrdered }}
      <br>
      {{ $t('messages.Time before you may order again: ') }} {{ countdown }}
      <br>
      <br>
      <button
        type="button"
        @click="navigateToCreateOrder"
        :disabled="buttonDisabled"
        :class="{
          'bg-blue-500 text-white': !buttonDisabled,
          'bg-gray-500 text-white cursor-not-allowed': buttonDisabled,
          'hover:bg-blue-700': !buttonDisabled
        }"
        class="font-bold py-2 px-4 rounded"
      >
        {{ $t('messages.Create Order') }}
      </button>
      <br>
      <br>
      <button type="button" @click="navigateToCheckout" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
        {{ $t('messages.Checkout') }}
      </button>
    </div>
  </Tablet>
</template>
