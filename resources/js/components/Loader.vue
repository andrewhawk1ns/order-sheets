<template>
  <div class="py-8 text-center">
    <pulse-loader :loading="loading" color="#4299e1"></pulse-loader>
  </div>
</template>

<script>
import PulseLoader from "vue-spinner/src/PulseLoader.vue";

export default {
  name: "Loader",
  components: {
    PulseLoader
  },
  props: {
    loading: {
      type: Boolean,
      default: false
    }
  }
};
</script>
