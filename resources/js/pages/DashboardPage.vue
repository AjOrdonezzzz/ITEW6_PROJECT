<template>
    <div class="dashboard-container">
        <sidebar :is-open="sidebarOpen" @toggle="sidebarOpen = !sidebarOpen"></sidebar>

        <div class="main-content">
            <app-header
                title="Welcome, User!"
                :subtitle="currentDate"
                :search-query="searchQuery"
                @update:search-query="searchQuery = $event"
            ></app-header>

            <div class="dashboard-content">
                <h1 class="dashboard-title">Dashboard</h1>

                <div v-if="filteredStats.length" class="stats-grid">
                    <div v-for="stat in filteredStats" :key="stat.title" class="stat-card">
                        <div class="stat-icon">{{ stat.icon }}</div>
                        <div class="stat-info">
                            <h3>{{ stat.title }}</h3>
                            <p class="stat-number">{{ stat.value }}</p>
                        </div>
                    </div>
                </div>

                <div v-else class="empty-state">
                    No dashboard statistics matched your search.
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
                            <tr v-for="student in filteredViolationStudents" :key="student.id">
                                <td>{{ student.name }}</td>
                                <td>{{ student.number }}</td>
                                <td>{{ student.date }}</td>
                                <td><span class="status-badge">{{ student.status }}</span></td>
                                <td>{{ student.violation }}</td>
                            </tr>
                            <tr v-if="!filteredViolationStudents.length">
                                <td colspan="5" class="empty-table-state">No violation records matched your search.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="events-section">
                    <h2>Upcoming Events</h2>
                    <div v-if="filteredUpcomingEvents.length" class="events-list">
                        <article v-for="event in filteredUpcomingEvents" :key="event.id" class="event-item">
                            <strong>{{ event.title }}</strong>
                            <span>{{ event.date }} | {{ event.location }}</span>
                        </article>
                    </div>
                    <div v-else class="events-placeholder">
                        <p>No upcoming events matched your search.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import AppHeader from '../components/AppHeader.vue';
import Sidebar from '../components/Sidebar.vue';

export default {
    name: 'DashboardPage',
    components: {
        AppHeader,
        Sidebar
    },
    data() {
        return {
            sidebarOpen: true,
            currentDate: '',
            searchQuery: '',
            stats: [
                { title: 'Total Students', value: '1,1111', icon: '👥' },
                { title: 'Active Profiles', value: '800', icon: '✅' },
                { title: 'Average Age', value: '20', icon: '📊' }
            ],
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
                    name: 'Aira Dela Cruz',
                    number: '2024-00123',
                    date: 'Mar 29, 2026',
                    status: 'Pending',
                    violation: 'Late submission of report'
                },
                {
                    id: 3,
                    name: 'Marco Reyes',
                    number: '2024-00124',
                    date: 'Mar 28, 2026',
                    status: 'Resolved',
                    violation: 'Dress code violation'
                },
                {
                    id: 4,
                    name: 'Joana Marie Lumogda',
                    number: '2024-00121',
                    date: 'Mar 27, 2026',
                    status: 'Active',
                    violation: 'Library noise complaint'
                }
            ],
            upcomingEvents: [
                { id: 1, title: 'Student Orientation', date: 'Apr 3, 2026', location: 'Main Hall' },
                { id: 2, title: 'Hackathon Kickoff', date: 'Apr 8, 2026', location: 'Lab 2' },
                { id: 3, title: 'Department Assembly', date: 'Apr 20, 2026', location: 'Auditorium' }
            ]
        };
    },
    computed: {
        filteredStats() {
            const query = this.searchQuery.trim().toLowerCase();
            if (!query) return this.stats;

            return this.stats.filter((stat) => {
                return [stat.title, stat.value].some((value) => String(value).toLowerCase().includes(query));
            });
        },
        filteredViolationStudents() {
            const query = this.searchQuery.trim().toLowerCase();
            if (!query) return this.violationStudents;

            return this.violationStudents.filter((student) => {
                return [student.name, student.number, student.date, student.status, student.violation]
                    .some((value) => String(value).toLowerCase().includes(query));
            });
        },
        filteredUpcomingEvents() {
            const query = this.searchQuery.trim().toLowerCase();
            if (!query) return this.upcomingEvents;

            return this.upcomingEvents.filter((event) => {
                return [event.title, event.date, event.location]
                    .some((value) => String(value).toLowerCase().includes(query));
            });
        }
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
}

.dashboard-content {
    padding: 40px;
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

.empty-state {
    margin: -12px 0 28px;
    padding: 18px 20px;
    border-radius: 18px;
    background: rgba(255, 255, 255, 0.9);
    color: #7a4a12;
    font-weight: 600;
    text-align: center;
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

.empty-table-state {
    text-align: center;
    color: #7a4a12;
    font-weight: 600;
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

.events-list {
    display: grid;
    gap: 14px;
}

.event-item {
    padding: 18px 20px;
    border-radius: 18px;
    background: rgba(255, 255, 255, 0.92);
    color: #3a2b1a;
}

.event-item strong {
    display: block;
    margin-bottom: 6px;
}

.events-placeholder {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    opacity: 0.88;
    font-size: 16px;
}

@media (max-width: 768px) {
    .dashboard-content {
        padding: 20px;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .dashboard-title {
        font-size: 24px;
    }
}
</style>
