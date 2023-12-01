<template>
    product detail
    <!--    {{ product }}-->
    <hr>
    <!--    <div v-if="product?.is_changeable_flower_count">-->
    <!--        <button @click="increaseFlowerCount" class="btn btn-dark">+</button>-->
    <!--        <div>{{ flower_count }}</div>-->
    <!--        <button @click="decreaseFlowerCount" :disabled="flower_count === product.changeable_flower.count"-->
    <!--                class="btn btn-dark">- -->
    <!--        </button>-->
    <!--    </div>-->

    <!--    &lt;!&ndash;  учитывать что продукт может быть не в наличии и выводить disabled кнопку 'товар закончился'  &ndash;&gt;-->
    <!--    <button v-if="!inCart" @click="addToCart" class="btn btn-dark">добавить в корзину</button>-->
    <!--    <button v-else class="btn btn-dark">уже в корзине</button>-->

    <div class="flex flex-col sm:flex-row">
        <div v-if="images.length > 1" class="sm:w-3/5">
            <div @mouseover="showArrows=true"
                 @mouseout="showArrows=false"
                 class="max-w-[500px] mx-auto mb-2 relative flex items-center"
            >
                <button
                    v-show="showArrows"
                    :class="{'bg-neutral-50' : isBeginning}"
                    class="hidden sm:inline-block absolute z-10 top-50 left-5 swiper-main-button-prev bg-neutral-100 rounded-full mr-1 p-2"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="w-6 h-6" :class="{'text-neutral-400' : isBeginning}">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                    </svg>
                </button>
                <swiper @swiper="onSwiperMain" @slide-change="handleMainSwiperSlideChange" :space-between="5"
                        :navigation="{
                          prevEl: '.swiper-main-button-prev',
                          nextEl: '.swiper-main-button-next',
                        }"
                        :slides-per-view="1"
                        :modules="[navigation]"
                >
                    <swiper-slide v-for="(image, index) in images">
                        <img class="rounded-lg" :src="'/storage/' + image"/>
                    </swiper-slide>
                </swiper>
                <button v-show="showArrows" :class="{'bg-neutral-50' : isEnd}"
                        class="hidden sm:inline-block absolute z-10 top-50 right-5 swiper-main-button-next bg-neutral-100 rounded-full p-2 ml-1">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="w-6 h-6" :class="{'text-neutral-400' : isEnd}">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                    </svg>
                </button>
            </div>

            <div class="max-w-[500px] mx-auto flex items-center">
                <button v-if="images.length > 4" :class="{'bg-neutral-50' : isBeginning}"
                        class="hidden sm:inline-block thumb-swiper-button-prev bg-neutral-100 rounded-full drop-shadow-xl mr-1 p-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="w-6 h-6" :class="{'text-neutral-400' : isBeginning}">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                    </svg>
                </button>
                <swiper
                    @swiper="onSwiperThumb"
                    @slide-change="handleThumbSwiperSlideChange"
                    :navigation="{
                      prevEl: '.thumb-swiper-button-prev',
                      nextEl: '.thumb-swiper-button-next',
                    }"
                    :space-between="5"
                    :slides-per-view="4"
                    :modules="[navigation]"
                >
                    <swiper-slide v-for="(image, index) in images">
                        <img @click="changeMainSlide(index)" :class="{'opacity-80': selectedIndex!==index}"
                             class="w-28 rounded-lg" :src="'/storage/' + image"/>
                    </swiper-slide>
                </swiper>

                <button v-if="images.length > 4" :class="{'bg-neutral-50' : isEnd}"
                        class="hidden sm:inline-block thumb-swiper-button-next bg-neutral-100 rounded-full drop-shadow-xl p-2 ml-1">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="w-6 h-6" :class="{'text-neutral-400' : isEnd}">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                    </svg>
                </button>
            </div>
        </div>
        <div v-else-if="images.length === 1" class="sm:w-3/5">
            <div class="max-w-[500px] mx-auto">
                <img class="rounded-lg" :src="'/storage/' + images[0]" alt="img"/>
            </div>

        </div>
        <div class="sm:w-2/5">{{ product }}</div>
    </div>
</template>

<script>
import axios from "axios";
import {Swiper, SwiperSlide} from 'swiper/vue';
import 'swiper/css';
import 'swiper/css/navigation';
import {Navigation} from 'swiper/modules';
import {ref} from "vue";

export default {
    name: "ProductDetail",
    components: {
        Swiper,
        SwiperSlide,
    },
    data() {
        return {
            product: undefined,
            flower_count: undefined,
            inCart: false,
            navigation: Navigation,
            selectedIndex: 0,
            thumbSwiper: null,
            mainSwiper: null,
            isBeginning: true,
            isEnd: true,
            showArrows: false,
            images: [],
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
        changeMainSlide(index) {
            this.selectedIndex = index
            this.mainSwiper.slideTo(index)
        },
        handleMainSwiperSlideChange() {
            this.selectedIndex = this.mainSwiper.activeIndex;
            this.thumbSwiper.slideTo(this.selectedIndex)
            this.isBeginning = this.mainSwiper.isBeginning
            this.isEnd = this.mainSwiper.isEnd
        },
        onSwiperMain(swiper) {
            this.mainSwiper = swiper
            this.isBeginning = swiper.isBeginning
            this.isEnd = swiper.isEnd
        },
        onSwiperThumb(swiper) {
            // Сохраняем ссылку на Swiper, когда он готов
            this.thumbSwiper = swiper
            this.isBeginning = swiper.isBeginning
            this.isEnd = swiper.isEnd
        },
        handleThumbSwiperSlideChange() {
            this.isBeginning = this.thumbSwiper.isBeginning
            this.isEnd = this.thumbSwiper.isEnd
        },
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
                if (response.data.data.main_img) {
                    this.images.push(response.data.data.main_img)
                }
                if (response.data.data.images) {
                    this.images.push(...response.data.data.images.map(obj => obj['image']))
                }
                console.log(this.images)
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
.swiper {
    width: 100%;
    height: 100%;
}

.swiper-slide {
    text-align: center;
    font-size: 18px;
    background: #fff;

    /* Center slide text vertically */
    display: flex;
    justify-content: center;
    align-items: center;
}

.swiper-slide img {
    display: block;
    object-fit: cover;
}
</style>
