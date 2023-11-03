<template>
    <h5 >Категории</h5>
    <div style="display: flex;">
        <div
            v-for="category in categories"
            :key="category.id"
            style="margin-right: 30px; border: black solid 1px; padding: 3px;"
            :class="{'selected': isSelectedCategory(category.slug)}"
        >
            <router-link class="nav-link" :to="{name: 'catalog', params: {categorySlug: category.slug}}">{{ category.name }}</router-link>
        </div>
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
.selected {
    background: rgb(128, 128, 128);
}
</style>
