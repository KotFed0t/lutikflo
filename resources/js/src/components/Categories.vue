<template>
    <h5 class="font-medium mb-2">Категории</h5>
    <div class="flex flex-wrap mb-5">
        <router-link
            v-for="category in categories"
            :key="category.id"
            class="font-medium  rounded p-1.5 px-2 mr-3 mb-2 shadow-lg hover:shadow-inner hover:bg-gray-200 transition-all duration-300"
            :class="isSelectedCategory(category.slug) ? 'bg-gray-200 shadow-inner' : 'bg-white'"
            :to="{name: 'catalog', params: {categorySlug: category.slug}}"
        >
            {{ category.name }}
        </router-link>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "Categories",
    data() {
        return {
            categories: [],
        }
    },
    mounted() {
        this.getCategories()
    },
    methods: {
        getCategories() {
            axios.get('api/categories').then(response => {
                this.categories = response.data.data
            }).catch(err => {
                console.log(err)
            })
        },
        isSelectedCategory(categorySlug) {
            if (this.$route.params.categorySlug) {
                return this.$route.params.categorySlug === categorySlug
            }
        },
    }
}
</script>

<style scoped>

</style>
