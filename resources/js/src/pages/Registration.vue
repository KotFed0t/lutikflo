<template>
    <h3>Registration</h3>
    <form style="width: 500px">
        <div class="mb-3">
            <label for="exampleInputEmail1" class="form-label">Name</label>
            <input v-model="person.name" type="text" class="form-control" aria-describedby="emailHelp">
        </div>
        <div class="mb-3">
            <label for="exampleInputEmail1" class="form-label">Email address</label>
            <input v-model="person.email" type="email" class="form-control" aria-describedby="emailHelp">
        </div>
        <div class="mb-3">
            <label for="exampleInputPassword1" class="form-label">Password</label>
            <input v-model="person.password" type="password" class="form-control" >
        </div>
        <div class="mb-3">
            <label for="exampleInputPassword1" class="form-label">Repeat password</label>
            <input v-model="person.password_confirmation" type="password" class="form-control" >
        </div>
        <button @click.prevent="registerUser" class="btn btn-primary">register</button>
    </form>
</template>

<script>
import axios from "axios";

export default {
    name: "Registration",
    data() {
        return {
            person: {
                name: '',
                email: '',
                password: '',
                password_confirmation: ''
            }
        }
    },
    methods: {
        registerUser() {
            axios.get('/sanctum/csrf-cookie').then(response => {
                console.log(response)
            }).catch(err => {
                console.log(err)
            })
            axios.post('api/register', this.person).then(response => {
                console.log(response)
            }).catch(err => {
                console.log(err.response.data.errors)
            })
        }
    }
}
</script>

<style scoped>

</style>
