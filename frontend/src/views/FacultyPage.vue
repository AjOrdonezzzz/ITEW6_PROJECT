<template>
    <div class="page-layout">
        <Sidebar :is-open="sidebarOpen" @toggle="sidebarOpen = !sidebarOpen" />

        <div class="main-content">
            <AppHeader
                title="Faculty Management"
                :subtitle="currentDate"
                :search-query="searchQuery"
                @update:search-query="searchQuery = $event"
            />

            <div class="page-content">
                <section class="hero-card">
                    <div>
                        <p class="eyebrow">People Directory</p>
                        <h1>Manage faculty profiles, departments, and teaching assignments.</h1>
                        <p class="hero-copy">This keeps adviser assignment and faculty records aligned with sections and courses.</p>
                    </div>
                    <button class="primary-btn" @click="resetForm">
                        {{ isEditing ? 'Create Another Faculty Record' : 'Add Faculty' }}
                    </button>
                </section>

                <section class="stats-grid">
                    <article class="stat-card">
                        <span>Total Faculty</span>
                        <strong>{{ filteredFaculty.length }}</strong>
                    </article>
                    <article class="stat-card">
                        <span>Active</span>
                        <strong>{{ statusCount.Active || 0 }}</strong>
                    </article>
                    <article class="stat-card">
                        <span>Inactive</span>
                        <strong>{{ statusCount.Inactive || 0 }}</strong>
                    </article>
                    <article class="stat-card">
                        <span>Sections Handled</span>
                        <strong>{{ totalSections }}</strong>
                    </article>
                </section>

                <section class="workspace-grid">
                    <article class="form-panel">
                        <div class="panel-heading">
                            <div>
                                <p class="panel-label">{{ isEditing ? 'Edit faculty' : 'New faculty' }}</p>
                                <h2>{{ isEditing ? `${form.first_name} ${form.last_name}`.trim() : 'Create a faculty record' }}</h2>
                            </div>
                            <button v-if="isEditing || hasFormValues" class="ghost-btn" @click="resetForm">Clear</button>
                        </div>

                        <form class="record-form" @submit.prevent="submitFaculty">
                            <label class="field">
                                <span>First Name</span>
                                <input v-model.trim="form.first_name" type="text" maxlength="50" placeholder="Ana">
                            </label>
                            <label class="field">
                                <span>Last Name</span>
                                <input v-model.trim="form.last_name" type="text" maxlength="50" placeholder="Santos">
                            </label>
                            <label class="field">
                                <span>Email</span>
                                <input v-model.trim="form.email" type="email" maxlength="100" placeholder="ana@example.com">
                            </label>
                            <label class="field">
                                <span>Department</span>
                                <select v-model="form.department_id">
                                    <option value="">Select department</option>
                                    <option v-for="department in departments" :key="department.department_id" :value="department.department_id">
                                        {{ department.department_name }}
                                    </option>
                                </select>
                            </label>
                            <label class="field">
                                <span>Position</span>
                                <input v-model.trim="form.position" type="text" maxlength="50" placeholder="Instructor">
                            </label>
                            <label class="field">
                                <span>Expertise</span>
                                <input v-model.trim="form.expertise" type="text" maxlength="50" placeholder="Web Development">
                            </label>
                            <label class="field">
                                <span>Status</span>
                                <select v-model="form.status">
                                    <option v-for="option in statusOptions" :key="option" :value="option">{{ option }}</option>
                                </select>
                            </label>

                            <div class="form-actions">
                                <button class="primary-btn" type="submit" :disabled="saving">
                                    {{ saving ? 'Saving...' : isEditing ? 'Update Faculty' : 'Save Faculty' }}
                                </button>
                                <span v-if="feedbackMessage" class="feedback success">{{ feedbackMessage }}</span>
                                <span v-if="errorMessage" class="feedback error">{{ errorMessage }}</span>
                            </div>
                        </form>
                    </article>

                    <article class="table-panel">
                        <div class="panel-heading">
                            <div>
                                <p class="panel-label">Faculty List</p>
                                <h2>Registered faculty members</h2>
                            </div>
                            <span class="result-pill">{{ filteredFaculty.length }} shown</span>
                        </div>

                        <div v-if="loading" class="state-card">Loading faculty records...</div>
                        <div v-else-if="!filteredFaculty.length" class="state-card">No faculty matched your search.</div>
                        <div v-else class="table-wrap">
                            <table class="record-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Department</th>
                                        <th>Position</th>
                                        <th>Expertise</th>
                                        <th>Status</th>
                                        <th>Sections</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="member in filteredFaculty" :key="member.faculty_id">
                                        <td>
                                            <div class="record-cell">
                                                <strong>{{ member.first_name }} {{ member.last_name }}</strong>
                                                <span>{{ member.email }}</span>
                                            </div>
                                        </td>
                                        <td>{{ member.department?.department_name || 'N/A' }}</td>
                                        <td>{{ member.position }}</td>
                                        <td>{{ member.expertise }}</td>
                                        <td><span class="status-pill" :class="member.status.toLowerCase()">{{ member.status }}</span></td>
                                        <td>{{ member.sections_count ?? 0 }}</td>
                                        <td>
                                            <div class="action-group">
                                                <button class="table-btn" @click="openEditForm(member)">Edit</button>
                                                <button class="table-btn danger" :disabled="deletingId === member.faculty_id" @click="deleteFaculty(member)">
                                                    {{ deletingId === member.faculty_id ? 'Deleting...' : 'Delete' }}
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </article>
                </section>
            </div>
        </div>
    </div>
