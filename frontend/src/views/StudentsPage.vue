<template>
    <div class="students-layout">
        <sidebar :is-open="sidebarOpen" @toggle="sidebarOpen = !sidebarOpen"></sidebar>

        <div class="main-content">
            <app-header
                title="Students Directory"
                :subtitle="currentDate"
                :search-query="searchQuery"
                @update:search-query="searchQuery = $event"
            ></app-header>

            <div class="students-content">
                <div class="page-header">
                    <div>
                        <h1 class="page-title">Students</h1>
                        <p class="page-subtitle">Browse enrolled students and quick profile details.</p>
                    </div>
                    <div class="header-actions">
                        <button class="view-toggle-btn" @click="toggleViewMode">
                            {{ viewMode === 'cards' ? 'Table View' : 'Card View' }}
                        </button>
                        <button class="add-student-btn" @click="showAddForm = !showAddForm">
                            {{ showAddForm ? 'Close Form' : 'Add Student' }}
                        </button>
                    </div>
                </div>

                <section v-if="showAddForm" class="student-form-card">
                    <div class="form-grid">
                        <div class="field">
                            <span>First Name <span class="required">*</span></span>
                            <input v-model="newStudent.first_name" type="text" placeholder="Enter first name">
                        </div>

                        <div class="field">
                            <span>Last Name <span class="required">*</span></span>
                            <input v-model="newStudent.last_name" type="text" placeholder="Enter last name">
                        </div>

                        <div class="field">
                            <span>Middle Name</span>
                            <input v-model="newStudent.middle_name" type="text" placeholder="Enter middle name (optional)">
                        </div>

                        <div class="field">
                            <span>Student Number <span class="required">*</span></span>
                            <input v-model="newStudent.studentNumber" type="text" placeholder="e.g. 2024-00121">
                        </div>

                        <div class="field">
                            <span>Gender <span class="required">*</span></span>
                            <select v-model="newStudent.gender">
                                <option value="">Select gender</option>
                                <option>Male</option>
                                <option>Female</option>
                                <option>Other</option>
                            </select>
                        </div>

                        <div class="field">
                            <span>Birthdate <span class="required">*</span></span>
                            <input v-model="newStudent.birthdate" type="date">
                        </div>

                        <div class="field">
                            <span>Civil Status</span>
                            <select v-model="newStudent.civil_status">
                                <option value="">Select civil status</option>
                                <option>Single</option>
                                <option>Married</option>
                                <option>Widowed</option>
                            </select>
                        </div>

                        <div class="field">
                            <span>Contact Number</span>
                            <input v-model="newStudent.contact_number" type="text" placeholder="e.g. 09xxxxxxxxx">
                        </div>

                        <div class="field">
                            <span>Email</span>
                            <input v-model="newStudent.email" type="email" placeholder="Enter email address">
                        </div>

                        <div class="field">
                            <span>Status <span class="required">*</span></span>
                            <select v-model="newStudent.status">
                                <option>Active</option>
                                <option>Inactive</option>
                                <option>Probationary</option>
                            </select>
                        </div>

                        <div class="field">
                            <span>Section Name <span class="required">*</span></span>
                            <input v-model="newStudent.section_name" type="text" placeholder="e.g. BSIT 3A">
                        </div>

                        <div class="field">
                            <span>Year Level <span class="required">*</span></span>
                            <input v-model="newStudent.year_level" type="number" placeholder="e.g. 3" min="1" max="6">
                        </div>

                        <div class="field">
                            <span>School Year <span class="required">*</span></span>
                            <input v-model="newStudent.school_year" type="text" placeholder="e.g. 2024-2025">
                        </div>

                        <div class="field">
                            <span>Guardian First Name <span class="required">*</span></span>
                            <input v-model="newStudent.guardian_first_name" type="text" placeholder="Enter guardian first name">
                        </div>

                        <div class="field">
                            <span>Guardian Last Name <span class="required">*</span></span>
                            <input v-model="newStudent.guardian_last_name" type="text" placeholder="Enter guardian last name">
                        </div>

                        <div class="field">
                            <span>Guardian Email <span class="required">*</span></span>
                            <input v-model="newStudent.guardian_email" type="email" placeholder="Enter guardian email">
                        </div>

                        <div class="field">
                            <span>Guardian Contact Number</span>
                            <input v-model="newStudent.guardian_contact" type="text" placeholder="e.g. 09xxxxxxxxx">
                        </div>

                        <div class="field" style="grid-column: span 2;">
                            <span>Address</span>
                            <input v-model="newStudent.address" type="text" placeholder="Enter full address">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button class="save-student-btn" @click="addStudent">Save Student</button>
                        <span v-if="studentMessage" class="student-message">{{ studentMessage }}</span>
                    </div>
                </section>

                <!-- ✅ Skill Filter Bar -->
                <div v-if="allSkills.length" class="skill-filter-bar">
                    <span class="filter-label">Filter by skill:</span>
                    <div class="skill-filter-pills">
                        <button
                            v-for="skill in allSkills"
                            :key="skill"
                            :class="['skill-filter-btn', { active: skillFilter === skill }]"
                            @click="setSkillFilter(skill)"
                        >
                            {{ skill }}
                        </button>
                        <button v-if="skillFilter" class="skill-filter-clear" @click="clearFilters">
                            ✕ Clear
                        </button>
                    </div>
                </div>

                <div v-if="loading" class="empty-state">Loading students...</div>

                <div v-else-if="paginatedStudents.length">
                    <!-- Card View -->
                    <div v-if="viewMode === 'cards'" class="student-grid">
                        <article
                            class="student-card"
                            v-for="student in paginatedStudents"
                            :key="student.id"
                            @click="openStudent(student)"
                        >
                            <div class="student-avatar">{{ student.initials }}</div>
                            <div class="student-info">
                                <h3>{{ student.name }}</h3>
                                <p>{{ student.course }}</p>
                                <span>{{ student.studentNumber }}</span>
                            </div>

                            <!-- ✅ Skill Badges -->
                            <div v-if="student.skills.length" class="skill-badges">
                                <span
                                    v-for="skill in student.skills.slice(0, 3)"
                                    :key="skill"
                                    class="skill-badge"
                                >{{ skill }}</span>
                                <span v-if="student.skills.length > 3" class="skill-badge skill-badge--more">
                                    +{{ student.skills.length - 3 }}
                                </span>
                            </div>

                            <div class="student-status">{{ student.status }}</div>
                        </article>
                    </div>

                    <!-- Table View -->
                    <div v-else class="student-table-container">
                        <table class="student-table">
                            <thead>
                                <tr>
                                    <th>Avatar</th>
                                    <th>Name</th>
                                    <th>Course</th>
                                    <th>Student Number</th>
                                    <th>Skills</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="student in paginatedStudents"
                                    :key="student.id"
                                    class="student-table-row"
                                    @click="openStudent(student)"
                                >
                                    <td class="avatar-cell">
                                        <div class="student-avatar">{{ student.initials }}</div>
                                    </td>
                                    <td>{{ student.name }}</td>
                                    <td>{{ student.course }}</td>
                                    <td>{{ student.studentNumber }}</td>
                                    <td>
                                        <div v-if="student.skills.length" class="skill-badges">
                                            <span
                                                v-for="skill in student.skills.slice(0, 3)"
                                                :key="skill"
                                                class="skill-badge"
                                            >{{ skill }}</span>
                                            <span v-if="student.skills.length > 3" class="skill-badge skill-badge--more">
                                                +{{ student.skills.length - 3 }}
                                            </span>
                                        </div>
                                        <span v-else>-</span>
                                    </td>
                                    <td>
                                        <span class="student-status">{{ student.status }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div v-else class="empty-state">
                    No students matched your search.
                </div>

                <!-- Pagination -->
                <div v-if="totalPages > 1" class="pagination">
                    <button
                        class="pagination-btn"
                        :disabled="currentPage === 1"
                        @click="prevPage"
                    >
                        Previous
                    </button>
                    <span class="pagination-info">
                        Page {{ currentPage }} of {{ totalPages }}
                    </span>
                    <button
                        class="pagination-btn"
                        :disabled="currentPage === totalPages"
                        @click="nextPage"
                    >
                        Next
                    </button>
                </div>

                <div v-if="selectedStudent" class="student-modal-overlay" @click.self="closeStudent">
                    <div class="student-modal">
                        <div class="student-modal-header">
                            <h2>Edit Student</h2>
                            <button class="close-btn" @click="closeStudent">×</button>
                        </div>

                        <div class="form-grid">
                            <div class="field">
                                <span>Full name</span>
                                <input v-model="selectedStudent.name" type="text">
                            </div>

                            <div class="field">
                                <span>Course</span>
                                <input v-model="selectedStudent.course" type="text">
                            </div>

                            <div class="field">
                                <span>Student number</span>
                                <input v-model="selectedStudent.studentNumber" type="text">
                            </div>

                            <div class="field">
                                <span>Status</span>
                                <select v-model="selectedStudent.status">
                                    <option>Active</option>
                                    <option>Inactive</option>
                                    <option>Probationary</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button class="save-student-btn" @click="updateStudent">Update Student</button>
                            <button class="delete-student-btn" @click="deleteStudent">Delete Student</button>
                            <span v-if="editMessage" class="student-message">{{ editMessage }}</span>
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
import api from '../../../backend/resources/js/services/api.js';

export default {
    name: 'StudentsPage',
    components: { AppHeader, Sidebar },

    data() {
        return {
            sidebarOpen: true,
            currentDate: '',
            showAddForm: false,
            studentMessage: '',
            editMessage: '',
            selectedStudent: null,
            searchQuery: '',
            skillFilter: null,
            loading: false,
            viewMode: 'cards', // 'cards' or 'table'
            currentPage: 1,
            itemsPerPage: 10,
            remoteTotalPages: 1,
            remoteTotalStudents: 0,

            newStudent: {
                first_name: '', last_name: '', middle_name: '',
                studentNumber: '', gender: '', birthdate: '',
                civil_status: '', contact_number: '', email: '',
                address: '', status: 'Active',
                section_name: '', year_level: '', school_year: '',
                guardian_first_name: '', guardian_last_name: '',
                guardian_email: '', guardian_contact: '',
            },

            students: []
        };
    },

    computed: {
        allSkills() {
            const skillSet = new Set();
            this.students.forEach((s) =>
                s.skills.forEach((sk) => skillSet.add(sk))
            );
            return [...skillSet].sort();
        },

        filteredStudents() {
            let list = this.students;

            if (this.skillFilter) {
                const filter = this.skillFilter.toLowerCase();
                list = list.filter((s) =>
                    s.skills.some((sk) => sk.toLowerCase().includes(filter))
                );
            }

            const query = this.searchQuery.trim().toLowerCase();
            if (!query) return list;
            return list.filter((student) =>
                [
                    student.name,
                    student.course,
                    student.studentNumber,
                    student.status,
                    ...student.skills,
                ].some((value) => String(value).toLowerCase().includes(query))
            );
        },

        isRemotePagination() {
            return this.remoteTotalStudents > 0;
        },

        paginatedStudents() {
            if (this.isRemotePagination) {
                return this.filteredStudents;
            }

            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.filteredStudents.slice(start, end);
        },

        totalPages() {
            // Prefer remote pagination info, else fallback to local calculation
            return this.remoteTotalPages > 0
                ? this.remoteTotalPages
                : Math.ceil(this.filteredStudents.length / this.itemsPerPage);
        },
    },

    methods: {
        getFormattedDate() {
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            return new Date().toLocaleDateString('en-US', options);
        },

        getInitials(name) {
            return name.split(' ').filter(Boolean).slice(0, 2)
                .map((p) => p[0]?.toUpperCase() || '').join('');
        },

        mapStudent(s) {
            const fullName = [s.first_name, s.middle_name ?? '', s.last_name]
                .filter(Boolean).join(' ');

            const skills = (s.skills ?? []).map((sk) =>
                sk.skill?.skill_name ?? sk.skill_name ?? ''
            ).filter(Boolean);

            return {
                id:            s.student_id,
                name:          fullName,
                course:        s.section?.section_name ?? 'N/A',
                studentNumber: s.student_number,
                status:        s.status,
                initials:      this.getInitials(fullName),
                skills,
                _raw:          s,
            };
        },

        setSkillFilter(skill) {
            this.skillFilter = this.skillFilter === skill ? null : skill;
        },

        clearFilters() {
            this.skillFilter = null;
            this.searchQuery = '';
            this.currentPage = 1;
        },

        toggleViewMode() {
            this.viewMode = this.viewMode === 'cards' ? 'table' : 'cards';
            this.currentPage = 1;
        },

        async nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
                await this.fetchStudents();
            }
        },

        async prevPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
                await this.fetchStudents();
            }
        },

        async goToPage(page) {
            if (page >= 1 && page <= this.totalPages) {
                this.currentPage = page;
                await this.fetchStudents();
            }
        },

        async fetchStudents() {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    limit: String(this.itemsPerPage),
                    page: String(this.currentPage),
                });

                const query = this.searchQuery.trim();
                if (query) {
                    params.set('q', query);
                }

                if (this.skillFilter) {
                    params.set('skill', this.skillFilter);
                }

                const { data } = await api.get(`/students?${params.toString()}`);

                // Laravel paginator returns {data: ..., current_page: ..., last_page: ..., total: ...}
                const pageData = Array.isArray(data.data) ? data.data : data;
                this.remoteTotalPages = data.last_page || 1;
                this.remoteTotalStudents = data.total || pageData.length;

                this.students = pageData.map(this.mapStudent);
            } catch (err) {
                console.error('Failed to load students:', err);
            } finally {
                this.loading = false;
            }
        },

        resetForm() {
            this.newStudent = {
                first_name: '', last_name: '', middle_name: '',
                studentNumber: '', gender: '', birthdate: '',
                civil_status: '', contact_number: '', email: '',
                address: '', status: 'Active',
                section_name: '', year_level: '', school_year: '',
                guardian_first_name: '', guardian_last_name: '',
                guardian_email: '', guardian_contact: '',
            };
        },

        async addStudent() {
            const {
                first_name, last_name, middle_name,
                studentNumber, gender, birthdate,
                civil_status, contact_number, email,
                address, status, section_name,
                year_level, school_year,
                guardian_first_name, guardian_last_name,
                guardian_email, guardian_contact,
            } = this.newStudent;

            const missing = [];
            if (!first_name.trim())          missing.push('First Name');
            if (!last_name.trim())           missing.push('Last Name');
            if (!studentNumber.trim())       missing.push('Student Number');
            if (!gender)                     missing.push('Gender');
            if (!birthdate)                  missing.push('Birthdate');
            if (!section_name.trim())        missing.push('Section Name');
            if (!year_level)                 missing.push('Year Level');
            if (!school_year.trim())         missing.push('School Year');
            if (!guardian_first_name.trim()) missing.push('Guardian First Name');
            if (!guardian_last_name.trim())  missing.push('Guardian Last Name');
            if (!guardian_email.trim())      missing.push('Guardian Email');

            if (missing.length) {
                this.studentMessage = `Missing: ${missing.join(', ')}`;
                return;
            }

            try {
                const guardianRes = await api.post('/guardians', {
                    first_name:     guardian_first_name.trim(),
                    last_name:      guardian_last_name.trim(),
                    email:          guardian_email.trim(),
                    contact_number: guardian_contact.trim() || null,
                });

                const sectionRes = await api.post('/sections', {
                    section_name: section_name.trim(),
                    year_level:   parseInt(year_level),
                    school_year:  school_year.trim(),
                    adviser_id:   null,
                });

                const { data } = await api.post('/students', {
                    student_number:  studentNumber.trim(),
                    first_name:      first_name.trim(),
                    last_name:       last_name.trim(),
                    middle_name:     middle_name.trim() || null,
                    gender,
                    birthdate,
                    civil_status:    civil_status || null,
                    contact_number:  contact_number.trim() || null,
                    email:           email.trim() || null,
                    address:         address.trim() || null,
                    status,
                    section_id:      sectionRes.data.section_id,
                    guardian_id:     guardianRes.data.guardian_id,
                });

                data.skills = [];
                this.students.unshift(this.mapStudent(data));
                this.resetForm();
                this.studentMessage = 'Student added successfully.';
                setTimeout(() => { this.studentMessage = ''; }, 2000);

            } catch (err) {
                console.error('Failed to add student:', err);
                const errors = err.response?.data?.errors;
                if (errors) {
                    this.studentMessage = Object.values(errors).flat().join(' | ');
                } else {
                    this.studentMessage = err.response?.data?.message ?? 'Failed to add student.';
                }
            }
        },
        //Dynamic Routing When a student card is clicked, their data is loaded dynamically
        openStudent(student) {
            this.selectedStudent = { ...student };
            this.editMessage = '';
        },

        closeStudent() {
            this.selectedStudent = null;
            this.editMessage = '';
        },

        async updateStudent() {
            if (!this.selectedStudent.name.trim() || !this.selectedStudent.studentNumber.trim()) {
                this.editMessage = 'Please complete all required fields.';
                return;
            }

            const nameParts   = this.selectedStudent.name.trim().split(' ').filter(Boolean);
            const first_name  = nameParts[0] ?? '';
            const last_name   = nameParts[nameParts.length - 1] ?? '';
            const middle_name = nameParts.length > 2 ? nameParts.slice(1, -1).join(' ') : null;

            try {
                const { data } = await api.put(`/students/${this.selectedStudent.id}`, {
                    student_number: this.selectedStudent.studentNumber.trim(),
                    first_name,
                    last_name,
                    middle_name,
                    status: this.selectedStudent.status,
                });

                data.skills = this.selectedStudent.skills.map((name) => ({
                    skill: { skill_name: name }
                }));

                const index = this.students.findIndex((s) => s.id === this.selectedStudent.id);
                if (index !== -1) this.students[index] = this.mapStudent(data);

                this.editMessage = 'Student updated successfully.';
                setTimeout(() => { this.closeStudent(); }, 1000);

            } catch (err) {
                console.error('Failed to update student:', err);
                const errors = err.response?.data?.errors;
                if (errors) {
                    this.editMessage = Object.values(errors).flat().join(' | ');
                } else {
                    this.editMessage = err.response?.data?.message ?? 'Failed to update student.';
                }
            }
        },

        async deleteStudent() {
            if (!this.selectedStudent) return;
            try {
                await api.delete(`/students/${this.selectedStudent.id}`);
                this.students = this.students.filter((s) => s.id !== this.selectedStudent.id);
                this.editMessage = 'Student deleted successfully.';
                setTimeout(() => { this.closeStudent(); }, 800);
            } catch (err) {
                console.error('Failed to delete student:', err);
                this.editMessage = err.response?.data?.message ?? 'Failed to delete student.';
            }
        },
    },

    watch: {
        searchQuery() {
            this.currentPage = 1;
            this.fetchStudents();
        },
        skillFilter() {
            this.currentPage = 1;
            this.fetchStudents();
        }
    },

    mounted() {
        this.currentDate = this.getFormattedDate();
        this.fetchStudents();
    }
};
</script>

