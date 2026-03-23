import './bootstrap';
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import LoginPage from './pages/LoginPage.vue';
import DashboardPage from './pages/DashboardPage.vue';

const routes = [
    {
        path: '/',
        name: 'Login',
        component: LoginPage
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

this.$router.push('/dashboard'); // Go to dashboard
this.$router.push('/'); // Go to login