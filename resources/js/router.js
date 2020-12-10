import VueRouter from "vue-router";
import Home from "./components/pages/Home"
import Callback from "./components/auth/Callback"
import Login from "./components/auth/Login"
import Users from "./components/pages/admin/users/Users"
import Events from "./components/pages/admin/events/Events"
import Vote from "./components/pages/admin/vote/Vote"

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
        meta: {
            auth: {
                redirect: { name: "admin.users" }
            },
            layout: "Admin",
            title: "Admin"
        }
    },
    {
        path: "/admin/users",
        name: "admin.users",
        component: Users,
        meta: {
            auth: true,
            layout: "Admin",
            title: "Users"
        }
    },
    {
        path: "/admin/events",
        name: "admin.events",
        component: Events,
        meta: {
            auth: true,
            layout: "Admin",
            title: "Events"
        }
    },
    {
        path: "/admin/vote",
        name: "admin.vote",
        component: Vote,
        meta: {
            auth: true,
            layout: "Admin",
            title: "Vote"
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
