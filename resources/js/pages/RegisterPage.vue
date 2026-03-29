<template>
    <div class="auth-page">
        <div class="auth-left">
            <div class="auth-card">
                <h1>Create account</h1>

                <form @submit.prevent="handleRegister" novalidate>
                    <div class="form-group">
                        <label for="fullName">Full name</label>
                        <input
                            id="fullName"
                            v-model="form.fullName"
                            type="text"
                            :class="{ error: errors.fullName }"
                            @blur="validateFullName"
                        >
                        <span class="error-message">{{ errors.fullName }}</span>
                    </div>

                    <div class="form-group">
                        <label for="username">Username</label>
                        <input
                            id="username"
                            v-model="form.username"
                            type="text"
                            :class="{ error: errors.username }"
                            @blur="validateUsername"
                        >
                        <span class="error-message">{{ errors.username }}</span>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            :class="{ error: errors.email }"
                            @blur="validateEmail"
                        >
                        <span class="error-message">{{ errors.email }}</span>
                    </div>

                    <div class="form-group">
                        <label for="role">Role</label>
                        <select
                            id="role"
                            v-model="form.role"
                            class="form-select"
                        >
                            <option value="staff">Staff</option>
                            <option value="faculty">Faculty</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            :class="{ error: errors.password }"
                            @blur="validatePassword"
                        >
                        <span class="error-message">{{ errors.password }}</span>
                    </div>

                    <div class="form-group">
                        <label for="confirmPassword">Confirm password</label>
                        <input
                            id="confirmPassword"
                            v-model="form.confirmPassword"
                            type="password"
                            :class="{ error: errors.confirmPassword }"
                            @blur="validateConfirmPassword"
                        >
                        <span class="error-message">{{ errors.confirmPassword }}</span>
                    </div>

                    <button type="submit" class="auth-btn" :disabled="isLoading">
                        {{ isLoading ? 'Creating...' : 'Register' }}
                    </button>

                    <p class="switch-prompt">
                        Already have an account?
                        <router-link to="/" class="switch-link">Log in</router-link>
                    </p>

                    <div
                        v-if="message.text"
                        :class="['message', message.type, { hidden: !message.show }]"
                    >
                        {{ message.text }}
                    </div>
                </form>
            </div>
        </div>

        <div class="auth-right">
            <div class="hero-circle"></div>
            <div class="hero-copy">
                <p class="eyebrow">CCS Department</p>
                <h2>Build your student account and start exploring the dashboard.</h2>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'RegisterPage',
    data() {
        return {
            form: {
                fullName: '',
                username: '',
                email: '',
                role: 'staff',
                password: '',
                confirmPassword: ''
            },
            errors: {
                fullName: '',
                username: '',
                email: '',
                password: '',
                confirmPassword: ''
            },
            message: {
                text: '',
                type: '',
                show: false
            },
            isLoading: false
        };
    },
    methods: {
        validateFullName() {
            this.errors.fullName = this.form.fullName.trim()
                ? ''
                : 'Full name is required';
        },

        validateUsername() {
            this.errors.username = this.form.username.trim()
                ? ''
                : 'Username is required';
        },

        validateEmail() {
            const email = this.form.email.trim();
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!email) {
                this.errors.email = 'Email is required';
            } else if (!emailPattern.test(email)) {
                this.errors.email = 'Enter a valid email address';
            } else {
                this.errors.email = '';
            }
        },

        validatePassword() {
            this.errors.password = this.form.password.length >= 8
                ? ''
                : 'Password must be at least 8 characters';
        },

        validateConfirmPassword() {
            this.errors.confirmPassword =
                this.form.confirmPassword === this.form.password
                    ? ''
                    : 'Passwords do not match';
        },

        validateForm() {
            this.validateFullName();
            this.validateUsername();
            this.validateEmail();
            this.validatePassword();
            this.validateConfirmPassword();

            return Object.values(this.errors).every((value) => !value);
        },

        mapBackendErrors(backendErrors) {
            if (backendErrors.name) {
                this.errors.fullName = backendErrors.name[0];
            }

            if (backendErrors.username) {
                this.errors.username = backendErrors.username[0];
            }

            if (backendErrors.email) {
                this.errors.email = backendErrors.email[0];
            }

            if (backendErrors.password) {
                this.errors.password = backendErrors.password[0];
            }
        },

        async handleRegister() {
            this.message = {
                text: '',
                type: '',
                show: false
            };

            this.errors = {
                fullName: '',
                username: '',
                email: '',
                password: '',
                confirmPassword: ''
            };

            if (!this.validateForm()) return;

            this.isLoading = true;

            try {
                await window.axios.get('/sanctum/csrf-cookie');

                await window.axios.post('/api/v1/register', {
                    name: this.form.fullName.trim(),
                    username: this.form.username.trim(),
                    email: this.form.email.trim(),
                    role: this.form.role,
                    password: this.form.password,
                    password_confirmation: this.form.confirmPassword
                });

                this.message = {
                    text: 'Account created successfully. Redirecting to dashboard...',
                    type: 'success',
                    show: true
                };

                setTimeout(() => {
                    this.$router.push('/dashboard');
                }, 1000);
            } catch (error) {
                if (error.response?.status === 422) {
                    this.mapBackendErrors(error.response.data.errors || {});
                    this.message = {
                        text: 'Please fix the highlighted fields.',
                        type: 'error',
                        show: true
                    };
                } else {
                    this.message = {
                        text: error.response?.data?.message || 'Registration failed. Please try again.',
                        type: 'error',
                        show: true
                    };
                }
            } finally {
                this.isLoading = false;
            }
        }
    }
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.auth-page {
    min-height: 100vh;
    display: grid;
    grid-template-columns: 1.05fr 0.95fr;
    background: linear-gradient(135deg, #b27722 0%, #7a3902 45%, #211000 100%);
    font-family: 'Poppins', sans-serif;
}

.auth-left {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding: 40px 60px 40px 20px;
}

.auth-card {
    width: min(430px, 100%);
    background: rgba(255, 255, 255, 0.96);
    border-radius: 24px;
    padding: 42px;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
}

.auth-card h1 {
    font-size: 32px;
    color: #18181b;
    margin-bottom: 26px;
}

.form-group {
    margin-bottom: 18px;
}

.form-group label {
    display: block;
    font-size: 13px;
    color: #666;
    margin-bottom: 8px;
    font-weight: 500;
}

.form-group input,
.form-select {
    width: 100%;
    padding: 12px 14px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
    font-family: 'Poppins', sans-serif;
    transition: all 0.3s ease;
    background: #fff;
}

.form-group input:focus,
.form-select:focus {
    outline: none;
    border-color: #b27722;
    box-shadow: 0 0 0 3px rgba(178, 119, 34, 0.1);
}

.form-group input.error {
    border-color: #ef4444;
    background: #fff5f5;
}

.error-message {
    display: block;
    min-height: 16px;
    margin-top: 4px;
    font-size: 12px;
    color: #ef4444;
    font-weight: 500;
}

.auth-btn {
    width: 100%;
    margin-top: 10px;
    padding: 14px;
    border: none;
    border-radius: 999px;
    background: linear-gradient(135deg, #a89080 0%, #8a7a6a 100%);
    color: white;
    font-size: 15px;
    font-weight: 600;
    font-family: 'Poppins', sans-serif;
    cursor: pointer;
    transition: all 0.3s ease;
}

.auth-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.18);
}

