<x-guest-layout>
    @vite(['resources/css/auth/login.css', 'resources/js/auth/forgot-password.js'])

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
                    <h2 class="branding-title">Keamanan Akun Anda Prioritas Kami</h2>
                    <p class="branding-subtitle">Kami akan membantu Anda memulihkan akses ke akun Anda dengan aman dan cepat.</p>
                    
                    <!-- Dynamic Glassmorphic Card -->
                    <div class="branding-feature-card">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="feature-text">
                                <h4>Proses Aman</h4>
                                <p>Pemulihan kata sandi dilakukan secara aman menggunakan tautan verifikasi terenkripsi.</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-headset"></i>
                            </div>
                            <div class="feature-text">
                                <h4>Butuh Bantuan?</h4>
                                <p>Tim dukungan kami siap membantu jika Anda mengalami kendala memulihkan akun.</p>
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
                        <p class="logo-subtitle">Lupa Password Akun</p>
                    </div>
                    
                    <!-- Desktop Header -->
                    <div class="form-header-desktop">
                        <h2 class="form-title">Lupa Password?</h2>
                        <p class="form-subtitle">Tuliskan email terdaftar Anda untuk mengirimkan link pemulihan password</p>
                    </div>

                    <!-- Session Status / Success Message (For non-AJAX fallback and AJAX success display) -->
                    <div class="alert alert-success mb-4 {{ session('status') ? 'show' : '' }}" id="successMessage" style="{{ session('status') ? '' : 'display: none;' }}">
                        <i class="fas fa-check-circle me-2"></i>
                        <span id="successText">{{ session('status') }}</span>
                    </div>

                    <!-- Validation Errors / Error Message -->
                    <div class="error-message" id="errorMessage">
                        <i class="fas fa-exclamation-circle error-icon"></i>
                        <span id="errorText"></span>
                    </div>

                    <!-- Forgot Password Form -->
                    <form id="forgotPasswordForm" method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <!-- Email Address -->
                        <x-form.input 
                            label="Email Terdaftar"
                            type="email"
                            name="email"
                            placeholder="Masukkan alamat email akun Anda"
                            autocomplete="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            icon="fas fa-envelope"
                        />

                        <!-- Submit Button -->
                        <button type="submit" class="btn-submit mt-4" id="submitBtn">
                            <span class="spinner"></span>
                            <span class="btn-text">Kirim Link Reset</span>
                        </button>
                    </form>

                    <!-- Footer -->
                    <div class="login-footer">
                        <p class="footer-text"><a href="{{ route('login') }}" class="forgot-link">Kembali ke Halaman Masuk</a></p>
                        <p class="footer-text mt-3">© 2026 LaundryPro. All rights reserved.</p>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</x-guest-layout>
