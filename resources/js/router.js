import vueRouter from 'vue-router';
import Vue from 'vue';

Vue.use(vueRouter);

import Index from "./views/Index";
import Incomes from "./views/Incomes";
import Future from "./views/Future";


const routes = [
    {
        path: "/",
        component: Index
    },
    {
        path: "/incomes",
        component: Incomes
    },
    {
        path: "/future",
        component: Future
    }
];

export default new vueRouter({
    mode: "history",
    routes
});
