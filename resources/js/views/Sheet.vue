<template>
  <div class="container mx-auto p-4 text-center">
    <div v-if="!loading">
      <h1 class="text-lg font-bold">{{ title }}</h1>
      <div class="text-center max-w-lg mx-auto py-8">
        <PDF :src="sheet.data.attributes.sheet_url" className="max-w-sm" />
      </div>
      <div v-if="!!error">
        <app-error-display :error="error" />
      </div>
    </div>
    <div v-else>
      <app-loader :loading="loading" />
    </div>
    <app-back-button route="/print-sheets" text="Back to sheets" />
  </div>
</template>

<script>
import moment from "moment-mini";
import PDF from "vue-pdf";
import Loader from "../components/Loader.vue";
import BackButton from "../components/BackButton.vue";
import printSheets from "../api/printSheets";

export default {
  name: "Sheet",
  data: () => ({
    error: "",
    sheet: null,
    loading: false
  }),
  components: {
    PDF,
    appLoader: Loader,
    appBackButton: BackButton
  },
  computed: {
    title() {
      return this.sheet.data && this.sheet.data.attributes
        ? moment(this.sheet.data.attributes.created_at).format("LLLL")
        : "Not Found";
    }
  },
  mounted() {
    this.fetchPrintSheet();
  },
  methods: {
    async fetchPrintSheet() {
      this.error = "";
      this.loading = true;
      try {
        const sheet = await printSheets.getSheet(this.$route.params.sheetId);

        this.sheet = sheet;
      } catch (error) {
        this.error = error;
      }
      this.loading = false;
    }
  }
};
</script>
