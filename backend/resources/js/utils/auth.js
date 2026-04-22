export const USER_STORAGE_KEY = 'user';
export const MANAGED_USERS_STORAGE_KEY = 'managedUsers';

const DEFAULT_USERS = [
    {
        id: 1,
        fullName: 'System Administrator',
        username: 'admin',
        password: 'password123',
        role: 'admin',
        status: 'active'
    },
    {
        id: 2,
        fullName: 'Gwillano',
        username: 'gwillano',
        password: 'demo123',
        role: 'user',
        status: 'active'
    },
    {
        id: 3,
        fullName: 'Standard User',
        username: 'user',
        password: 'user123',
        role: 'user',
        status: 'active'
    }
];

export function getStoredUser() {
    try {
        return JSON.parse(localStorage.getItem(USER_STORAGE_KEY) || 'null');
    } catch {
        return null;
    }
}

export function setStoredUser(user) {
    localStorage.setItem(USER_STORAGE_KEY, JSON.stringify(user));
}

export function clearStoredUser() {
    localStorage.removeItem(USER_STORAGE_KEY);
}

export function isAdminUser(user = getStoredUser()) {
    return user?.role === 'admin';
}

export function isAuthenticated() {
    return Boolean(getStoredUser());
}

export function getManagedUsers() {
    try {
        const raw = localStorage.getItem(MANAGED_USERS_STORAGE_KEY);
        const parsed = raw ? JSON.parse(raw) : null;

        if (Array.isArray(parsed) && parsed.length) {
            return parsed;
        }

        localStorage.setItem(MANAGED_USERS_STORAGE_KEY, JSON.stringify(DEFAULT_USERS));
        return [...DEFAULT_USERS];
    } catch {
        localStorage.setItem(MANAGED_USERS_STORAGE_KEY, JSON.stringify(DEFAULT_USERS));
        return [...DEFAULT_USERS];
    }
}

export function saveManagedUsers(users) {
    localStorage.setItem(MANAGED_USERS_STORAGE_KEY, JSON.stringify(users));
}

export function findManagedUser(username) {
    const users = getManagedUsers();

    return users.find(
        (user) => user.username.toLowerCase() === String(username).trim().toLowerCase()
    ) || null;
}