<template>
  <router-view :key="$route.fullPath"></router-view>
</template>

<script>
export default {
  name: "App",
  watch: {
    $route(to) {
      document.title = to.meta.title;
    },
  },
};
</script>

<style scoped></style>
