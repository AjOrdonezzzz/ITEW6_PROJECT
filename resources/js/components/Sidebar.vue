<template>
    <aside :class="['sidebar', { collapsed: !isOpen }]">
        <div class="sidebar-header">
            <div class="logo-area" v-show="isOpen">
                <span class="logo-text">Menu</span>
            </div>
            <button class="toggle-btn" @click="$emit('toggle')">
                {{ isOpen ? '◀' : '▶' }}
            </button>
        </div>

        <nav class="sidebar-nav">
            <component
                v-for="item in menuItems"
                :key="item.id"
                :is="item.route ? 'router-link' : 'div'"
                v-bind="item.route ? { to: item.route } : {}"
                class="nav-item"
                :class="{ active: item.route && $route.path === item.route }"
            >
                <span class="nav-icon">{{ item.icon }}</span>
                <span class="nav-label" v-show="isOpen">{{ item.label }}</span>
                <div class="tooltip" v-show="!isOpen">{{ item.label }}</div>
            </component>
        </nav>

        <div class="sidebar-footer">
            <button class="logout-btn" @click="handleLogout">
                <span class="logout-icon">🚪</span>
                <span class="logout-text" v-show="isOpen">Logout</span>
            </button>
        </div>
    </aside>
</template>

<script>
export default {
    name: 'Sidebar',
    props: {
        isOpen: {
            type: Boolean,
            default: true
        }
    },
    data() {
        return {
            menuItems: [
                { id: 1, label: 'Dashboard', icon: '📊', route: '/dashboard' },
                { id: 2, label: 'Students', icon: '👥', route: '/students' },
                { id: 3, label: 'Violations', icon: '⚠️', route: '/violations' },
                { id: 4, label: 'Events', icon: '📅', route: '/events' },
                { id: 5, label: 'Reports', icon: '📋', route: '/reports' },
                { id: 6, label: 'Settings', icon: '⚙️', route: '/settings' }
            ]
        };
    },
    methods: {
        handleLogout() {
            localStorage.removeItem('user');
            this.$router.push('/');
        }
    }
};
</script>

<style scoped>
.sidebar {
    width: 250px;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(10px);
    padding: 20px 15px;
    display: flex;
    flex-direction: column;
    transition: width 0.3s ease;
    position: relative;
    border-right: 1px solid rgba(255, 255, 255, 0.1);
    overflow: hidden;
}

.sidebar.collapsed {
    width: 70px;
}

.sidebar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    min-height: 36px;
}

.logo-text {
    color: white;
    font-weight: 700;
    font-size: 16px;
    white-space: nowrap;
}

.toggle-btn {
    background: rgba(255, 255, 255, 0.1);
    border: none;
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.3s ease;
    flex-shrink: 0;
    margin-left: auto;
}

.toggle-btn:hover {
    background: rgba(255, 255, 255, 0.2);
}

.sidebar-nav {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.nav-item {
    position: relative;
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 12px;
    color: rgba(255, 255, 255, 0.7);
    cursor: pointer;
    border-radius: 8px;
    transition: all 0.3s ease;
    font-size: 15px;
    font-weight: 500;
    white-space: nowrap;
    text-decoration: none;
}

.nav-item:hover,
.nav-item.active {
    background: rgba(255, 255, 255, 0.12);
    color: white;
}

.nav-icon {
    font-size: 20px;
    width: 28px;
    text-align: center;
    flex-shrink: 0;
}

.nav-label {
    overflow: hidden;
    transition: opacity 0.2s ease;
}

.tooltip {
    position: absolute;
    left: 65px;
    background: rgba(0, 0, 0, 0.85);
    color: white;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 13px;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s ease;
    z-index: 999;
}

.nav-item:hover .tooltip {
    opacity: 1;
}

.sidebar-footer {
    padding-top: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.logout-btn {
    width: 100%;
    padding: 12px;
    background: #ff6b35;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    font-family: 'Poppins', sans-serif;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    white-space: nowrap;
    overflow: hidden;
}

.logout-btn:hover {
    background: #ff5520;
    transform: translateY(-2px);
}

.logout-icon {
    font-size: 18px;
    flex-shrink: 0;
}

@media (max-width: 768px) {
    .sidebar {
        position: fixed;
        left: 0;
        top: 0;
        height: 100vh;
        z-index: 1000;
        transform: translateX(-100%);
        width: 250px !important;
    }

    .sidebar.open {
        transform: translateX(0);
    }
}
</style>
