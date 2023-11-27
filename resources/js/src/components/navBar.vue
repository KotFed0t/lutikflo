<template>
    <LoginDialog v-model:show="loginDialogVisible"></LoginDialog>
    <div class="flex justify-between py-5 mb-5">
        <brand class="font-bold text-2xl">
            <router-link :to="{name: 'home'}">LUTIKFLO</router-link>
        </brand>
        <ul class="flex">
            <li>
                <div class="flex flex-col items-center">
                    <p class="text-xs text-gray-600">Город доставки</p>
                    <p class="font-medium">Новосибирск</p>
                </div>
            </li>
            <li class="ml-10">
                <div class="flex flex-col items-center">
                    <p class="text-xs text-gray-600">Ежедневно с 10:00 до 21:00</p>
                    <a class="font-medium" href="tel:+79833064216">+7(983)306-42-16</a>
                </div>
            </li>
            <li class="ml-10">
                <router-link :to="{name: 'cart'}">
                    <div class="relative">
                        <!-- Иконка корзины -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" class="w-6 h-6">-->
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                        </svg>
                        <!-- Счетчик в круглешке -->
                        <div v-if="cartItemsCount > 0"
                            class="absolute top-3.5 right-0 bg-red-500 text-white w-4 h-4 flex items-center justify-center rounded-full">
                            <span class="text-xs font-medium">{{ cartItemsCount }}</span>
                        </div>
                    </div>
                </router-link>
            </li>
            <li v-if="isAuth" class="ml-10">
                <button @click.prevent="logout" class="font-medium hover:border-b-2 border-black">выйти</button>
            </li>
            <li class="ml-10">
                <div v-if="!isAuth">
                    <button @click.prevent="showLoginDialogVisible" class="font-medium hover:border-b-2 border-black">войти</button>
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
        },
        cartItemsCount() {
            return this.$store.getters.itemsCount
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

</style>
