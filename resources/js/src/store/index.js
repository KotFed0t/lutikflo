import {createStore} from "vuex";
import {userModule} from "./modules/userModule.js";
import {cartModule} from "./modules/cartModule.js";



export default createStore({
    modules: {
        user: userModule,
        cart: cartModule,
    }
})
