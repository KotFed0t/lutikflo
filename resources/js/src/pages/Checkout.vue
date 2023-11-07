<template>
    Страница оформления заказа
    <p>общая стоимость товаров: {{ calculateTotalPrice() }}</p>
    <div class="form-control">
        <input type="text" placeholder="ваше имя" style="margin-bottom: 20px; margin-right: 10px">
        <input type="checkbox"> доставить анонимно
        <br>
        <input type="" placeholder="телефон" style="margin-bottom: 20px; margin-right: 10px">
        <input type="email" placeholder="email">
        <br>
        <button @click="" class="btn btn-dark" style="margin-bottom: 20px; margin-right: 10px">доставить получателю
        </button>
        <button @click="" class="btn btn-dark" style="margin-bottom: 20px; margin-right: 10px">доставить мне</button>

        <p>Мы можем согласовать дату и время с получателем:</p>
        <div>
            <input type="radio" id="huey" name="drone" value="huey" checked/>
            <label for="huey">Да, согласовать с получателем</label>
        </div>
        <div>
            <input type="radio" id="dewey" name="drone" value="dewey"/>
            <label for="dewey">Нет, не звонить получателю</label>
        </div>
        <br>

        <input type="text" placeholder="имя получателя" style="margin-bottom: 20px; margin-right: 10px">
        <input type="" placeholder="телефон получателя" style="margin-bottom: 20px; margin-right: 10px">

        <AddressInput></AddressInput>
        <br>
        <p v-if="!showCommentForCurrier" @click="this.showCommentForCurrier = true">+ Добавить комментарий для
            курьера</p>
        <div v-else>
            <p @click="this.showCommentForCurrier = false">- Убрать комментарий для курьера</p>
            <textarea placeholder="введите комментарий для курьера" style="width: 300px"></textarea>
        </div>
        <br>

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
        <select name="time" id="time-select" style="margin-bottom: 20px; margin-right: 10px" v-model="selectedDateTime">
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
        выбрано время: {{selectedDateTime}}
        <p>Особые пожелания:</p>
        <textarea placeholder="Комментарии" style="width: 300px"></textarea>
        <br>
        <button @click="" class="btn btn-dark" style="margin-bottom: 20px; margin-right: 10px">Оплатить заказ</button>

    </div>
</template>

<script>
import axios from "axios";
import cartMixin from "../mixins/cartMixin.js";
import AddressInput from "../components/AddressInput.vue";
import {DateTime} from "luxon";

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
            selectedDateTime: ''
        }
    },
    mounted() {
        this.getCartData()
        this.setDateOptions()
        this.setTimeOptions()
    },
    methods: {
        getCartData() {
            if (this.cart.length !== 0) {
                axios.post('api/cart', {cart: this.cart}).then(response => {
                    this.cartFullData = response.data.data
                }).catch(err => {
                    console.log(err)
                })
            }
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
            this.selectedDateTime = ''
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
                    this.timeOptions.push({value: dateTimeNearestOption.toFormat('yyyy-MM-dd H:mm'), content: this.getTimeRange(dateTimeNearestOption)})
                    let dateTimeNextOption = dateTimeNearestOption.plus({minutes: 30})
                    while (dateTimeNextOption <= lastOption) {
                        this.timeOptions.push({value: dateTimeNextOption.toFormat('yyyy-MM-dd H:mm'), content: this.getTimeRange(dateTimeNextOption)})
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
            this.timeOptions.push({value: firstOption.toFormat('yyyy-MM-dd H:mm'), content: this.getTimeRange(firstOption)})
            let dateTimeNextOption = firstOption.plus({minutes: 30})
            while (dateTimeNextOption <= lastOption) {
                this.timeOptions.push({value: dateTimeNextOption.toFormat('yyyy-MM-dd H:mm'), content: this.getTimeRange(dateTimeNextOption)})
                dateTimeNextOption = dateTimeNextOption.plus({minutes: 30})
            }
        }
    }
}
</script>

<style scoped>

</style>
