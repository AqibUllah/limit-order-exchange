import {ref} from "vue";
import {defineStore} from "pinia";
import axios from 'axios'
import {useRouter} from "vue-router";
import router from '@/router'
import {useUserStore} from "./user.js";

export const useAuthStore = defineStore('auth', () => {

    // defining store variables
    const loading = ref(false)
    const error = ref(null)
    const token = ref(null)
    const isAuthenticated = () => !!localStorage.getItem('auth_token') && !!useUserStore().user
    // implementations
    const login = async (credentials) => {
            loading.value = true
            error.value = null
            try {
                await axios.get('/sanctum/csrf-cookie')
                const res = await axios.post('/api/login', credentials)
                token.value = res.data.token

                // token handling
                axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
                localStorage.setItem('auth_token', token.value)
            } catch (e) {
                error.value = e.response?.data?.message || 'Login failed'
                throw e
            } finally {
                loading.value = false
            }
        }

    const logout = async () => {
        try{
            await axios.post('/api/logout')
        }catch (e){

        } finally {
            token.value = null
            localStorage.removeItem('auth_token')
            delete axios.defaults.headers.common['Authorization']
            if (window.Echo) window.Echo.disconnect()
            router.push('/login')
        }


    }

    // returning all variables
    return {
        //methods
        login,
        logout,
        isAuthenticated,

        //variables
        loading,
        error,
        token,
    }

})
