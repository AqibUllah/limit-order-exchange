<template>
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Left: Brand -->
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-gray-900">MyApp</h1>
                </div>

                <!-- Right: User Dropdown -->
                <div class="relative" v-click-outside="closeDropdown">
                    <button
                        @click.stop="isOpen = !isOpen"
                        class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900 focus:outline-none"
                    >
                        <span class="mr-2">{{ userName }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"><!-- Icon from Material Design Light by Pictogrammers - https://github.com/Templarian/MaterialDesignLight/blob/master/LICENSE.md --><path fill="currentColor" d="m5.84 9.59l5.66 5.66l5.66-5.66l-.71-.7l-4.95 4.95l-4.95-4.95z"/></svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <transition
                        enter-active-class="transition ease-out duration-100"
                        enter-from-class="transform opacity-0 scale-95"
                        enter-to-class="transform opacity-100 scale-100"
                        leave-active-class="transition ease-in duration-75"
                        leave-from-class="transform opacity-100 scale-100"
                        leave-to-class="transform opacity-0 scale-95"
                    >
                        <div
                            v-if="isOpen"
                            class="absolute right-0 mt-2 w-48 origin-top-right rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10"
                        >
                            <div class="py-1">
                                <div class="px-4 py-2 text-sm text-gray-700 border-b border-gray-200">
                                    Signed in as <strong>{{ userName }}</strong>
                                </div>
                                <button
                                    @click="handleLogout"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                >
                                    Logout
                                </button>
                            </div>
                        </div>
                    </transition>
                </div>
            </div>
        </div>
    </header>
</template>

<script setup>
import { ref } from 'vue';
import {useAuthStore} from "../stores/auth.js";
import {useUserStore} from "../stores/user.js";

// Replace with your actual user data (e.g., from store, props, or auth)

const userName = ref(useUserStore().user.name || null);
const isOpen = ref(false);

const authStore = useAuthStore()
const handleLogout = () => {
    authStore.logout()
};

const closeDropdown = () => {
    isOpen.value = false
}
</script>
