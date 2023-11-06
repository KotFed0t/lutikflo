import './bootstrap';
import {createApp} from "vue";
import App from "./src/App.vue";
import router from "./src/router/router.js";
import store from "./src/store/index.js";


createApp(App)
    .use(router)
    .use(store)
    .mount('#app')
