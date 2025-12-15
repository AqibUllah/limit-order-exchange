<script setup>

import { ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const email = ref('')
const password = ref('')
const error = ref(null)

const router = useRouter()

const login = async () => {
    error.value = null

    try {
        await axios.get('/sanctum/csrf-cookie')

        await axios.post('/api/login', {
            email: email.value,
            password: password.value
        })

        await axios.get('/api/profile')

        await router.push('/')
    } catch (e) {
        error.value = e.response?.data?.message || 'Login failed'
    }
}

</script>

<template>
    <div class="max-w-md mx-auto mt-20">
        <h1 class="text-xl mb-4">Login</h1>

        <div v-if="error" class="text-red-500 mb-2">
            {{ error }}
        </div>

        <input
            v-model="email"
            type="email"
            placeholder="Email"
            class="border w-full mb-2 p-2"
        />

        <input
            v-model="password"
            type="password"
            placeholder="Password"
            class="border w-full mb-4 p-2"
        />

        <button
            @click="login"
            class="bg-black text-white px-4 py-2"
        >
            Login
        </button>
    </div>
</template>

<style scoped>

</style>
