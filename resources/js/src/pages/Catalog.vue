<template>
    <Filters></Filters>
    <hr>
    <h3 class="text-2xl font-bold my-8">{{ categoryName }}</h3>

    <div class="flex mb-8 text-sm items-center sm:text-base">
        <p class="mr-2">Сортировать по:</p>
        <select v-model="sort" @change="sortProducts" class="border px-2 py-1.5 rounded-lg bg-neutral-50">
        <option value="popular" :selected="sort === 'popular'">По популярности</option>
        <option value="price-asc" :selected="sort === 'price-desc'">Цена по возрастанию</option>
        <option value="price-desc" :selected="sort === 'price-asc'">Цена по убыванию</option>
        <option value="novelty" :selected="sort === 'price-asc'">По новизне</option>
    </select>
    </div>



    <div class="grid grid-cols-2 gap-3 gap-y-5 sm:grid-cols-3 lg:grid-cols-4 lg:gap-5 lg:gap-y-8">
        <div v-for="product in products">
            <ProductCard :product="product"></ProductCard>
        </div>
    </div>

    <div ref="observer" class="observer h-2 mt-24"></div>
</template>

<script>
import Filters from "../components/Filters.vue";
import Categories from "../components/Categories.vue";
import axios from "axios";
import ProductCard from "../components/ProductCard.vue";

export default {
    name: "Catalog",
    components: {ProductCard, Categories, Filters},
    data() {
        return {
            categoryName: undefined,
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
        this.getCategoryName()

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
            this.getCategoryName()
        }
    },
    methods: {
        getCategoryName() {
            if (this.$route.params.categorySlug) {
                axios.get(`api/categories/${this.$route.params.categorySlug}`).then(response => {
                    this.categoryName = response.data.data.name
                }).catch(err => {
                    console.log(err)
                })
            } else {
                this.categoryName = 'Каталог товаров'
            }
        },
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
