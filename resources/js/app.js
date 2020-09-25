import Vue from "vue";
import router from "./router";
import App from "./components/App.vue";

// eslint-disable-next-line no-new
new Vue({
    el: "#app",
    components: {
        App
    },
    router,
});
