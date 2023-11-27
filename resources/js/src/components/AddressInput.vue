<template>
    <p style="color: red">{{ this.message }}</p>
    <input type="text" name="city" list="suggestions" v-model="query" @input="getSuggestions" @change="checkAddress"
           style="width: 800px;">
    <datalist id="suggestions">
        <option v-for="el in suggestions">{{ el.value }}</option>
    </datalist>

    <p>Стоимость доставки: {{ deliveryPrice }}</p>

    <div>
        <p></p>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "AddressInput",
    emits: ['get'],
    data() {
        return {
            suggestions: [],
            query: '',
            timer: undefined,
            selectedAddress: undefined,
            message: '',
            deliveryPrice: undefined,

        }
    },
    methods: {
        checkAddress() {
            this.selectedAddress = undefined
            console.log(this.query)
            const result = this.suggestions.find((el) => el.value === this.query)
            if (result !== undefined) {
                if (result.data.house === null) {
                    this.message = 'продолжите вводить адрес вплоть до номера дома'
                } else {
                    this.selectedAddress = result
                    console.log('selected', this.selectedAddress.value)
                    this.message = 'все введено корректно, ты красавчик!'
                    this.getDeliveryPrice()
                    if (this.selectedAddress.data.geo_lat && this.selectedAddress.data.geo_lon) {
                        this.$emit('get', {
                            'address': this.selectedAddress.value,
                            'latitude': this.selectedAddress.data.geo_lat,
                            'longitude': this.selectedAddress.data.geo_lon,
                        })
                    }
                }
            } else {
                this.message = 'введите корректный адрес и выберите предложенное значение'
            }

        },
        getSuggestions() {
            clearTimeout(this.timer)
            this.timer = setTimeout(() => {
                axios.post('https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address', {
                    "query": this.query,
                    "locations": [{
                        "area": "Новосибирский"
                    }, {
                        "city": "Новосибирск"
                    }]
                }, {
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "Authorization": "Token " + import.meta.env.VITE_DADATA_API_KEY
                    }
                }).then(response => {
                    console.log(response.data.suggestions)
                    this.suggestions = response.data.suggestions
                }).catch(err => {
                    console.log(err)
                })
            }, 500)
        },
        getDeliveryPrice() {
            if (this.selectedAddress !== undefined) {
                axios.get('api/delivery-price', {
                    params: {
                        'latitude': this.selectedAddress.data.geo_lat,
                        'longitude': this.selectedAddress.data.geo_lon
                    }
                }).then(response => {
                    this.deliveryPrice = response.data.price
                }).catch(err => {
                    console.log(err)
                    this.message = err.response.data.error_message
                })
            }
        }
    }
}
</script>

<style scoped>

</style>
