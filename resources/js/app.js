import Vue from "vue";
import axios from "axios";
import VueAuth from "@websanova/vue-auth";
import VueAxios from "vue-axios";
import VueRouter from 'vue-router'
import router from "./router";
import auth from "./auth";
import "./bootstrap";
import Vuex from "vuex";
import store from "./components/store/store";

// Set Vue router
Vue.router = router;
Vue.use(VueRouter);

Vue.use(VueAxios, axios);
axios.defaults.baseURL = `/api`;
Vue.use(VueAuth, auth);

// Load Index
import Index from "./Index";
Vue.component("index", Index);

//Vuelidate
import Vuelidate from 'vuelidate'
Vue.use(Vuelidate)

//Set Vuex
Vue.use(Vuex);

router.beforeEach((to, from, next) => {
    if (to.meta.title) {
        document.title = `${to.meta.title}`;
    }

    if (to.meta.layout) {
        store.state.layout = to.meta.layout;
    }

    next();
});

new Vue({
    render: h => h(Index),
    router,
    store
}).$mount('#app')
