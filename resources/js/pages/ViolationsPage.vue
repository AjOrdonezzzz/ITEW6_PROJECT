<template>
    <div class="violations-layout">
        <sidebar :is-open="sidebarOpen" @toggle="sidebarOpen = !sidebarOpen"></sidebar>

        <div class="main-content">
            <app-header
                title="Violations Monitoring"
                :subtitle="currentDate"
                :search-query="searchQuery"
                @update:search-query="searchQuery = $event"
            ></app-header>

            <div class="violations-content">
                <div class="page-header">
                    <h1 class="page-title">Violations</h1>
                    <p class="page-subtitle">Review active student cases and recent incident reports.</p>
                </div>

                <div class="summary-grid">
                    <div class="summary-card" v-for="summary in filteredSummaries" :key="summary.title">
                        <span class="summary-label">{{ summary.title }}</span>
                        <strong class="summary-value">{{ summary.value }}</strong>
                    </div>
                </div>

                <div v-if="!filteredSummaries.length" class="empty-state">
                    No summary cards matched your search.
                </div>

                <div class="table-card">
                    <h2>Recent Reports</h2>

                    <table class="violations-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Student Number</th>
                                <th>Violation</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="record in filteredRecords" :key="record.id">
                                <td>{{ record.name }}</td>
                                <td>{{ record.studentNumber }}</td>
                                <td>{{ record.violation }}</td>
                                <td>{{ record.date }}</td>
                                <td><span class="status-badge">{{ record.status }}</span></td>
                            </tr>
                            <tr v-if="!filteredRecords.length">
                                <td colspan="5" class="empty-table-state">No violation reports matched your search.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import AppHeader from '../components/AppHeader.vue';
import Sidebar from '../components/Sidebar.vue';

export default {
    name: 'ViolationsPage',
    components: {
        AppHeader,
        Sidebar
    },
    data() {
        return {
            sidebarOpen: true,
            currentDate: '',
            searchQuery: '',
            summaries: [
                { title: 'Open Cases', value: 12 },
                { title: 'Resolved This Week', value: 8 },
                { title: 'Pending Review', value: 5 }
            ],
            records: [
                { id: 1, name: 'Nicoli B. Alonso', studentNumber: '2024-00122', violation: 'JABOLING IN  class', date: 'Mar 31, 2026', status: 'Active' },
                { id: 2, name: 'Aira Dela Cruz', studentNumber: '2024-00123', violation: 'Late submission of report', date: 'Mar 29, 2026', status: 'Pending' },
                { id: 3, name: 'Marco Reyes', studentNumber: '2024-00124', violation: 'Dress code violation', date: 'Mar 28, 2026', status: 'Resolved' },
                { id: 4, name: 'Joana Marie Lumogda', studentNumber: '2024-00121', violation: 'Library noise complaint', date: 'Mar 27, 2026', status: 'Active' }
            ]
        };
    },
    computed: {
        filteredSummaries() {
            const query = this.searchQuery.trim().toLowerCase();
            if (!query) return this.summaries;

            return this.summaries.filter((summary) => {
                return [summary.title, summary.value].some((value) => String(value).toLowerCase().includes(query));
            });
        },
        filteredRecords() {
            const query = this.searchQuery.trim().toLowerCase();
            if (!query) return this.records;

            return this.records.filter((record) => {
                return [record.name, record.studentNumber, record.violation, record.date, record.status]
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
.violations-layout {
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

.violations-content {
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

.summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 18px;
    margin-bottom: 24px;
}

.empty-state {
    margin: -4px 0 24px;
    padding: 18px 20px;
    border-radius: 18px;
    background: rgba(255, 255, 255, 0.9);
    color: #7a4a12;
    font-weight: 600;
    text-align: center;
}

.summary-card {
    background: white;
    border-radius: 18px;
    padding: 22px;
    box-shadow: 0 10px 28px rgba(0, 0, 0, 0.12);
}

.summary-label {
    display: block;
    color: #6b7280;
    margin-bottom: 12px;
}

.summary-value {
    font-size: 34px;
    color: #1a1a1a;
}

.table-card {
    background: white;
    border-radius: 24px;
    padding: 28px;
    box-shadow: 0 10px 28px rgba(0, 0, 0, 0.12);
}

.table-card h2 {
    font-size: 24px;
    margin-bottom: 20px;
    color: #1a1a1a;
}

.violations-table {
    width: 100%;
    border-collapse: collapse;
}

.violations-table th {
    padding: 14px 12px;
    text-align: left;
    color: #555;
    border-bottom: 2px solid #eee;
    background: #f7f7f7;
}

.violations-table td {
    padding: 14px 12px;
    border-bottom: 1px solid #eee;
    color: #333;
}

.status-badge {
    display: inline-flex;
    padding: 6px 12px;
    border-radius: 999px;
    background: #fde68a;
    color: #92400e;
    font-size: 12px;
    font-weight: 600;
}

.empty-table-state {
    text-align: center;
    color: #7a4a12;
    font-weight: 600;
}

@media (max-width: 768px) {
    .violations-content,
    .top-bar {
        padding-left: 20px;
        padding-right: 20px;
    }

    .page-title {
        font-size: 30px;
    }
}
</style>
