<template>
    product detail
    {{ product }}
    <hr>
    <div v-if="product?.is_changeable_flower_count">
        <button @click="increaseFlowerCount" class="btn btn-dark">+</button>
        <div>{{ flower_count }}</div>
        <button @click="decreaseFlowerCount" :disabled="flower_count === product.changeable_flower.count"
                class="btn btn-dark">-
        </button>
    </div>

    <!--  учитывать что продукт может быть не в наличии и выводить disabled кнопку 'товар закончился'  -->
    <button v-if="!inCart" @click="addToCart" class="btn btn-dark">добавить в корзину</button>
    <button v-else class="btn btn-dark">уже в корзине</button>

</template>

<script>
import axios from "axios";

export default {
    name: "ProductDetail",
    data() {
        return {
            product: undefined,
            flower_count: undefined,
            inCart: false,
        }
    },
    mounted() {
        this.getProduct()
    },
    computed: {
        cart() {
            return this.$store.getters.cart
        }
    },
    watch: {
        flower_count() {
            this.isProductInCart()
        }
    },
    methods: {
        isProductInCart() {
            let result = this.cart.findIndex(item =>
                item.product_id === this.product?.id
                && item.flower_count === this.flower_count
            ) !== -1;

            this.inCart = result
        },
        getProduct() {
            axios.get(`api/products/${this.$route.params.productSlug}`).then(response => {
                this.product = response.data.data
                if (response.data.data.changeable_flower) {
                    if (this.$route.query.flower_count > response.data.data.changeable_flower.count) {
                        this.flower_count = Number(this.$route.query.flower_count)
                        return
                    }
                    this.flower_count = response.data.data.changeable_flower.count
                }
            }).catch(err => {
                console.log(err)
            })
        },
        increaseFlowerCount() {
            this.flower_count += 1
        },
        decreaseFlowerCount() {
            this.flower_count -= 1
        },
        addToCart() {
            if (this.product.is_changeable_flower_count) {
                this.$store.dispatch('addToCart', {product_id: this.product.id, flower_count: this.flower_count})
            } else {
                this.$store.dispatch('addToCart', {product_id: this.product.id})
            }
            this.inCart = true
        }
    }
}
</script>

<style scoped>

</style>
