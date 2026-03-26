<template>
    <div class="students-layout">
        <sidebar :is-open="sidebarOpen" @toggle="sidebarOpen = !sidebarOpen"></sidebar>

        <div class="main-content">
            <app-header title="Students Directory" :subtitle="currentDate"></app-header>

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

                <div class="student-grid">
                    <article class="student-card" v-for="student in students" :key="student.id">
                        <div class="student-avatar">{{ student.initials }}</div>
                        <div class="student-info">
                            <h3>{{ student.name }}</h3>
                            <p>{{ student.course }}</p>
                            <span>{{ student.studentNumber }}</span>
                        </div>
                        <div class="student-status">{{ student.status }}</div>
                    </article>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import AppHeader from '../components/AppHeader.vue';
import Sidebar from '../components/Sidebar.vue';

export default {
    name: 'StudentsPage',
    components: {
        AppHeader,
        Sidebar
    },
    data() {
        return {
            sidebarOpen: true,
            currentDate: '',
            showAddForm: false,
            studentMessage: '',
            newStudent: {
                name: '',
                course: '',
                studentNumber: '',
                status: 'Active'
            },
            students: [
                { id: 1, name: 'Joana Marie Lumogda', course: 'BS Information Technology', studentNumber: '2024-00121', status: 'Active', initials: 'JL' },
                { id: 2, name: 'Nicoli B. Alonso', course: 'BS Computer Science', studentNumber: '2024-00122', status: 'Active', initials: 'NA' },
                { id: 3, name: 'Aira Dela Cruz', course: 'BS Information Systems', studentNumber: '2024-00123', status: 'Regular', initials: 'AD' },
                { id: 4, name: 'Marco Reyes', course: 'BS Information Technology', studentNumber: '2024-00124', status: 'Probationary', initials: 'MR' },
                { id: 5, name: 'Leah Santos', course: 'BS Computer Science', studentNumber: '2024-00125', status: 'Active', initials: 'LS' },
                { id: 6, name: 'Paolo Fernandez', course: 'BS Information Technology', studentNumber: '2024-00126', status: 'Regular', initials: 'PF' },
                { id: 7, name: 'Denise Mercado', course: 'BS Information Systems', studentNumber: '2024-00127', status: 'Active', initials: 'DM' },
                { id: 8, name: 'Ralph Mendoza', course: 'BS Computer Science', studentNumber: '2024-00128', status: 'Active', initials: 'RM' },
                { id: 9, name: 'Angela Robles', course: 'BS Information Technology', studentNumber: '2024-00129', status: 'Regular', initials: 'AR' },
                { id: 10, name: 'Jared Villanueva', course: 'BS Information Systems', studentNumber: '2024-00130', status: 'Probationary', initials: 'JV' },
                { id: 11, name: 'Trisha Gomez', course: 'BS Computer Science', studentNumber: '2024-00131', status: 'Active', initials: 'TG' },
                { id: 12, name: 'Ethan Cruz', course: 'BS Information Technology', studentNumber: '2024-00132', status: 'Active', initials: 'EC' },
                { id: 13, name: 'Camille Navarro', course: 'BS Information Systems', studentNumber: '2024-00133', status: 'Regular', initials: 'CN' },
                { id: 14, name: 'Bryan Dizon', course: 'BS Computer Science', studentNumber: '2024-00134', status: 'Active', initials: 'BD' },
                { id: 15, name: 'Mikaela Torres', course: 'BS Information Technology', studentNumber: '2024-00135', status: 'Active', initials: 'MT' },
                { id: 16, name: 'Sean Bautista', course: 'BS Information Systems', studentNumber: '2024-00136', status: 'Regular', initials: 'SB' },
                { id: 17, name: 'Patricia Lim', course: 'BS Computer Science', studentNumber: '2024-00137', status: 'Active', initials: 'PL' },
                { id: 18, name: 'Harvey Garcia', course: 'BS Information Technology', studentNumber: '2024-00138', status: 'Probationary', initials: 'HG' },
                { id: 19, name: 'Nicole Aquino', course: 'BS Information Systems', studentNumber: '2024-00139', status: 'Active', initials: 'NA' },
                { id: 20, name: 'Vincent Ramos', course: 'BS Computer Science', studentNumber: '2024-00140', status: 'Regular', initials: 'VR' }
            ]
        };
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
        addStudent() {
            const { name, course, studentNumber, status } = this.newStudent;

            if (!name.trim() || !course.trim() || !studentNumber.trim()) {
                this.studentMessage = 'Please complete all student fields.';
                return;
            }

            this.students.unshift({
                id: Date.now(),
                name: name.trim(),
                course: course.trim(),
                studentNumber: studentNumber.trim(),
                status,
                initials: this.getInitials(name.trim())
            });

            this.newStudent = {
                name: '',
                course: '',
                studentNumber: '',
                status: 'Active'
            };

            this.studentMessage = 'Student added successfully.';

            setTimeout(() => {
                this.studentMessage = '';
            }, 2000);
        }
    },
    mounted() {
        this.currentDate = this.getFormattedDate();
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

.student-card {
    background: white;
    border-radius: 20px;
    padding: 22px;
    box-shadow: 0 10px 28px rgba(0, 0, 0, 0.12);
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
