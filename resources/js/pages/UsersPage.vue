<template>
    <div class="users-layout">
        <sidebar :is-open="sidebarOpen" @toggle="sidebarOpen = !sidebarOpen"></sidebar>

        <div class="main-content">
            <app-header
                title="User Management"
                :subtitle="currentDate"
                :search-query="searchQuery"
                @update:search-query="searchQuery = $event"
            ></app-header>

            <div class="users-content">
                <div class="page-header">
                    <div>
                        <h1 class="page-title">Users</h1>
                        <p class="page-subtitle">Admin-only user and role management.</p>
                    </div>
                </div>

                <div class="content-grid">
                    <section class="user-form-card">
                        <h3>Create User</h3>

                        <form @submit.prevent="handleCreateUser">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input v-model="form.fullName" type="text">
                            </div>

                            <div class="form-group">
                                <label>Username</label>
                                <input v-model="form.username" type="text">
                            </div>

                            <div class="form-group">
                                <label>Password</label>
                                <input v-model="form.password" type="password">
                            </div>

                            <div class="form-group">
                                <label>Role</label>
                                <select v-model="form.role">
                                    <option value="user">User</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>

                            <button type="submit" class="primary-btn">Add User</button>

                            <p v-if="message" class="form-message">{{ message }}</p>
                        </form>
                    </section>

                    <section class="user-list-card">
                        <h3>Existing Users</h3>

                        <div v-if="filteredUsers.length" class="user-list">
                            <div v-for="user in filteredUsers" :key="user.id" class="user-row">
                                <div>
                                    <strong>{{ user.fullName }}</strong>
                                    <p>@{{ user.username }}</p>
                                </div>

                                <div class="user-meta">
                                    <span class="badge">{{ user.role }}</span>
                                    <button
                                        class="danger-btn"
                                        @click="deleteUser(user.id)"
                                        :disabled="user.username === 'admin'"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div v-else class="empty-state">
                            No users found.
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
import { getManagedUsers, saveManagedUsers } from '../utils/auth';

export default {
    name: 'UsersPage',
    components: {
        AppHeader,
        Sidebar
    },
    data() {
        return {
            sidebarOpen: true,
            currentDate: '',
            searchQuery: '',
            users: [],
            form: {
                fullName: '',
                username: '',
                password: '',
                role: 'user'
            },
            message: ''
        };
    },
    computed: {
        filteredUsers() {
            const query = this.searchQuery.trim().toLowerCase();

            if (!query) return this.users;

            return this.users.filter((user) =>
                [user.fullName, user.username, user.role]
                    .some((value) => String(value).toLowerCase().includes(query))
            );
        }
    },
    methods: {
        loadUsers() {
            this.users = getManagedUsers();
        },
        getFormattedDate() {
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            return new Date().toLocaleDateString('en-US', options);
        },
        handleCreateUser() {
            this.message = '';

            const fullName = this.form.fullName.trim();
            const username = this.form.username.trim().toLowerCase();
            const password = this.form.password;

            if (!fullName || !username || !password) {
                this.message = 'All fields are required.';
                return;
            }

            const exists = this.users.some(
                (user) => user.username.toLowerCase() === username
            );

            if (exists) {
                this.message = 'Username already exists.';
                return;
            }

            const newUser = {
                id: Date.now(),
                fullName,
                username,
                password,
                role: this.form.role,
                status: 'active'
            };

            this.users = [...this.users, newUser];
            saveManagedUsers(this.users);

            this.form = {
                fullName: '',
                username: '',
                password: '',
                role: 'user'
            };

            this.message = 'User created successfully.';
        },
        deleteUser(userId) {
            this.users = this.users.filter((user) => user.id !== userId);
            saveManagedUsers(this.users);
        }
    },
    mounted() {
        this.currentDate = this.getFormattedDate();
        this.loadUsers();
    }
};
</script>

<style scoped>
.users-layout {
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

.users-content {
    padding: 24px 32px 40px;
}

.page-header {
    margin-bottom: 24px;
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

.content-grid {
    display: grid;
    grid-template-columns: 360px 1fr;
    gap: 20px;
}

.user-form-card,
.user-list-card {
    background: white;
    border-radius: 22px;
    padding: 24px;
    box-shadow: 0 10px 28px rgba(0, 0, 0, 0.12);
}

.form-group {
    margin-bottom: 16px;
}

.form-group label {
    display: block;
    margin-bottom: 6px;
    font-size: 13px;
    color: #555;
    font-weight: 600;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 12px 14px;
    border: 1px solid #ddd;
    border-radius: 10px;
    font-size: 14px;
}

.primary-btn,
.danger-btn {
    border: none;
    border-radius: 10px;
    padding: 10px 14px;
    cursor: pointer;
    font-weight: 600;
}

.primary-btn {
    width: 100%;
    background: #8a5a20;
    color: white;
}

.danger-btn {
    background: #fee2e2;
    color: #991b1b;
}

.form-message {
    margin-top: 12px;
    font-size: 14px;
    color: #8a5a20;
    font-weight: 600;
}

.user-list {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.user-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    padding: 14px 0;
    border-bottom: 1px solid #eee;
}

.user-row p {
    margin-top: 4px;
    color: #666;
    font-size: 13px;
}

.user-meta {
    display: flex;
    align-items: center;
    gap: 10px;
}

.badge {
    padding: 6px 10px;
    border-radius: 999px;
    background: #f4e7d3;
    color: #7a4a12;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
}

.empty-state {
    padding: 18px;
    text-align: center;
    color: #7a4a12;
    font-weight: 600;
}

@media (max-width: 900px) {
    .content-grid {
        grid-template-columns: 1fr;
    }
}
</style>