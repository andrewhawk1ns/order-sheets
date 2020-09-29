<template>
  <div class="container mx-auto text-center">
    <h1 class="text-lg font-bold">Sheets</h1>
    <div v-if="!loading">
      <div v-if="!!error">
        <app-error-display :error="error" />
      </div>
      <div v-if="sheets.data && sheets.data.length > 0" class="py-8">
        <app-sheet-archive :sheets="sheets.data" />
      </div>
    </div>
    <div v-else>
      <app-loader :loading="loading" />
    </div>
    <app-back-button />
  </div>
</template>

<script>
import printSheets from "../api/printSheets";
import SheetArchive from "../components/SheetArchive.vue";
import ErrorDisplay from "../components/ErrorDisplay.vue";
import BackButton from "../components/BackButton.vue";
import Loader from "../components/Loader.vue";

export default {
  name: "Sheets",
  mounted() {
    this.fetchSheets();
  },
  data: () => ({
    error: "",
    loading: false,
    sheets: []
  }),
  components: {
    appSheetArchive: SheetArchive,
    appErrorDisplay: ErrorDisplay,
    appLoader: Loader,
    appBackButton: BackButton
  },
  methods: {
    async fetchSheets() {
      this.error = "";
      this.sheets = [];
      this.loading = true;
      try {
        const sheets = await printSheets.getSheets();

        this.sheets = sheets;
      } catch (error) {
        console.log(error);
        this.error = error;
      }

      this.loading = false;
    }
  }
};
</script>
