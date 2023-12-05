<template>
    <LoginDialog v-model:show="loginDialogVisible" :redirectTo="'home'"></LoginDialog>
    <div class="text-center mt-24">
        <p class="font-medium">Чтобы продолжить, пожалуйста авторизуйтесь</p>
        <button class="bg-black text-white rounded-lg px-4 py-1.5 hover:bg-neutral-700 mt-5" @click="showLoginDialog">Войти</button>
    </div>
</template>

<script>
import axios from "axios";
import LoginDialog from "../components/LoginDialog.vue";
import router from "../router/router.js";
export default {
    name: "Login",
    components: {LoginDialog},
    data() {
        return {
            email: '',
            password: '',
            loginDialogVisible: false
        }
    },
    methods: {
        showLoginDialog() {
            this.loginDialogVisible = true
        }
    }
}
</script>

<style scoped>

</style>
