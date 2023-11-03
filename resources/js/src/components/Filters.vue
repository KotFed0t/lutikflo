<template>
    <h5>Фильтры</h5>
    <h6>типы цветов</h6>
    <div style="display: flex;">
        <div
            v-for="flowerType in flowerTypes"
            :key="flowerType.id"
            @click="selectFlowerType(flowerType.id)"
            style="margin-right: 30px; border: black solid 1px; padding: 3px;"
            :class="{'selected': selectedFlowerTypes.includes(flowerType.id)}"
        >
            <div>{{ flowerType.name }}</div>
        </div>
    </div>
    <h5>цены</h5>
    <input type="number" v-model="price_from" :placeholder="minPrice" style="margin-right: 20px">
    <input type="number" v-model="price_to" :placeholder="maxPrice" style="margin-right: 20px">
    <button @click="pushToCatalogWithParams" class="btn btn-dark">применить все фильтры</button>
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
