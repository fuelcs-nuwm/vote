import bearer from '@websanova/vue-auth/drivers/auth/bearer'
import axios from '@websanova/vue-auth/drivers/http/axios.1.x'
import router from '@websanova/vue-auth/drivers/router/vue-router.2.x'
// Auth base configuration some of this options
// can be override in method calls
const config = {
    auth: bearer,
    http: axios,
    router: router,
    tokenDefaultName: 'token',
    rolesVar: "user_roles",
    tokenStore: ['localStorage'],
    forbiddenRedirect: {path: '/403'},
    notFoundRedirect: {path: '/404'},
    authRedirect: {path: '/login'},
    loginData: {url: 'auth/callback', method: 'GET', redirect: "/admin", fetchUser: true},
    logoutData: {url: 'auth/logout', method: 'POST', redirect: '/', makeRequest: true},
    fetchData: {url: 'auth/me', method: 'POST', enabled: true},
    refreshData: {url: 'auth/refresh', method: 'GET', enabled: true, interval: 30}
}
export default config
