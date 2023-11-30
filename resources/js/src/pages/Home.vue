<template>
    <Filters></Filters>
    <hr>
    <div v-for="category in categoriesWithProducts" :key="category.id" class="mt-3">
        <h4 class="text-xl mb-4 mt-4 md:text-2xl">{{ category.name }}</h4>
        <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-4">
            <div v-for="product in category.products" :key="product.id">
                <ProductCard :product="product"></ProductCard>
            </div>
        </div>

        <div class="mt-8 mb-10" @click.prevent="$router.push({name: 'catalog', params: {categorySlug: category.slug} })">
            <button class="flex items-center shadow-xl bg-neutral-100 rounded-full py-2 px-3 mx-auto hover:shadow-none transition-shadow duration-300">
                <span class="mr-2">Показать больше</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12.75 15l3-3m0 0l-3-3m3 3h-7.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </button>
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
