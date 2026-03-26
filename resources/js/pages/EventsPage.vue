<template>
    <div class="events-layout">
        <sidebar :is-open="sidebarOpen" @toggle="sidebarOpen = !sidebarOpen"></sidebar>

        <div class="main-content">
            <app-header
                title="Upcoming Activities"
                :subtitle="currentDate"
                :search-query="searchQuery"
                @update:search-query="searchQuery = $event"
            ></app-header>

            <div class="events-content">
                <div class="page-header">
                    <h1 class="page-title">Events</h1>
                    <p class="page-subtitle">Track department schedules, deadlines, and campus activities.</p>
                </div>

                <div class="events-grid">
                    <article class="event-card" v-for="event in filteredEvents" :key="event.id">
                        <div class="event-date">
                            <span class="event-month">{{ event.month }}</span>
                            <strong class="event-day">{{ event.day }}</strong>
                        </div>
                        <div class="event-details">
                            <h3>{{ event.title }}</h3>
                            <p>{{ event.description }}</p>
                            <span class="event-meta">{{ event.time }} • {{ event.location }}</span>
                        </div>
                    </article>
                </div>

                <div v-if="!filteredEvents.length" class="empty-state">
                    No events matched your search.
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import AppHeader from '../components/AppHeader.vue';
import Sidebar from '../components/Sidebar.vue';

export default {
    name: 'EventsPage',
    components: {
        AppHeader,
        Sidebar
    },
    data() {
        return {
            sidebarOpen: true,
            currentDate: '',
            searchQuery: '',
            events: [
                { id: 1, month: 'APR', day: '03', title: 'Student Orientation', description: 'Welcome session for incoming CCS students.', time: '9:00 AM', location: 'Main Hall' },
                { id: 2, month: 'APR', day: '08', title: 'Hackathon Kickoff', description: 'Team formation and mechanics briefing.', time: '1:00 PM', location: 'Lab 2' },
                { id: 3, month: 'APR', day: '14', title: 'Thesis Proposal Review', description: 'Scheduled presentation for graduating students.', time: '10:00 AM', location: 'Conference Room' },
                { id: 4, month: 'APR', day: '20', title: 'Department Assembly', description: 'Faculty and student updates for the semester.', time: '3:00 PM', location: 'Auditorium' }
            ]
        };
    },
    computed: {
        filteredEvents() {
            const query = this.searchQuery.trim().toLowerCase();
            if (!query) return this.events;

            return this.events.filter((event) => {
                return [event.month, event.day, event.title, event.description, event.time, event.location]
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
.events-layout {
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

.events-content {
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

.events-grid {
    display: grid;
    gap: 18px;
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

.event-card {
    display: grid;
    grid-template-columns: 110px 1fr;
    gap: 22px;
    background: white;
    border-radius: 22px;
    padding: 22px;
    box-shadow: 0 10px 28px rgba(0, 0, 0, 0.12);
}

.event-date {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    border-radius: 18px;
    background: linear-gradient(135deg, #f4c78f, #a45b12);
    color: white;
    min-height: 110px;
}

.event-month {
    font-size: 14px;
    letter-spacing: 0.14em;
}

.event-day {
    font-size: 34px;
    line-height: 1.1;
}

.event-details h3 {
    font-size: 22px;
    color: #1a1a1a;
    margin-bottom: 8px;
}

.event-details p {
    color: #666;
    margin-bottom: 10px;
}

.event-meta {
    color: #8a5a20;
    font-size: 14px;
    font-weight: 600;
}

@media (max-width: 768px) {
    .events-content,
    .top-bar {
        padding-left: 20px;
        padding-right: 20px;
    }

    .event-card {
        grid-template-columns: 1fr;
    }

    .page-title {
        font-size: 30px;
    }
}
</style>
