<template>

    <router-link class="" :to="{name: 'productDetail', params: {productSlug: product.slug}}">
        <div
            class="max-w-[300px] shadow-xl rounded-lg border border-transparent hover:shadow-none hover:border-neutral-300 transition-all duration-300 max-w-xs">
            <img :src="'/storage/'+product.main_img" class="rounded-t-lg w-full">
            <div class="px-3 py-2">
                <p class="text-xs font-bold text-neutral-600 h-8 overflow-hidden overflow-ellipsis leading-tight mb-1 whitespace-normal">{{ name }}</p>
                <p class="text-sm font-bold mb-3">{{ product.price }} руб</p>
                <div class="flex justify-center">
                    <button v-if="product.is_changeable_flower_count"
                            class="text-xs bg-black text-white rounded-lg hover:bg-neutral-700 py-2 w-full">Выбрать количество
                    </button>
                    <button v-if="!inCart && !product.is_changeable_flower_count" @click.prevent="addToCart"
                            class="text-xs bg-black text-white rounded-lg hover:bg-neutral-700 py-2 w-full"
                    >
                        Добавить в корзину
                    </button>
                    <button v-if="inCart && !product.is_changeable_flower_count" @click.prevent="$router.push({name: 'cart'})"
                            class="text-xs bg-neutral-200 rounded-lg hover:bg-neutral-400 py-2 w-full ">уже в корзине
                    </button>
                </div>
            </div>
        </div>
    </router-link>
</template>

<script>
import router from "../router/router.js";

export default {
    name: "ProductCard",
    props: {
        product: {
            type: Object,
        }
    },
    computed: {
        inCart() {
            return this.$store.getters.cart.findIndex(item => item.product_id === this.product.id) !== -1;
        },
        name() {
            if (this.product.is_changeable_flower_count) {
                return this.product.name + ' (' + this.product.changeable_flower.count + 'шт.)'
            }
            return this.product.name
        }
    },
    methods: {
        router() {
            return router
        },
        addToCart() {
            this.$store.dispatch('addToCart', {product_id: this.product.id})
        }
    }
}
</script>

<style scoped>

</style>
