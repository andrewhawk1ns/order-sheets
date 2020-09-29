<template>
  <div class="py-8">
    <p>The following error occurred:</p>
    <p>
      {{ error }}
    </p>
  </div>
</template>

<script>
export default {
  name: "ErrorDisplay",
  props: {
    error: {
      type: String,
      required: true
    }
  }
};
</script>
