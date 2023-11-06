export default {
    data() {
        return {
            cartFullData: undefined,
        }
    },
    computed: {
        cart() {
            return this.$store.getters.cart
        }
    },
    methods: {
        getCartItemCount(cartItem) {
            return this.cart.find(item =>
                item.product_id === cartItem.product_id
                && item.flower_count === cartItem.flower_count
            ).count
        },
        getCartItemPrice(cartItem) {
            if (cartItem.flower_count !== undefined) {
                let additional_flower_price = (cartItem.flower_count - cartItem.flower.min_count) * cartItem.flower.price
                return (cartItem.product.price + additional_flower_price) * this.getCartItemCount(cartItem)
            }
            return cartItem.product.price * this.getCartItemCount(cartItem)
        },
        calculateTotalPrice() {
            return this.cartFullData?.reduce((sum, item) => sum + this.getCartItemPrice(item), 0)
        }
    }
}
