import axios from "axios";
import Cookies from "js-cookie";

export const cartModule = {
    state: () => ({
        cart: JSON.parse(localStorage.getItem('cart')) || []
    }),
    getters: {
        cart: state => state.cart,
        itemsCount: state => state.cart.length,
    },
    mutations: {
        updateCart(state, cart) {
            state.cart = cart
        }
    },
    actions: {
        addToCart(context, {product_id, flower_count = undefined}) {
            let cart = JSON.parse(localStorage.getItem('cart')) || []
            let cart_item = {product_id: product_id, count: 1}
            if (flower_count !== undefined) {
                cart_item.flower_count = flower_count
            }
            let existed_item_id = cart.findIndex(item => item.product_id === product_id && item.flower_count === flower_count);
            if (existed_item_id !== -1) {
                cart[existed_item_id].count += 1
                localStorage.setItem('cart', JSON.stringify(cart))
                context.commit('updateCart', cart)
            } else {
                cart.push(cart_item)
                localStorage.setItem('cart', JSON.stringify(cart))
                context.commit('updateCart', cart)
            }
        },
        deleteFromCart(context, {product_id, flower_count = undefined}) {
            let cart = JSON.parse(localStorage.getItem('cart'))
            let existed_item_id = cart.findIndex(item => item.product_id === product_id && item.flower_count === flower_count);
            if (existed_item_id !== -1) {
                cart.splice(existed_item_id, 1)
                localStorage.setItem('cart', JSON.stringify(cart))
                context.commit('updateCart', cart)
            }
        },
        decreaseProductCount(context, {product_id, flower_count = undefined}) {
            let cart = JSON.parse(localStorage.getItem('cart'))
            let existed_item_id = cart.findIndex(item => item.product_id === product_id && item.flower_count === flower_count);
            if (existed_item_id !== -1) {
                if (cart[existed_item_id].count === 1) {
                    cart.splice(existed_item_id, 1)
                } else {
                    cart[existed_item_id].count -= 1
                }
                localStorage.setItem('cart', JSON.stringify(cart))
                context.commit('updateCart', cart)
            }
        }
    },
}

