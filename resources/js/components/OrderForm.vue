<script setup>
import { ref } from 'vue'
import { useOrderStore } from '../stores/order.js'
import { useUserStore } from '../stores/user.js'
import { toast } from 'vue-sonner'

const orderStore = useOrderStore()
const userStore = useUserStore()

const symbol = ref('BTC')
const side = ref('buy')
const price = ref('')
const amount = ref('')
const error = ref(null)
const success = ref(null)

const submitOrder = async () => {
    error.value = null
    success.value = null

    try {
        await orderStore.placeOrder({
            symbol: symbol.value,
            side: side.value,
            price: price.value,
            amount: amount.value,
        })

        // refreshing wallet
        await userStore.fetchProfile()

        success.value = 'Order placed successfully'
        toast.info('Order placed successfully')
        price.value = ''
        amount.value = ''
    } catch (e) {
        error.value = e.response?.data?.message || 'Order failed'
        toast.error(e.response?.data?.message || 'Order failed')
    }
}
</script>

<template>
    <div class="bg-white p-4 rounded shadow">
        <h2 class="text-lg font-semibold mb-4">Place Limit Order</h2>

        <div v-if="error" class="text-red-500 mb-2">
            {{ error }}
        </div>

        <div v-if="success" class="text-green-600 mb-2">
            {{ success }}
        </div>

        <div class="grid grid-cols-2 gap-3 mb-3">
            <div>
                <label class="block text-sm mb-1">Symbol</label>
                <select v-model="symbol" class="border p-2 w-full">
                    <option value="BTC">BTC</option>
                    <option value="ETH">ETH</option>
                </select>
            </div>

            <div>
                <label class="block text-sm mb-1">Side</label>
                <select v-model="side" class="border p-2 w-full">
                    <option value="buy">Buy</option>
                    <option value="sell">Sell</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="block text-sm mb-1">Price (USD)</label>
            <input
                v-model="price"
                type="number"
                step="0.01"
                class="border p-2 w-full"
            />
        </div>

        <div class="mb-4">
            <label class="block text-sm mb-1">Amount</label>
            <input
                v-model="amount"
                type="number"
                step="0.00000001"
                class="border p-2 w-full"
            />
        </div>

        <button
            @click="submitOrder"
            :disabled="orderStore.loading"
            class="bg-black text-white px-4 py-2 disabled:opacity-50"
        >
            {{ orderStore.loading ? 'Placing...' : 'Place Order' }}
        </button>
    </div>
</template>
