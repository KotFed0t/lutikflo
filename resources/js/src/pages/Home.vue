<template>
    <h3>Home</h3>

</template>

<script>
import axios from "axios";

export default {
    name: "Home",
    mounted() {
        this.getUsers()
    },
    methods: {
        getUsers() {
            axios.get('api/users').then(response => {
                console.log(response.data.data)
            }).catch(err => {
                console.log(err)
            })
        }
    }
}
</script>

<style scoped>

</style>
