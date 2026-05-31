<x-guest-layout>
    @vite(['resources/css/auth/login.css', 'resources/js/auth/login.js'])

    <div class="login-page-wrapper">
        <!-- Animated Background -->
        <div class="bg-decoration">
            <div class="bg-shape"></div>
            <div class="bg-shape"></div>
            <div class="bg-shape"></div>
        </div>

        <!-- Login Container -->
        <div class="login-container">
            <div class="login-card">
                <!-- Logo Section -->
                <div class="logo-section">
                    <div class="logo-icon">
                        <i class="fas fa-tshirt"></i>
                    </div>
                    <h1 class="logo-title">LaundryPro</h1>
                    <p class="logo-subtitle">Sistem Manajemen Laundry Multi Outlet</p>
                </div>

                <!-- Laravel Session Status -->
                @if (session('status'))
                    <div class="alert alert-success mb-4">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Validation Errors / Error Message -->
                @if ($errors->any())
                    <div class="error-message show" id="errorMessage">
                        <i class="fas fa-exclamation-circle error-icon"></i>
                        <span id="errorText">{{ $errors->first() }}</span>
                    </div>
                @endif

                <!-- Login Form -->
                <form id="loginForm" method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Username / Email -->
                    <div class="form-group">
                        <label class="form-label" for="login">Username atau Email</label>
                        <div class="input-wrapper">
                            <input 
                                type="text" 
                                id="login" 
                                name="login"
                                class="form-control" 
                                placeholder="Masukkan username atau email"
                                autocomplete="username"
                                value="{{ old('login') }}"
                                required
                                autofocus
                            >
                            <i class="fas fa-user input-icon"></i>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <div class="input-wrapper">
                            <input 
                                type="password" 
                                id="password" 
                                name="password"
                                class="form-control" 
                                placeholder="Masukkan password"
                                autocomplete="current-password"
                                required
                            >
                            <i class="fas fa-lock input-icon"></i>
                            <button 
                                type="button" 
                                class="password-toggle" 
                                id="passwordToggleBtn"
                                aria-label="Toggle password visibility"
                            >
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember & Forgot -->
                    <div class="form-options">
                        <div class="checkbox-wrapper">
                            <input 
                                type="checkbox" 
                                id="rememberMe" 
                                name="remember"
                                class="custom-checkbox"
                            >
                            <label class="checkbox-label" for="rememberMe">
                                Ingat Saya
                            </label>
                        </div>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="forgot-link">
                                Lupa Password?
                            </a>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-submit" id="submitBtn">
                        <span class="spinner"></span>
                        <span class="btn-text">Masuk</span>
                    </button>
                </form>

                <!-- Footer -->
                <div class="login-footer">
                    <p class="footer-text">© 2026 LaundryPro. All rights reserved.</p>
                    <div class="footer-links">
                        <a href="#" class="footer-link">Bantuan</a>
                        <a href="#" class="footer-link">Privasi</a>
                        <a href="#" class="footer-link">Syarat & Ketentuan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
