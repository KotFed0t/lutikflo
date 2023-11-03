<template>
    <br>
        Чтобы продолжить, пожалуйста авторизуйтесь
        <LoginDialog v-model:show="loginDialogVisible"></LoginDialog>
        <button @click.prevent="showLoginDialog" class="btn btn-primary">Войти</button>
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
