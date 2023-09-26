<template>
    <h3>Login</h3>
    <form style="width: 500px">
        <div class="mb-3">
            <label for="exampleInputEmail1" class="form-label">Email address</label>
            <input v-model="email" type="email" class="form-control" aria-describedby="emailHelp">
        </div>
        <div class="mb-3">
            <label for="exampleInputPassword1" class="form-label">Password</label>
            <input v-model="password" type="password" class="form-control" >
        </div>
        <button @click.prevent="loginUser" class="btn btn-primary">Submit</button>
    </form>
</template>

<script>
import axios from "axios";
import router from "../router/router.js";
export default {
    name: "Login",
    data() {
        return {
            email: '',
            password: ''
        }
    },
    methods: {
        loginUser() {
            axios.get('/sanctum/csrf-cookie').then(response => {
                console.log(response)
            }).catch(err => {
                console.log(err)
            })

            axios.post('api/login', {email: this.email, password: this.password})
                .then(response => {
                    console.log(response)
                    // router.push('/register')
                })
                .catch(err => {
                    console.log(err)
                })
        }
    }
}
</script>

<style scoped>

</style>
