import Vue from "vue";

import VueRouter from "vue-router";

import Home from "./views/Home.vue";

import Sheet from "./views/Sheet.vue";

import Sheets from "./views/Sheets.vue";

Vue.use(VueRouter);

export default new VueRouter({
    mode: "history",
    routes: [
        {
            path: "/",
            name: "home",
            component: Home,
            meta: { title: "Sheets" }
        },
        {
            path: "/print-sheets/:sheetId",
            name: "printSheet.show",
            component: Sheet,
            meta: { title: "Sheet" }
        },
        {
            path: "/print-sheets",
            name: "printSheet.index",
            component: Sheets,
            meta: { title: "Sheets" }
        }
    ]
});
