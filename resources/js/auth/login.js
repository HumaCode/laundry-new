document.addEventListener('DOMContentLoaded', () => {

    // ─── Toggle Password Visibility ───────────────────────────────────────────
    const toggleBtn = document.getElementById('passwordToggleBtn');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            const passwordInput = document.getElementById('password');
            const toggleIcon    = toggleBtn.querySelector('i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    }

    // ─── Input Focus Animation ────────────────────────────────────────────────
    document.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('focus',  function () { this.parentElement.classList.add('focused'); });
        input.addEventListener('blur',   function () { this.parentElement.classList.remove('focused'); });
    });

    // ─── Lockout countdown helpers ────────────────────────────────────────────
    let lockoutInterval = null;

    function startLockoutUI(totalSeconds) {
        const submitBtn = $('#submitBtn');
        const btnText   = submitBtn.find('.btn-text');
        const errorMsg  = $('#errorMessage');
        const errorText = $('#errorText');

        // Disable button immediately
        submitBtn.addClass('locked').prop('disabled', true);

        // Show lockout card
        const minutes       = Math.ceil(totalSeconds / 60);
        const lockoutHtml   = `
            <span id="lockoutIcon" style="margin-right:.5rem;">🔒</span>
            Terlalu banyak percobaan gagal. Akun diblokir sementara.<br>
            <small style="font-weight:500;">Coba lagi dalam: <strong id="lockoutTimer">${formatTime(totalSeconds)}</strong></small>
        `;
        errorText.html(lockoutHtml);
        errorMsg.addClass('show');

        // Show toast
        if (typeof window.showToast === 'function') {
            window.showToast(
                `Akun diblokir ${minutes} menit karena terlalu banyak percobaan gagal.`,
                'error',
                '🔒 Akun Terkunci'
            );
        }

        // Countdown
        let remaining = totalSeconds;
        clearInterval(lockoutInterval);

        lockoutInterval = setInterval(() => {
            remaining--;
            const timerEl = document.getElementById('lockoutTimer');
            if (timerEl) timerEl.textContent = formatTime(remaining);

            if (remaining <= 0) {
                clearInterval(lockoutInterval);
                submitBtn.removeClass('locked').prop('disabled', false);
                btnText.text('Masuk');
                errorMsg.removeClass('show');
                if (typeof window.showToast === 'function') {
                    window.showToast('Akun Anda sudah dapat digunakan kembali.', 'info', 'Blokir Dicabut');
                }
            }
        }, 1000);
    }

    function formatTime(seconds) {
        const m = Math.floor(seconds / 60);
        const s = seconds % 60;
        return `${m}:${String(s).padStart(2, '0')}`;
    }

    // Extract seconds from throttle error message (fallback: 600)
    function extractSeconds(message) {
        const match = message.match(/(\d+)\s*detik/);
        if (match) return parseInt(match[1], 10);
        const minMatch = message.match(/(\d+)\s*menit/);
        if (minMatch) return parseInt(minMatch[1], 10) * 60;
        return 600;
    }

    // ─── AJAX Login ───────────────────────────────────────────────────────────
    const loginForm = document.getElementById('loginForm');
    if (!loginForm) return;

    loginForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const form      = $(this);
        const submitBtn = $('#submitBtn');
        const btnText   = submitBtn.find('.btn-text');
        const errorMsg  = $('#errorMessage');

        // Don't submit if locked out
        if (submitBtn.hasClass('locked')) return;

        // Loading state
        submitBtn.addClass('loading').prop('disabled', true);
        btnText.text('Sedang proses...');
        errorMsg.removeClass('show');

        if (typeof window.showLoadingDialog === 'function') window.showLoadingDialog();

        $.ajax({
            url     : form.attr('action'),
            method  : form.attr('method'),
            data    : form.serialize(),
            dataType: 'json',
            headers : { 'Accept': 'application/json' },

            success(response) {
                if (typeof window.hideLoadingDialog === 'function') window.hideLoadingDialog();

                if (typeof window.showToast === 'function') {
                    window.showToast(
                        `Selamat datang kembali, ${response.user.name}! 👋`,
                        'success',
                        'Login Berhasil'
                    );
                }

                setTimeout(() => { window.location.href = response.redirect; }, 1500);
            },

            error(xhr) {
                if (typeof window.hideLoadingDialog === 'function') window.hideLoadingDialog();

                // Reset button (unless we detect throttle below)
                submitBtn.removeClass('loading').prop('disabled', false);
                btnText.text('Masuk');

                let errorMsgText = 'Terjadi kesalahan. Silakan coba lagi.';
                let isThrottle   = false;

                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    const firstKey = Object.keys(xhr.responseJSON.errors)[0];
                    errorMsgText   = xhr.responseJSON.errors[firstKey][0];

                    // Detect throttle/lockout message from server
                    if (errorMsgText.toLowerCase().includes('blokir') ||
                        errorMsgText.toLowerCase().includes('terlalu banyak') ||
                        errorMsgText.toLowerCase().includes('menit') ||
                        firstKey === 'login' && errorMsgText.match(/\d+\s*(detik|menit)/)) {
                        isThrottle = true;
                    }
                } else if (xhr.status === 419) {
                    errorMsgText = 'Sesi telah berakhir. Silakan muat ulang halaman.';
                } else if (xhr.status === 429) {
                    errorMsgText = 'Terlalu banyak percobaan. Akun diblokir sementara.';
                    isThrottle   = true;
                }

                if (isThrottle) {
                    // Parse remaining seconds from server message
                    const lockSeconds = extractSeconds(errorMsgText);
                    startLockoutUI(lockSeconds);
                } else {
                    // Normal error
                    if (typeof window.showToast === 'function') window.showToast(errorMsgText, 'error');
                    $('#errorText').text(errorMsgText);
                    errorMsg.addClass('show');
                }
            },
        });
    });
});
