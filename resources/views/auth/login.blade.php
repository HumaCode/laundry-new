<x-guest-layout>
    @vite(['resources/css/auth/login.css', 'resources/js/auth/login.js'])

    <div class="login-page-wrapper">
        <div class="login-split-container">
            
            <!-- Left Branding Panel (Hidden on Mobile) -->
            <div class="login-branding-panel">
                <div class="branding-overlay"></div>
                <div class="branding-content">
                    <!-- App Logo -->
                    <div class="branding-logo">
                        <i class="fas fa-tshirt"></i>
                        <span>LaundryPro</span>
                    </div>
                    
                    <!-- Headline -->
                    <h2 class="branding-title">Elevate Your Laundry Business Management</h2>
                    <p class="branding-subtitle">Sistem manajemen laundry cerdas, multi-outlet, dan real-time untuk memaksimalkan efisiensi operasional.</p>
                    
                    <!-- Dynamic Glassmorphic Card -->
                    <div class="branding-feature-card">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="feature-text">
                                <h4>Monitor Real-Time</h4>
                                <p>Pantau semua transaksi dan status order kapan saja.</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-store"></i>
                            </div>
                            <div class="feature-text">
                                <h4>Multi Outlet</h4>
                                <p>Kelola semua cabang laundry dalam satu dashboard.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Animated abstract shapes in background -->
                <div class="branding-bg-shape-1"></div>
                <div class="branding-bg-shape-2"></div>
            </div>
            
            <!-- Right Form Panel -->
            <div class="login-form-panel">
                <!-- Background decorations -->
                <div class="bg-decoration">
                    <div class="bg-shape"></div>
                    <div class="bg-shape"></div>
                </div>
                
                <div class="login-form-container">
                    <!-- Logo on Mobile (Hidden on Desktop) -->
                    <div class="mobile-logo-section">
                        <div class="logo-icon">
                            <i class="fas fa-tshirt"></i>
                        </div>
                        <h1 class="logo-title">LaundryPro</h1>
                        <p class="logo-subtitle">Sistem Manajemen Laundry Multi Outlet</p>
                    </div>
                    
                    <!-- Desktop Header -->
                    <div class="form-header-desktop">
                        <h2 class="form-title">Selamat Datang</h2>
                        <p class="form-subtitle">Silakan masuk ke akun Anda</p>
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
    </div>
</x-guest-layout>
