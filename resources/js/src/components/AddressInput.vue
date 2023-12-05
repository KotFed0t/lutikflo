<template>
    <p v-if="message" class="text-sm font-medium text-red-800 mb-2 bg-red-50 px-2 py-1.5 rounded-lg my-1">{{ this.message }}</p>

    <label for="address" class="block text-neutral-700 text-sm font-medium mb-1">адрес доставки <span class="text-red-500 font-bold">*</span></label>
    <input id="address" ref="addressInput" type="text" autocomplete="off" @focus="showSuggestions=true" @blur="showSuggestions=false" v-model="query"
           class="w-full border border-black px-2 py-1.5 rounded-lg"
    >

    <div class="relative">
        <div v-if="showSuggestions && query!== ''" @mousedown.prevent class="w-full absolute z-20 bg-white border shadow-2xl rounded-lg">
            <div v-for="el in suggestions" @click="query = el.value" class="cursor-pointer hover:bg-neutral-200 leading-5 p-2">
                {{ el.value }}
            </div>
        </div>
    </div>

    <div class="flex items-center mt-2">
        <p >Стоимость доставки:</p>
        <p v-if="deliveryPrice" class="ml-1 font-medium"> {{ deliveryPrice }} ₽</p>
    </div>


    <div>
        <p></p>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "AddressInput",
    emits: ['get', 'getPrice'],
    data() {
        return {
            suggestions: [],
            query: '',
            timer: undefined,
            selectedAddress: undefined,
            message: '',
            deliveryPrice: undefined,
            showSuggestions: false
        }
    },
    watch: {
      query() {
          this.getSuggestions()
      },
      suggestions() { //подсказки подгружаются не сразу, нужно ждать пока обновятся
          this.checkAddress()
      }
    },
    methods: {
        checkAddress() {
            this.selectedAddress = undefined
            this.deliveryPrice = undefined
            this.$emit('getPrice', this.deliveryPrice)
            this.message = ''
            const result = this.suggestions.find((el) => el.value === this.query)
            if (result !== undefined) {
                if (result.data.house === null) {
                    this.message = 'продолжите вводить адрес вплоть до номера дома'
                } else {
                    this.selectedAddress = result
                    this.$refs.addressInput.blur()
                    this.getDeliveryPrice()
                    if (this.selectedAddress.data.geo_lat && this.selectedAddress.data.geo_lon) {
                        this.$emit('get', {
                            'address': this.selectedAddress.value,
                            'latitude': this.selectedAddress.data.geo_lat,
                            'longitude': this.selectedAddress.data.geo_lon
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
                    this.$emit('getPrice', this.deliveryPrice)
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
