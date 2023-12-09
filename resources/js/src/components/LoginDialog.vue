<template>
    <div class="fixed z-40 top-0 left-0 w-full h-full bg-black bg-opacity-50 flex justify-center items-center" v-if="show" @click="hideDialog">
        <div @click.stop class="mx-2 bg-white rounded-lg p-5 max-w-[400px]">
            <div v-if="showEnterPhoneDialog">
                <h4 class="text-lg font-medium mb-4">Войдите или зарегистрируйтесь</h4>
                <p v-if="errorMessage" class="text-red-800 font-medium text-sm bg-red-50 p-2 rounded-lg mb-4">{{ errorMessage }}</p>
                <p class="text-neutral-600 mb-4">Введите номер телефона. Вам позвонит робот и назовет код из 3 цифр.</p>
                <input
                    type="tel"
                    v-model="phone"
                    v-mask="'7##########'"
                    placeholder="+7"
                    class="rounded border border-black focus:outline-none py-1.5 px-2 w-full mb-4"
                >

                <button
                    @click="sendCode"
                    :disabled="isEnterPhoneLoading"
                    type="button"
                    class="text-white bg-black hover:bg-neutral-800 rounded-lg text-md px-4 py-2 text-center inline-flex items-center justify-center w-full">
                    <span v-if="isEnterPhoneLoading">
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
                    {{ isEnterPhoneLoading ? 'Набираем номер...' : 'Получить код в звонке' }}
                </button>

                <p class="text-xs mt-4">
                    Продолжая, вы соглашаетесь c
                    <router-link to="agreement" class="font-bold hover:border-b hover:border-black">пользовательским соглашением</router-link>
                    и <router-link to="privacy" class="font-bold hover:border-b hover:border-black">политикой обработки персональных данных</router-link>.
                </p>
            </div>
            <div v-if="showEnterCodeDialog">
                <h4 class="text-lg font-medium mb-4">Подтвердите номер</h4>
                <p v-if="errorMessage" class="text-red-800 font-medium text-sm bg-red-50 p-2 rounded-lg mb-4">{{ errorMessage }}</p>
                <div v-if="!incoming_call_from">
                    <p class="mb-4">Прошло менее 1 минуты с момента совершения предыдещего звонка. Вы можете повторно ввести код, полученный в звонке от робота.</p>
                </div>
                <div v-else>
                    <p class="mb-4">Cейчас вам позвонит робот с номера {{ incoming_call_from }} и продиктует код.</p>
                </div>
                <p>Ваш номер +{{ phone }}</p>
                <button @click="goToEnterPhone" class="text-neutral-600 block border-b border-neutral-500 mb-4 hover:text-black">изменить номер</button>
                <input v-model="code" v-mask="'###'" class="rounded border border-black focus:outline-none py-1.5 px-2 w-full mb-4">

                <button
                    @click="checkCode"
                    :disabled="isEnterCodeLoading"
                    type="button"
                    class="text-white bg-black hover:bg-neutral-800 rounded-lg text-md px-4 py-2 text-center inline-flex items-center justify-center w-full mb-4">
                    <span v-if="isEnterCodeLoading">
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
                    {{ isEnterCodeLoading ? 'Отправка...' : 'Отправить' }}
                </button>

                <button v-if="countdown===0" @click="sendCode" class="text-neutral-600 block border-b border-neutral-500 hover:text-black">Отправить код повторно</button>
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
            errorMessage: undefined,
            isEnterPhoneLoading: false,
            isEnterCodeLoading: false
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
            document.body.classList.remove('overflow-hidden');
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
                this.isEnterPhoneLoading = true
                axios.post(`api/voice-password/send/${this.phone}`)
                    .then(response => {
                        this.isEnterPhoneLoading = false
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
                        this.isEnterPhoneLoading = false
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
                            this.errorMessage = 'Превышено количество звонков на один номер. Повторить попытку можно только через час. Сейчас вы можете ввести другой номер телефона.'
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
                this.isEnterCodeLoading = true
                axios.post(`api/voice-password/check/${this.phone}/${this.code}`)
                    .then(response => {
                        this.isEnterCodeLoading = false
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
                            this.$emit('loggedIn', true)
                        }
                    })
                    .catch(err => {
                        this.isEnterCodeLoading = false
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

</style>
