<template>
    Страница оформления заказа
    <p>общая стоимость товаров: {{ calculateTotalPrice() }}</p>
    <div class="form-control">
        <input type="text" placeholder="ваше имя" style="margin-bottom: 20px; margin-right: 10px">
        <input type="checkbox"> доставить анонимно
        <br>
        <input type="" placeholder="телефон" style="margin-bottom: 20px; margin-right: 10px">
        <input type="email" placeholder="email">
        <br>
        <button @click="" class="btn btn-dark" style="margin-bottom: 20px; margin-right: 10px">доставить получателю</button>
        <button @click="" class="btn btn-dark" style="margin-bottom: 20px; margin-right: 10px">доставить мне</button>

        <p>Мы можем согласовать дату и время с получателем:</p>
        <div>
            <input type="radio" id="huey" name="drone" value="huey" checked/>
            <label for="huey">Да, согласовать с получателем</label>
        </div>
        <div>
            <input type="radio" id="dewey" name="drone" value="dewey"/>
            <label for="dewey">Нет, не звонить получателю</label>
        </div>
        <br>

        <input type="text" placeholder="имя получателя" style="margin-bottom: 20px; margin-right: 10px">
        <input type="" placeholder="телефон получателя" style="margin-bottom: 20px; margin-right: 10px">

        <AddressInput></AddressInput>
        <br>
        <p v-if="!showCommentForCurrier" @click="this.showCommentForCurrier = true">+ Добавить комментарий для курьера</p>
        <div v-else>
            <p @click="this.showCommentForCurrier = false">- Убрать комментарий для курьера</p>
            <textarea placeholder="введите комментарий для курьера" style="width: 300px"></textarea>
        </div>
        <br>

        <label for="date-select">выберите дату:</label>
        <select name="date" id="pet-select">
            <option value="сегодня">сегодня</option>
            <option value="завтра">завтра </option>
        </select>

        <label for="date-select">выберите время:</label>
        <select name="time" id="pet-select" style="margin-bottom: 20px; margin-right: 10px">
            <option value="сегодня">10:00 - 12:00</option>
            <option value="завтра">10:00 - 12:00</option>
        </select>
        <br>
        <p>Особые пожелания:</p>
        <textarea placeholder="Комментарии" style="width: 300px"></textarea>
        <br>
        <button @click="" class="btn btn-dark" style="margin-bottom: 20px; margin-right: 10px">Оплатить заказ</button>

    </div>
</template>

<script>
import axios from "axios";
import cartMixin from "../mixins/cartMixin.js";
import AddressInput from "../components/AddressInput.vue";

export default {
    name: "Checkout",
    components: {AddressInput},
    mixins: [cartMixin],
    data() {
        return {
            showCommentForCurrier: false,
        }
    },
    mounted() {
        this.getCartData()
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
    }
}
</script>

<style scoped>

</style>
