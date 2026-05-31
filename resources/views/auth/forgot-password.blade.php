<x-guest-layout>
    @vite(['resources/css/auth/login.css', 'resources/js/auth/forgot-password.js'])

    <style>
    /* ============================================================
       Premium Success Alert – Pulse & Animated
    ============================================================ */
    .fp-success-card {
        display: none;
        position: relative;
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        border: 1px solid rgba(16, 185, 129, 0.3);
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1.75rem;
        overflow: hidden;
        animation: successSlideIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    .fp-success-card.show {
        display: block;
    }

    @keyframes successSlideIn {
        from { opacity: 0; transform: translateY(-16px) scale(0.97); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }

    /* Glowing border pulse */
    .fp-success-card::before {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 16px;
        border: 2px solid rgba(16, 185, 129, 0.5);
        animation: borderPulse 2.5s ease-in-out infinite;
        pointer-events: none;
    }

    @keyframes borderPulse {
        0%, 100% { opacity: 0.3; transform: scale(1); }
        50%       { opacity: 1;   transform: scale(1.015); }
    }

    /* Background shimmer sweep */
    .fp-success-card::after {
        content: '';
        position: absolute;
        top: 0;
        left: -75%;
        width: 50%;
        height: 100%;
        background: linear-gradient(
            120deg,
            transparent 0%,
            rgba(255,255,255,0.45) 50%,
            transparent 100%
        );
        animation: shimmerSweep 3s ease-in-out infinite;
        pointer-events: none;
    }

    @keyframes shimmerSweep {
        0%   { left: -75%; }
        60%  { left: 125%; }
        100% { left: 125%; }
    }

    .fp-success-inner {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        position: relative;
        z-index: 1;
    }

    /* Pulsing icon ring */
    .fp-success-icon-wrap {
        position: relative;
        flex-shrink: 0;
        width: 46px;
        height: 46px;
    }

    .fp-success-icon-wrap .pulse-ring {
        position: absolute;
        inset: -4px;
        border-radius: 50%;
        background: rgba(16, 185, 129, 0.2);
        animation: pulseRing 1.8s ease-out infinite;
    }

    @keyframes pulseRing {
        0%   { transform: scale(1);   opacity: 0.7; }
        70%  { transform: scale(1.5); opacity: 0;   }
        100% { transform: scale(1.5); opacity: 0;   }
    }

    .fp-success-icon {
        position: relative;
        width: 46px;
        height: 46px;
        background: linear-gradient(135deg, #10b981, #059669);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.15rem;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
        animation: iconBounce 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) 0.2s both;
    }

    @keyframes iconBounce {
        from { transform: scale(0) rotate(-45deg); }
        to   { transform: scale(1) rotate(0deg); }
    }

    .fp-success-body {
        flex: 1;
    }

    .fp-success-title {
        font-size: 0.975rem;
        font-weight: 700;
        color: #065f46;
        margin: 0 0 0.3rem;
        letter-spacing: -0.01em;
    }

    .fp-success-text {
        font-size: 0.84rem;
        color: #047857;
        margin: 0;
        line-height: 1.5;
    }

    /* ============================================================
       Error Alert
    ============================================================ */
    .fp-error-card {
        display: none;
        position: relative;
        background: linear-gradient(135deg, #fff1f2 0%, #ffe4e6 100%);
        border: 1px solid rgba(244, 63, 94, 0.25);
        border-radius: 16px;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.75rem;
        animation: successSlideIn 0.4s ease forwards;
    }

    .fp-error-card.show {
        display: flex;
        align-items: center;
        gap: 0.875rem;
    }

    .fp-error-icon {
        color: #f43f5e;
        font-size: 1.2rem;
        flex-shrink: 0;
        animation: shake 0.4s ease;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25%       { transform: translateX(-5px); }
        75%       { transform: translateX(5px); }
    }

    .fp-error-text {
        font-size: 0.875rem;
        font-weight: 600;
        color: #be123c;
        margin: 0;
    }
    </style>

    <div class="login-page-wrapper">
        <div class="login-split-container">

            <!-- Left Branding Panel (Hidden on Mobile) -->
            <div class="login-branding-panel">
                <div class="branding-overlay"></div>
                <div class="branding-content">
                    <div class="branding-logo">
                        <i class="fas fa-tshirt"></i>
                        <span>LaundryPro</span>
                    </div>

                    <h2 class="branding-title">Keamanan Akun Anda Prioritas Kami</h2>
                    <p class="branding-subtitle">Kami akan membantu Anda memulihkan akses ke akun dengan aman dan cepat.</p>

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

                <div class="branding-bg-shape-1"></div>
                <div class="branding-bg-shape-2"></div>
            </div>

            <!-- Right Form Panel -->
            <div class="login-form-panel">
                <div class="bg-decoration">
                    <div class="bg-shape"></div>
                    <div class="bg-shape"></div>
                </div>

                <div class="login-form-container">
                    <!-- Logo on Mobile -->
                    <div class="mobile-logo-section">
                        <div class="logo-icon">
                            <i class="fas fa-tshirt"></i>
                        </div>
                        <h1 class="logo-title">LaundryPro</h1>
                        <p class="logo-subtitle">Lupa Kata Sandi</p>
                    </div>

                    <!-- Desktop Header -->
                    <div class="form-header-desktop">
                        <h2 class="form-title">Lupa Kata Sandi?</h2>
                        <p class="form-subtitle">Masukkan email terdaftar Anda, kami akan mengirimkan tautan pemulihan.</p>
                    </div>

                    <!-- Premium Success Alert (animated pulse) -->
                    <div class="fp-success-card {{ session('status') ? 'show' : '' }}"
                         id="successMessage"
                         style="{{ session('status') ? '' : 'display:none;' }}">
                        <div class="fp-success-inner">
                            <div class="fp-success-icon-wrap">
                                <div class="pulse-ring"></div>
                                <div class="fp-success-icon">
                                    <i class="fas fa-paper-plane"></i>
                                </div>
                            </div>
                            <div class="fp-success-body">
                                <p class="fp-success-title">Email Berhasil Dikirim!</p>
                                <p class="fp-success-text" id="successText">{{ session('status') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Error Alert -->
                    <div class="fp-error-card" id="errorMessage">
                        <i class="fas fa-exclamation-circle fp-error-icon"></i>
                        <p class="fp-error-text" id="errorText"></p>
                    </div>

                    <!-- Form -->
                    <form id="forgotPasswordForm" method="POST" action="{{ route('password.email') }}">
                        @csrf

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

                        <button type="submit" class="btn-submit mt-4" id="submitBtn">
                            <span class="spinner"></span>
                            <span class="btn-text">Kirim Tautan Reset</span>
                        </button>
                    </form>

                    <!-- Footer -->
                    <div class="login-footer">
                        <p class="footer-text">
                            <a href="{{ route('login') }}" class="forgot-link">
                                <i class="fas fa-arrow-left me-1"></i> Kembali ke Halaman Masuk
                            </a>
                        </p>
                        <p class="footer-text mt-3">© 2026 LaundryPro. Hak cipta dilindungi.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-guest-layout>
