<template>
  <div>
    <a :href="localeUrls.en">English</a>
    <a :href="localeUrls.nl">Dutch</a>
  </div>
</template>

<script>
export default {
  data() {
    return {
      localeUrls: window.localeUrls || {
        en: '/locale/en',
        nl: '/locale/nl',
      }
    };
  }
}
</script>