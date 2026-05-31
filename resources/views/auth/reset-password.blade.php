<x-guest-layout>
    @vite(['resources/css/auth/login.css', 'resources/js/auth/reset-password.js'])

    <style>
    /* ============================================================
       Password Strength Meter
    ============================================================ */
    .strength-track {
        height: 4px;
        background: #E2E8F0;
        border-radius: 99px;
        margin-top: 8px;
        overflow: hidden;
    }

    .strength-bar {
        height: 100%;
        width: 0%;
        border-radius: 99px;
        transition: width 0.4s ease, background 0.4s ease;
    }

    .strength-label {
        font-size: 0.75rem;
        font-weight: 600;
        margin-top: 4px;
        min-height: 1rem;
        transition: color 0.3s ease;
    }

    /* ============================================================
       Success Card (matches forgot-password style)
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

    .fp-success-card.show { display: block; }

    @keyframes successSlideIn {
        from { opacity: 0; transform: translateY(-16px) scale(0.97); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }

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

    .fp-success-card::after {
        content: '';
        position: absolute;
        top: 0; left: -75%;
        width: 50%; height: 100%;
        background: linear-gradient(120deg, transparent 0%, rgba(255,255,255,0.45) 50%, transparent 100%);
        animation: shimmerSweep 3s ease-in-out infinite;
        pointer-events: none;
    }

    @keyframes shimmerSweep {
        0%  { left: -75%; }
        60% { left: 125%; }
        100%{ left: 125%; }
    }

    .fp-success-inner {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        position: relative;
        z-index: 1;
    }

    .fp-success-icon-wrap {
        position: relative;
        flex-shrink: 0;
        width: 46px; height: 46px;
    }

    .fp-success-icon-wrap .pulse-ring {
        position: absolute;
        inset: -4px;
        border-radius: 50%;
        background: rgba(16, 185, 129, 0.2);
        animation: pulseRing 1.8s ease-out infinite;
    }

    @keyframes pulseRing {
        0%  { transform: scale(1);   opacity: 0.7; }
        70% { transform: scale(1.5); opacity: 0;   }
        100%{ transform: scale(1.5); opacity: 0;   }
    }

    .fp-success-icon {
        position: relative;
        width: 46px; height: 46px;
        background: linear-gradient(135deg, #10b981, #059669);
        border-radius: 50%;
        display: flex;
        align-items: center; justify-content: center;
        color: white;
        font-size: 1.15rem;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
        animation: iconBounce 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) 0.2s both;
    }

    @keyframes iconBounce {
        from { transform: scale(0) rotate(-45deg); }
        to   { transform: scale(1) rotate(0deg); }
    }

    .fp-success-body { flex: 1; }

    .fp-success-title {
        font-size: 0.975rem; font-weight: 700;
        color: #065f46; margin: 0 0 0.3rem;
    }

    .fp-success-text {
        font-size: 0.84rem; color: #047857;
        margin: 0; line-height: 1.5;
    }

    /* Error Card */
    .fp-error-card {
        display: none;
        background: linear-gradient(135deg, #fff1f2 0%, #ffe4e6 100%);
        border: 1px solid rgba(244, 63, 94, 0.25);
        border-radius: 16px;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.75rem;
    }

    .fp-error-card.show { display: flex; align-items: center; gap: 0.875rem; }

    .fp-error-icon { color: #f43f5e; font-size: 1.2rem; flex-shrink: 0; animation: shake 0.4s ease; }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25%       { transform: translateX(-5px); }
        75%       { transform: translateX(5px); }
    }

    .fp-error-text { font-size: 0.875rem; font-weight: 600; color: #be123c; margin: 0; }

    /* Redirect countdown bar */
    .redirect-bar-wrap {
        height: 3px;
        background: #d1fae5;
        border-radius: 99px;
        margin-top: 0.75rem;
        overflow: hidden;
    }

    .redirect-bar {
        height: 100%;
        width: 100%;
        background: #10b981;
        border-radius: 99px;
        animation: drainBar 2.4s linear forwards;
    }

    @keyframes drainBar {
        from { width: 100%; }
        to   { width: 0%; }
    }
    </style>

    <div class="login-page-wrapper">
        <div class="login-split-container">

            <!-- Left Branding Panel -->
            <div class="login-branding-panel">
                <div class="branding-overlay"></div>
                <div class="branding-content">
                    <div class="branding-logo">
                        <i class="fas fa-tshirt"></i>
                        <span>LaundryPro</span>
                    </div>

                    <h2 class="branding-title">Buat Kata Sandi Baru yang Kuat</h2>
                    <p class="branding-subtitle">Pastikan kata sandi baru Anda unik dan tidak pernah digunakan di tempat lain.</p>

                    <div class="branding-feature-card">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-lock"></i>
                            </div>
                            <div class="feature-text">
                                <h4>Tips Kata Sandi Kuat</h4>
                                <p>Gunakan kombinasi huruf besar, angka, dan simbol agar akun Anda lebih aman.</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="feature-text">
                                <h4>Keamanan Dijamin</h4>
                                <p>Kata sandi Anda dienkripsi dan tidak dapat dilihat oleh siapapun termasuk tim kami.</p>
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
                    <!-- Mobile Logo -->
                    <div class="mobile-logo-section">
                        <div class="logo-icon">
                            <i class="fas fa-tshirt"></i>
                        </div>
                        <h1 class="logo-title">LaundryPro</h1>
                        <p class="logo-subtitle">Atur Ulang Kata Sandi</p>
                    </div>

                    <!-- Desktop Header -->
                    <div class="form-header-desktop">
                        <h2 class="form-title">Buat Kata Sandi Baru</h2>
                        <p class="form-subtitle">Masukkan kata sandi baru Anda di bawah ini untuk menyelesaikan proses pemulihan akun.</p>
                    </div>

                    <!-- Success Card -->
                    <div class="fp-success-card" id="successMessage" style="display:none;">
                        <div class="fp-success-inner">
                            <div class="fp-success-icon-wrap">
                                <div class="pulse-ring"></div>
                                <div class="fp-success-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                            </div>
                            <div class="fp-success-body">
                                <p class="fp-success-title">Kata Sandi Berhasil Diubah!</p>
                                <p class="fp-success-text" id="successText">Mengalihkan ke halaman masuk...</p>
                                <div class="redirect-bar-wrap">
                                    <div class="redirect-bar"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Error Card -->
                    <div class="fp-error-card" id="errorMessage">
                        <i class="fas fa-exclamation-circle fp-error-icon"></i>
                        <p class="fp-error-text" id="errorText"></p>
                    </div>

                    <!-- Reset Password Form -->
                    <form id="resetPasswordForm" method="POST" action="{{ route('password.store') }}">
                        @csrf

                        {{-- Hidden token --}}
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        {{-- Email --}}
                        <x-form.input
                            label="Alamat Email"
                            type="email"
                            name="email"
                            placeholder="Email terdaftar akun Anda"
                            autocomplete="username"
                            value="{{ old('email', $request->email) }}"
                            required
                            autofocus
                            icon="fas fa-envelope"
                        />

                        {{-- Password --}}
                        <div class="form-group">
                            <label class="form-label">Kata Sandi Baru</label>
                            <div class="input-wrapper">
                                <i class="fas fa-lock input-icon"></i>
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    class="form-control"
                                    placeholder="Masukkan kata sandi baru"
                                    autocomplete="new-password"
                                    required
                                    style="padding-right: 3rem;"
                                >
                                <button type="button" class="password-toggle" tabindex="-1">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <!-- Strength Meter -->
                            <div class="strength-track">
                                <div class="strength-bar" id="strengthBar"></div>
                            </div>
                            <div class="strength-label" id="strengthLabel"></div>
                        </div>

                        {{-- Confirm Password --}}
                        <div class="form-group">
                            <label class="form-label">Konfirmasi Kata Sandi</label>
                            <div class="input-wrapper">
                                <i class="fas fa-lock-open input-icon"></i>
                                <input
                                    id="password_confirmation"
                                    type="password"
                                    name="password_confirmation"
                                    class="form-control"
                                    placeholder="Ulangi kata sandi baru"
                                    autocomplete="new-password"
                                    required
                                    style="padding-right: 3rem;"
                                >
                                <button type="button" class="password-toggle" tabindex="-1">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Submit -->
                        <button type="submit" class="btn-submit mt-2" id="submitBtn">
                            <span class="spinner"></span>
                            <span class="btn-text">Simpan Kata Sandi Baru</span>
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
