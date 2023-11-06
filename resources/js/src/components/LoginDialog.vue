<template>
    <div class="dialog" v-if="show" @click="hideDialog">
        <div @click.stop class="dialog_content">
            <div v-if="showEnterPhoneDialog">
                <h4>Ввод телефона</h4>
                <p>введите номер телефона. Вам позвонит бот и назовет код из 3 цифр.</p>
                <input v-model="phone" type="number" name="phone" id="phone">
                <button @click.prevent="sendCode" class="btn btn-primary">Отправить</button>
            </div>
            <div v-if="showEnterCodeDialog">
                <h4>Ввод кода</h4>
                <p>сейчас вом позвнит бот с номера {{incoming_call_from}}</p>
                <p>введите код, который вам продиктовал бот</p>
                <input v-model="code" type="number" name="phone" id="phone">
                <button @click.prevent="checkCode" class="btn btn-primary">Отправить</button>
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
            incoming_call_from: ''
        }
    },
    methods: {
        hideDialog() {
            this.$emit('update:show', false)
        },

        sendCode() {
            axios.post(`api/voice-password/send/${this.phone}`)
                .then(response => {
                    console.log(response)
                    if (response.data.status === 'success') {
                        this.showEnterPhoneDialog = false
                        this.showEnterCodeDialog = true
                        if (response.data.incoming_call_from) {
                            this.incoming_call_from = response.data.incoming_call_from
                        }
                    }
                    // router.push('/register')
                })
                .catch(err => {
                    console.log(err)
                    //учесть ошибки валидации и ошибки из контроллера
                    //а также учесть trottle 5 в минуту
                })
        },

        checkCode() {
            axios.post(`api/voice-password/check/${this.phone}/${this.code}`)
                .then(response => {
                    console.log(response)
                    if (response.data.status === 'success') {
                        this.$store.dispatch('login')
                        this.hideDialog()
                        if (this.redirectTo !== '') {
                            router.push({name: this.redirectTo})
                        }
                    }
                })
                .catch(err => {
                    console.log(err)
                    //учесть ошибки валидации и ошибки из контроллера
                    //а также учесть trottle 5 в минуту
                })
        }
    }
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
    background: white;
    border-radius: 12px;
    min-height: 50px;
    min-width: 300px;
    padding: 20px;
}
</style>
