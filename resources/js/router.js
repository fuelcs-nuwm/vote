import VueRouter from "vue-router";
import Home from "./components/Home"
import Callback from "./components/auth/Callback"
import Login from "./components/auth/Login"
import Admin from "./components/admin/Admin"

// Routes
const routes = [
    {
        path: "/",
        name: "home",
        component: Home,
        meta: {
            auth: undefined,
            layout: "Root",
            title: "Корпуси"
        }
    },
    {
        path: "/callback",
        name: "callback",
        component: Callback,
        meta: {
            auth: undefined,
            layout: "Empty",
            title: "Корпуси"
        }
    },
    {
        path: "/login",
        name: "login",
        component: Login,
        meta: {
            auth: undefined,
            layout: "Empty",
            title: "Login"
        }
    },
    {
        path: "/admin",
        name: "admin",
        component: Admin,
        meta: {
            auth: true,
            layout: "Admin",
            title: "Admin"
        }
    },
];

const router = new VueRouter({
    history: true,
    mode: "history",
    linkExactActiveClass: "is-active",
    routes
});

export default router;
