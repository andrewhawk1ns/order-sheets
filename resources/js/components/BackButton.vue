<template>
  <router-link
    tag="button"
    :to="route"
    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 focus:outline-none"
  >
    {{ text }}
  </router-link>
</template>

<script>
export default {
  name: "BackButton",
  props: {
    route: {
      type: String,
      default: "/"
    },
    text: {
      type: String,
      default: "Back to home"
    }
  }
};
</script>
