<template>
  <div class="container mx-auto text-center">
    <h1 class="text-lg font-bold">Home</h1>
    <div class="text-center py-4">
      <div v-if="!loading">
        <button
          class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 mb-4 focus:outline-none"
          @click.prevent="handlePrintAll"
        >
          Print Orders
        </button>
        <div v-if="!!error">
          <app-error-display :error="error" />
        </div>
        <div v-if="sheets.data && sheets.data.length > 0">
          <p>Sheets were generated successfully.</p>
          <p><router-link to="/print-sheets">View Sheets</router-link></p>
        </div>
      </div>

      <div v-else>
        <app-loader :loading="loading" />
      </div>
    </div>
  </div>
</template>

<script>
import printSheets from "../api/printSheets";
import ErrorDisplay from "../components/ErrorDisplay.vue";
import Loader from "../components/Loader.vue";

export default {
  name: "Home",
  data: () => ({
    error: "",
    loading: false,
    sheets: []
  }),
  components: {
    appErrorDisplay: ErrorDisplay,
    appLoader: Loader
  },
  methods: {
    async handlePrintAll() {
      this.error = "";
      this.sheets = [];
      this.loading = true;
      try {
        const sheets = await printSheets.createSheets({
          type: "test"
        });

        this.sheets = sheets;

        console.log(this.sheets);
      } catch (error) {
        console.log(error);
        this.error = error;
      }

      this.loading = false;
    }
  }
};
</script>
