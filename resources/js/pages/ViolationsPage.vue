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
                    <div class="summary-card" v-for="summary in summaryItems" :key="summary.title">
                        <span class="summary-label">{{ summary.title }}</span>
                        <strong class="summary-value">{{ summary.value }}</strong>
                    </div>
                </div>

                <div v-if="!summaryItems.length" class="empty-state">
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
                            <tr v-for="record in filteredRecords" :key="record.id" class="record-row" @click="openRecord(record)">
                                <td>{{ record.name }}</td>
                                <td>{{ record.studentNumber }}</td>
                                <td>{{ record.violation }}</td>
                                <td>{{ record.date }}</td>
                                <td>
                                    <div class="status-cell">
                                        <span class="status-badge">{{ record.status }}</span>
                                        <button class="view-btn" @click.stop="openRecord(record)">View</button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!filteredRecords.length">
                                <td colspan="5" class="empty-table-state">No violation reports matched your search.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="selectedRecord" class="record-modal-overlay" @click.self="closeRecord">
                    <div class="record-modal">
                        <div class="record-modal-header">
                            <h2>Edit Report</h2>
                            <button class="close-btn" @click="closeRecord">X</button>
                        </div>

                        <div class="form-grid">
                            <label class="field">
                                <span>Name</span>
                                <input v-model="selectedRecord.name" type="text">
                            </label>

                            <label class="field">
                                <span>Student Number</span>
                                <input v-model="selectedRecord.studentNumber" type="text">
                            </label>

                            <label class="field field-full">
                                <span>Violation</span>
                                <input v-model="selectedRecord.violation" type="text">
                            </label>

                            <label class="field">
                                <span>Date</span>
                                <input v-model="selectedRecord.date" type="text">
                            </label>

                            <label class="field">
                                <span>Status</span>
                                <select v-model="selectedRecord.status">
                                    <option>Active</option>
                                    <option>Pending</option>
                                    <option>Resolved</option>
                                </select>
                            </label>
                        </div>

                        <div class="form-actions">
                            <button class="save-record-btn" @click="updateRecord">Update Report</button>
                            <button class="delete-record-btn" @click="deleteRecord">Delete Report</button>
                            <span v-if="editMessage" class="record-message">{{ editMessage }}</span>
                        </div>
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
            selectedRecord: null,
            editMessage: '',
            records: [
                { id: 1, name: 'Nicoli B. Alonso', studentNumber: '2024-00122', violation: 'JABOLING IN  class', date: 'Mar 31, 2026', status: 'Active' },
                { id: 2, name: 'Aira Dela Cruz', studentNumber: '2024-00123', violation: 'Late submission of report', date: 'Mar 29, 2026', status: 'Pending' },
                { id: 3, name: 'Marco Reyes', studentNumber: '2024-00124', violation: 'Dress code violation', date: 'Mar 28, 2026', status: 'Resolved' },
                { id: 4, name: 'Joana Marie Lumogda', studentNumber: '2024-00121', violation: 'Library noise complaint', date: 'Mar 27, 2026', status: 'Active' }
            ]
        };
    },
    computed: {
        filteredRecords() {
            const query = this.searchQuery.trim().toLowerCase();
            if (!query) return this.records;

            return this.records.filter((record) => {
                return [record.name, record.studentNumber, record.violation, record.date, record.status]
                    .some((value) => String(value).toLowerCase().includes(query));
            });
        },
        summaryItems() {
            const baseRecords = this.searchQuery.trim() ? this.filteredRecords : this.records;
            const openCases = baseRecords.filter((record) => record.status === 'Active').length;
            const pendingReview = baseRecords.filter((record) => record.status === 'Pending').length;
            const resolved = baseRecords.filter((record) => record.status === 'Resolved').length;

            return [
                { title: 'Open Cases', value: openCases },
                { title: 'Resolved This Week', value: resolved },
                { title: 'Pending Review', value: pendingReview }
            ];
        }
    },
    methods: {
        getFormattedDate() {
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            return new Date().toLocaleDateString('en-US', options);
        },
        saveRecords() {
            localStorage.setItem('violationRecords', JSON.stringify(this.records));
        },
        loadRecords() {
            const savedRecords = JSON.parse(localStorage.getItem('violationRecords') || 'null');
            if (savedRecords && Array.isArray(savedRecords) && savedRecords.length) {
                this.records = savedRecords;
            }
        },
        openRecord(record) {
            this.selectedRecord = { ...record };
            this.editMessage = '';
        },
        closeRecord() {
            this.selectedRecord = null;
            this.editMessage = '';
        },
        updateRecord() {
            if (!this.selectedRecord.name.trim() || !this.selectedRecord.studentNumber.trim() || !this.selectedRecord.violation.trim()) {
                this.editMessage = 'Please complete all report fields.';
                return;
            }

            const index = this.records.findIndex((record) => record.id === this.selectedRecord.id);
            if (index === -1) return;

            this.records[index] = {
                ...this.selectedRecord,
                name: this.selectedRecord.name.trim(),
                studentNumber: this.selectedRecord.studentNumber.trim(),
                violation: this.selectedRecord.violation.trim()
            };
            this.saveRecords();

            this.editMessage = 'Report updated successfully.';
            setTimeout(() => {
                this.closeRecord();
            }, 900);
        },
        deleteRecord() {
            if (!this.selectedRecord) return;

            this.records = this.records.filter((record) => record.id !== this.selectedRecord.id);
            this.saveRecords();
            this.editMessage = 'Report deleted.';

            setTimeout(() => {
                this.closeRecord();
            }, 700);
        }
    },
    mounted() {
        this.currentDate = this.getFormattedDate();
        this.loadRecords();
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

.record-row {
    cursor: pointer;
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

.status-cell {
    display: flex;
    align-items: center;
    gap: 10px;
    justify-content: space-between;
    min-width: 170px;
}

.view-btn {
    padding: 6px 12px;
    border: none;
    border-radius: 999px;
    background: rgba(138, 90, 32, 0.12);
    color: #8a5a20;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
}

.view-btn:hover {
    background: rgba(138, 90, 32, 0.2);
}

.record-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.45);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px;
    z-index: 60;
}

.record-modal {
    width: min(760px, 100%);
    background: white;
    border-radius: 24px;
    padding: 24px;
    box-shadow: 0 18px 40px rgba(0, 0, 0, 0.22);
}

.record-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 18px;
}

.record-modal-header h2 {
    color: #1a1a1a;
}

.close-btn {
    width: 40px;
    height: 40px;
    border: none;
    border-radius: 50%;
    background: #f3f4f6;
    font-size: 18px;
    cursor: pointer;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 18px;
}

.field {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.field span {
    color: #555;
    font-size: 14px;
    font-weight: 600;
}

.field input,
.field select {
    width: 100%;
    padding: 12px 14px;
    border: 1px solid #ddd;
    border-radius: 12px;
    font: inherit;
}

.field-full {
    grid-column: 1 / -1;
}

.form-actions {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-top: 20px;
}

.save-record-btn {
    padding: 12px 18px;
    border: none;
    border-radius: 999px;
    background: linear-gradient(135deg, #a89080 0%, #8a7a6a 100%);
    color: white;
    font: inherit;
    font-weight: 600;
    cursor: pointer;
}

.delete-record-btn {
    padding: 12px 18px;
    border: none;
    border-radius: 999px;
    background: #ef4444;
    color: white;
    font: inherit;
    font-weight: 600;
    cursor: pointer;
}

.record-message {
    color: #0f766e;
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
