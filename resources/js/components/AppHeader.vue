<template>
    <header class="app-header">
        <div class="header-left">
            <button v-if="showBack" class="back-btn" @click="goBack">
                <span class="back-icon">←</span>
                <span>Back</span>
            </button>
            <div class="welcome-section">
                <h2>{{ title }}</h2>
                <p>{{ subtitle }}</p>
            </div>
        </div>

        <div class="top-bar-right">
            <div class="search-container">
                <span class="search-icon">🔍</span>
                <input
                    :value="searchQuery"
                    type="text"
                    placeholder="Search"
                    class="search-input"
                    @input="$emit('update:searchQuery', $event.target.value)"
                >
            </div>
            <button class="notif-btn">🔔</button>
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
            profile: { ...DEFAULT_PROFILE }
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
        }
    },
    mounted() {
        this.loadProfile();
        window.addEventListener('storage', this.loadProfile);
        window.addEventListener('focus', this.loadProfile);
    },
    beforeUnmount() {
        window.removeEventListener('storage', this.loadProfile);
        window.removeEventListener('focus', this.loadProfile);
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

.notif-btn:hover {
    background: rgba(255, 255, 255, 0.28);
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
        align-items: flex-start;
        padding-left: 20px;
        padding-right: 20px;
    }

    .header-left {
        flex-direction: column;
    }

    .top-bar-right {
        width: 100%;
        flex-wrap: wrap;
    }

    .search-container,
    .search-input {
        width: 100%;
        max-width: none;
    }
}
</style>
