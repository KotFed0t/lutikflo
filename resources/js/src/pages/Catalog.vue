<template>
    <h3 style="text-align: center">Catalog</h3>
    <Filters></Filters>
    <hr>
    Сортировать по:
    <select v-model="sort" @change="sortProducts">
        <option value="popular" :selected="sort === 'popular'">По популярности</option>
        <option value="price-asc" :selected="sort === 'price-desc'">Цена по возрастанию</option>
        <option value="price-desc" :selected="sort === 'price-asc'">Цена по убыванию</option>
        <option value="novelty" :selected="sort === 'price-asc'">По новизне</option>
    </select>
    <hr>
    <div v-for="product in products" style="margin-bottom: 20px">
        {{product}}
    </div>
    <div ref="observer" class="observer" style="height: 30px; border: black solid 2px;"></div>
</template>

<script>
import Filters from "../components/Filters.vue";
import Categories from "../components/Categories.vue";
import axios from "axios";

export default {
    name: "Catalog",
    components: {Categories, Filters},
    data() {
        return {
            category: undefined,
            flower_types_id: undefined,
            price_from: undefined,
            price_to: undefined,
            products: [],
            sort: 'popular',
            categorySlug: '',
            currentPage: 1,
            lastPage: 0
        }
    },
    mounted() {
        this.getProducts()
        this.initFromQueryParams()

        let options = {
            rootMargin: "0px",
            threshold: 1.0,
        };
        let observer = new IntersectionObserver((entries, observer) => {
            if (entries[0].isIntersecting && this.currentPage <= this.lastPage) {
                this.getProducts()
            }
        }, options);
        observer.observe(this.$refs.observer)
    },
    watch: {
        $route() {
            this.currentPage = 1
            this.lastPage = 0
            this.products = []
            this.getProducts()
            this.resetSort()
        }
    },
    methods: {
        getProducts() {
            let params = {}
            if (this.$route.params.categorySlug) {
                params.category_slug = this.$route.params.categorySlug
            }
            if (this.$route.query.flower_types_id) {
                if (Array.isArray(this.$route.query.flower_types_id)) {
                    params.flower_types_id = this.$route.query.flower_types_id
                } else {
                    params.flower_types_id = [this.$route.query.flower_types_id]
                }
            }
            if (this.$route.query.price_from) {
                params.price_from = this.$route.query.price_from
            }
            if (this.$route.query.price_to) {
                params.price_to = this.$route.query.price_to
            }
            if (this.$route.query.sort) {
                params.sort = this.$route.query.sort
            }

            params.page = this.currentPage

            axios.get('api/products', {params: params}).then(response => {
                this.products = [...this.products, ...response.data.data]
                this.currentPage += 1
                this.lastPage = response.data.meta.last_page
            }).catch(err => {
                console.log(err)
            })
        },
        sortProducts() {
            console.log(this.sort)
            let categorySlug = ''
            if (this.$route.name === 'catalog' && this.$route.params.categorySlug) {
                categorySlug = this.$route.params.categorySlug
            }

            this.$router.push({
                name: 'catalog',
                params: {categorySlug: categorySlug},
                query: {
                    ...this.$route.query,
                    sort: this.sort,
                }
            })
        },
        initFromQueryParams() {
            if (this.$route.query.sort) {
                this.sort = this.$route.query.sort
            }
            if (this.$route.params.categorySlug) {
                this.categorySlug = this.$route.params.categorySlug
            }
        },
        resetSort() {
            if (this.categorySlug !== this.$route.params.categorySlug) {
                this.categorySlug = this.$route.params.categorySlug
                this.sort = 'popular'
            }
        }
    }
}
</script>

<style scoped>

</style>
