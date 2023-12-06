<template>
    <h3>Заказы</h3>
    <div v-if="orders.length > 0">
        <div v-for="order in orders" class="mb-8 bg-neutral-50 p-4 rounded-lg shadow-xl">
            <div class="flex items-center mb-2">
                <p class="font-medium">Заказ № {{order.id}}</p>
                <p class="ml-2">от {{ order.created_at }}</p>
            </div>

            <div class="flex items-center">
                <p>статус</p>
                <p class="bg-neutral-200 ml-2 rounded-lg px-2 py-0.5">{{ order.status }}</p>
            </div>

            <p>Адрес доставки: {{order.delivery_address}}</p>
            <p>Дата и время доставки: {{order.delivery_date_time}}</p>

        </div>
    </div>

</template>

<script>
import axios from "axios";

export default {
    name: "Orders",
    data() {
        return {
            orders: [],
        }
    },
    mounted() {
        this.getOrders()
    },
    methods: {
        getOrders() {
            axios.get('api/orders').then(response => {
                console.log(response)
                this.orders = response.data.data
            }).catch(err => {
                console.log(err)
            })
        }
    }
}
</script>

<style scoped>

</style>
