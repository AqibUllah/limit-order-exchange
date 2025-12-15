import { createRouter, createWebHistory } from 'vue-router'
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
    if (to.path === '/login') {
        return next()
    }

    try {
        await axios.get('/api/profile')
        next()
    } catch {
        next('/login')
    }
})

export default router
