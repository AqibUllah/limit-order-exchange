import { createApp } from 'vue'
import router from './router'
import './bootstrap'

import '../css/app.css'

const app = createApp({})

app.use(router)

app.mount('#app')
