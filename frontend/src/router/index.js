import { createRouter, createWebHistory } from 'vue-router'
import App from '@/App.vue'

const routes = [
    {
        path: '/',
        name: 'home',
        component: App
    },
    {
        path: '/editor/:id?',
        name: 'editor',
        component: App
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router
