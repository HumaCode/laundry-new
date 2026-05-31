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
                    <x-form.input 
                        label="Username atau Email"
                        type="text"
                        name="login"
                        placeholder="Masukkan username atau email"
                        autocomplete="username"
                        value="{{ old('login') }}"
                        required
                        autofocus
                        icon="fas fa-user"
                    />

                    <!-- Password -->
                    <x-form.input 
                        label="Password"
                        type="password"
                        name="password"
                        placeholder="Masukkan password"
                        autocomplete="current-password"
                        required
                        icon="fas fa-lock"
                        is-password="true"
                    />

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
