<script setup>

import { onMounted } from 'vue'
import OrderForm from "../components/OrderForm.vue";
import { useUserStore } from '../stores/user.js'
import { useOrderStore } from '../stores/order.js'

const userStore = useUserStore()
const orderStore = useOrderStore()

onMounted(async () => {
    await userStore.fetchProfile()
    await orderStore.fetchOrders()
})
</script>

<template>
    <div class="p-6 max-w-4xl mx-auto">
        <h1 class="text-2xl font-semibold mb-6">Exchange</h1>

        <div class="grid grid-cols-2 gap-6">
            <!-- Wallet -->
            <div class="bg-gradient-to-r from-indigo-500 to-blue-500 text-white p-4 shadow-blue-500/50 shadow-2xl rounded shadow">
                <h2 class="font-semibold mb-3">Wallet</h2>

                <div class="mb-2">
                    <strong>USD</strong>
                    <h1 class="text-5xl font-bold">{{ userStore.balance }}</h1>
                </div>

                <div v-for="asset in userStore.assets" :key="asset.symbol">
                    <strong>{{ asset.symbol }}:</strong>
                    {{ asset.amount }} (locked: {{ asset.locked_amount }})
                </div>
            </div>

            <!-- Order Form -->
            <OrderForm />
        </div>

        <div class="mt-8">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Orders</h2>

            <div class="overflow-hidden border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr class="[&_th]:px-4 [&_th]:py-3 [&_th]:text-left [&_th]:text-xs [&_th]:font-medium [&_th]:text-gray-600 [&_th]:uppercase">
                        <th>Side</th>
                        <th>Price</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Cancel</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="o in orderStore.orders" :key="o.id" class="hover:bg-gray-50 [&_td]:px-4 [&_td]:py-3 text-sm [&_td]:text-gray-900">
                        <td>
                        <span :class="{
                            'font-medium': true,
                            'text-green-600': o.side.toLowerCase() === 'buy',
                            'text-red-600': o.side.toLowerCase() === 'sell'
                            }">
                            {{ o.side.toUpperCase() }}
                        </span>
                        </td>
                        <td>{{ o.price }}</td>
                        <td>{{ o.amount }}</td>
                        <td>
                            <span class="text-gray-600">{{ o.status_label }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <button
                                v-if="o.status === 1 || o.status_label === 'open'"
                                @click="orderStore.cancelOrder(o.id)"
                                class="text-red-500 hover:text-red-700 hover:underline hover:cursor-pointer"
                            >
                                Cancel
                            </button>
                            <span v-else class="text-sm text-gray-400">—</span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
