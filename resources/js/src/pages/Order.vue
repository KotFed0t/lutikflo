<template>
<!--    <VueDatePicker v-model="date"-->
<!--                   locale="ru"-->
<!--                   select-text="Cохранить"-->
<!--                   cancel-text="Отменить"-->
<!--                   @closed="log"-->
<!--    ></VueDatePicker>-->


</template>

<script>
// import VueDatePicker from '@vuepic/vue-datepicker';
// import '@vuepic/vue-datepicker/dist/main.css'

import axios from "axios";

export default {
    // components: { VueDatePicker },
    data() {
        return {
            date: null,
        };
    },
    mounted() {
        this.getUser()
    },
    methods: {
        getUser() {
            axios.get('api/user').then(response => {
                console.log(response.data)
            }).catch(err => {
                console.log(err)
            })
        }
    }
}
</script>
