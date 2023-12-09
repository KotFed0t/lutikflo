<template>



    <div class="flex flex-col mt-14 mb-28 sm:flex-row sm:mt-20">
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
        <div class="mt-8 mx-1 sm:w-2/5 sm:mt-0 sm:mx-10 lg:ml-0">
            <h3 class="text-xl font-bold">{{ name }}</h3>
            <div v-if="product?.is_changeable_flower_count" class="mt-8 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                </svg>
                <p class="text-sm ml-1 font-medium">Выберите кол-во цветов в букете:</p>
            </div>
            <div class="mt-10 flex justify-between">
                <p class="text-xl font-bold">{{ price }} ₽</p>
                <div v-if="product?.is_changeable_flower_count" class="flex items-center">
                    <button @click="decreaseFlowerCount" :disabled="flower_count === product.changeable_flower.count"
                            class="pr-2 bg-neutral-100 p-1.5 rounded-l-md">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15"/>
                        </svg>
                    </button>
                    <div class="pr-2 bg-neutral-100 p-1.5">
                        {{ flower_count }}
                    </div>
                    <button @click="increaseFlowerCount" class="bg-neutral-100 p-1.5 rounded-r-md">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="my-10">
                <button v-if="!inCart && product?.is_active === true"
                        @click="addToCart"
                        class=" bg-black text-white rounded-lg hover:bg-neutral-700 py-2 w-full"
                >
                    Добавить в корзину
                </button>
                <button v-else-if="inCart && product?.is_active === true"
                        class=" bg-neutral-200 rounded-lg hover:bg-neutral-400 py-2 w-full"
                        @click.prevent="$router.push({name: 'cart'})">уже в корзине
                </button>
                <button v-else-if="product?.is_active !== true"
                        class="disabled bg-neutral-200 rounded-lg py-2 w-full"
                >Товар закончился
                </button>
            </div>

            <div v-if="product?.flowers.length > 0" class="mt-5">
                <h3 class="text-lg font-medium">Состав:</h3>
                <p v-for="flower in product?.flowers">
                    <span v-if="product?.is_changeable_flower_count">
                        {{ flower.name }} - {{ flower_count }} шт.
                    </span>
                    <span v-else>
                        {{ flower.name }} - {{ flower.count }} шт.
                    </span>
                </p>
            </div>

            <div v-if="product?.packages.length > 0" class="mt-5">
                <h3 class="text-lg font-medium">Упаковка:</h3>
                <p v-for="product_package in product?.packages">
                    {{ product_package.name }} - {{ product_package.count }} шт.
                </p>
            </div>

            <div v-if="product?.description" class="mt-5">
                <h3 class="text-lg font-medium">Описание:</h3>
                <div v-html="product.description"></div>
            </div>

        </div>
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
        },
        name() {
            if (this.product?.is_changeable_flower_count) {
                return this.product.name + ' (' + this.flower_count + ' шт.)'
            }
            return this.product?.name
        },
        price() {
            if (this.product?.is_changeable_flower_count) {
                return this.product.price + (this.flower_count - this.product.flowers[0].count) * this.product.flowers[0].price_by_flower
            }
            return this.product?.price
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
                    } else {
                        this.flower_count = response.data.data.changeable_flower.count
                    }
                }
                if (response.data.data.main_img) {
                    this.images.push(response.data.data.main_img)
                }
                if (response.data.data.images) {
                    this.images.push(...response.data.data.images.map(obj => obj['image']))
                }

                this.isProductInCart()
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
