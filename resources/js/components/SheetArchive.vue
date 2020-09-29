<template>
  <div class="h-64 overflow-y-auto mx-auto max-w-sm text-center">
    <app-sheet-item v-for="(sheet, sheetKey) in sheets" :key="sheetKey" :sheet="sheet" />
  </div>
</template>

<script>
import SheetItem from "./SheetItem.vue";

export default {
  components: {
    appSheetItem: SheetItem
  },
  props: {
    sheets: {
      type: Array,
      required: true
    }
  },
  name: "SheetArchive"
};
</script>