</template>

<script>
import AppHeader from '../components/AppHeader.vue';
import Sidebar from '../components/Sidebar.vue';
import api from '../services/api.js';

const defaultForm = () => ({
    first_name: '',
    last_name: '',
    email: '',
    department_id: '',
    position: '',
    expertise: '',
    status: 'Active'
});

export default {
    name: 'FacultyPage',
    components: { AppHeader, Sidebar },
    data() {
        return {
            sidebarOpen: true,
            currentDate: '',
            searchQuery: '',
            loading: false,
            saving: false,
            deletingId: null,
            editingId: null,
            feedbackMessage: '',
            errorMessage: '',
            faculty: [],
            departments: [],
            form: defaultForm(),
            statusOptions: ['Active', 'Inactive']
        };
    },
    computed: {
        isEditing() {
            return this.editingId !== null;
        },
        hasFormValues() {
            return Object.values(this.form).some((value) => value !== '' && value !== 'Active');
        },
        filteredFaculty() {
            const query = this.searchQuery.trim().toLowerCase();

            if (!query) return this.faculty;

            return this.faculty.filter((member) => [
                member.first_name,
                member.last_name,
                member.email,
                member.department?.department_name,
                member.position,
                member.expertise,
                member.status
            ].some((value) => String(value || '').toLowerCase().includes(query)));
        },
        statusCount() {
            return this.filteredFaculty.reduce((counts, member) => {
                const key = member.status || 'Unknown';
                counts[key] = (counts[key] || 0) + 1;
                return counts;
            }, {});
        },
        totalSections() {
            return this.filteredFaculty.reduce((count, member) => count + Number(member.sections_count || 0), 0);
        }
    },
    methods: {
        getFormattedDate() {
            return new Date().toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },
        normalizePayload() {
            return {
                first_name: this.form.first_name,
                last_name: this.form.last_name,
                email: this.form.email,
                department_id: Number(this.form.department_id),
                position: this.form.position,
                expertise: this.form.expertise,
                status: this.form.status
            };
        },
        resetForm(clearMessages = true) {
            this.form = defaultForm();
            this.editingId = null;
            if (clearMessages) {
                this.feedbackMessage = '';
                this.errorMessage = '';
            }
        },
        openEditForm(member) {
            this.editingId = member.faculty_id;
            this.feedbackMessage = '';
            this.errorMessage = '';
            this.form = {
                first_name: member.first_name || '',
                last_name: member.last_name || '',
                email: member.email || '',
                department_id: member.department_id ?? '',
                position: member.position || '',
                expertise: member.expertise || '',
                status: member.status || 'Active'
            };
        },
        async fetchFaculty() {
            this.loading = true;
            try {
                const [{ data: faculty }, { data: departments }] = await Promise.all([
                    api.get('/faculty'),
                    api.get('/departments')
                ]);

                this.faculty = Array.isArray(faculty) ? faculty : [];
                this.departments = Array.isArray(departments) ? departments : [];
            } catch (error) {
                this.errorMessage = error?.response?.data?.message || 'Unable to load faculty records.';
            } finally {
                this.loading = false;
            }
        },
        async submitFaculty() {
            this.saving = true;
            this.feedbackMessage = '';
            this.errorMessage = '';

            try {
                const payload = this.normalizePayload();
                const wasEditing = this.isEditing;

                if (wasEditing) {
                    await api.put(`/faculty/${this.editingId}`, payload);
                } else {
                    await api.post('/faculty', payload);
                }

                await this.fetchFaculty();
                this.resetForm(false);
                this.feedbackMessage = wasEditing ? 'Faculty record updated successfully.' : 'Faculty record created successfully.';
            } catch (error) {
                const validationErrors = error?.response?.data?.errors;
                this.errorMessage = validationErrors
                    ? Object.values(validationErrors).flat().join(' ')
                    : (error?.response?.data?.message || 'Unable to save faculty record.');
            } finally {
                this.saving = false;
            }
        },
        async deleteFaculty(member) {
            if (!window.confirm(`Delete ${member.first_name} ${member.last_name}?`)) {
                return;
            }

            this.deletingId = member.faculty_id;
            this.feedbackMessage = '';
            this.errorMessage = '';

            try {
                await api.delete(`/faculty/${member.faculty_id}`);
                if (this.editingId === member.faculty_id) {
                    this.resetForm(false);
                }
                await this.fetchFaculty();
                this.feedbackMessage = 'Faculty record deleted successfully.';
            } catch (error) {
                this.errorMessage = error?.response?.data?.message || 'Unable to delete faculty record.';
            } finally {
                this.deletingId = null;
            }
        }
    },
    mounted() {
        this.currentDate = this.getFormattedDate();
        this.fetchFaculty();
    }
};
</script>

