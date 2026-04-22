import { createRouter, createWebHistory } from 'vue-router';

// 1. Import your pages
import TestPage from '../views/TestPage.vue';
import LoginPage from '../views/LoginPage.vue';
import DashboardPage from '../views/DashboardPage.vue';
import StudentsPage from '../views/StudentsPage.vue';
import ViolationsPage from '../views/ViolationsPage.vue';
import EventsPage from '../views/EventsPage.vue';
import ReportsPage from '../views/ReportsPage.vue';
import SettingsPage from '../views/SettingsPage.vue';
import ProfilePage from '../views/ProfilePage.vue';
import UsersPage from '../views/UsersPage.vue';

// 2. Define the routes array
const routes = [
    { path: '/test-connection', name: 'test', component: TestPage },
    { path: '/', name: 'Login', component: LoginPage },
    { path: '/dashboard', name: 'Dashboard', component: DashboardPage, meta: { requiresAuth: true } },
    { path: '/students', name: 'Students', component: StudentsPage, meta: { requiresAuth: true } },
    { path: '/violations', name: 'Violations', component: ViolationsPage, meta: { requiresAuth: true } },
    { path: '/events', name: 'Events', component: EventsPage, meta: { requiresAuth: true } },
    { path: '/reports', name: 'Reports', component: ReportsPage, meta: { requiresAuth: true, requiresAdmin: true } },
    { path: '/users', name: 'Users', component: UsersPage, meta: { requiresAuth: true, requiresAdmin: true } },
    { path: '/settings', name: 'Settings', component: SettingsPage, meta: { requiresAuth: true } },
    { path: '/profile', name: 'Profile', component: ProfilePage, meta: { requiresAuth: true } }
];

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes
});

// 3. Navigation Guard (Corrected version)
router.beforeEach((to, from) => {
    // For now, since your auth utils are commented out, 
    // we return true (allow all) so the app doesn't crash.
    return true; 

    /* Once you fix your auth utils, use this logic:
    const user = getStoredUser();
    if (to.meta.requiresAuth && !user) return { name: 'Login' };
    if (to.path === '/' && user) return { name: 'Dashboard' };
    */
});

export default router;