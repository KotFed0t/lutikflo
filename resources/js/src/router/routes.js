import Home from "../pages/Home.vue";
import Login from "../pages/Login.vue";
import Registration from "../pages/Registration.vue";

const routes = [
    {
        path: '/',
        component: Home,
        name: 'home'
    },
    {
        path: '/login',
        component: Login,
        name: 'login'
    },
    {
        path: '/register',
        component: Registration,
        name: 'register'
    },
]

export default routes
