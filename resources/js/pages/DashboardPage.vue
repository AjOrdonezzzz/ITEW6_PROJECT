<template>
    <div class="dashboard-container">
        <sidebar :is-open="sidebarOpen" @toggle="sidebarOpen = !sidebarOpen"></sidebar>
        
        <div class="main-content">
            <header class="top-bar">
                <div class="welcome-section">
                    <h2>Welcome, User!</h2>
                    <p>{{ currentDate }}</p>
                </div>

                <div class="top-bar-right">
                    <div class="search-container">
                        <span class="search-icon">🔍</span>
                        <input type="text" placeholder="Search" class="search-input">
                    </div>
                    <button class="notif-btn">🔔</button>
                    <router-link to="/profile" class="profile-link">
                        <div class="user-profile">
                            <img v-if="profile.avatar" :src="profile.avatar" alt="Profile" class="profile-img">
                            <div v-else class="profile-fallback">{{ profileInitials }}</div>
                        </div>
                        <span class="profile-name">{{ shortName }}</span>
                    </router-link>
                </div>
            </header>

            <div class="dashboard-content">
                <h1 class="dashboard-title">Dashboard</h1>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">👥</div>
                        <div class="stat-info">
                            <h3>Total Students</h3>
                            <p class="stat-number">1,1111</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">✅</div>
                        <div class="stat-info">
                            <h3>Active Profiles</h3>
                            <p class="stat-number">800</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">📊</div>
                        <div class="stat-info">
                            <h3>Average Age</h3>
                            <p class="stat-number">20</p>
                        </div>
                    </div>
                </div>

                <div class="violations-section">
                    <h2>Students With Violations</h2>
                    <table class="violations-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Student Number</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Violation Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="student in violationStudents" :key="student.id">
                                <td>{{ student.name }}</td>
                                <td>{{ student.number }}</td>
                                <td>{{ student.date }}</td>
                                <td><span class="status-badge">{{ student.status }}</span></td>
                                <td>{{ student.violation }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="events-section">
                    <h2>Upcoming Events</h2>
                    <div class="events-placeholder">
                        <p>No upcoming events</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Sidebar from '../components/Sidebar.vue';

export default {
    name: 'DashboardPage',
    components: {
        Sidebar
    },
    data() {
        return {
            sidebarOpen: true,
            currentDate: '',
            profile: {
                fullName: 'Joana Lumogda',
                avatar: ''
            },
            violationStudents: [
                {
                    id: 1,
                    name: 'Nicoli B. Alonso',
                    number: '12312312323',
                    date: 'Mar 31, 2026',
                    status: 'Active',
                    violation: 'Nagsalita sa loob ng room'
                },
                {
                    id: 2,
                    name: 'Nicoli B. Alonso',
                    number: '12312312323',
                    date: 'Mar 31, 2026',
                    status: 'Active',
                    violation: 'Nagsalita sa loob ng room'
                },
                {
                    id: 3,
                    name: 'Nicoli B. Alonso',
                    number: '12312312323',
                    date: 'Mar 31, 2026',
                    status: 'Active',
                    violation: 'Nagsalita sa loob ng room'
                },
                {
                    id: 4,
                    name: 'Nicoli B. Alonso',
                    number: '12312312323',
                    date: 'Mar 31, 2026',
                    status: 'Active',
                    violation: 'Nagsalita sa loob ng room'
                },
                {
                    id: 5,
                    name: 'Nicoli B. Alonso',
                    number: '12312312323',
                    date: 'Mar 31, 2026',
                    status: 'Active',
                    violation: 'Nagsalita sa loob ng room'
                },
                {
                    id: 6,
                    name: 'Nicoli B. Alonso',
                    number: '12312312323',
                    date: 'Mar 31, 2026',
                    status: 'Active',
                    violation: 'Nagsalita sa loob ng room'
                },
            ]
        };
    },
    methods: {
        getFormattedDate() {
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            return new Date().toLocaleDateString('en-US', options);
        },
        loadProfile() {
            const savedProfile = JSON.parse(localStorage.getItem('profileData') || 'null');
            if (savedProfile) {
                this.profile = {
                    fullName: savedProfile.fullName || 'Joana Lumogda',
                    avatar: savedProfile.avatar || ''
                };
            }
        }
    },
    computed: {
        shortName() {
            return this.profile.fullName || 'Profile';
        },
        profileInitials() {
            return this.profile.fullName
                .split(' ')
                .filter(Boolean)
                .slice(0, 2)
                .map((part) => part[0]?.toUpperCase() || '')
                .join('') || 'JP';
        }
    },
    mounted() {
        this.currentDate = this.getFormattedDate();
        this.loadProfile();
    }
};
</script>

<style scoped>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.dashboard-container {
    display: flex;
    min-height: 100vh;
    background: linear-gradient(135deg, #b27722 0%, #7a3902 45%, #211000 100%);
    font-family: 'Poppins', sans-serif;
}

.main-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
}

.top-bar {
    background:transparent;
    padding: 20px 30px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    backdrop-filter: blur(10px);
}

.top-bar-right {
    display: flex;
    align-items: center;
    gap: 15px;
}

.profile-link {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #1a1a1a;
    text-decoration: none;
}

.notif-btn {
    background: rgba(255, 255, 255, 0.15);
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    transition: background 0.3s ease;
}

.notif-btn:hover {
    background: rgba(255, 255, 255, 0.25);
}

.menu-btn {
    display: none;
    flex-direction: column;
    gap: 5px;
    background: none;
    border: none;
    cursor: pointer;
}

.menu-btn span {
    width: 25px;
    height: 3px;
    background: white;
    border-radius: 2px;
    transition: all 0.3s ease;
}

.search-container {
    position: relative;
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
    max-width: 300px;
}

.search-icon {
    position: absolute;
    left: 12px;
    font-size: 14px;
    pointer-events: none;
    opacity: 0.5;
}
.search-input {
    padding: 10px 15px 10px 36px; /* left padding makes room for the icon */
    border: none;
    border-radius: 25px;
    font-size: 14px;
    font-family: 'Poppins', sans-serif;
    width: 220px;
    background: rgba(255, 255, 255, 0.9);
}


.search-btn {
    background: #ff6b35;
    color: white;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.search-btn:hover {
    background: #ff5520;
    transform: scale(1.05);
}

.user-profile {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    overflow: hidden;
    cursor: pointer;
    background: rgba(255, 255, 255, 0.18);
    display: flex;
    align-items: center;
    justify-content: center;
}

.profile-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-fallback {
    color: white;
    font-size: 14px;
    font-weight: 700;
}

.profile-name {
    color: #1a1a1a;
    font-size: 15px;
    font-weight: 600;
}

.dashboard-content {
    flex: 1;
    padding: 40px;
    overflow-y: auto;
}

.welcome-section {
    color: white;
    margin-bottom: 30px;
}

.welcome-section h2 {
    font-size: 20px;
    margin-bottom: 5px;
}

.welcome-section p {
    font-size: 14px;
    opacity: 0.8;
}

.dashboard-title {
    font-size: 32px;
    color: white;
    margin-bottom: 30px;
    font-weight: 700;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.stat-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    display: flex;
    align-items: center;
    gap: 20px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

.stat-icon {
    font-size: 40px;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 107, 53, 0.1);
    border-radius: 12px;
}

.stat-info h3 {
    font-size: 14px;
    color: #666;
    font-weight: 500;
    margin-bottom: 8px;
}

.stat-number {
    font-size: 28px;
    color: #1a1a1a;
    font-weight: 700;
}

.violations-section {
    background: white;
    border-radius: 15px;
    padding: 30px;
    margin-bottom: 40px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.violations-section h2 {
    font-size: 20px;
    color: #1a1a1a;
    margin-bottom: 25px;
    font-weight: 700;
}

.violations-table {
    width: 100%;
    border-collapse: collapse;
}

.violations-table thead {
    background: #f5f5f5;
}

.violations-table th {
    padding: 15px;
    text-align: left;
    font-size: 13px;
    font-weight: 600;
    color: #555;
    border-bottom: 2px solid #eee;
}

.violations-table td {
    padding: 15px;
    border-bottom: 1px solid #eee;
    font-size: 14px;
    color: #333;
}

.violations-table tbody tr:hover {
    background: #f9f9f9;
}

.status-badge {
    background: #d1fae5;
    color: #065f46;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.events-section {
    background: linear-gradient(135deg, #ff6b35 0%, #ff5520 100%);
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    min-height: 200px;
    display: flex;
    flex-direction: column;
}

.events-section h2 {
    font-size: 20px;
    color: white;
    margin-bottom: 20px;
    font-weight: 700;
}

.events-placeholder {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    opacity: 0.8;
    font-size: 16px;
}

@media (max-width: 768px) {
    .dashboard-content {
        padding: 20px;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .menu-btn {
        display: flex;
    }

    .dashboard-title {
        font-size: 24px;
    }
}
</style>
