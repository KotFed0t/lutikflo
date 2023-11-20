<template>
    Страница оформления заказа
    <p>общая стоимость товаров: {{ totalPrice }}</p>
    <div class="form-control">
        <input type="text" v-model="name" placeholder="ваше имя" style="margin-right: 10px" :disabled="disableName">
        <input type="checkbox" v-model="is_anonymous_sender"> доставить анонимно
        <p v-if="currentName && disableName === true" @click="disableName=false">редактировать</p>
        <p v-else-if="currentName && disableName === false" @click="disableName=true; name=currentName">отменить редактирование</p>
        <br>
        <input type="email" v-model="email" placeholder="email" :disabled="disableEmail">
        <p v-if="currentEmail && disableEmail === true" @click="disableEmail=false">редактировать</p>
        <p v-else-if="currentEmail && disableEmail === false" @click="disableEmail=true; email=currentEmail">отменить редактирование</p>
        <p v-if="errors['email']" style="color: red">{{ errors['email'] }}</p>
        <p v-if="emailWarning" style="color: red">возможно email введен некорректно</p>
        <br>
        <button @click="is_recipient_current_user=false" class="btn btn-dark"
                style="margin-bottom: 20px; margin-right: 10px" :disabled="!is_recipient_current_user">доставить
            получателю
        </button>
        <button @click="is_recipient_current_user=true" class="btn btn-dark"
                style="margin-bottom: 20px; margin-right: 10px" :disabled="is_recipient_current_user">доставить мне
        </button>

        <div v-if="!is_recipient_current_user">
            <p>Мы можем согласовать дату и время с получателем:</p>
            <div>
                <input type="radio" id="huey" name="drone" :value="true" v-model="previously_call_to_recipient"
                       :checked="previously_call_to_recipient===true"/>
                <label for="huey">Да, согласовать с получателем</label>
            </div>
            <div>
                <input type="radio" id="dewey" name="drone" :value="false" v-model="previously_call_to_recipient"
                       :checked="previously_call_to_recipient===false"/>
                <label for="dewey">Нет, не звонить получателю</label>
            </div>
            <br>
            <input v-model="recipient_name" type="text" placeholder="имя получателя" style="margin-bottom: 20px; margin-right: 10px">
            <input
                type="tel"
                v-model="recipient_phone"
                v-mask="'7##########'"
                placeholder="+7(___)-__-__"
                style="margin-bottom: 20px; margin-right: 10px"
            >
            <p v-if="errors['recipient_phone']" style="color: red">{{ errors['recipient_phone'] }}</p>
        </div>

        <AddressInput @get="getDeliveryAddressData"></AddressInput>
        <p v-if="errors['delivery_address']" style="color: red">{{ errors['delivery_address'] }}</p>
        <input type="text" placeholder="подъезд" v-model="entrance">
        <input type="text" placeholder="этаж" v-model="floor">
        <input type="text" placeholder="квартира" v-model="houseNumber">
        <br>

        <p v-if="!showCommentForCurrier" @click="this.showCommentForCurrier = true">+ Добавить комментарий для
            курьера</p>
        <div v-else>
            <p @click="this.showCommentForCurrier = false">- Убрать комментарий для курьера</p>
            <textarea v-model="comment_for_courier" placeholder="введите комментарий для курьера"
                      style="width: 300px"></textarea>
        </div>
        <br>
        <p>Часовой пояс: Новосибирск UTC+7</p>
        <label for="date-select">выберите дату:</label>
        <select name="date" id="date-select" @change="setTimeOptions" v-model="selectedDateOffset">
            <option
                v-for="option in dateOptions"
                :key="option.offset"
                :value="option.offset"
            >
                {{ option.content }}
            </option>
        </select>

        <label for="time-select">выберите время:</label>
        <select name="time" id="time-select" style="margin-bottom: 20px; margin-right: 10px"
                v-model="delivery_date_time">
            <option value="" disabled>выберите время</option>
            <option v-if="timeOptions.length === 0" disabled>Сегодня уже нельзя выбрать время доставки</option>
            <option
                v-for="option in timeOptions"
                :key="option.value"
                :value="option.value"
            >
                {{ option.content }}
            </option>
        </select>
        <br>
        <p v-if="errors['delivery_date_time']" style="color: red">{{ errors['delivery_date_time'] }}</p>
        выбрано время: {{ delivery_date_time }}
        <p>Особые пожелания:</p>
        <textarea v-model="client_wishes" placeholder="Комментарии" style="width: 300px"></textarea>
        <br>
        <div v-if="Object.keys(this.errorsBackend).length !== 0" style="border: red solid 1px">
            Пожалуйста исправьте следующие ошибки:
            <ul>
                <li v-for="(value, key) in errorsBackend">
                    {{ key }}:
                    <ul>
                        <li v-for="error in value">{{ error }}</li>
                    </ul>
                </li>
            </ul>
        </div>
        <br>
        <button @click="checkout" class="btn btn-dark" style="margin-bottom: 20px; margin-right: 10px">Оплатить заказ
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
            comment_for_courier: undefined,
            client_wishes: undefined,
            errors: {},
            errorsBackend: {},
            emailWarning: false,
            totalPrice: undefined
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

                axios.post('api/orders', {cart: this.cart, form_data: form_data}).then(response => {
                    console.log(response)
                }).catch(err => {
                    console.log(err)
                    if (err.response.status === 422) {
                        console.log(err.response.data.errors)
                        this.handleFormErrors(err)
                    }
                    if (this.handleCartErrors(err)) {
                        this.getCartData()
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
