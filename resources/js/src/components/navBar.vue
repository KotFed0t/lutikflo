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
                <router-link class="nav-link" :to="{name: 'addresses'}">Adresses</router-link>
            </li>
            <li class="nav-item">
                <router-link class="nav-link" :to="{name: 'order'}">Order</router-link>
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
    <!--    <nav class="navbar navbar-expand-lg bg-body-tertiary">-->
    <!--        <div class="container-fluid">-->
    <!--            <a class="navbar-brand" >Navbar</a>-->
    <!--            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">-->
    <!--                <span class="navbar-toggler-icon"></span>-->
    <!--            </button>-->
    <!--            <div class="collapse navbar-collapse" id="navbarNav">-->
    <!--                <ul class="navbar-nav">-->
    <!--                    <li class="nav-item">-->
    <!--                        <router-link class="nav-link active" aria-current="page" :to="{name: 'home'}">Home</router-link>-->
    <!--                    </li>-->
    <!--                    <li class="nav-item">-->
    <!--                        <router-link class="nav-link" :to="{name: 'login'}">Login</router-link>-->
    <!--                    </li>-->
    <!--                    <li class="nav-item">-->
    <!--                        <router-link class="nav-link" :to="{name: 'register'}">Registration</router-link>-->
    <!--                    </li>-->
    <!--                    <li class="nav-item">-->
    <!--                        <router-link class="nav-link" :to="{name: 'addresses'}">Adresses</router-link>-->
    <!--                    </li>-->
    <!--                    <li class="nav-item">-->
    <!--                        <router-link class="nav-link" :to="{name: 'order'}">Order</router-link>-->
    <!--                    </li>-->
    <!--                    <li class="nav-item">-->
    <!--                        <div v-if="isAuth">-->
    <!--                            <button @click.prevent="logout" class="btn btn-primary">выйти</button>-->
    <!--                        </div>-->
    <!--                    </li>-->
    <!--                    <li class="nav-item">-->
<!--    <div v-if="!isAuth">-->
<!--        <button @click.prevent="showLoginDialogVisible" class="btn btn-primary">войти</button>-->
<!--    </div>-->
    <!--                    </li>-->
    <!--                </ul>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </nav>-->
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
