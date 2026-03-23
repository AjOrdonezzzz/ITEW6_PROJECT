<template>
    <div class="login-page">
        <div class="login-left">
            <div class="login-card">
                <h1>Log in</h1>
                <form @submit.prevent="handleLogin" novalidate>
                    <div class="form-group">
                        <label for="username">Your username</label>
                        <input 
                            id="username"
                            v-model="form.username"
                            type="text"
                            placeholder=""
                            :class="{ error: errors.username }"
                            @blur="validateUsername"
                        >
                        <span class="error-message">{{ errors.username }}</span>
                    </div>

                    <div class="form-group">
                        <label for="password">Your password</label>
                        <input 
                            id="password"
                            v-model="form.password"
                            type="password"
                            placeholder=""
                            :class="{ error: errors.password }"
                            @blur="validatePassword"
                        >
                        <span class="error-message">{{ errors.password }}</span>
                    </div>

                    <div class="checkbox-group">
                        <input 
                            id="remember"
                            v-model="form.remember"
                            type="checkbox"
                        >
                        <label for="remember">Remember me</label>
                    </div>

                    <button type="submit" class="login-btn" :disabled="isLoading">
                        {{ isLoading ? 'Logging in...' : 'Log in' }}
                    </button>

                    <div 
                        v-if="message.text" 
                        :class="['message', message.type, { hidden: !message.show }]"
                    >
                        {{ message.text }}
                    </div>
                </form>
            </div>
        </div>

        <div class="login-right">
            <img src="https://via.placeholder.com/280/cccccc/cccccc?text=Logo" alt="Logo" class="logo-circle">
        </div>
    </div>
</template>

<script>
export default {
    name: 'LoginPage',
    data() {
        return {
            form: {
                username: '',
                password: '',
                remember: false
            },
            errors: {
                username: '',
                password: ''
            },
            message: {
                text: '',
                type: '',
                show: false
            },
            isLoading: false,
            validUsers: {
                'admin': 'password123',
                'gwillano': 'demo123',
                'user': 'user123'
            }
        };
    },
    methods: {
        validateUsername() {
            if (!this.form.username.trim()) {
                this.errors.username = 'Username is required';
            } else {
                this.errors.username = '';
            }
        },
        validatePassword() {
            if (!this.form.password) {
                this.errors.password = 'Password is required';
            } else {
                this.errors.password = '';
            }
        },
        validateForm() {
            this.errors.username = '';
            this.errors.password = '';

            if (!this.form.username.trim()) {
                this.errors.username = 'Username is required';
            }
            if (!this.form.password) {
                this.errors.password = 'Password is required';
            }

            return !this.errors.username && !this.errors.password;
        },
        handleLogin() {
            if (!this.validateForm()) return;

            this.isLoading = true;

            setTimeout(() => {
                const username = this.form.username.trim();
                const password = this.form.password;

                if (this.validUsers[username] && this.validUsers[username] === password) {
                    this.message = {
                        text: `Welcome back, ${username}! 🎉`,
                        type: 'success',
                        show: true
                    };

                    localStorage.setItem('user', JSON.stringify({
                        username: username,
                        rememberMe: this.form.remember
                    }));

                    this.form.password = '';
                    
                    setTimeout(() => {
                        this.message.show = false;
                    }, 3000);
                } else {
                    this.message = {
                        text: 'Invalid username or password.',
                        type: 'error',
                        show: true
                    };
                    this.errors.password = 'Incorrect password';
                }

                this.isLoading = false;
            }, 800);
        },
        loadSavedUser() {
            const savedUser = localStorage.getItem('user');
            if (savedUser) {
                const user = JSON.parse(savedUser);
                this.form.username = user.username;
                this.form.remember = user.rememberMe || false;
            }
        }
    },
    mounted() {
        this.loadSavedUser();
    }
};
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html, body {
    width: 100%;
    height: 100%;
    font-family: 'Poppins', sans-serif;
}

body {
    background: linear-gradient(135deg, #b27722 0%, #7a3902 45%, #211000 100%);
}

#app {
    width: 100%;
    height: 100%;
}

.login-page {
    display: grid;
    grid-template-columns: 1fr 1fr;
    width: 100%;
    min-height: 100vh;
    background: linear-gradient(135deg, #b27722 0%, #7a3902 45%, #211000 100%);
}

.login-left {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding-right: 60px;
}

.login-card {
    background: white;
    border-radius: 20px;
    padding: 50px;
    width: 70vh;
    height: 80vh;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.login-card h1 {
    font-size: 28px;
    text-align: center;
    margin-bottom: 35px;
    color: #1a1a1a;
    font-weight: 700;
    width: 100%;
}

.login-card form {
    width: 100%;
    max-width: 280px;
}

.form-group {
    margin-bottom: 22px;
}

.form-group label {
    display: block;
    font-size: 13px;
    color: #666;
    margin-bottom: 8px;
    font-weight: 500;
}

.form-group input[type="text"],
.form-group input[type="password"] {
    width: 100%;
    padding: 12px 14px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    font-family: 'Poppins', sans-serif;
    transition: all 0.3s ease;
    background: white;
}

.form-group input[type="text"]:focus,
.form-group input[type="password"]:focus {
    outline: none;
    border-color: #b27722;
    box-shadow: 0 0 0 3px rgba(178, 119, 34, 0.1);
}

.form-group input.error {
    border-color: #ef4444;
    background: #fff5f5;
}

.error-message {
    font-size: 12px;
    color: #ef4444;
    margin-top: 4px;
    display: block;
    min-height: 16px;
    font-weight: 500;
}

.checkbox-group {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 25px;
    margin-top: 20px;
}

.checkbox-group input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: #b27722;
}

.checkbox-group label {
    margin: 0;
    font-size: 13px;
    color: #555;
    cursor: pointer;
    font-weight: 500;
}

.login-btn {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #a89080 0%, #8a7a6a 100%);
    color: white;
    border: none;
    border-radius: 25px;
    font-size: 15px;
    font-weight: 600;
    font-family: 'Poppins', sans-serif;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 10px;
}

.login-btn:hover:not(:disabled) {
    background: linear-gradient(135deg, #998f7f 0%, #7a6a5a 100%);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.login-btn:disabled {
    background: #ccc;
    cursor: not-allowed;
    transform: none;
}

.message {
    margin-top: 16px;
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 14px;
    text-align: center;
    animation: slideDown 0.3s ease;
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

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.login-right {
    display: flex;
    align-items: center;
    justify-content: center;
}

.logo-circle {
    width: 280px;
    height: 280px;
    border-radius: 50%;
    object-fit: cover;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

@media (max-width: 1024px) {
    .login-left {
        padding-right: 40px;
    }

    .login-card {
        width: 300px;
        padding: 45px;
    }

    .logo-circle {
        width: 250px;
        height: 250px;
    }
}

@media (max-width: 768px) {
    .login-page {
        grid-template-columns: 1fr;
        padding: 20px;
    }

    .login-left {
        padding-right: 0;
        justify-content: center;
    }

    .login-right {
        display: none;
    }

    .login-card {
        width: 100%;
        max-width: 350px;
    }
}
</style>