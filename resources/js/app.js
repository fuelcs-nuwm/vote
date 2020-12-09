import Vue from "vue";
import axios from "axios";
import VueAuth from "@websanova/vue-auth";
import VueAxios from "vue-axios";
import VueRouter from 'vue-router'
import router from "./router";
import auth from "./auth";
import "./bootstrap";

// Set Vue router
Vue.router = router;
Vue.use(VueRouter);

Vue.use(VueAxios, axios);
axios.defaults.baseURL = `/api`;
Vue.use(VueAuth, auth);

// Load Index
import Index from "./Index";
Vue.component("index", Index);

new Vue({
    render: h => h(Index),
    router
}).$mount('#app')
