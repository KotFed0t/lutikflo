<template>
    <h5 class="font-medium mb-2">Цветы в составе</h5>
    <div class="flex flex-wrap mb-5">
        <button
            v-for="flowerType in flowerTypes"
            :key="flowerType.id"
            @click="selectFlowerType(flowerType.id)"
            class="text-sm rounded-full font-medium p-1.5 px-3 mr-3 mb-2 hover:shadow-lg transition-all duration-300 sm:text-base"
            :class="selectedFlowerTypes.includes(flowerType.id) ? 'bg-neutral-400 shadow-lg' : 'bg-neutral-100'"
        >
            {{ flowerType.name }}
        </button>
    </div>
    <h5 class="font-medium mb-2">Цена</h5>
    <div class="mb-3">
        <input type="number" v-model="price_from" :placeholder="' от '+minPrice"
               class="w-28 px-2 border border-neutral-300 border-2 rounded-lg mb-2 mr-2 focus:outline-none focus:border-neutral-400">
        <input type="number" v-model="price_to" :placeholder="' до '+maxPrice"
               class="w-28 px-2 border border-neutral-300 border-2 rounded-lg mb-2 mr-2 focus:outline-none focus:border-neutral-400">
        <button v-if="selectedFlowerTypes.length !== 0 || price_from || price_to" @click="pushToCatalogWithParams"
                class="py-1.5 px-2 rounded-lg text-white bg-black hover:bg-neutral-700">применить
        </button>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "Filters",
    data() {
        return {
            flowerTypes: [],
            minPrice: 0,
            maxPrice: 0,
            selectedFlowerTypes: [],
            price_from: undefined,
            price_to: undefined,
            categorySlug: ''
        }
    },
    mounted() {
        this.getFilters()
        this.initializeFromQueryParams()
    },
    watch: {
        $route() {
            this.updateFilters()
        }
    },
    methods: {
        getFilters() {
            let params = {}
            if (this.$route.params.categorySlug) {
                params = {category_slug: this.$route.params.categorySlug}
            }
            axios.get('api/filters', {params: params}).then(response => {
                this.flowerTypes = response.data.flowerTypes
                this.minPrice = response.data.price.min
                this.maxPrice = response.data.price.max
            }).catch(err => {
                console.log(err)
            })
        },
        selectFlowerType(id) {
            if (this.selectedFlowerTypes.includes(id)) {
                let index = this.selectedFlowerTypes.indexOf(id)
                this.selectedFlowerTypes.splice(index, 1)
            } else {
                this.selectedFlowerTypes.push(id)
            }
        },
        pushToCatalogWithParams() {
            let categorySlug = ''
            if (this.$route.name === 'catalog' && this.$route.params.categorySlug) {
                categorySlug = this.$route.params.categorySlug
            }

            this.$router.push({
                name: 'catalog',
                params: {categorySlug: categorySlug},
                query: {
                    ...this.$route.query,
                    flower_types_id: this.selectedFlowerTypes,
                    price_from: this.price_from,
                    price_to: this.price_to
                }
            })
        },
        initializeFromQueryParams() {
            if (this.$route.query.flower_types_id) {
                if (Array.isArray(this.$route.query.flower_types_id)) {
                    this.selectedFlowerTypes = this.$route.query.flower_types_id.map(Number)
                } else {
                    this.selectedFlowerTypes.push(Number(this.$route.query.flower_types_id))
                }
            }
            if (this.$route.query.price_from) {
                this.price_from = this.$route.query.price_from
            }
            if (this.$route.query.price_to) {
                this.price_to = this.$route.query.price_to
            }
            if (this.$route.params.categorySlug) {
                this.categorySlug = this.$route.params.categorySlug
            }
        },
        updateFilters() {
            if (this.categorySlug !== this.$route.params.categorySlug) {
                this.categorySlug = this.$route.params.categorySlug
                this.selectedFlowerTypes = []
                this.price_from = undefined
                this.price_to = undefined
                this.getFilters()
            }
        }
    }
}
</script>

<style scoped>
.selected {
    background: rgb(128, 128, 128);
}
</style>
