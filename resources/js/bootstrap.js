/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
import Cookies from 'js-cookie';
import router from "./src/router/router.js";
import store from "./src/store/index.js";

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';
window.axios.defaults.baseURL = import.meta.env.VITE_APP_URL;

window.axios.interceptors.request.use(function (config) {
    if ((config.method === 'post' || config.method === 'put' || config.method === 'delete') && !Cookies.get('XSRF-TOKEN')) {
        return axios.get('/sanctum/csrf-cookie')
            .then(response => config);
    }
    return config;
});

window.axios.interceptors.response.use(function (response) {
    return response;
}, function (error) {
    if (error.response.status === 401 || error.response.status === 419) {
        Cookies.remove('XSRF-TOKEN')
        store.dispatch('clearStateAndRemoveCookie')
        router.push({name: 'login'})
        return Promise.reject(error);
    }
    if (error.response.status === 404) {
        router.push({path: '/404', replace:true})
        return Promise.reject(error);
    }
    return Promise.reject(error);
});

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// import Pusher from 'pusher-js';
// window.Pusher = Pusher;

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
//     wsHost: import.meta.env.VITE_PUSHER_HOST ? import.meta.env.VITE_PUSHER_HOST : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
//     wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
//     wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
//     forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
//     enabledTransports: ['ws', 'wss'],
// });
