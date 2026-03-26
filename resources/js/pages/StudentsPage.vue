<template>
    <div class="students-layout">
        <sidebar :is-open="sidebarOpen" @toggle="sidebarOpen = !sidebarOpen"></sidebar>

        <div class="main-content">
            <app-header title="Students Directory" :subtitle="currentDate"></app-header>

            <div class="students-content">
                <div class="page-header">
                    <h1 class="page-title">Students</h1>
                    <p class="page-subtitle">Browse enrolled students and quick profile details.</p>
                </div>

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
            students: [
                { id: 1, name: 'Joana Marie Lumogda', course: 'BS Information Technology', studentNumber: '2024-00121', status: 'Active', initials: 'JL' },
                { id: 2, name: 'Nicoli B. Alonso', course: 'BS Computer Science', studentNumber: '2024-00122', status: 'Active', initials: 'NA' },
                { id: 3, name: 'Aira Dela Cruz', course: 'BS Information Systems', studentNumber: '2024-00123', status: 'Regular', initials: 'AD' },
                { id: 4, name: 'Marco Reyes', course: 'BS Information Technology', studentNumber: '2024-00124', status: 'Probationary', initials: 'MR' }
            ]
        };
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

    .page-title {
        font-size: 30px;
    }
}
</style>
