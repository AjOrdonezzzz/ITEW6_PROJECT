<template>
    <div class="page-layout">
        <Sidebar :is-open="sidebarOpen" @toggle="sidebarOpen = !sidebarOpen" />

        <div class="main-content">
            <AppHeader
                title="Section Management"
                :subtitle="currentDate"
                :search-query="searchQuery"
                @update:search-query="searchQuery = $event"
            />

            <div class="page-content">
                <section class="hero-card">
                    <div>
                        <p class="eyebrow">Enrollment Groups</p>
                        <h1>Manage sections, advisers, rooms, and school years.</h1>
                        <p class="hero-copy">This gives you a cleaner place to assign each section to a faculty adviser and room.</p>
                    </div>
                    <button v-if="canManage" class="primary-btn" @click="resetForm">
                        {{ isEditing ? 'Create Another Section' : 'Add Section' }}
                    </button>
                    <span v-else class="reference-pill">Reference Only</span>
                </section>

                <section class="stats-grid">
                    <article class="stat-card">
                        <span>Total Sections</span>
                        <strong>{{ filteredSections.length }}</strong>
                    </article>
                    <article class="stat-card">
                        <span>With Advisers</span>
                        <strong>{{ advisedSections }}</strong>
                    </article>
                    <article class="stat-card">
                        <span>With Rooms</span>
                        <strong>{{ assignedRooms }}</strong>
                    </article>
                    <article class="stat-card">
                        <span>Total Students</span>
                        <strong>{{ totalStudents }}</strong>
                    </article>
                </section>

                <section class="workspace-grid">
                    <article v-if="canManage" class="form-panel">
                        <div class="panel-heading">
                            <div>
                                <p class="panel-label">{{ isEditing ? 'Edit section' : 'New section' }}</p>
                                <h2>{{ isEditing ? form.section_name : 'Create a section record' }}</h2>
                            </div>
                            <button v-if="isEditing || hasFormValues" class="ghost-btn" @click="resetForm">Clear</button>
                        </div>

                        <form class="record-form" @submit.prevent="submitSection">
                            <label class="field">
                                <span>Section Name</span>
                                <input v-model.trim="form.section_name" type="text" maxlength="50" placeholder="BSIT-3A">
                            </label>
                            <label class="field">
                                <span>Year Level</span>
                                <select v-model="form.year_level">
                                    <option value="">Select year level</option>
                                    <option v-for="year in yearLevels" :key="year" :value="year">{{ year }}</option>
                                </select>
                            </label>
                            <label class="field">
                                <span>School Year</span>
                                <input v-model.trim="form.school_year" type="text" maxlength="20" placeholder="2025-2026">
                            </label>
                            <label class="field">
                                <span>Adviser</span>
                                <select v-model="form.adviser_id">
                                    <option value="">Unassigned</option>
                                    <option v-for="member in faculty" :key="member.faculty_id" :value="member.faculty_id">
                                        {{ member.first_name }} {{ member.last_name }}
                                    </option>
                                </select>
                            </label>
                            <label class="field">
                                <span>Room</span>
                                <select v-model="form.room_id">
                                    <option value="">Unassigned</option>
                                    <option v-for="room in rooms" :key="room.room_id" :value="room.room_id">
                                        {{ room.room_name }} - {{ room.building }}
                                    </option>
                                </select>
                            </label>

                            <div class="form-actions">
                                <button class="primary-btn" type="submit" :disabled="saving">
                                    {{ saving ? 'Saving...' : isEditing ? 'Update Section' : 'Save Section' }}
                                </button>
                                <span v-if="feedbackMessage" class="feedback success">{{ feedbackMessage }}</span>
                                <span v-if="errorMessage" class="feedback error">{{ errorMessage }}</span>
                            </div>
                        </form>
                    </article>

                    <article class="table-panel">
                        <div class="panel-heading">
                            <div>
                                <p class="panel-label">Sections</p>
                                <h2>Registered section groups</h2>
                            </div>
                            <span class="result-pill">{{ filteredSections.length }} shown</span>
                        </div>

                        <div v-if="loading" class="state-card">Loading sections...</div>
                        <div v-else-if="!filteredSections.length" class="state-card">No sections matched your search.</div>
                        <div v-else class="table-wrap">
                            <table class="record-table">
                                <thead>
                                    <tr>
                                        <th>Section</th>
                                        <th>Year</th>
                                        <th>School Year</th>
                                        <th>Adviser</th>
                                        <th>Room</th>
                                        <th>Students</th>
                                        <th v-if="canManage">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="section in filteredSections" :key="section.section_id">
                                        <td><strong>{{ section.section_name }}</strong></td>
                                        <td>{{ section.year_level }}</td>
                                        <td>{{ section.school_year }}</td>
                                        <td>{{ formatAdviser(section.adviser) }}</td>
                                        <td>{{ section.room ? `${section.room.room_name} - ${section.room.building}` : 'Unassigned' }}</td>
                                        <td>{{ section.students_count ?? 0 }}</td>
                                        <td v-if="canManage">
                                            <div class="action-group">
                                                <button class="table-btn" @click="openEditForm(section)">Edit</button>
                                                <button class="table-btn danger" :disabled="deletingId === section.section_id" @click="deleteSection(section)">
                                                    {{ deletingId === section.section_id ? 'Deleting...' : 'Delete' }}
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
import { isAdminUser } from '../utils/auth';

