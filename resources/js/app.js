import './bootstrap';
import {createApp} from "vue";
import App from "./src/App.vue";
import router from "./src/router/router.js";
import store from "./src/store/index.js";
import VueTheMask from 'vue-the-mask'


createApp(App)
    .use(router)
    .use(store)
    .use(VueTheMask)
    .mount('#app')
