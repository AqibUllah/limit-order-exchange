import { createRouter, createWebHistory } from 'vue-router'
import { useUserStore } from '../stores/user.js'
import axios from 'axios'

import Login from '../pages/Login.vue'
import Orders from '../pages/Orders.vue'

const routes = [
    { path: '/login', component: Login },
    { path: '/', component: Orders }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

router.beforeEach(async (to, from, next) => {
    const userStore = useUserStore()

    if (to.path === '/login') {
        return next()
    }

    try {
        if (!userStore.loaded) {
            await userStore.fetchProfile()
        }

        next()
    } catch {
        userStore.reset()
        next('/login')
    }
})

export default router
