<template>
  <div class="overflow-x-hidden w-2/3">
    <router-view :key="$route.fullPath"></router-view>
  </div>
</template>

<script>
export default {
  name: "App",
  watch: {
    $route(to) {
      document.title = to.meta.title;
    }
  }
};
</script>

<style scoped></style>