const defaultForm = () => ({
    section_name: '',
    year_level: '',
    school_year: '',
    adviser_id: '',
    room_id: ''
});

export default {
    name: 'SectionsPage',
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
            sections: [],
            faculty: [],
            rooms: [],
            form: defaultForm(),
            yearLevels: [1, 2, 3, 4]
        };
    },
    computed: {
        isEditing() {
            return this.editingId !== null;
        },
        canManage() {
            return isAdminUser();
        },
        hasFormValues() {
            return Object.values(this.form).some((value) => value !== '');
        },
        filteredSections() {
            const query = this.searchQuery.trim().toLowerCase();

            if (!query) return this.sections;

            return this.sections.filter((section) => [
                section.section_name,
                section.school_year,
                String(section.year_level),
                this.formatAdviser(section.adviser),
                section.room?.room_name,
                section.room?.building
            ].some((value) => String(value || '').toLowerCase().includes(query)));
        },
        advisedSections() {
            return this.filteredSections.filter((section) => section.adviser).length;
        },
        assignedRooms() {
            return this.filteredSections.filter((section) => section.room).length;
        },
        totalStudents() {
            return this.filteredSections.reduce((count, section) => count + Number(section.students_count || 0), 0);
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
        formatAdviser(adviser) {
            return adviser ? `${adviser.first_name} ${adviser.last_name}` : 'Unassigned';
        },
        normalizePayload() {
            return {
                section_name: this.form.section_name,
                year_level: Number(this.form.year_level),
                school_year: this.form.school_year,
                adviser_id: this.form.adviser_id ? Number(this.form.adviser_id) : null,
                room_id: this.form.room_id ? Number(this.form.room_id) : null
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
        openEditForm(section) {
            this.editingId = section.section_id;
            this.feedbackMessage = '';
            this.errorMessage = '';
            this.form = {
                section_name: section.section_name || '',
                year_level: section.year_level ?? '',
                school_year: section.school_year || '',
                adviser_id: section.adviser_id ?? '',
                room_id: section.room_id ?? ''
            };
        },
        async fetchSections() {
            this.loading = true;
            try {
                const [{ data: sections }, { data: faculty }, { data: rooms }] = await Promise.all([
                    api.get('/sections'),
                    api.get('/faculty'),
                    api.get('/rooms')
                ]);

                this.sections = Array.isArray(sections) ? sections : [];
                this.faculty = Array.isArray(faculty) ? faculty : [];
                this.rooms = Array.isArray(rooms) ? rooms : [];
            } catch (error) {
                this.errorMessage = error?.response?.data?.message || 'Unable to load section records.';
            } finally {
                this.loading = false;
            }
        },
        async submitSection() {
            this.saving = true;
            this.feedbackMessage = '';
            this.errorMessage = '';

            try {
                const payload = this.normalizePayload();
                const wasEditing = this.isEditing;

                if (wasEditing) {
                    await api.put(`/sections/${this.editingId}`, payload);
                } else {
                    await api.post('/sections', payload);
                }

                await this.fetchSections();
                this.resetForm(false);
                this.feedbackMessage = wasEditing ? 'Section updated successfully.' : 'Section created successfully.';
            } catch (error) {
                const validationErrors = error?.response?.data?.errors;
                this.errorMessage = validationErrors
                    ? Object.values(validationErrors).flat().join(' ')
                    : (error?.response?.data?.message || 'Unable to save section.');
            } finally {
                this.saving = false;
            }
        },
        async deleteSection(section) {
            if (!window.confirm(`Delete ${section.section_name} for ${section.school_year}?`)) {
                return;
            }

            this.deletingId = section.section_id;
            this.feedbackMessage = '';
            this.errorMessage = '';

            try {
                await api.delete(`/sections/${section.section_id}`);
                if (this.editingId === section.section_id) {
                    this.resetForm(false);
                }
                await this.fetchSections();
                this.feedbackMessage = 'Section deleted successfully.';
            } catch (error) {
                this.errorMessage = error?.response?.data?.message || 'Unable to delete section.';
            } finally {
                this.deletingId = null;
            }
        }
    },
    mounted() {
        this.currentDate = this.getFormattedDate();
        this.fetchSections();
    }
};
</script>

<style scoped>
.page-layout {
    display: flex;
    min-height: 100vh;
    background:
        radial-gradient(circle at top left, rgba(214, 255, 230, 0.36), transparent 28%),
        linear-gradient(145deg, #ebfff4 0%, #8ed1a9 42%, #1f6e53 100%);
    font-family: 'Poppins', sans-serif;
}
.main-content { flex: 1; display: flex; flex-direction: column; }
.page-content { padding: 24px 32px 40px; display: flex; flex-direction: column; gap: 20px; }
.hero-card, .stat-card, .form-panel, .table-panel { border-radius: 24px; }
.hero-card {
    display: flex; align-items: center; justify-content: space-between; gap: 20px; padding: 28px;
    background: linear-gradient(135deg, rgba(18, 84, 60, 0.92), rgba(36, 125, 89, 0.9)); color: white;
    box-shadow: 0 18px 40px rgba(15, 83, 60, 0.2);
}
.eyebrow, .panel-label { margin: 0 0 10px; text-transform: uppercase; letter-spacing: .12em; font-size: 12px; font-weight: 700; opacity: .72; }
.hero-card h1, .panel-heading h2 { margin: 0; }
.hero-card h1 { max-width: 560px; font-size: 34px; line-height: 1.1; }
.hero-copy { margin: 12px 0 0; max-width: 560px; color: rgba(242, 255, 248, 0.82); }
.stats-grid, .workspace-grid { display: grid; gap: 18px; }
.stats-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
.workspace-grid { grid-template-columns: minmax(320px, 390px) minmax(0, 1fr); align-items: start; }
.stat-card, .form-panel, .table-panel { background: rgba(251, 255, 252, 0.94); box-shadow: 0 16px 34px rgba(16, 80, 58, 0.12); }
.stat-card { padding: 20px 22px; }
.stat-card span { display: block; color: #377d60; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; }
.stat-card strong { display: block; margin-top: 10px; color: #144b37; font-size: 34px; }
.form-panel, .table-panel { padding: 24px; }
.panel-heading { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 18px; }
.panel-heading h2 { color: #184f3b; font-size: 24px; }
.record-form { display: grid; grid-template-columns: 1fr; gap: 16px; }
.field { display: flex; flex-direction: column; gap: 6px; }
.field span { color: #3d6f5b; font-size: 13px; font-weight: 700; }
.field input, .field select {
    border: 1px solid rgba(31, 110, 83, .14); border-radius: 14px; background: #fff; color: #184b38;
    font: inherit; padding: 12px 14px; transition: border-color .2s ease, box-shadow .2s ease;
}
.field input:focus, .field select:focus {
    outline: none; border-color: rgba(43, 148, 108, .75); box-shadow: 0 0 0 4px rgba(142, 209, 169, .22);
}
.form-actions { display: flex; align-items: center; flex-wrap: wrap; gap: 12px; margin-top: 8px; }
.primary-btn, .ghost-btn, .table-btn {
    border: none; border-radius: 999px; font: inherit; font-weight: 700; cursor: pointer;
    transition: transform .2s ease, opacity .2s ease;
}
.primary-btn { padding: 12px 18px; background: linear-gradient(135deg, #a8ecc3, #39a172); color: #103827; box-shadow: 0 12px 24px rgba(18, 92, 61, .2); }
.ghost-btn, .table-btn { padding: 10px 14px; background: rgba(31, 110, 83, .09); color: #1e7355; }
.primary-btn:hover, .ghost-btn:hover, .table-btn:hover { transform: translateY(-1px); }
.primary-btn:disabled, .table-btn:disabled { cursor: not-allowed; opacity: .6; transform: none; }
.feedback { font-size: 13px; font-weight: 600; }
.feedback.success { color: #0f766e; }
.feedback.error { color: #b91c1c; }
.result-pill { display: inline-flex; align-items: center; border-radius: 999px; background: rgba(31, 110, 83, .08); color: #1f6e53; padding: 8px 12px; font-size: 12px; font-weight: 700; }
.reference-pill { display: inline-flex; align-items: center; border-radius: 999px; background: rgba(255, 255, 255, .16); color: white; padding: 10px 14px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; }
.state-card { padding: 36px 24px; border-radius: 20px; background: rgba(235, 252, 242, .82); text-align: center; color: #1f6e53; font-weight: 600; }
.table-wrap { overflow-x: auto; }
.record-table { width: 100%; border-collapse: collapse; }
.record-table th, .record-table td { padding: 16px 12px; border-bottom: 1px solid rgba(31, 110, 83, .08); text-align: left; }
.record-table th { color: #4e856f; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; }
.record-table td { color: #184b38; font-size: 14px; }
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
