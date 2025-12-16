import {ref} from "vue";
import {defineStore} from "pinia";
import axios from 'axios'
export const useAuthStore = defineStore('auth', () => {

    // defining store variables
    const loading = ref(false)
    const error = ref(null)
    const token = ref(null)

    // implementations
    const login = async (credentials) => {
            loading.value = true
            error.value = null
            try {
                await axios.get('/sanctum/csrf-cookie')
                await axios.post('/api/login', credentials)
                    .then(res => {
                        token.value = res.data.token
                    })
            } catch (e) {
                error.value = e.response?.data?.message || 'Login failed'
                throw e
            } finally {
                loading.value = false
            }
        }

    const logout = async () => await axios.post('/api/logout')


    // returning all variables
    return {
        //methods
        login,
        logout,

        //variables
        loading,
        error,
        token
    }

})
