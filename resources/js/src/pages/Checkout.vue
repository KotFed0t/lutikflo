<template>
    <div class="lg:flex items-start justify-center mt-10">
        <div class="max-w-[640px] lg:w-8/12 lg:mr-auto">

            <div class="grid grid-cols-1 sm:grid-cols-2">
                <div>
                    <label for="name" class="block text-neutral-700 text-sm font-medium mb-1">Ваше имя</label>
                    <input id="name" type="text" v-model="name" placeholder="ваше имя" :disabled="disableName"
                           :class="{'bg-neutral-200 ': disableName}"
                           class="w-full border border-black rounded-lg px-2 py-1.5">
                    <button v-if="currentName && disableName === true" @click="disableName=false"
                            class="block text-neutral-700 hover:text-black text-sm mt-1">редактировать
                    </button>
                    <button v-else-if="currentName && disableName === false" @click="disableName=true; name=currentName"
                            class="block text-neutral-700 hover:text-black text-sm mt-1">отменить
                        редактирование
                    </button>
                </div>
                <div class="flex items-center mt-5 sm:ml-5 sm:mt-0">
                    <input type="checkbox" v-model="is_anonymous_sender">
                    <p class="ml-2">доставить анонимно</p>
                </div>

            </div>


            <br>
            <p v-if="errors['email']"
               class="text-sm font-medium text-red-800 mb-2 bg-red-50 px-2 py-1.5 rounded-lg my-1 sm:w-1/2">
                {{ errors['email'] }}</p>
            <p v-if="emailWarning"
               class="text-sm font-medium text-red-800 mb-2 bg-red-50 px-2 py-1.5 rounded-lg my-1 sm:w-1/2">возможно
                email введен некорректно</p>
            <label for="email" class="block text-neutral-700 text-sm font-medium mb-1">email <span
                class="text-red-500 font-bold">*</span></label>
            <input id="email" type="email" v-model="email" placeholder="email" :disabled="disableEmail"
                   :class="{'bg-neutral-200 ': disableEmail}"
                   class="w-full border border-black rounded-lg px-2 py-1.5 sm:w-1/2">
            <button v-if="currentEmail && disableEmail === true" @click="disableEmail=false"
                    class="block text-neutral-700 hover:text-black text-sm mt-1">редактировать
            </button>
            <button v-else-if="currentEmail && disableEmail === false" @click="disableEmail=true; email=currentEmail"
                    class="block text-neutral-700 hover:text-black text-sm mt-1">отменить
                редактирование
            </button>


            <br>
            <div class="mb-5 flex">
                <button @click="is_recipient_current_user=false"
                        :class="{'bg-black text-white': !is_recipient_current_user}"
                        class="block border border-black px-2 py-1.5 rounded-lg mr-4"
                        :disabled="!is_recipient_current_user">доставить получателю
                </button>
                <button @click="is_recipient_current_user=true"
                        :class="{'bg-black text-white': is_recipient_current_user}"
                        class="block border border-black px-2 py-1.5 rounded-lg"
                        :disabled="is_recipient_current_user">доставить мне
                </button>
            </div>

            <div v-if="!is_recipient_current_user">
                <p class="font-medium mb-1">Мы можем согласовать дату и время с получателем:</p>
                <div>
                    <input type="radio" id="yes" class="mr-2 mb-2" :value="true" v-model="previously_call_to_recipient"
                           :checked="previously_call_to_recipient===true"/>
                    <label for="yes">Да, согласовать с получателем</label>
                </div>
                <div>
                    <input type="radio" id="no" class="mr-2" :value="false" v-model="previously_call_to_recipient"
                           :checked="previously_call_to_recipient===false"/>
                    <label for="no">Нет, не звонить получателю</label>
                </div>
                <br>
                <p v-if="errors['recipient_phone']"
                   class="text-sm font-medium text-red-800 mb-2 bg-red-50 px-2 py-1.5 rounded-lg my-1">
                    {{ errors['recipient_phone'] }}</p>
                <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="recipientName" class="block text-neutral-700 text-sm font-medium mb-1">имя
                            получателя</label>
                        <input v-model="recipient_name"
                               id="recipientName"
                               type="text"
                               placeholder="имя получателя"
                               class="w-full border border-black rounded-lg px-2 py-1.5"
                        >
                    </div>

                    <div>
                        <label for="recipientPhone" class="block text-neutral-700 text-sm font-medium mb-1">телефон
                            получателя <span v-if="previously_call_to_recipient" class="text-red-500 font-bold">*</span></label>
                        <input
                            id="recipientPhone"
                            type="tel"
                            v-model="recipient_phone"
                            v-mask="'7##########'"
                            placeholder="+7"
                            class="w-full border border-black rounded-lg px-2 py-1.5"
                        >
                    </div>

                </div>


            </div>

            <p v-if="errors['delivery_address']"
               class="text-sm font-medium text-red-800 mb-2 bg-red-50 px-2 py-1.5 rounded-lg my-1">
                {{ errors['delivery_address'] }}</p>
            <AddressInput @get="getDeliveryAddressData" @getPrice="getDeliveryPrice"></AddressInput>


            <div class="grid grid-cols-3 gap-2 mt-4">
                <div>
                    <label for="entrance" class="block text-neutral-700 text-sm font-medium mb-1">подъезд</label>
                    <input id="entrance" type="text" placeholder="подъезд"
                           class="w-full  border border-black rounded-lg px-2 py-1.5" v-model="entrance">
                </div>

                <div>
                    <label for="floor" class="block text-neutral-700 text-sm font-medium mb-1">этаж</label>
                    <input id="floor" type="text" placeholder="этаж"
                           class="w-full border border-black rounded-lg px-2 py-1.5" v-model="floor">
                </div>

                <div>
                    <label for="houseNumber" class="block text-neutral-700 text-sm font-medium mb-1">квартира</label>
                    <input id="houseNumber" type="text" placeholder="квартира"
                           class="w-full  border border-black rounded-lg px-2 py-1.5" v-model="houseNumber">
                </div>
            </div>

            <br>

            <div v-if="!showCommentForCurrier" @click="this.showCommentForCurrier = true"
                 class="flex items-center cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                <p class="ml-1">Добавить комментарий для курьера</p>
            </div>
            <div v-else>
                <div @click="this.showCommentForCurrier = false" class="flex items-center cursor-pointer mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15"/>
                    </svg>
                    <p class="ml-1">Убрать комментарий для курьера</p>
                </div>

                <textarea v-model="comment_for_courier" placeholder="введите комментарий для курьера"
                          class="resize-none h-32 w-full p-2 border border-black rounded-lg"></textarea>
            </div>
            <br>
            <p class="font-medium mb-2">Часовой пояс: Новосибирск UTC+7</p>

            <p v-if="errors['delivery_date_time']"
               class="text-sm font-medium text-red-800 mb-2 bg-red-50 px-2 py-1.5 rounded-lg my-1">
                {{ errors['delivery_date_time'] }}</p>
            <div class="grid grid-cols-2 gap-2">
                <div class="w-full">
                    <label for="date-select" class="block text-neutral-700 text-sm font-medium mb-1">дата доставки <span
                        v-if="!previously_call_to_recipient || is_recipient_current_user"
                        class="text-red-500 font-bold">*</span></label>
                    <select name="date" id="date-select" @change="setTimeOptions" v-model="selectedDateOffset"
                            class="w-full border border-black rounded-lg px-2 py-1.5 text-sm sm:text-base">
                        <option
                            v-for="option in dateOptions"
                            :key="option.offset"
                            :value="option.offset"
                        >
                            {{ option.content }}
                        </option>
                    </select>
                </div>

                <div>
                    <label for="time-select" class="block text-neutral-700 text-sm font-medium mb-1">время доставки
                        <span v-if="!previously_call_to_recipient || is_recipient_current_user"
                              class="text-red-500 font-bold">*</span></label>
                    <select name="time" id="time-select"
                            v-model="delivery_date_time"
                            class="w-full border border-black rounded-lg px-2 py-1.5 text-sm sm:text-base"
                    >
                        <option value="" disabled>выберите время</option>
                        <option v-if="timeOptions.length === 0" disabled>Сегодня уже нельзя выбрать время доставки
                        </option>
                        <option
                            v-for="option in timeOptions"
                            :key="option.value"
                            :value="option.value"
                        >
                            {{ option.content }}
                        </option>
                    </select>
                </div>
            </div>


            <br>

            <p class="text-neutral-700 text-sm font-medium mb-1">Особые пожелания:</p>
            <textarea v-model="client_wishes" placeholder="Комментарии"
                      class="resize-none h-32 w-full p-2 border border-black rounded-lg"></textarea>
            <br>
            <div v-if="Object.keys(this.errorsBackend).length !== 0"
                 class="text-sm font-medium text-red-800 mb-2 bg-red-50 p-2 rounded-lg mt-2">
                <p class="mb-2 font-bold">Пожалуйста исправьте следующие ошибки:</p>
                <ul>
                    <li v-for="(value, key) in errorsBackend">
                        <p class="font-bold">{{ key }}:</p>
                        <ul>
                            <li v-for="error in value" class="ml-2 mb-1">- {{ error }}</li>
                        </ul>
                    </li>
                </ul>
            </div>
            <br>

        </div>


        <div v-if="cart.length !== 0"  class=" max-w-[400px] mt-2 mx-auto shadow-2xl p-5 rounded-xl lg:w-4/12 lg:mt-0 lg:mx-0 lg:ml-5">
            <div class="flex justify-between mb-3 font-bold">
                <p>Стоимость товаров ({{ cart?.length }})</p>
                <p>{{ totalPrice }} ₽</p>
            </div>

            <div v-if="delivery_price" class="flex justify-between">
                <p class="text-sm">Стоимость доставки</p>
                <p>{{ delivery_price }} ₽</p>
            </div>

            <div class="flex justify-between text-xl font-bold mt-6">
                <p>Итого</p>
                <p>{{ totalPrice + (delivery_price !== undefined ? delivery_price : 0) }} ₽</p>
            </div>

        </div>

    </div>

    <div class="flex justify-center mt-6 lg:mt-0 lg:justify-start">
        <button
            @click="checkout"
            :disabled="isLoading"
            type="button"
            class="text-white bg-black hover:bg-gray-800 rounded-lg text-md px-4 py-2 text-center inline-flex items-center"
        >
            <span v-if="isLoading">
            <svg aria-hidden="true" role="status" class="inline w-4 h-4 me-3 text-white animate-spin"
                 viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                    fill="#E5E7EB"/>
                <path
                    d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                    fill="currentColor"/>
            </svg>
            </span>
            {{ isLoading ? 'обработка...' : 'оплатить заказ' }}
        </button>
    </div>


