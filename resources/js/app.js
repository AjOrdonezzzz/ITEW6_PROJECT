import './bootstrap';
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import LoginPage from './pages/LoginPage.vue';
import DashboardPage from './pages/DashboardPage.vue';
import RegisterPage from './pages/RegisterPage.vue';

const routes = [
    {
        path: '/',
        name: 'Login',
        component: LoginPage
    },
    {
        path: '/register',
        name: 'Register',
        component: RegisterPage
    },
    {
        path: '/dashboard',
        name: 'Dashboard',
        component: DashboardPage
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

const app = createApp({
    template: '<router-view />'
});

app.use(router);
app.mount('#app');
