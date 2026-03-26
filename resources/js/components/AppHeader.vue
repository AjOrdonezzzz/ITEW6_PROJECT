<template>
    <header class="app-header">
        <div class="header-left">
            <button v-if="showBack" class="back-btn" @click="goBack">
                <span class="back-icon">&#8592;</span>
                <span>Back</span>
            </button>
            <div class="welcome-section">
                <h2>{{ title }}</h2>
                <p>{{ subtitle }}</p>
            </div>
        </div>

        <div class="top-bar-right">
            <div class="search-container">
                <span class="search-icon">&#128269;</span>
                <input
                    :value="searchQuery"
                    type="text"
                    placeholder="Search"
                    class="search-input"
                    @input="$emit('update:searchQuery', $event.target.value)"
                >
            </div>
            <div class="calendar-wrap">
                <button class="calendar-btn" title="Calendar" @click="toggleCalendar">&#128197;</button>

                <div v-if="showCalendar" class="calendar-popup">
                    <div class="calendar-popup-header">
                        <button class="calendar-nav-btn" @click="changeMonth(-1)">&#8249;</button>
                        <strong>{{ calendarLabel }}</strong>
                        <button class="calendar-nav-btn" @click="changeMonth(1)">&#8250;</button>
                    </div>

                    <div class="calendar-weekdays">
                        <span v-for="day in weekDays" :key="day">{{ day }}</span>
                    </div>

                    <div class="calendar-grid">
                        <button
                            v-for="day in calendarDays"
                            :key="day.key"
                            class="calendar-day"
                            :class="{ 'is-muted': !day.isCurrentMonth, 'is-today': day.isToday }"
                            @click="selectDate(day)"
                        >
                            {{ day.date.getDate() }}
                        </button>
                    </div>

                    <p class="calendar-selected">{{ selectedDateLabel }}</p>
                </div>
            </div>
            <button class="notif-btn" title="Notifications">&#128276;</button>
            <router-link to="/profile" class="profile-link">
                <div class="user-profile">
                    <img v-if="profile.avatar" :src="profile.avatar" alt="Profile" class="profile-img">
                    <div v-else class="profile-fallback">{{ profileInitials }}</div>
                </div>
                <span class="profile-name">{{ shortName }}</span>
            </router-link>
        </div>
    </header>
</template>

<script>
const DEFAULT_PROFILE = {
    fullName: 'Joana Lumogda',
    avatar: ''
};

export default {
    name: 'AppHeader',
    props: {
        title: {
            type: String,
            required: true
        },
        subtitle: {
            type: String,
            default: ''
        },
        showBack: {
            type: Boolean,
            default: false
        },
        searchQuery: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            profile: { ...DEFAULT_PROFILE },
            showCalendar: false,
            currentMonthDate: new Date(),
            selectedDate: new Date()
        };
    },
    computed: {
        shortName() {
            return this.profile.fullName || 'Profile';
        },
        profileInitials() {
            return this.profile.fullName
                .split(' ')
                .filter(Boolean)
                .slice(0, 2)
                .map((part) => part[0]?.toUpperCase() || '')
                .join('') || 'JP';
        },
        weekDays() {
            return ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];
        },
        calendarLabel() {
            return this.currentMonthDate.toLocaleDateString('en-US', {
                month: 'long',
                year: 'numeric'
            });
        },
        selectedDateLabel() {
            return `Selected: ${this.selectedDate.toLocaleDateString('en-US', {
                month: 'long',
                day: 'numeric',
                year: 'numeric'
            })}`;
        },
        calendarDays() {
            const monthStart = new Date(
                this.currentMonthDate.getFullYear(),
                this.currentMonthDate.getMonth(),
                1
            );
            const monthEnd = new Date(
                this.currentMonthDate.getFullYear(),
                this.currentMonthDate.getMonth() + 1,
                0
            );
            const gridStart = new Date(monthStart);
            gridStart.setDate(gridStart.getDate() - gridStart.getDay());

            const gridEnd = new Date(monthEnd);
            gridEnd.setDate(gridEnd.getDate() + (6 - gridEnd.getDay()));

            const today = new Date();
            const days = [];
            const cursor = new Date(gridStart);

            while (cursor <= gridEnd) {
                const current = new Date(cursor);
                days.push({
                    key: current.toISOString(),
                    date: current,
                    isCurrentMonth: current.getMonth() === this.currentMonthDate.getMonth(),
                    isToday:
                        current.getDate() === today.getDate() &&
                        current.getMonth() === today.getMonth() &&
                        current.getFullYear() === today.getFullYear()
                });
                cursor.setDate(cursor.getDate() + 1);
            }

            return days;
        }
    },
    methods: {
        loadProfile() {
            const savedProfile = JSON.parse(localStorage.getItem('profileData') || 'null');
            if (savedProfile) {
                this.profile = {
                    fullName: savedProfile.fullName || DEFAULT_PROFILE.fullName,
                    avatar: savedProfile.avatar || ''
                };
            }
        },
        goBack() {
            if (window.history.length > 1) {
                this.$router.back();
                return;
            }

            this.$router.push('/dashboard');
        },
        toggleCalendar() {
            this.showCalendar = !this.showCalendar;
        },
        changeMonth(direction) {
            this.currentMonthDate = new Date(
                this.currentMonthDate.getFullYear(),
                this.currentMonthDate.getMonth() + direction,
                1
            );
        },
        selectDate(day) {
            this.selectedDate = new Date(day.date);
        },
        handleOutsideClick(event) {
            if (!this.showCalendar) return;

            const calendarElement = this.$el.querySelector('.calendar-wrap');
            if (calendarElement && !calendarElement.contains(event.target)) {
                this.showCalendar = false;
            }
        }
    },
    mounted() {
        this.loadProfile();
        window.addEventListener('storage', this.loadProfile);
        window.addEventListener('focus', this.loadProfile);
        document.addEventListener('click', this.handleOutsideClick);
    },
    beforeUnmount() {
        window.removeEventListener('storage', this.loadProfile);
        window.removeEventListener('focus', this.loadProfile);
        document.removeEventListener('click', this.handleOutsideClick);
    }
};
</script>

