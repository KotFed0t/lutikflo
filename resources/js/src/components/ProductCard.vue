<template>
    <div style="border: black solid 1px; width: 300px; padding: 10px; margin-right: 20px; margin-bottom: 20px">
        <router-link class="nav-link" :to="{name: 'productDetail', params: {productSlug: product.slug}}">
            <img src="http://127.0.0.1:8000/storage/products/IuhswyYODPdg01BEfiVdyH0HZxyExLsjLYe7eFp0.jpg" style="width: 200px">
        </router-link>

        <p>название: {{name}}</p>
        <p>цена: {{ product.price}}</p>
        <button v-if="product.is_changeable_flower_count" class="btn btn-dark">Выбрать количество цветов</button>
        <button v-if="!inCart && !product.is_changeable_flower_count" @click="addToCart" class="btn btn-dark">Добавить в корзину</button>
        <button v-if="inCart && !product.is_changeable_flower_count" class="btn btn-dark">уже в корзине</button>
    </div>
</template>

<script>
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
               return this.product.name +' ('+ this.product.changeable_flower.count + 'шт.)'
            }
            return this.product.name
        }
    },
    methods: {
        addToCart() {
            this.$store.dispatch('addToCart', {product_id: this.product.id})
        }
    }
}
</script>

<style scoped>

</style>
