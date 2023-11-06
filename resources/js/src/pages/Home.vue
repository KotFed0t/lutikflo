<template>
    <h3 style="text-align: center">Home</h3>
    <Filters></Filters>
    <hr>
    Корзина <br>
    {{this.$store.getters.cart}}
    <hr>
    <div v-for="category in categoriesWithProducts" :key="category.id">
        <h4>{{category.name}}</h4>
        <div v-for="product in category.products" :key="product.id" style="display: inline-block">
            <ProductCard :product="product"></ProductCard>
        </div>
    </div>


</template>

<script>
import axios from "axios";
import Filters from "../components/Filters.vue";
import Categories from "../components/Categories.vue";
import ProductCard from "../components/ProductCard.vue";

export default {
    name: "Home",
    components: {ProductCard, Categories, Filters},
    data() {
        return {
            categoriesWithProducts: undefined
        }
    },
    mounted() {
        this.getCategoriesWithProducts()
        // this.$store.dispatch('addToCart', {product_id: 6})
        // console.log(this.$store.getters.cart)
    },
    methods: {
        getCategoriesWithProducts() {
            axios.get('api/categories/products').then(response => {
                this.categoriesWithProducts = response.data.data
                console.log(response.data.data)
            }).catch(err => {
                console.log(err)
            })
        }
    }
}
</script>

<style scoped>

</style>
