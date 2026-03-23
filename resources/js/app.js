import './bootstrap';
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import LoginPage from './pages/LoginPage.vue';

const routes = [
    {
        path: '/',
        name: 'Login',
        component: LoginPage
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