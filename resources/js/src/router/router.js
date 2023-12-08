import {createRouter, createWebHistory} from "vue-router";
import Home from "../pages/Home.vue";
import Login from "../pages/Login.vue";
import store from "../store/index.js";
import PageNotFound from "../pages/PageNotFound.vue";
import Catalog from "../pages/Catalog.vue";
import ProductDetail from "../pages/ProductDetail.vue";
import Cart from "../pages/Cart.vue";
import Checkout from "../pages/Checkout.vue";
import Orders from "../pages/Orders.vue";
import Delivery from "../pages/Delivery.vue";
import Contacts from "../pages/Contacts.vue";


const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            component: Home,
            name: 'home',
        },
        {
            path: '/login',
            component: Login,
            name: 'login',
        },
        {
            path: '/cart',
            component: Cart,
            name: 'cart',
        },
        {
            path: '/delivery',
            component: Delivery,
            name: 'delivery',
        },
        {
            path: '/contacts',
            component: Contacts,
            name: 'contacts',
        },
        {
            path: '/checkout',
            component: Checkout,
            name: 'checkout',
            meta: {requiresAuth: true}
        },
        {
            path: '/orders',
            component: Orders,
            name: 'orders',
            meta: {requiresAuth: true}
        },
        {
            path: '/catalog/:categorySlug?',
            component: Catalog,
            name: 'catalog',
        },
        {
            path: '/product/:productSlug',
            component: ProductDetail,
            name: 'productDetail',
        },
        {
            path: '/:pathMatch(.*)',
            component: PageNotFound,
            name: 'pageNotFound',
        },
    ]
})

router.beforeEach((to, from, next) => {
    if (to.meta.requiresAuth && !store.getters.isAuth) {
        return next({name: 'login'})
    }

    if (store.getters.isAuth && to.name === 'login') {
        return next({name: 'home'})
    }

    return next()
})

export default router

