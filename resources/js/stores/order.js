import {ref} from "vue";
import {defineStore} from "pinia";
import axios from 'axios'

export const useOrderStore = defineStore('order', () => {

    // defining store variables
    const orders = ref([])
    const loading =  ref(false)
    const symbol = ref('BTC')

    // implementations
     const fetchOrders = async(symbol_param = null) => {
        loading.value = true

        try {
            if (symbol_param) {
                symbol.value = symbol_param
            }

            const res = await axios.get('/api/orders', {
                params: { symbol_param: symbol.value }
            })

            orders.value = res.data
        } finally {
            loading.value = false
        }
    }
    const placeOrder = async (payload) => {
        loading.value = true
        try {

            await axios.post('/api/orders',payload)
            //refreshing orders
            await fetchOrders(payload.symbol)
        } finally {
            loading.value = false
        }

    }

    const cancelOrder = async (id) => {
        loading.value = true
        try {
            await axios.post(`/api/orders/${id}/cancel`)
            await fetchOrders()
        } finally {
            loading.value = false
        }
    }

    const reset = () => {
        orders.value = []
        loading.value = false
    }




    // returning all variables
    return {

        //methods
        fetchOrders,
        placeOrder,
        cancelOrder,
        reset,

        //variables
        orders,
        loading,

    }

})
