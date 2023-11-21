import Cookies from "js-cookie";
import axios from "axios";

export const userModule = {
    state: () => ({
        isAuth: !!Cookies.get('isAuth'),
    }),
    getters: {
        isAuth: state => state.isAuth
    },
    mutations: {
        setAuth(state, $isAuth) {
            state.isAuth = $isAuth
        }
    },
    actions: {
        login(context) {
            Cookies.set('isAuth', 'true', {expires: 3/24})
            context.commit('setAuth', true)
        },
        logout(context) {
            axios.post('api/logout')
                .then(response => {
                    Cookies.remove('isAuth')
                    context.commit('setAuth', false)
                })
                .catch(err => {
                    console.log(err)
                })
        },
        clearStateAndRemoveCookie(context) {
            Cookies.remove('isAuth')
            context.commit('setAuth', false)
        }
    }
}
