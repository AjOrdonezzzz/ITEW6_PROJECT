import './bootstrap';
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import LoginPage from './pages/LoginPage.vue';
import DashboardPage from './pages/DashboardPage.vue';
import RegisterPage from './pages/RegisterPage.vue';
import StudentsPage from './pages/StudentsPage.vue';

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
    },
    {
        path: '/students',
        name: 'Students',
        component: StudentsPage
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
