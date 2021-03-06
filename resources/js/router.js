import VueRouter from "vue-router";
import Home from "./components/pages/Home"
import Callback from "./components/auth/Callback"
import Login from "./components/auth/Login"
import Router from "./components/Router"
import VotePage from "./components/pages/user/vote/VotePage"
import Admins from "./components/pages/admin/users/Admins"
import Events from "./components/pages/admin/events/Events"
import EventQuestions from "./components/pages/admin/events/EventQuestions"
import EventCustomers from "./components/pages/admin/events/EventCustomers"
import EventGroups from "./components/pages/admin/events/EventGroups"
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
        path: "/router",
        name: "router",
        component: Router,
        meta: {
            auth: undefined,
            layout: "Empty",
            title: "Router"
        }
    },
    {
        path: "/vote",
        name: "vote",
        component: VotePage,
        meta: {
            auth: [1,2],
            layout: "Vote",
            title: "Router"
        }
    },
    {
        path: "/admin",
        name: "admin",
        meta: {
            auth: {
                role: 1,
                redirect: { name: "admin.admins" }
            },
            layout: "Admin",
            title: "Admin"
        }
    },
    {
        path: "/admin/admins",
        name: "admin.admins",
        component: Admins,
        meta: {
            auth: 1,
            layout: "Admin",
            title: "Users"
        }
    },
    {
        path: "/admin/events",
        name: "admin.events",
        component: Events,
        meta: {
            auth: 1,
            layout: "Admin",
            title: "Events"
        }
    },
    {
        path: "/admin/events/:id/questions",
        name: "admin.events.questions",
        component: EventQuestions,
        meta: {
            auth: 1,
            layout: "Admin",
            title: 'EventQuestions'
        }
    },
    {
        path: "/admin/events/:id/groups",
        name: "admin.events.groups",
        component: EventGroups,
        meta: {
            auth: 1,
            layout: "Admin",
            title: 'EventQuestions'
        }
    },
    {
        path: "/admin/events/:id/customers",
        name: "admin.events.customers",
        component: EventCustomers,
        meta: {
            auth: 1,
            layout: "Admin",
            title: 'EventCustomers'
        }
    },
    {
        path: "/admin/vote",
        name: "admin.vote",
        component: Vote,
        meta: {
            auth: 1,
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
