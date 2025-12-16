import {ref} from "vue";
import {defineStore} from "pinia";
import axios from 'axios'

export const useOrderStore = defineStore('order', () => {

    // defining store variables
    const orders = ref([])
    const loading =  ref(false)


    // implementations
    const placeOrder = async (payload) => {
        loading.value = true
        try {

        const res = await axios.post('/api/orders',payload)

        } finally {
            loading.value = false
        }

    }

    const cancelOrder = async (id) => {
        await axios.post(`/api/orders/${id}/cancel`)
    }

    const reset = () => {
        orders.value = []
        loading.value = false
    }




    // returning all variables
    return {

        //methods
        placeOrder,
        cancelOrder,
        reset,

        //variables
        orders,
        loading,

    }

})
