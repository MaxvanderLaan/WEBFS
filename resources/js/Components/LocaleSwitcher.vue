<template>
  <div>
    <span>
      <a :href="localeUrls.en">{{ $t('messages.English') }}</a>
      <span> / </span>
      <a :href="localeUrls.nl">{{ $t('messages.Dutch') }}</a></span>
  </div>
</template>

<script>
export default {
  data() {
    return {
      localeUrls: window.localeUrls || {
        en: "/locale/en",
        nl: "/locale/nl",
      },
    };
  },
};
</script>