<style scoped>
.page-layout {
    display: flex;
    min-height: 100vh;
    background:
        radial-gradient(circle at top left, rgba(255, 232, 201, 0.38), transparent 28%),
        linear-gradient(145deg, #fff3e1 0%, #d4a970 42%, #7b4519 100%);
    font-family: 'Poppins', sans-serif;
}
.main-content { flex: 1; display: flex; flex-direction: column; }
.page-content { padding: 24px 32px 40px; display: flex; flex-direction: column; gap: 20px; }
.hero-card, .stat-card, .form-panel, .table-panel { border-radius: 24px; }
.hero-card {
    display: flex; align-items: center; justify-content: space-between; gap: 20px; padding: 28px;
    background: linear-gradient(135deg, rgba(104, 56, 15, 0.92), rgba(154, 95, 39, 0.9)); color: white;
    box-shadow: 0 18px 40px rgba(87, 43, 8, 0.2);
}
.eyebrow, .panel-label { margin: 0 0 10px; text-transform: uppercase; letter-spacing: .12em; font-size: 12px; font-weight: 700; opacity: .72; }
.hero-card h1, .panel-heading h2 { margin: 0; }
.hero-card h1 { max-width: 560px; font-size: 34px; line-height: 1.1; }
.hero-copy { margin: 12px 0 0; max-width: 560px; color: rgba(255, 245, 234, 0.82); }
.stats-grid, .workspace-grid { display: grid; gap: 18px; }
.stats-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
.workspace-grid { grid-template-columns: minmax(320px, 390px) minmax(0, 1fr); align-items: start; }
.stat-card, .form-panel, .table-panel { background: rgba(255, 252, 248, 0.94); box-shadow: 0 16px 34px rgba(87, 43, 8, 0.12); }
.stat-card { padding: 20px 22px; }
.stat-card span { display: block; color: #855326; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; }
.stat-card strong { display: block; margin-top: 10px; color: #4c2c12; font-size: 34px; }
.form-panel, .table-panel { padding: 24px; }
.panel-heading { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 18px; }
.panel-heading h2 { color: #4b2e15; font-size: 24px; }
.record-form { display: grid; grid-template-columns: 1fr; gap: 16px; }
.field { display: flex; flex-direction: column; gap: 6px; }
.field span { color: #7a5531; font-size: 13px; font-weight: 700; }
.field input, .field select {
    border: 1px solid rgba(123, 69, 25, .14); border-radius: 14px; background: #fff; color: #4b2d14;
    font: inherit; padding: 12px 14px; transition: border-color .2s ease, box-shadow .2s ease;
}
.field input:focus, .field select:focus {
    outline: none; border-color: rgba(174, 114, 53, .75); box-shadow: 0 0 0 4px rgba(212, 169, 112, .22);
}
.form-actions { display: flex; align-items: center; flex-wrap: wrap; gap: 12px; margin-top: 8px; }
.primary-btn, .ghost-btn, .table-btn {
    border: none; border-radius: 999px; font: inherit; font-weight: 700; cursor: pointer;
    transition: transform .2s ease, opacity .2s ease;
}
.primary-btn { padding: 12px 18px; background: linear-gradient(135deg, #f1c48f, #b87432); color: #3b210d; box-shadow: 0 12px 24px rgba(116, 69, 28, .2); }
.ghost-btn, .table-btn { padding: 10px 14px; background: rgba(123, 69, 25, .09); color: #8b592b; }
.primary-btn:hover, .ghost-btn:hover, .table-btn:hover { transform: translateY(-1px); }
.primary-btn:disabled, .table-btn:disabled { cursor: not-allowed; opacity: .6; transform: none; }
.feedback { font-size: 13px; font-weight: 600; }
.feedback.success { color: #0f766e; }
.feedback.error { color: #b91c1c; }
.result-pill { display: inline-flex; align-items: center; border-radius: 999px; background: rgba(123, 69, 25, .08); color: #9b622f; padding: 8px 12px; font-size: 12px; font-weight: 700; }
.state-card { padding: 36px 24px; border-radius: 20px; background: rgba(255, 244, 232, .82); text-align: center; color: #8b592b; font-weight: 600; }
.table-wrap { overflow-x: auto; }
.record-table { width: 100%; border-collapse: collapse; }
.record-table th, .record-table td { padding: 16px 12px; border-bottom: 1px solid rgba(123, 69, 25, .08); text-align: left; }
.record-table th { color: #97653a; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; }
.record-table td { color: #4b2d14; font-size: 14px; }
.record-cell { display: flex; flex-direction: column; gap: 4px; }
.record-cell strong { font-size: 15px; }
.record-cell span { color: #8f6b4a; font-size: 12px; }
.status-pill { display: inline-flex; align-items: center; justify-content: center; min-width: 90px; padding: 8px 12px; border-radius: 999px; font-size: 12px; font-weight: 700; }
.status-pill.active { background: #dcfce7; color: #166534; }
.status-pill.inactive { background: #fee2e2; color: #b91c1c; }
.action-group { display: flex; gap: 8px; }
.table-btn.danger { background: rgba(220, 38, 38, .12); color: #b91c1c; }
@media (max-width: 1100px) { .stats-grid, .workspace-grid { grid-template-columns: 1fr; } }
@media (max-width: 768px) {
    .page-content { padding: 16px 20px 32px; }
    .hero-card { flex-direction: column; align-items: flex-start; }
    .hero-card h1 { font-size: 28px; }
    .form-actions, .action-group { flex-direction: column; align-items: stretch; }
    .primary-btn, .ghost-btn, .table-btn { width: 100%; text-align: center; }
}
</style>
