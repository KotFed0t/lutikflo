<template>
    <LoginDialog v-model:show="loginDialogVisible"></LoginDialog>
    <div class="navigation">
        <ul class="nav-list">
            <li class="nav-item">
                <router-link class="nav-link active" aria-current="page" :to="{name: 'home'}">Home</router-link>
            </li>
            <li class="nav-item">
                <router-link class="nav-link" :to="{name: 'login'}">Login</router-link>
            </li>
            <li class="nav-item">
                <router-link class="nav-link" :to="{name: 'register'}">Registration</router-link>
            </li>
            <li class="nav-item">
                <router-link class="nav-link" :to="{name: 'order'}">Order</router-link>
            </li>
            <li class="nav-item">
                <router-link class="nav-link" :to="{name: 'cart'}">Cart</router-link>
            </li>
            <li class="nav-item">
                <div v-if="isAuth">
                    <button @click.prevent="logout" class="btn btn-primary">выйти</button>
                </div>
            </li>
            <li class="nav-item">
                <div v-if="!isAuth">
                    <button @click.prevent="showLoginDialogVisible" class="btn btn-primary">войти</button>
                </div>
            </li>
        </ul>
    </div>
</template>

<script>
import axios from "axios";
import LoginDialog from "./LoginDialog.vue";

export default {
    name: "navBar",
    data() {
        return {
            loginDialogVisible: false
        }
    },
    components: {LoginDialog},
    computed: {
        isAuth() {
            return this.$store.getters.isAuth
        }
    },
    methods: {
        logout() {
            this.$store.dispatch('logout')
        },
        showLoginDialogVisible() {
            this.loginDialogVisible = true
        }
    }
}
</script>

<style scoped>
.navigation {
    display: flex;
}
.nav-list {
    display: flex;
}
.nav-item {
    margin-right: 30px;
}
</style>
