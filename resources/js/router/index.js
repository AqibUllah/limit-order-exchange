import { createRouter, createWebHistory } from 'vue-router'
import { useUserStore } from '../stores/user.js'
import axios from 'axios'

import Login from '../pages/Login.vue'
import Orders from '../pages/Orders.vue'
import DashboardLayout from "../layouts/DashboardLayout.vue";
import MainLayout from "../layouts/MainLayout.vue";

const routes = [
    {
        path: '/login',
        component: MainLayout,
        meta: { requiresAuth: false },
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
    history: createWebHistory(),
    routes
})

// Restore token on app start (important for page refresh)
const savedToken = localStorage.getItem('auth_token')
if (savedToken) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${savedToken}`
}

router.beforeEach(async (to, from, next) => {
    const userStore = useUserStore()

    if (!to.meta.requiresAuth) {
        if (!userStore.loaded) {
            try {
                await userStore.fetchProfile()
            } catch (e) {
                return next()
            }
        }

        // if user already logged in then redirecting to home page
        if (userStore.user) {
            return next('/')
        }

        return next()
    }

    // only for safe(protected) routes
    if (to.meta.requiresAuth) {
        if (!userStore.loaded) {
            try {
                await userStore.fetchProfile()
            } catch (e) {
                userStore.reset()
                return next('/login')
            }
        }

        // user is authenticated
        if (userStore.user) {
            return next()
        }

        // unauthenticated : redirecting to login
        return next('/login')
    }

    next()
})

export default router