</template>

<script>
import axios from "axios";
import cartMixin from "../mixins/cartMixin.js";
import AddressInput from "../components/AddressInput.vue";
import {DateTime} from "luxon";
import login from "./Login.vue";
import router from "../router/router.js";

export default {
    name: "Checkout",
    components: {AddressInput},
    mixins: [cartMixin],
    data() {
        return {
            showCommentForCurrier: false,
            timeOptions: [],
            dateOptions: [],
            selectedDateOffset: 0,
            delivery_date_time: '',
            name: '',
            email: '',
            currentName: '',
            currentEmail: '',
            disableName: false,
            disableEmail: false,
            is_recipient_current_user: false,
            is_anonymous_sender: false,
            previously_call_to_recipient: false,
            recipient_name: undefined,
            recipient_phone: undefined,
            entrance: undefined,
            floor: undefined,
            houseNumber: undefined,
            delivery_address: undefined,
            delivery_address_latitude: undefined,
            delivery_address_longitude: undefined,
            delivery_price: undefined,
            comment_for_courier: undefined,
            client_wishes: undefined,
            errors: {},
            errorsBackend: {},
            emailWarning: false,
            totalPrice: undefined,
            isLoading: false
        }
    },
    mounted() {
        this.getCartData()
        this.setDateOptions()
        this.setTimeOptions()
        this.getUserData()
    },
    watch: {
        email() {
            this.validateEmail()
        }
    },
    methods: {
        getCartData() {
            if (this.cart.length !== 0) {
                axios.post('api/cart', {cart: this.cart}).then(response => {
                    this.cartFullData = response.data.data
                    this.totalPrice = this.calculateTotalPrice()
                }).catch(err => {
                    console.log(err)
                    if (this.handleCartErrors(err)) {
                        this.getCartData()
                    } else {
                        alert('Что-то пошло не так при загрузке информации о товарах из корзины...')
                    }
                })
            } else {
                router.push({name: 'cart'})
            }
        },
        getUserData() {
            axios.get('api/user').then(response => {
                if (response.data.name) {
                    this.name = response.data.name
                    this.currentName = response.data.name
                    this.disableName = true
                }
                if (response.data.email) {
                    this.email = response.data.email
                    this.currentEmail = response.data.email
                    this.disableEmail = true
                }
            }).catch(err => {
                console.log(err)
            })
        },
        getDeliveryAddressData(data) {
            this.delivery_address = data.address
            this.delivery_address_longitude = data.longitude
            this.delivery_address_latitude = data.latitude
        },
        getDeliveryPrice(delivery_price) {
            this.delivery_price = delivery_price
        },
        setDateOptions() {
            let now = DateTime.now().setZone("Asia/Novosibirsk")
            let months = {
                1: 'Января',
                2: 'Февраля',
                3: 'Марта',
                4: 'Апреля',
                5: 'Мая',
                6: 'Июня',
                7: 'Июля',
                8: 'Августа',
                9: 'Сентября',
                10: 'Октября',
                11: 'Ноября',
                12: 'Декабря'
            }
            for (let i = 0; i <= 7; i++) {
                let nextDate = now.plus({days: i})
                let content = nextDate.day + ' ' + months[nextDate.month]
                if (i === 0) {
                    content = nextDate.day + ' сегодня'
                }
                if (i === 1) {
                    content = nextDate.day + ' завтра'
                }
                this.dateOptions.push({content: content, offset: i})
            }
        },
        setTimeOptions() {
            this.timeOptions = []
            this.delivery_date_time = ''
            if (this.selectedDateOffset === 0) {
                let now = DateTime.now().setZone("Asia/Novosibirsk")
                let nowPlusOffset = now.plus({hours: 2})
                let dateTimeNearestOption = undefined
                if (nowPlusOffset.minute < 30) {
                    dateTimeNearestOption = DateTime.fromObject(
                        {
                            year: nowPlusOffset.year,
                            month: nowPlusOffset.month,
                            day: nowPlusOffset.day,
                            hour: nowPlusOffset.hour,
                            minute: 0
                        },
                        {zone: "Asia/Novosibirsk"}
                    )
                } else {
                    dateTimeNearestOption = DateTime.fromObject(
                        {
                            year: nowPlusOffset.year,
                            month: nowPlusOffset.month,
                            day: nowPlusOffset.day,
                            hour: nowPlusOffset.hour,
                            minute: 30
                        },
                        {zone: "Asia/Novosibirsk"}
                    )
                }
                let firstOption = DateTime.fromObject({
                    year: now.year,
                    month: now.month,
                    day: now.day,
                    hour: 10,
                    minute: 30
                }, {zone: "Asia/Novosibirsk"})
                let lastOption = DateTime.fromObject({
                    year: now.year,
                    month: now.month,
                    day: now.day,
                    hour: 21,
                    minute: 30
                }, {zone: "Asia/Novosibirsk"})

                if (dateTimeNearestOption >= firstOption && dateTimeNearestOption <= lastOption) {
                    //формируем от dateTimeNearestOption до lastOption
                    this.timeOptions.push({
                        value: dateTimeNearestOption.toFormat('yyyy-MM-dd H:mm'),
                        content: this.getTimeRange(dateTimeNearestOption)
                    })
                    let dateTimeNextOption = dateTimeNearestOption.plus({minutes: 30})
                    while (dateTimeNextOption <= lastOption) {
                        this.timeOptions.push({
                            value: dateTimeNextOption.toFormat('yyyy-MM-dd H:mm'),
                            content: this.getTimeRange(dateTimeNextOption)
                        })
                        dateTimeNextOption = dateTimeNextOption.plus({minutes: 30})
                    }
                } else if (dateTimeNearestOption < firstOption) {
                    //формируем от firstOption до lastOption
                    this.setFullTimeRangeOptionList(firstOption, lastOption)
                }
            } else {
                let date = DateTime.now().plus({days: this.selectedDateOffset}).setZone("Asia/Novosibirsk")
                let firstOption = DateTime.fromObject({
                    year: date.year,
                    month: date.month,
                    day: date.day,
                    hour: 10,
                    minute: 30
                }, {zone: "Asia/Novosibirsk"})
                let lastOption = DateTime.fromObject({
                    year: date.year,
                    month: date.month,
                    day: date.day,
                    hour: 21,
                    minute: 30
                }, {zone: "Asia/Novosibirsk"})

                this.setFullTimeRangeOptionList(firstOption, lastOption)
            }
        },
        getTimeRange(dtOption) {
            let dtOptionPlus30Min = dtOption.plus({minutes: 30})
            return dtOption.toFormat('H:mm') + ' - ' + dtOptionPlus30Min.toFormat('H:mm')
        },
        setFullTimeRangeOptionList(firstOption, lastOption) {
            this.timeOptions.push({
                value: firstOption.toFormat('yyyy-MM-dd H:mm'),
                content: this.getTimeRange(firstOption)
            })
            let dateTimeNextOption = firstOption.plus({minutes: 30})
            while (dateTimeNextOption <= lastOption) {
                this.timeOptions.push({
                    value: dateTimeNextOption.toFormat('yyyy-MM-dd H:mm'),
                    content: this.getTimeRange(dateTimeNextOption)
                })
                dateTimeNextOption = dateTimeNextOption.plus({minutes: 30})
            }
        },
        checkout() {
            if (this.validateForm()) {
                let form_data = {
                    delivery_date_time: this.delivery_date_time,
                    is_recipient_current_user: this.is_recipient_current_user,
                    is_anonymous_sender: this.is_anonymous_sender,
                    entrance: this.entrance,
                    floor: this.floor,
                    houseNumber: this.houseNumber,
                    delivery_address: this.delivery_address,
                    delivery_address_latitude: this.delivery_address_latitude,
                    delivery_address_longitude: this.delivery_address_longitude,
                    comment_for_courier: this.comment_for_courier,
                    client_wishes: this.client_wishes,
                }

                if (this.name !== this.currentName) {
                    form_data.name = this.name
                }
                if (this.email !== this.currentEmail) {
                    form_data.email = this.email
                }
                if (!this.is_recipient_current_user) {
                    form_data.previously_call_to_recipient = this.previously_call_to_recipient
                    form_data.recipient_name = this.recipient_name
                    form_data.recipient_phone = this.recipient_phone
                }

                this.isLoading = true
                axios.post('api/orders', {cart: this.cart, form_data: form_data}).then(response => {
                    this.isLoading = false
                    if (response.data.payment_url) {
                        this.$store.dispatch('clearCart')
                        window.location.href = response.data.payment_url
                    }
                }).catch(err => {
                    this.isLoading = false
                    console.log(err)
                    if (err.response.status === 422) {
                        console.log(err.response.data.errors)
                        this.handleFormErrors(err)
                    } else if (this.handleCartErrors(err)) {
                        this.getCartData()
                    } else if (err.response.data.error === 'deliveryPriceError') {
                        alert('Не удалось вычислить цену доставки, попробуйте ввести адрес еще раз')
                    } else {
                        alert('Что-то пошло не так при формировании заказа...');
                    }

                })
            }
        },
        validateForm() {
            this.errors = {}
            if (!this.email) {
                this.errors.email = 'пожалуйста введите ваш email'
            }
            if (!this.delivery_address) {
                this.errors.delivery_address = 'пожалуйста введите адрес доставки'
            }
            if ((!this.delivery_date_time && this.is_recipient_current_user) || (!this.delivery_date_time && !this.is_recipient_current_user && !this.previously_call_to_recipient)) {
                this.errors.delivery_date_time = 'пожалуйста выберите дату и время доставки'
            }
            if (this.delivery_date_time) {
                let minTime = DateTime.now().plus({minutes: 89}).setZone("Asia/Novosibirsk")
                let delivery_date_time = DateTime.fromFormat(this.delivery_date_time, "yyyy-MM-dd H:mm").setZone("Asia/Novosibirsk");
                if (minTime > delivery_date_time) {
                    this.errors.delivery_date_time = 'время успело обновиться, пожалуйста выберите снова дату и время доставки'
                    this.setDateOptions()
                    this.setTimeOptions()
                }
            }
            if (!this.is_recipient_current_user && this.previously_call_to_recipient) {
                if (!this.recipient_phone) {
                    this.errors.recipient_phone = 'пожалуйста введите телефон получателя'
                } else {
                    const phoneRegex = /^7\d{10}$/
                    if (!phoneRegex.test(this.recipient_phone)) {
                        this.errors.recipient_phone = 'пожалуйста введите корректно номер телефона получателя'
                    }
                }
            }

            return Object.keys(this.errors).length === 0;
        },
        validateEmail() {
            const emailRegex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/
            this.emailWarning = !emailRegex.test(this.email);
        },
        handleFormErrors(err) {
            this.errorsBackend = {}
            let errors = err.response.data.errors
            if (errors['form_data.email']) {
                this.errorsBackend['email'] = errors['form_data.email']
            }
            if (errors['form_data.name']) {
                this.errorsBackend['ваше имя'] = errors['form_data.name']
            }
            if (errors['form_data.recipient_name']) {
                this.errorsBackend['имя получателя'] = errors['form_data.recipient_name']
            }
            if (errors['form_data.recipient_phone']) {
                this.errorsBackend['телефон получателя'] = errors['form_data.recipient_phone']
            }
            if (errors['form_data.delivery_address']) {
                this.errorsBackend['адрес доставки'] = errors['form_data.delivery_address']
            }
            if (errors['form_data.entrance']) {
                this.errorsBackend['подъезд'] = errors['form_data.entrance']
            }
            if (errors['form_data.floor']) {
                this.errorsBackend['этаж'] = errors['form_data.floor']
            }
            if (errors['form_data.apartment_number']) {
                this.errorsBackend['квартира'] = errors['form_data.apartment_number']
            }
            if (errors['form_data.comment_for_courier']) {
                this.errorsBackend['комментарий для курьера'] = errors['form_data.comment_for_courier']
            }
            if (errors['form_data.delivery_date_time']) {
                this.errorsBackend['дата и время доставки'] = errors['form_data.delivery_date_time']
            }
            if (errors['form_data.client_wishes']) {
                this.errorsBackend['особые пожелания'] = errors['form_data.client_wishes']
            }
            if (Object.keys(this.errorsBackend).length === 0) {
                alert('что-то пошло не так, обратитесь к администратору сайта')
            }
        }
    }
}
</script>

<style scoped>

</style>
