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
                    <button class="add-student-btn" @click="showAddForm = !showAddForm">
                        {{ showAddForm ? 'Close Form' : 'Add Student' }}
                    </button>
                </div>

                <section v-if="showAddForm" class="student-form-card">
                    <div class="form-grid">
                        <label class="field">
                            <span>Full name</span>
                            <input v-model="newStudent.name" type="text" placeholder="Enter student name">
                        </label>

                        <label class="field">
                            <span>Course</span>
                            <input v-model="newStudent.course" type="text" placeholder="Enter course">
                        </label>

                        <label class="field">
                            <span>Student number</span>
                            <input v-model="newStudent.studentNumber" type="text" placeholder="Enter student number">
                        </label>

                        <label class="field">
                            <span>Status</span>
                            <select v-model="newStudent.status">
                                <option>Active</option>
                                <option>Regular</option>
                                <option>Probationary</option>
                            </select>
                        </label>
                    </div>

                    <div class="form-actions">
                        <button class="save-student-btn" @click="addStudent">Save Student</button>
                        <span v-if="studentMessage" class="student-message">{{ studentMessage }}</span>
                    </div>
                </section>

                <div v-if="filteredStudents.length" class="student-grid">
                    <article class="student-card" v-for="student in filteredStudents" :key="student.id" @click="openStudent(student)">
                        <div class="student-avatar">{{ student.initials }}</div>
                        <div class="student-info">
                            <h3>{{ student.name }}</h3>
                            <p>{{ student.course }}</p>
                            <span>{{ student.studentNumber }}</span>
                        </div>
                        <div class="student-status">{{ student.status }}</div>
                    </article>
                </div>

                <div v-else class="empty-state">
                    No students matched your search.
                </div>

                <div v-if="selectedStudent" class="student-modal-overlay" @click.self="closeStudent">
                    <div class="student-modal">
                        <div class="student-modal-header">
                            <h2>Edit Student</h2>
                            <button class="close-btn" @click="closeStudent">×</button>
                        </div>

                        <div class="form-grid">
                            <label class="field">
                                <span>Full name</span>
                                <input v-model="selectedStudent.name" type="text">
                            </label>

                            <label class="field">
                                <span>Course</span>
                                <input v-model="selectedStudent.course" type="text">
                            </label>

                            <label class="field">
                                <span>Student number</span>
                                <input v-model="selectedStudent.studentNumber" type="text">
                            </label>

                            <label class="field">
                                <span>Status</span>
                                <select v-model="selectedStudent.status">
                                    <option>Active</option>
                                    <option>Regular</option>
                                    <option>Probationary</option>
                                </select>
                            </label>
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
import api from '../services/api.js';

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
            loading: false,

            newStudent: {
                name: '',
                course: '',
                studentNumber: '',
                status: 'Active'
            },

            students: []
        };
    },

    computed: {
        filteredStudents() {
            const query = this.searchQuery.trim().toLowerCase();
            if (!query) return this.students;
            return this.students.filter((student) =>
                [student.name, student.course, student.studentNumber, student.status]
                    .some((value) => String(value).toLowerCase().includes(query))
            );
        }
    },

    methods: {
        getFormattedDate() {
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            return new Date().toLocaleDateString('en-US', options);
        },

        getInitials(name) {
            return name
                .split(' ')
                .filter(Boolean)
                .slice(0, 2)
                .map((part) => part[0]?.toUpperCase() || '')
                .join('');
        },

        // Map API student shape → template shape
        mapStudent(s) {
            const fullName = [s.first_name, s.middle_name ? s.middle_name[0] + '.' : '', s.last_name]
                .filter(Boolean)
                .join(' ');

            return {
                id:            s.student_id,
                name:          fullName,
                course:        s.section?.section_name ?? 'N/A',
                studentNumber: s.student_number,
                status:        s.status,
                initials:      this.getInitials(fullName),
                // keep raw fields for update
                _raw: s,
            };
        },

        // GET /api/v1/students
        async fetchStudents() {
            this.loading = true;
            try {
                const { data } = await api.get('/students');
                this.students = data.map(this.mapStudent);
            } catch (err) {
                console.error('Failed to load students:', err);
            } finally {
                this.loading = false;
            }
        },

        // POST /api/v1/students
        async addStudent() {
            const { name, course, studentNumber, status } = this.newStudent;

            if (!name.trim() || !course.trim() || !studentNumber.trim()) {
                this.studentMessage = 'Please complete all student fields.';
                return;
            }

            // Split name into parts: first last (middle optional)
            const nameParts  = name.trim().split(' ').filter(Boolean);
            const first_name = nameParts[0] ?? '';
            const last_name  = nameParts[nameParts.length - 1] ?? '';
            const middle_name = nameParts.length > 2 ? nameParts.slice(1, -1).join(' ') : null;

            try {
                const { data } = await api.post('/students', {
                    student_number: studentNumber.trim(),
                    first_name,
                    last_name,
                    middle_name,
                    gender:      'N/A',   // update form if you add gender field
                    birthdate:   '2000-01-01', // update form if you add birthdate field
                    section_id:  1,       // update if you add section dropdown
                    guardian_id: 1,       // update if you add guardian dropdown
                    status,
                });

                this.students.unshift(this.mapStudent(data));

                this.newStudent = { name: '', course: '', studentNumber: '', status: 'Active' };
                this.studentMessage = 'Student added successfully.';
                setTimeout(() => { this.studentMessage = ''; }, 2000);

            } catch (err) {
                console.error('Failed to add student:', err);
                this.studentMessage = err.response?.data?.message ?? 'Failed to add student.';
            }
        },

        openStudent(student) {
            this.selectedStudent = { ...student };
            this.editMessage = '';
        },

        closeStudent() {
            this.selectedStudent = null;
            this.editMessage = '';
        },

        // PUT /api/v1/students/{id}
        async updateStudent() {
            if (
                !this.selectedStudent.name.trim() ||
                !this.selectedStudent.course.trim() ||
                !this.selectedStudent.studentNumber.trim()
            ) {
                this.editMessage = 'Please complete all student fields.';
                return;
            }

            const nameParts  = this.selectedStudent.name.trim().split(' ').filter(Boolean);
            const first_name = nameParts[0] ?? '';
            const last_name  = nameParts[nameParts.length - 1] ?? '';
            const middle_name = nameParts.length > 2 ? nameParts.slice(1, -1).join(' ') : null;

            try {
                const { data } = await api.put(`/students/${this.selectedStudent.id}`, {
                    student_number: this.selectedStudent.studentNumber.trim(),
                    first_name,
                    last_name,
                    middle_name,
                    status: this.selectedStudent.status,
                });

                // Update in local list
                const index = this.students.findIndex((s) => s.id === this.selectedStudent.id);
                if (index !== -1) this.students[index] = this.mapStudent(data);

                this.editMessage = 'Student updated successfully.';
                setTimeout(() => { this.closeStudent(); }, 1000);

            } catch (err) {
                console.error('Failed to update student:', err);
                this.editMessage = err.response?.data?.message ?? 'Failed to update student.';
            }
        },

        // DELETE /api/v1/students/{id}
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

    mounted() {
        this.currentDate = this.getFormattedDate();
        this.fetchStudents();
    }
};
</script>

<style scoped>
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

.student-status {
    display: inline-flex;
    margin-top: 18px;
    padding: 8px 14px;
    border-radius: 999px;
    background: #fef3c7;
    color: #92400e;
    font-size: 13px;
    font-weight: 600;
}

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

.student-modal-header h2 {
    color: #1a1a1a;
}

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
    .students-content,
    .top-bar {
        padding-left: 20px;
        padding-right: 20px;
    }

    .page-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .form-grid {
        grid-template-columns: 1fr;
    }

    .page-title {
        font-size: 30px;
    }
}
</style>
