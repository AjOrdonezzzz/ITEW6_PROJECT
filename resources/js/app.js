import './bootstrap';
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import LoginPage from './pages/LoginPage.vue';
import DashboardPage from './pages/DashboardPage.vue';
import RegisterPage from './pages/RegisterPage.vue';
import StudentsPage from './pages/StudentsPage.vue';
import ViolationsPage from './pages/ViolationsPage.vue';
import EventsPage from './pages/EventsPage.vue';
import ReportsPage from './pages/ReportsPage.vue';
import SettingsPage from './pages/SettingsPage.vue';
import ProfilePage from './pages/ProfilePage.vue';

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
    },
    {
        path: '/violations',
        name: 'Violations',
        component: ViolationsPage
    },
    {
        path: '/events',
        name: 'Events',
        component: EventsPage
    },
    {
        path: '/reports',
        name: 'Reports',
        component: ReportsPage
    },
    {
        path: '/settings',
        name: 'Settings',
        component: SettingsPage
    },
    {
        path: '/profile',
        name: 'Profile',
        component: ProfilePage
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