<style scoped>
.app-header {
    position: sticky;
    top: 0;
    z-index: 40;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 20px;
    padding: 22px 32px 18px;
    color: white;
    background: linear-gradient(180deg, rgba(98, 48, 4, 0.96), rgba(125, 63, 6, 0.93));
    backdrop-filter: blur(12px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.12);
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.18);
}

.header-left {
    display: flex;
    align-items: flex-start;
    gap: 16px;
}

.welcome-section h2 {
    font-size: 20px;
    margin-bottom: 6px;
}

.welcome-section p {
    opacity: 0.85;
}

.back-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 10px 16px;
    border: none;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.22);
    color: white;
    font: inherit;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.18);
    transition: transform 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
}

.back-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-1px);
    box-shadow: 0 10px 24px rgba(0, 0, 0, 0.22);
}

.back-icon {
    font-size: 18px;
    line-height: 1;
}

.top-bar-right {
    display: flex;
    align-items: center;
    gap: 15px;
}

.search-container {
    position: relative;
    display: flex;
    align-items: center;
    flex: 1;
    max-width: 300px;
}

.search-icon {
    position: absolute;
    left: 12px;
    font-size: 14px;
    pointer-events: none;
    opacity: 0.5;
}

.search-input {
    padding: 10px 15px 10px 36px;
    border: none;
    border-radius: 25px;
    font-size: 14px;
    font-family: 'Poppins', sans-serif;
    width: 220px;
    background: rgba(255, 255, 255, 0.92);
}

.calendar-wrap {
    position: relative;
}

.calendar-btn,
.notif-btn {
    background: rgba(255, 255, 255, 0.18);
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    transition: background 0.3s ease;
}

.calendar-btn:hover,
.notif-btn:hover {
    background: rgba(255, 255, 255, 0.28);
}

.calendar-popup {
    position: absolute;
    top: calc(100% + 12px);
    right: 0;
    width: 280px;
    padding: 16px;
    border-radius: 20px;
    background: rgba(255, 248, 240, 0.98);
    color: #3a2b1a;
    box-shadow: 0 18px 40px rgba(0, 0, 0, 0.2);
    border: 1px solid rgba(122, 74, 18, 0.12);
}

.calendar-popup-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
}

.calendar-popup-header strong {
    font-size: 15px;
}

.calendar-nav-btn {
    width: 32px;
    height: 32px;
    border: none;
    border-radius: 50%;
    background: rgba(138, 90, 32, 0.12);
    color: #8a5a20;
    cursor: pointer;
    font-size: 20px;
    line-height: 1;
}

.calendar-weekdays,
.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, minmax(0, 1fr));
    gap: 6px;
}

.calendar-weekdays {
    margin-bottom: 10px;
}

.calendar-weekdays span {
    text-align: center;
    font-size: 12px;
    font-weight: 600;
    color: #8a5a20;
}

.calendar-day {
    border: none;
    border-radius: 12px;
    padding: 10px 0;
    background: rgba(138, 90, 32, 0.08);
    color: #3a2b1a;
    font: inherit;
    cursor: pointer;
    transition: background 0.2s ease, transform 0.2s ease;
}

.calendar-day:hover {
    background: rgba(138, 90, 32, 0.18);
    transform: translateY(-1px);
}

.calendar-day.is-muted {
    opacity: 0.45;
}

.calendar-day.is-today {
    background: linear-gradient(135deg, #f0c48a, #9d5a16);
    color: white;
    font-weight: 700;
}

.calendar-selected {
    margin-top: 14px;
    font-size: 13px;
    font-weight: 600;
    color: #8a5a20;
    text-align: center;
}

.profile-link {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #1a1a1a;
    text-decoration: none;
}

.user-profile {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    overflow: hidden;
    cursor: pointer;
    background: rgba(255, 255, 255, 0.18);
    display: flex;
    align-items: center;
    justify-content: center;
}

.profile-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-fallback {
    color: white;
    font-size: 14px;
    font-weight: 700;
}

.profile-name {
    color: #1a1a1a;
    font-size: 15px;
    font-weight: 600;
}

@media (max-width: 768px) {
    .app-header {
        flex-direction: column;
        align-items: stretch;
        padding: 18px 20px 16px;
    }

    .header-left,
    .top-bar-right {
        width: 100%;
    }

    .top-bar-right {
        flex-wrap: wrap;
        justify-content: flex-start;
    }

    .search-input {
        width: 100%;
    }

    .profile-name {
        font-size: 14px;
    }
}
</style>
