<template>
  <div class="py-2">
    <router-link :to="`/print-sheets/${sheet.data.print_sheet_id}`">
      {{ title }}
    </router-link>
  </div>
</template>

<script>
import moment from "moment-mini";

export default {
  computed: {
    title() {
      return this.sheet.data && this.sheet.data.attributes
        ? moment(this.sheet.data.attributes.created_at).format("LLLL")
        : "Not Found";
    }
  },
  props: {
    sheet: {
      type: Object,
      required: true
    }
  },
  name: "SheetItem"
};
</script>
