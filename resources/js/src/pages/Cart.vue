<template>
    <LoginDialog v-model:show="loginDialogVisible" redirectTo="checkout"></LoginDialog>

    <div v-if="cart.length === 0" class="text-center mt-24">
        <p class="font-medium">В корзине пока что нет товаров...</p>
        <button class="bg-black text-white rounded-full px-4 py-1.5 hover:bg-neutral-700 mt-5" @click="$router.push({name: 'home'})">На главную</button>
    </div>

    <div v-if="cartFullData" class="mt-10 lg:flex lg:items-start lg:justify-center">
        <div class="rounded-xl pt-0 px-3 pb-5 shadow-2xl lg:w-9/12 lg:mr-16 lg:px-5">
            <div v-for="cartItem in cartFullData" :key="cartItem.product.slug">

                <div class="flex py-6 border-b">
                    <router-link
                        class=""
                        :to="{
                        name: 'productDetail',
                        params: {productSlug: cartItem.product.slug},
                        query: {flower_count: cartItem.flower_count}
                        }"
                    >
                        <img class="rounded-lg w-24 sm:w-28" :src="'/storage/' + cartItem.product.main_img" alt="img"/>
                    </router-link>


                    <div class="ml-4 flex flex-1 flex-col">
                        <div>
                            <div class="flex justify-between items-start text-sm font-medium mb-2 lg:text-base">
                                <router-link
                                    class=""
                                    :to="{
                                    name: 'productDetail',
                                    params: {productSlug: cartItem.product.slug},
                                    query: {flower_count: cartItem.flower_count}
                                    }"
                                >
                                    <p>{{ cartItem.product.name }}
                                        {{ cartItem.flower_count ? '( ' + cartItem.flower_count + ' шт. )' : '' }}
                                    </p>
                                </router-link>
                                <div @click="deleteFromCart(cartItem)" class="flex items-center cursor-pointer ml-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                         stroke-width="1.5" stroke="currentColor" class="w-4 h-4 hover:text-black">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    <button type="button" class="text-neutral-600 text-xs hover:text-black">Удалить</button>
                                </div>

                            </div>
                        </div>
                        <div class="flex flex-1 items-end justify-between">
                            <div class="flex items-center">
                                <button
                                    @click="decreaseProductCount(cartItem)"
                                    :disabled="getCartItemCount(cartItem) === 1"
                                    class="pr-2 bg-neutral-100 p-1.5 rounded-l-md"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                         stroke-width="1.5"
                                         stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15"/>
                                    </svg>
                                </button>
                                <div class="pr-2 bg-neutral-100 p-1.5">
                                    {{ getCartItemCount(cartItem) }}
                                </div>
                                <button @click="increaseProductCount(cartItem)" class="bg-neutral-100 p-1.5 rounded-r-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                         stroke-width="1.5"
                                         stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                    </svg>
                                </button>
                            </div>

                            <div class="flex">
                                <p class="ml-4 font-bold">{{ getCartItemPrice(cartItem) }} ₽</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>


        <div v-if="cart.length !== 0"  class="mt-8 max-w-[425px] mx-auto shadow-2xl p-5 rounded-xl lg:w-3/12 lg:mt-0">
            <div class="flex justify-between mb-5 font-bold">
                <p v-if="cart.length !== 0">Сумма</p>
                <p>{{ totalPrice }} ₽</p>
            </div>

            <p class="text-xs font-medium text-neutral-700 mb-4">Стоимость доставки будет рассчитана на следующем этапе.</p>


            <button v-if="cart.length !== 0"
                    @click="goToCheckout"
                    class="text-white bg-black hover:bg-neutral-800 rounded-lg text-md px-4 py-2 text-center inline-flex items-center justify-center w-full"
            >
                Оформить заказ
            </button>
        </div>
    </div>

</template>

<script>
import axios from "axios";
import router from "../router/router.js";
import LoginDialog from "../components/LoginDialog.vue";
import cartMixin from "../mixins/cartMixin.js";
import login from "./Login.vue";

export default {
    name: "Cart",
    mixins: [cartMixin],
    components: {LoginDialog},
    data() {
        return {
            loginDialogVisible: false,
            totalPrice: undefined,
        }
    },
    mounted() {
        this.getCartData()
    },
    watch: {
        cart() {
            this.totalPrice = this.calculateTotalPrice()
        }
    },
    methods: {
        getCartData() {
            if (this.cart.length !== 0) {
                axios.post('api/cart', {cart: this.cart}).then(response => {
                    this.cartFullData = response.data.data
                    this.totalPrice = this.calculateTotalPrice()
                }).catch(err => {
                    console.log(err)
                    if (this.handleCartErrors(err)) {
                        this.getCartData()
                    } else {
                        alert('Что-то пошло не так при загрузке информации о товарах из корзины...')
                    }
                })
            }
        },
        increaseProductCount(cartItem) {
            this.$store.dispatch('addToCart', {product_id: cartItem.product_id, flower_count: cartItem.flower_count})
        },
        decreaseProductCount(cartItem) {
            this.$store.dispatch('decreaseProductCount', {
                product_id: cartItem.product_id,
                flower_count: cartItem.flower_count
            })
        },
        deleteFromCart(cartItem) {
            this.$store.dispatch('deleteFromCart', {
                product_id: cartItem.product_id,
                flower_count: cartItem.flower_count
            })
            let item_id = this.cartFullData.findIndex(item => item.product_id === cartItem.product_id && item.flower_count === cartItem.flower_count);
            if (item_id !== -1) {
                this.cartFullData.splice(item_id, 1)
            }
        },
        goToCheckout() {
            if (this.$store.getters.isAuth) {
                this.$router.push({name: 'checkout'})
            } else {
                this.loginDialogVisible = true
                document.body.classList.add('overflow-hidden');
            }
        }
    }
}
</script>

<style scoped>

</style>
