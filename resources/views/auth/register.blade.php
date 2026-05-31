<x-guest-layout>
    @vite(['resources/css/auth/login.css', 'resources/js/auth/register.js'])

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
                    <h2 class="branding-title">Nikmati Kemudahan Layanan Laundry Terbaik</h2>
                    <p class="branding-subtitle">Daftar sekarang untuk melacak status pakaian Anda, memesan layanan antar-jemput, dan mendapatkan promo menarik.</p>
                    
                    <!-- Dynamic Glassmorphic Card -->
                    <div class="branding-feature-card">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-truck"></i>
                            </div>
                            <div class="feature-text">
                                <h4>Antar Jemput Pakaian</h4>
                                <p>Layanan jemput dan antar pakaian langsung ke pintu rumah Anda.</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-tags"></i>
                            </div>
                            <div class="feature-text">
                                <h4>Promo & Diskon Eksklusif</h4>
                                <p>Nikmati harga spesial dan penawaran menarik khusus member.</p>
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
                        <p class="logo-subtitle">Daftar Akun Baru Customer</p>
                    </div>
                    
                    <!-- Desktop Header -->
                    <div class="form-header-desktop">
                        <h2 class="form-title">Daftar Akun</h2>
                        <p class="form-subtitle">Lengkapi formulir untuk daftar sebagai Customer</p>
                    </div>

                    <!-- Register Form -->
                    <form id="registerForm" method="POST" action="{{ route('register') }}">
                        @csrf

                        <!-- Inline error message (shown above fields) -->
                        <div class="field-error-box{{ $errors->any() ? ' show' : '' }}" id="errorMessage">
                            <div class="field-error-inner">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span id="errorText">{{ $errors->first() }}</span>
                            </div>
                        </div>

                        <!-- Name -->
                        <x-form.input 
                            label="Nama Lengkap"
                            type="text"
                            name="name"
                            placeholder="Masukkan nama lengkap Anda"
                            autocomplete="name"
                            value="{{ old('name') }}"
                            required
                            autofocus
                            icon="fas fa-user-circle"
                        />

                        <!-- Username -->
                        <x-form.input 
                            label="Username"
                            type="text"
                            name="username"
                            placeholder="Buat username unik"
                            autocomplete="username"
                            value="{{ old('username') }}"
                            required
                            icon="fas fa-user"
                        />

                        <!-- Email Address -->
                        <x-form.input 
                            label="Email"
                            type="email"
                            name="email"
                            placeholder="Masukkan alamat email aktif"
                            autocomplete="email"
                            value="{{ old('email') }}"
                            required
                            icon="fas fa-envelope"
                        />

                        <!-- Password -->
                        <x-form.input 
                            label="Password"
                            type="password"
                            name="password"
                            placeholder="Buat password aman"
                            autocomplete="new-password"
                            required
                            icon="fas fa-lock"
                            is-password="true"
                        />

                        <!-- Confirm Password -->
                        <x-form.input 
                            label="Konfirmasi Password"
                            type="password"
                            name="password_confirmation"
                            placeholder="Masukkan ulang password"
                            autocomplete="new-password"
                            required
                            icon="fas fa-shield-alt"
                            is-password="true"
                        />

                        <!-- Submit Button -->
                        <button type="submit" class="btn-submit mt-4" id="submitBtn">
                            <span class="spinner"></span>
                            <span class="btn-text">Daftar</span>
                        </button>
                    </form>

                    <!-- Footer -->
                    <div class="login-footer">
                        <p class="footer-text">Sudah punya akun? <a href="{{ route('login') }}" class="forgot-link">Masuk Sekarang</a></p>
                        <p class="footer-text mt-3">© 2026 LaundryPro. All rights reserved.</p>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</x-guest-layout>
