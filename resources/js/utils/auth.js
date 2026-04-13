export const USER_STORAGE_KEY = 'user';
export const REGISTERED_USER_STORAGE_KEY = 'registeredUser';

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

export function getStoredRegisteredUser() {
    try {
        return JSON.parse(localStorage.getItem(REGISTERED_USER_STORAGE_KEY) || 'null');
    } catch {
        return null;
    }
}

export function isAdminUser(user = getStoredUser()) {
    return user?.role === 'admin';
}

export function isAuthenticated() {
    return Boolean(getStoredUser());
}
