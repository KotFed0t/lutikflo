<template>
    <div class="dialog" v-if="show" @click="hideDialog">
        <div @click.stop class="dialog_content">
            <div v-if="showEnterPhoneDialog">
                <h4>Ввод телефона</h4>
                <p v-if="errorMessage" style="color: red">{{ errorMessage }}</p>
                <p>введите номер телефона. Вам позвонит робот и назовет код из 3 цифр.</p>
                <input
                    type="tel"
                    v-model="phone"
                    v-mask="'7##########'"
                    placeholder="+7"
                >
                <button @click.prevent="sendCode" class="btn btn-primary">Отправить</button>
            </div>
            <div v-if="showEnterCodeDialog">
                <h4>Ввод кода</h4>
                <p v-if="errorMessage" style="color: red">{{ errorMessage }}</p>
                <div v-if="!incoming_call_from">
                    <p>Прошло менее 1 минуты с момента совершения предыдещего звонка</p>
                    <p>Вы можете повторно ввести код, полученный в звонке от робота</p>
                </div>
                <div v-else>
                    <p>сейчас вам позвнит бот с номера {{ incoming_call_from }}</p>
                    <p>введите код, который вам продиктовал бот</p>
                </div>
                <p>ваш номер +{{ phone }}</p>
                <p @click="goToEnterPhone" style="color: #0a53be">изменить номер</p>
                <input v-model="code" v-mask="'###'">
                <button @click.prevent="checkCode" class="btn btn-primary">Отправить</button>
                <p v-if="countdown===0" @click="sendCode" style="color: #0a53be">отправить код повторно</p>
                <p v-if="countdown!==0">Отправить код еще раз через {{ formattedTime }}</p>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import Cookies from "js-cookie";
import router from "../router/router.js";

export default {
    name: "LoginDialog",
    props: {
        show: {
            type: Boolean,
            default: false
        },
        redirectTo: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            showEnterPhoneDialog: true,
            showEnterCodeDialog: false,
            phone: '',
            code: '',
            incoming_call_from: '',
            countdown: 60,
            timer: null,
            errorMessage: undefined
        }
    },
    computed: {
        formattedTime() {
            // Функция для форматирования времени в формат "минуты:секунды"
            const minutes = Math.floor(this.countdown / 60);
            const seconds = this.countdown % 60;
            return `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
        },
    },
    methods: {
        hideDialog() {
            this.$emit('update:show', false)
        },
        startCountdown(countdown = 60) {
            this.countdown = countdown
            // Запускаем таймер обратного отсчета
            this.timer = setInterval(() => {
                if (this.countdown > 0) {
                    this.countdown--
                } else {
                    clearInterval(this.timer) // Остановка таймера при достижении нуля
                }
            }, 1000) // Обновление каждую секунду
        },
        goToEnterPhone(cleanErrorMessage=true) {
            if (cleanErrorMessage) {
                this.errorMessage = ''
            }
            this.showEnterPhoneDialog = true
            this.showEnterCodeDialog = false
            this.incoming_call_from = ''
            this.code = ''
            clearInterval(this.timer);
        },
        sendCode() {
            if (this.validatePhoneInput()) {
                axios.post(`api/voice-password/send/${this.phone}`)
                    .then(response => {
                        console.log(response)
                        if (response.data.status === 'success') {
                            this.showEnterPhoneDialog = false
                            this.showEnterCodeDialog = true
                            this.errorMessage = undefined
                            if (response.data.incoming_call_from) {
                                this.incoming_call_from = response.data.incoming_call_from
                            }
                            this.startCountdown()
                        }
                    })
                    .catch(err => {
                        console.log(err)
                        if (err.response.data.error === "send_code_rate_limit") {
                            this.showEnterPhoneDialog = false
                            this.showEnterCodeDialog = true
                            this.startCountdown(err.response.data.seconds_remaining)
                        }
                        if (err.response.data.error === "unknown_error") {
                            this.errorMessage = 'Что-то пошло не так, попробуйте еще раз или повторите попытку позже'
                        }
                        if (err.response.status === 429 && err.response.statusText === "Too Many Requests") {
                            this.errorMessage = 'Превышено количество звонков на один номер. Повторить попытку можно только через час с момента блокировки. Сейчас вы можете ввести другой номер телефона.'
                            this.goToEnterPhone(false)
                        }
                        if (err.response.status === 422 && err.response.statusText === "Unprocessable Content") {
                            this.errorMessage = 'введите корректный номер телефона'
                        }
                    })
            }
        },

        checkCode() {
            if (this.validateCodeInput()) {
                axios.post(`api/voice-password/check/${this.phone}/${this.code}`)
                    .then(response => {
                        console.log(response)
                        if (response.data.status === 'success') {
                            this.$store.dispatch('login')
                            this.hideDialog()
                            this.showEnterPhoneDialog = true
                            this.showEnterCodeDialog = false
                            this.incoming_call_from = ''
                            this.code = ''
                            this.phone = ''
                            this.errorMessage = undefined
                            clearInterval(this.timer);
                            if (this.redirectTo !== '') {
                                router.push({name: this.redirectTo})
                            }
                        }
                    })
                    .catch(err => {
                        console.log(err)
                        if (err.response.status === 422 && err.response.statusText === "Unprocessable Content") {
                            this.errorMessage = 'код должен состоять из трех цифр!'
                        }
                        if (err.response.status === 429 && err.response.statusText === "Too Many Requests") {
                            this.errorMessage = 'Превышено количество попыток ввода кода. Повторить попытку можно через минуту.'
                        }
                        if (err.response.data.error === "incorrect_code") {
                            this.errorMessage = 'введен неверный код'
                        }
                        if (err.response.data.error === "code_expired") {
                            this.errorMessage = 'срок жизни кода истек, необходимо запросить новый код'
                        }
                    })
            }
        },
        validateCodeInput() {
            const codeRegex = /^\d{3}$/
            if (!codeRegex.test(this.code)) {
                this.errorMessage = 'необходимо ввести код из трех цифр'
                return false
            }
            return true
        },
        validatePhoneInput() {
            const phoneRegex = /^7\d{10}$/
            if (!phoneRegex.test(this.phone)) {
                this.errorMessage = 'пожалуйста введите корректный номер телефона'
                return false
            }
            return true
        }
    },
    beforeDestroy() {
        // Очищаем таймер при уничтожении компонента
        clearInterval(this.timer);
    },
}
</script>

<style scoped>
.dialog {
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.5);
    position: fixed;
    display: flex;
}

.dialog_content {
    margin: auto;
    background-color: rgba(255, 255, 255, 1);
    border-radius: 12px;
    min-height: 50px;
    min-width: 300px;
    padding: 20px;
}
</style>
