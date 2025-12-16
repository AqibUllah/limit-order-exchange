import { createRouter, createWebHistory } from 'vue-router'
import { useUserStore } from '../stores/user.js'
import axios from 'axios'

import Login from '../pages/Login.vue'
import Orders from '../pages/Orders.vue'
import DashboardLayout from "../layouts/DashboardLayout.vue";
import MainLayout from "../layouts/MainLayout.vue";
import {useAuthStore} from "../stores/auth.js";

const routes = [
    {
        path: '/login',
        component: MainLayout,
        meta: {
            requiresAuth: false,
        },
        children: [
            {
                path: '',
                component: Login,
            }
        ]
    },
    {
        path: '/',
        component: DashboardLayout,
        meta: { requiresAuth: true },
        children:[
            {
                path:'',
                component: Orders,
                name: 'orders'
            }
        ]
    },
    { path: '/:pathMatch(.*)*', redirect: '/' }
]

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes
})

// Restore token on app start (important for page refresh)
const savedToken = localStorage.getItem('auth_token')
if (savedToken) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${savedToken}`
}


router.beforeEach(async (to, from, next) => {
    const userStore = useUserStore()

    if (!userStore.loaded) {
        try {
            await userStore.fetchProfile()
        } catch (e) {
            userStore.reset()
        }
        userStore.loaded = true
    }


    if (to.meta.requiresAuth) {
        if (useAuthStore().isAuthenticated()) {
            return next()
        } else {
            return next('/login')
        }
    } else {
        if (useAuthStore().isAuthenticated()) {
            return next('/')
        } else {
            return next()
        }
    }
})

export default router
