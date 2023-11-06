import {createRouter, createWebHistory} from "vue-router";
import Home from "../pages/Home.vue";
import Login from "../pages/Login.vue";
import Registration from "../pages/Registration.vue";
import Order from "../pages/Order.vue";
import store from "../store/index.js";
import PageNotFound from "../pages/PageNotFound.vue";
import Catalog from "../pages/Catalog.vue";
import ProductDetail from "../pages/ProductDetail.vue";
import Cart from "../pages/Cart.vue";
import Checkout from "../pages/Checkout.vue";


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
            path: '/register',
            component: Registration,
            name: 'register',
            // meta: {requiresAuth: true}
        },
        {
            path: '/order',
            component: Order,
            name: 'order',
            meta: {requiresAuth: true}
        },
        {
            path: '/cart',
            component: Cart,
            name: 'cart',
        },
        {
            path: '/checkout',
            component: Checkout,
            name: 'checkout',
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

