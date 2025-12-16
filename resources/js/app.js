import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { Toaster } from 'vue-sonner'
import router from './router'
import './bootstrap'
import App from './App.vue';

import '../css/app.css'
import 'vue-sonner/style.css'

const app = createApp(App,{})
const pinia = createPinia()

app.use(router)
app.use(pinia)
app.component('Toaster',Toaster)
app.mount('#app')
