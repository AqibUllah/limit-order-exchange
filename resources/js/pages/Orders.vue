<script setup>

import { useUserStore } from '../stores/user.js'
import OrderForm from "../components/OrderForm.vue";

const userStore = useUserStore()

</script>

<template>
    <div class="p-6 max-w-4xl mx-auto">
        <h1 class="text-2xl font-semibold mb-6">Orders Dashboard</h1>

        <div class="grid grid-cols-2 gap-6">
            <!-- Wallet -->
            <div class="bg-gradient-to-r from-indigo-500 to-blue-500 text-white p-4 shadow-blue-500/50 shadow-2xl rounded shadow">
                <h2 class="font-semibold mb-3">Wallet</h2>

                <div class="mb-2">
                    <strong>USD:</strong> {{ userStore.balance }}
                </div>

                <div v-for="asset in userStore.assets" :key="asset.symbol">
                    <strong>{{ asset.symbol }}:</strong>
                    {{ asset.amount }} (locked: {{ asset.locked_amount }})
                </div>
            </div>

            <!-- Order Form -->
            <OrderForm />
        </div>
    </div>
</template>

<style scoped>
.card{
    background: #2c5ba0;
    color: #fff;
}
</style>
