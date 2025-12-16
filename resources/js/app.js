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


app.directive('click-outside', {
    beforeMount(el, binding) {
        el._clickOutside = (event) => {
            // Check if click is outside the element and not on the element itself
            if (!(el === event.target || el.contains(event.target))) {
                // Use binding.value() — call the function passed (e.g., close dropdown)
                binding.value(event)
            }
        }

        // Use capture: false (default), but delay attachment to avoid immediate trigger
        document.addEventListener('click', el._clickOutside)
    },
    unmounted(el) {
        document.removeEventListener('click', el._clickOutside)
        delete el._clickOutside
    }
})
