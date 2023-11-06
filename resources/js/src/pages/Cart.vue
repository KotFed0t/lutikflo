<template>
    <LoginDialog v-model:show="loginDialogVisible" redirectTo="checkout"></LoginDialog>
    Корзина
    <p>общая стоимость товаров: {{calculateTotalPrice()}}</p>
    <button @click="goToCheckout" class="btn btn-dark">Оформить заказ</button>

    <div v-if="cart.length === 0"> В корзине пока что нет товаров...</div>
    <div v-for="cartItem in cartFullData" :key="cartItem.product.slug" style="border: black solid 1px">
        <div>{{ cartItem }}</div>
        <hr>
        <router-link class="nav-link" :to="{
            name: 'productDetail',
            params: {productSlug: cartItem.product.slug},
            query: {flower_count: cartItem.flower_count}
        }">
            <p style="color: #0a53be">название: {{ cartItem.product.name }} {{ cartItem.flower_count?'( '+cartItem.flower_count + ' шт. )':'' }}</p>
        </router-link>
        <button @click="increaseProductCount(cartItem)" class="btn btn-dark">+</button>
        <div>количество: {{ getCartItemCount(cartItem) }}</div>
        <button @click="decreaseProductCount(cartItem)" :disabled="getCartItemCount(cartItem) === 1" class="btn btn-dark">-</button>
        <p>цена: {{getCartItemPrice(cartItem)}} </p>
        <hr>
        <button @click="deleteFromCart(cartItem)" class="btn btn-dark">удалить</button>
    </div>
</template>

<script>
import axios from "axios";
import router from "../router/router.js";
import LoginDialog from "../components/LoginDialog.vue";
import cartMixin from "../mixins/cartMixin.js";

export default {
    name: "Cart",
    mixins: [cartMixin],
    components: {LoginDialog},
    data() {
        return {
            loginDialogVisible: false,
        }
    },
    mounted() {
        this.getCartData()
    },
    watch: {
      cart() {
          this.calculateTotalPrice()
      }
    },
    methods: {
        getCartData() {
            if (this.cart.length !== 0) {
                axios.post('api/cart', {cart: this.cart}).then(response => {
                    this.cartFullData = response.data.data
                }).catch(err => {
                    console.log(err)
                })
            }
        },
        increaseProductCount(cartItem) {
            this.$store.dispatch('addToCart', {product_id: cartItem.product_id, flower_count: cartItem.flower_count})
        },
        decreaseProductCount(cartItem) {
            this.$store.dispatch('decreaseProductCount', {product_id: cartItem.product_id, flower_count: cartItem.flower_count})
        },
        deleteFromCart(cartItem) {
            this.$store.dispatch('deleteFromCart', {product_id: cartItem.product_id, flower_count: cartItem.flower_count})
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
            }
        }
    }
}
</script>

<style scoped>

</style>
