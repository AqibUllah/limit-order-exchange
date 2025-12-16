import {ref} from "vue";
import {defineStore} from "pinia";
import axios from 'axios'
import {useOrderStore} from "./order.js";

export const useUserStore = defineStore('user', () => {

    // defining store variables
    const user = ref()
    const balance =  ref(0)
    const assets =  ref()
    const loaded =  ref(false)


    // implementations
    const fetchProfile = async () => {
        const res = await axios.get('/api/profile')

        user.value = {
            id: res.data.id,
            email: res.data.email,
            name: res.data.name,
        }

        balance.value = res.data.balance
        assets.value = res.data.assets
        loaded.value = true
    }

    const reset = () => {
        user.value = null
        balance.value = 0
        assets.value = []
        loaded.value = false

        if (window.Echo) {
            window.Echo.disconnect()
        }
    }

    const listenForMatches = () => {
        if (!user.value) return

        const orderStore = useOrderStore()
        window.Echo.private(`user.${user.value.id}`)
            .listen('.OrderMatchedEvent', async () => {
                await fetchProfile()
                await orderStore.fetchOrders()
            })
    }




    // returning all variables
    return {

        //methods
        fetchProfile,
        reset,
        listenForMatches,

        //variables
        user,
        balance,
        assets,
        loaded

    }

})
