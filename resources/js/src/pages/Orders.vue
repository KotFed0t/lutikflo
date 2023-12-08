<template>
    <h3 class="my-8 text-xl font-medium sm:my-10 sm:text-2xl">Заказы</h3>
    <div v-if="orders.length > 0">
        <div v-for="order in orders" class="mb-14 bg-neutral-50 p-4 rounded-lg drop-shadow-xl text-sm sm:text-base">
            <div class="sm:flex sm:justify-between">
                <div>
                    <div class="flex items-center mb-2">
                        <p class="font-medium">Заказ № {{order.id}}</p>
                        <p class="ml-2">от {{ order.created_at }}</p>
                    </div>

                    <div class="flex items-center mb-2">
                        <p>Статус:</p>
                        <p class="bg-neutral-200 ml-2 rounded-lg px-2 py-0.5">{{ order.status }}</p>
                    </div>

                    <div class="flex items-center mb-2">
                        <p>Адрес доставки:</p>
                        <p class="ml-2 font-medium">{{order.delivery_address}}</p>
                    </div>

                    <div class="flex items-center mb-2">
                        <p>Дата и время доставки:</p>
                        <p class="ml-2 font-medium">{{order.delivery_date_time ? order.delivery_date_time : 'уточнить у получателя'}}</p>
                    </div>

                    <div v-if="order.recipient_phone" class="flex items-center mt-2">
                        <p>Телефон получателя:</p>
                        <p class="ml-2 font-medium">{{order.recipient_phone}}</p>
                    </div>
                </div>

                <div class="mt-4 sm:mt-0 sm:text-right">
                    <p class="mb-1 sm:mb-0">За товары: {{order.order_price}} ₽</p>
                    <p class="mb-1 sm:mb-0">Доставка: {{order.delivery_price}} ₽</p>
                    <p class="font-medium">Итого: {{order.total_price}} ₽</p>
                </div>
            </div>

            <hr class="mt-4">

            <div v-for="product in order.products" class="py-4 border-b">
                <div class="flex justify-between">
                    <div class="flex">
                        <router-link
                            :to="{
                        name: 'productDetail',
                        params: {productSlug: product.slug},
                        query: {flower_count: product.flower_count !== null ? product.flower_count : undefined}
                        }"
                        >
                            <img class="rounded-lg w-24 sm:w-28" :src="'/storage/' + product.main_img" alt="img"/>
                        </router-link>

                        <div class="hidden sm:inline-block sm:ml-4">
                            <router-link
                                :to="{
                                        name: 'productDetail',
                                        params: {productSlug: product.slug},
                                        query: {flower_count: product.flower_count !== null ? product.flower_count : undefined}
                                        }"
                            >
                                <p class="font-medium">{{product.name}} {{ product.flower_count ? '( ' + product.flower_count + ' шт. )' : '' }}</p>
                            </router-link>
                        </div>
                    </div>

                    <div class="text-right">
                        <p class="font-medium">Цена: {{ product.price_by_count_product }} ₽</p>
                        <p class="text-xs sm:text-sm">{{ product.product_count}} шт. x  {{ product.price_by_one_product }} ₽</p>
                    </div>

                </div>

                <div class="mt-4 sm:hidden">
                    <router-link
                        :to="{
                            name: 'productDetail',
                            params: {productSlug: product.slug},
                            query: {flower_count: product.flower_count !== null ? product.flower_count : undefined}
                            }"
                    >
                        <p class="font-medium">{{product.name}} {{ product.flower_count ? '( ' + product.flower_count + ' шт. )' : '' }}</p>
                    </router-link>
                </div>

            </div>
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
