<template>
    <h5 class="font-medium mb-2">Категории</h5>
    <div class="flex flex-wrap mb-5">
        <router-link
            :to="{name: 'catalog'}"
            class="text-sm font-medium rounded p-1.5 px-2 mr-3 mb-2 hover:shadow-inner hover:bg-neutral-300 transition-all duration-300 sm:text-base"
            :class="$route.name === 'catalog' && !$route.params.categorySlug ? 'bg-neutral-300 shadow-inner' : 'bg-neutral-100 shadow-lg'"
        >
            Весь каталог
        </router-link>
        <router-link
            v-for="category in categories"
            :key="category.id"
            class="text-sm font-medium rounded p-1.5 px-2 mr-3 mb-2 hover:shadow-inner hover:bg-neutral-300 transition-all duration-300 sm:text-base"
            :class="isSelectedCategory(category.slug) ? 'bg-neutral-300 shadow-inner' : 'bg-neutral-100 shadow-lg'"
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
