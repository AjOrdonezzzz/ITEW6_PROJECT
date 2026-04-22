<template>
    <div class="reports-layout">
        <sidebar :is-open="sidebarOpen" @toggle="sidebarOpen = !sidebarOpen"></sidebar>

        <div class="main-content">
            <app-header
                title="Reports Center"
                :subtitle="currentDate"
                :search-query="searchQuery"
                @update:search-query="searchQuery = $event"
            ></app-header>

            <div class="reports-content">
                <div class="page-header">
                    <h1 class="page-title">Reports</h1>
                    <p class="page-subtitle">View generated summaries for attendance, violations, and student status.</p>
                </div>

                <div class="report-grid">
                    <article class="report-card" v-for="report in filteredReports" :key="report.id">
                        <div class="report-icon">{{ report.icon }}</div>
                        <h3>{{ report.title }}</h3>
                        <p>{{ report.description }}</p>
                        <span class="report-meta">Updated {{ report.updated }}</span>
                    </article>
                </div>

                <div v-if="!filteredReports.length" class="empty-state">
                    No reports matched your search.
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import AppHeader from '../components/AppHeader.vue';
import Sidebar from '../components/Sidebar.vue';
import { isAdminUser } from '../../../backend/resources/js/utils/auth';

export default {
    name: 'ReportsPage',
    components: {
        AppHeader,
        Sidebar
    },
    data() {
        return {
            sidebarOpen: true,
            currentDate: '',
            searchQuery: '',
            reports: [
                { id: 1, icon: '📄', title: 'Attendance Summary', description: 'Overview of monthly attendance trends and absences.', updated: '2 hours ago' },
                { id: 2, icon: '📊', title: 'Violation Analytics', description: 'Breakdown of reported cases by category and status.', updated: 'Today' },
                { id: 3, icon: '🧾', title: 'Student Enrollment List', description: 'Latest roster of enrolled students for the semester.', updated: 'Yesterday' },
                { id: 4, icon: '📌', title: 'Pending Case Report', description: 'List of unresolved and under-review student cases.', updated: '3 days ago' }
            ]
        };
    },
    computed: {
        filteredReports() {
            const query = this.searchQuery.trim().toLowerCase();
            if (!query) return this.reports;

            return this.reports.filter((report) => {
                return [report.title, report.description, report.updated]
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
        if (!isAdminUser()) {
            this.$router.replace('/dashboard');
            return;
        }

        this.currentDate = this.getFormattedDate();
    }
};
</script>

<style scoped>
.reports-layout {
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

.reports-content {
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

.report-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
}

.empty-state {
    margin-top: 18px;
    padding: 18px 20px;
    border-radius: 18px;
    background: rgba(255, 255, 255, 0.9);
    color: #7a4a12;
    font-weight: 600;
    text-align: center;
}

.report-card {
    background: white;
    border-radius: 22px;
    padding: 24px;
    box-shadow: 0 10px 28px rgba(0, 0, 0, 0.12);
}

.report-icon {
    font-size: 30px;
    margin-bottom: 16px;
}

.report-card h3 {
    font-size: 22px;
    color: #1a1a1a;
    margin-bottom: 10px;
}

.report-card p {
    color: #666;
    margin-bottom: 12px;
}

.report-meta {
    color: #8a5a20;
    font-size: 14px;
    font-weight: 600;
}

@media (max-width: 768px) {
    .reports-content,
    .top-bar {
        padding-left: 20px;
        padding-right: 20px;
    }

    .page-title {
        font-size: 30px;
    }
}
</style>
