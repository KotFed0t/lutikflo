<template>
    <h3 style="text-align: center">Home</h3>
    <Filters></Filters>
    <hr>
    <div v-for="category in categoriesWithProducts" :key="category.id">
        <h4>{{category.name}}</h4>
        <div v-for="product in category.products" style="display: inline-block">
            <div style="border: black solid 1px; width: 300px; padding: 10px; margin-right: 20px; margin-bottom: 20px">
                <img src="http://127.0.0.1:8000/storage/products/IuhswyYODPdg01BEfiVdyH0HZxyExLsjLYe7eFp0.jpg" style="width: 200px">
                <p>название: {{product.name}}</p>
                <p>цена: {{ product.price}}</p>
                <button class="btn btn-dark">в корзину</button>
            </div>

        </div>
    </div>


</template>

<script>
import axios from "axios";
import Filters from "../components/Filters.vue";
import Categories from "../components/Categories.vue";

export default {
    name: "Home",
    components: {Categories, Filters},
    data() {
        return {
            categoriesWithProducts: undefined
        }
    },
    mounted() {
        this.getCategoriesWithProducts()
    },
    methods: {
        getCategoriesWithProducts() {
            axios.get('api/categories/products').then(response => {
                this.categoriesWithProducts = response.data.data
                console.log(response.data)
            }).catch(err => {
                console.log(err)
            })
        }
    }
}
</script>

<style scoped>

</style>
