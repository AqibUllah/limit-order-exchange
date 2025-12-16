<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { toast } from 'vue-sonner'
import { useUserStore } from '@/stores/user.js'
import { useAuthStore } from "@/stores/auth";

const email = ref('')
const password = ref('')
const error = ref(null)

const authStore = useAuthStore()
const userStore = useUserStore()
const router = useRouter()

const login = async () => {
    error.value = null
    try {
        await authStore.login({
            email: email.value,
            password: password.value
        })

        await userStore.fetchProfile()

        router.push('/')
    } catch(err) {
        console.log(err.message)
        error.value = authStore.error
        toast.error(error.value ?? err.message)
    }
}
</script>


<template>
    <div class="max-w-md mx-auto mt-20">
        <h1 class="text-xl mb-4">Login</h1>

        <div v-if="error" class="text-red-500 mb-2">
            {{ error }}
        </div>

        <form @submit.prevent="login">

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
            type="submit"
            class="bg-black text-white px-4 py-2"
        >
            Login
        </button>
        </form>
    </div>
</template>

<style scoped>

</style>