<style scoped>
.required { color: #ef4444; }

.students-layout {
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

.students-content {
    padding: 24px 32px 40px;
}

.page-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 26px;
    color: white;
}

.header-actions {
    display: flex;
    gap: 12px;
    align-items: center;
}

.view-toggle-btn {
    padding: 12px 18px;
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.1);
    color: white;
    font: inherit;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.view-toggle-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.5);
}

.page-title {
    font-size: 38px;
    margin-bottom: 8px;
}

.page-subtitle {
    font-size: 15px;
    opacity: 0.85;
}

.add-student-btn,
.save-student-btn {
    padding: 12px 18px;
    border: none;
    border-radius: 999px;
    background: linear-gradient(135deg, #a89080 0%, #8a7a6a 100%);
    color: white;
    font: inherit;
    font-weight: 600;
    cursor: pointer;
}

.delete-student-btn {
    padding: 12px 18px;
    border: none;
    border-radius: 999px;
    background: #ef4444;
    color: white;
    font: inherit;
    font-weight: 600;
    cursor: pointer;
}

.student-form-card {
    background: white;
    border-radius: 20px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 10px 28px rgba(0, 0, 0, 0.12);
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

.form-actions {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-top: 20px;
}

.student-message {
    color: #0f766e;
    font-weight: 600;
}

/* ✅ Skill Filter Bar */
.skill-filter-bar {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.filter-label {
    color: rgba(255, 255, 255, 0.85);
    font-size: 13px;
    font-weight: 600;
    white-space: nowrap;
}

.skill-filter-pills {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.skill-filter-btn {
    padding: 6px 14px;
    border: 1.5px solid rgba(255, 255, 255, 0.45);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.08);
    color: rgba(255, 255, 255, 0.85);
    font: inherit;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.skill-filter-btn:hover {
    background: rgba(255, 255, 255, 0.18);
    border-color: rgba(255, 255, 255, 0.8);
    color: white;
}

.skill-filter-btn.active {
    background: white;
    color: #7a3902;
    border-color: white;
}

.skill-filter-clear {
    padding: 6px 14px;
    border: none;
    border-radius: 999px;
    background: rgba(239, 68, 68, 0.85);
    color: white;
    font: inherit;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s ease;
}

.skill-filter-clear:hover {
    background: #ef4444;
}

/* Student Grid */
.student-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 20px;
}

.empty-state {
    padding: 28px;
    border-radius: 20px;
    background: rgba(255, 255, 255, 0.9);
    color: #7a4a12;
    font-size: 16px;
    font-weight: 600;
    text-align: center;
}

.student-card {
    background: white;
    border-radius: 20px;
    padding: 22px;
    box-shadow: 0 10px 28px rgba(0, 0, 0, 0.12);
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.student-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 32px rgba(0, 0, 0, 0.16);
}

.student-avatar {
    width: 56px;
    height: 56px;
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 18px;
    background: linear-gradient(135deg, #f0c48a, #9d5a16);
    color: white;
    font-size: 20px;
    font-weight: 700;
}

.student-info h3 {
    font-size: 20px;
    color: #1a1a1a;
    margin-bottom: 8px;
}

.student-info p {
    color: #6b7280;
    margin-bottom: 6px;
}

.student-info span {
    color: #8a5a20;
    font-size: 14px;
    font-weight: 600;
}

/* ✅ Skill Badges on Cards */
.skill-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 14px;
}

.skill-badge {
    padding: 4px 10px;
    border-radius: 999px;
    background: rgba(255, 107, 53, 0.08);
    color: #7a3902;
    font-size: 11px;
    font-weight: 600;
    border: 1px solid rgba(255, 107, 53, 0.2);
}

.skill-badge--more {
    background: #f3f4f6;
    color: #6b7280;
    border-color: #e5e7eb;
}

.student-status {
    display: inline-flex;
    margin-top: 14px;
    padding: 8px 14px;
    border-radius: 999px;
    background: #fef3c7;
    color: #92400e;
    font-size: 13px;
    font-weight: 600;
}

/* Table View */
.student-table-container {
    background: white;
    border-radius: 20px;
    padding: 24px;
    box-shadow: 0 10px 28px rgba(0, 0, 0, 0.12);
    overflow-x: auto;
}

.student-table {
    width: 100%;
    border-collapse: collapse;
}

.student-table th {
    text-align: left;
    padding: 16px 12px;
    background: #f8f9fa;
    color: #1a1a1a;
    font-weight: 600;
    font-size: 14px;
    border-bottom: 2px solid #e9ecef;
}

.student-table td {
    padding: 16px 12px;
    border-bottom: 1px solid #e9ecef;
    vertical-align: middle;
}

.student-table-row {
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.student-table-row:hover {
    background-color: #f8f9fa;
}

.avatar-cell {
    width: 80px;
}

/* Pagination */
.pagination {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 16px;
    margin-top: 24px;
    padding: 16px;
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 28px rgba(0, 0, 0, 0.12);
}

.pagination-btn {
    padding: 10px 16px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background: white;
    color: #7a3902;
    font: inherit;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.pagination-btn:hover:not(:disabled) {
    background: #f8f9fa;
    border-color: #7a3902;
}

.pagination-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.pagination-info {
    color: #6b7280;
    font-weight: 600;
}

/* Modal */
.student-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.45);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px;
    z-index: 60;
}

.student-modal {
    width: min(760px, 100%);
    background: white;
    border-radius: 24px;
    padding: 24px;
    box-shadow: 0 18px 40px rgba(0, 0, 0, 0.22);
}

.student-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 18px;
}

.student-modal-header h2 { color: #1a1a1a; }

.close-btn {
    width: 40px;
    height: 40px;
    border: none;
    border-radius: 50%;
    background: #f3f4f6;
    font-size: 24px;
    cursor: pointer;
}

@media (max-width: 768px) {
    .students-content { padding-left: 20px; padding-right: 20px; }
    .page-header { flex-direction: column; align-items: flex-start; }
    .header-actions { flex-direction: column; width: 100%; }
    .view-toggle-btn, .add-student-btn { width: 100%; }
    .form-grid { grid-template-columns: 1fr; }
    .page-title { font-size: 30px; }
    .skill-filter-bar { flex-direction: column; align-items: flex-start; }
    .student-table-container { padding: 16px; }
    .pagination { flex-direction: column; gap: 12px; }
}
</style>