.auth-btn:disabled {
    background: #ccc;
    cursor: not-allowed;
}

.switch-prompt {
    margin-top: 18px;
    font-size: 13px;
    text-align: center;
    color: #666;
}

.switch-link {
    color: #b27722;
    font-weight: 600;
    text-decoration: none;
}

.switch-link:hover {
    text-decoration: underline;
}

.message {
    margin-top: 16px;
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 14px;
    text-align: center;
    font-weight: 600;
}

.message.success {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #6ee7b7;
}

.message.error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fca5a5;
}

.message.hidden {
    display: none;
}

.auth-right {
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 60px;
    color: white;
    overflow: hidden;
}

.hero-circle {
    position: absolute;
    right: 80px;
    width: 320px;
    height: 320px;
    border-radius: 50%;
    background: radial-gradient(circle at 30% 30%, rgba(255, 188, 92, 0.35), rgba(74, 31, 0, 0.08));
    box-shadow: 0 0 0 30px rgba(255, 255, 255, 0.03);
}

.hero-copy {
    position: relative;
    max-width: 360px;
}

.eyebrow {
    margin-bottom: 14px;
    font-size: 13px;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    opacity: 0.7;
}

.hero-copy h2 {
    font-size: 38px;
    line-height: 1.15;
}

@media (max-width: 900px) {
    .auth-page {
        grid-template-columns: 1fr;
    }

    .auth-left {
        justify-content: center;
        padding: 24px;
    }

    .auth-right {
        display: none;
    }
}
</style>