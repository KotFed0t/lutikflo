<template>
    <transition name="fade">
        <div v-if="isMenuModalOpen"
             class="fixed z-40 top-0 left-0 w-full h-full bg-white ">
            <!-- Крестик для закрытия -->
            <button @click="closeMenuModal" class="absolute top-5 right-2 text-gray-700 hover:text-gray-900">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <!-- Содержимое модального окна -->
            <div class="p-4 mt-14">
                <div v-if="!isAuth" class="mt-4">
                    <button @click="showLoginDialogVisible" class="text-lg font-semibold">Войти</button>
                </div>
                <div v-if="isAuth" class="mt-4">
                    <button @click="logout(); $router.push({name: 'home'}); closeMenuModal();" class="text-lg font-semibold">Выйти</button>
                </div>
                <div v-if="isAuth" class="mt-4">
                    <button @click="$router.push({name: 'orders'}); closeMenuModal();" class="text-lg font-semibold">Заказы</button>
                </div>

                <button @click="$router.push({name: 'contacts'}); closeMenuModal();" class="block text-lg font-semibold mt-4">Контакты</button>

                <button @click="$router.push({name: 'delivery'}); closeMenuModal();" class="block text-lg font-semibold mt-4">Доставка</button>

                <hr class="mt-4">
                <p class="mt-4 text-sm font-medium text-gray-600">Город доставки - Новосибирск</p>
                <p class="mt-4 mb-2 text-sm font-medium  text-gray-600">Работаем ежедневно с 10:00 до 21:00</p>
                <a class="font-medium" href="tel:+79833064216">+7(983)306-42-16</a>
            </div>

        </div>
    </transition>

    <LoginDialog v-model:show="loginDialogVisible" @loggedIn="closeMenuModal()" @closeMenuModal="closeMenuModal()"></LoginDialog>
    <div class="flex justify-between py-5 mb-5">
        <div class="font-bold text-2xl">
            <router-link :to="{name: 'home'}">LUTIKFLO</router-link>
        </div>
        <ul class="flex">
            <li class="hidden lg:inline-block">
                <div class="flex flex-col items-center">
                    <p class="text-xs text-gray-600">Город доставки</p>
                    <p class="font-medium">Новосибирск</p>
                </div>
            </li>
            <li class="hidden lg:inline-block ml-10">
                <div class="flex flex-col items-center">
                    <p class="text-xs text-gray-600">Ежедневно с 10:00 до 21:00</p>
                    <a class="font-medium" href="tel:+79833064216">+7(983)306-42-16</a>
                </div>
            </li>

            <li class="hidden lg:inline-block ml-10">
                <router-link :to="{name: 'contacts'}" class="font-medium hover:border-b-2 border-black pb-0.5">
                    контакты
                </router-link>
            </li>

            <li class="hidden lg:inline-block ml-10">
                <router-link :to="{name: 'delivery'}" class="font-medium hover:border-b-2 border-black pb-0.5">
                    доставка
                </router-link>
            </li>


            <li v-if="isAuth" class="hidden lg:inline-block ml-10">
                <router-link :to="{name: 'orders'}" class="font-medium hover:border-b-2 border-black pb-0.5">
                    заказы
                </router-link>
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
            <li class="ml-10 cursor-pointer rounded bg-neutral-100 lg:hidden" @click="showMenuModal()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="w-8 h-8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                </svg>

            </li>

            <li v-if="isAuth" class="hidden lg:inline-block ml-10">
                <button @click="logout(); $router.push({name: 'home'});" class="font-medium hover:border-b-2 border-black">выйти</button>
            </li>
            <li class="hidden lg:inline-block">
                <div v-if="!isAuth">
                    <button @click.prevent="showLoginDialogVisible" class="font-medium hover:border-b-2 border-black ml-10">
                        войти
                    </button>
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
            loginDialogVisible: false,
            isMenuModalOpen: false
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
            document.body.classList.add('overflow-hidden');
        },
        showMenuModal() {
            this.isMenuModalOpen = true
            document.body.classList.add('overflow-hidden');
        },
        closeMenuModal() {
            this.isMenuModalOpen = false
            document.body.classList.remove('overflow-hidden');
        }
    }
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
