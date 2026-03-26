<template>
    <div class="settings-layout">
        <sidebar :is-open="sidebarOpen" @toggle="sidebarOpen = !sidebarOpen"></sidebar>

        <div class="main-content">
            <app-header title="System Preferences" :subtitle="currentDate"></app-header>

            <div class="settings-content">
                <div class="page-header">
                    <h1 class="page-title">Settings</h1>
                    <p class="page-subtitle">Manage profile details, notifications, and account preferences.</p>
                </div>

                <div class="settings-grid">
                    <section class="settings-card">
                        <h3>Profile</h3>
                        <div class="setting-row">
                            <span>Display Name</span>
                            <strong>Joana Lumogda</strong>
                        </div>
                        <div class="setting-row">
                            <span>Role</span>
                            <strong>Student Admin</strong>
                        </div>
                    </section>

                    <section class="settings-card">
                        <h3>Preferences</h3>
                        <div class="setting-row">
                            <span>Email Notifications</span>
                            <strong>Enabled</strong>
                        </div>
                        <div class="setting-row">
                            <span>Theme</span>
                            <strong>Warm Brown</strong>
                        </div>
                    </section>

                    <section class="settings-card">
                        <h3>Security</h3>
                        <div class="setting-row">
                            <span>Password Status</span>
                            <strong>Updated recently</strong>
                        </div>
                        <div class="setting-row">
                            <span>Session</span>
                            <strong>Active on this device</strong>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import AppHeader from '../components/AppHeader.vue';
import Sidebar from '../components/Sidebar.vue';

export default {
    name: 'SettingsPage',
    components: {
        AppHeader,
        Sidebar
    },
    data() {
        return {
            sidebarOpen: true,
            currentDate: ''
        };
    },
    methods: {
        getFormattedDate() {
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            return new Date().toLocaleDateString('en-US', options);
        }
    },
    mounted() {
        this.currentDate = this.getFormattedDate();
    }
};
</script>

<style scoped>
.settings-layout {
    display: flex;
    min-height: 100vh;
    background: linear-gradient(135deg, #b27722 0%, #7a3902 45%, #211000 100%);
    font-family: 'Poppins', sans-serif;
}

.main-content {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.settings-content {
    padding: 24px 32px 40px;
}

.page-header {
    margin-bottom: 26px;
    color: white;
}

.page-title {
    font-size: 38px;
    margin-bottom: 8px;
}

.page-subtitle {
    font-size: 15px;
    opacity: 0.85;
}

.settings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
}

.settings-card {
    background: white;
    border-radius: 22px;
    padding: 24px;
    box-shadow: 0 10px 28px rgba(0, 0, 0, 0.12);
}

.settings-card h3 {
    font-size: 22px;
    color: #1a1a1a;
    margin-bottom: 18px;
}

.setting-row {
    display: flex;
    justify-content: space-between;
    gap: 16px;
    padding: 12px 0;
    border-bottom: 1px solid #eee;
    color: #555;
}

.setting-row:last-child {
    border-bottom: none;
}

.setting-row strong {
    color: #8a5a20;
}

@media (max-width: 768px) {
    .settings-content,
    .top-bar {
        padding-left: 20px;
        padding-right: 20px;
    }

    .page-title {
        font-size: 30px;
    }
}
</style>
