import './bootstrap';
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';

import LoginPage from './pages/LoginPage.vue';
import RegisterPage from './pages/RegisterPage.vue';
import DashboardPage from './pages/DashboardPage.vue';
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
        component: LoginPage,
        meta: { guest: true }
    },
    {
        path: '/register',
        name: 'Register',
        component: RegisterPage,
        meta: { guest: true }
    },
    {
        path: '/dashboard',
        name: 'Dashboard',
        component: DashboardPage,
        meta: { requiresAuth: true }
    },
    {
        path: '/students',
        name: 'Students',
        component: StudentsPage,
        meta: { requiresAuth: true }
    },
    {
        path: '/violations',
        name: 'Violations',
        component: ViolationsPage,
        meta: { requiresAuth: true }
    },
    {
        path: '/events',
        name: 'Events',
        component: EventsPage,
        meta: { requiresAuth: true }
    },
    {
        path: '/reports',
        name: 'Reports',
        component: ReportsPage,
        meta: { requiresAuth: true }
    },
    {
        path: '/settings',
        name: 'Settings',
        component: SettingsPage,
        meta: { requiresAuth: true }
    },
    {
        path: '/profile',
        name: 'Profile',
        component: ProfilePage,
        meta: { requiresAuth: true }
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

async function getCurrentUser() {
    try {
        const response = await window.axios.get('/api/v1/user');
        return response.data.user;
    } catch (error) {
        return null;
    }
}

router.beforeEach(async (to, from, next) => {
    const user = await getCurrentUser();

    if (to.meta.requiresAuth && !user) {
        return next({ name: 'Login' });
    }

    if (to.meta.guest && user) {
        return next({ name: 'Dashboard' });
    }

    next();
});

const app = createApp({
    template: '<router-view />'
});

app.use(router);
app.mount('#app');