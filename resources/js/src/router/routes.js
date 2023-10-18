import Home from "../pages/Home.vue";
import Login from "../pages/Login.vue";
import Registration from "../pages/Registration.vue";
import Addresses from "../pages/Addresses.vue";
import Order from "../pages/Order.vue";

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
    {
        path: '/addresses',
        component: Addresses,
        name: 'addresses'
    },
    {
        path: '/order',
        component: Order,
        name: 'order'
    },
]

export default routes
