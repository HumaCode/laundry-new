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

    // ─── Helpers ──────────────────────────────────────────────────────────────

    function formatTime(seconds) {
        const m = Math.floor(seconds / 60);
        const s = seconds % 60;
        return `${m}:${String(s).padStart(2, '0')}`;
    }

    // Extract seconds from throttle message or default to 600 (10 min)
    function extractSeconds(message) {
        const detikMatch = String(message).match(/(\d+)\s*detik/);
        if (detikMatch) return parseInt(detikMatch[1], 10);
        const menitMatch = String(message).match(/(\d+)\s*menit/);
        if (menitMatch) return parseInt(menitMatch[1], 10) * 60;
        return 600;
    }

    function isThrottleMessage(msg) {
        if (!msg) return false;
        const lower = String(msg).toLowerCase();
        return lower.includes('blokir')
            || lower.includes('terlalu banyak')
            || lower.includes('throttle')
            || lower.includes('too many')
            || lower.match(/\d+\s*(detik|menit)/) !== null;
    }

    // ─── Lockout countdown UI ─────────────────────────────────────────────────
    let lockoutInterval = null;

    function startLockoutUI(totalSeconds) {
        const submitBtn = $('#submitBtn');
        const btnText   = submitBtn.find('.btn-text');
        const errorCard = $('.field-error-box');
        const errorText = $('#errorText');

        submitBtn.addClass('locked').prop('disabled', true);
        btnText.text('Akun Terkunci 🔒');

        const minutes = Math.ceil(totalSeconds / 60);
        errorText.html(
            `🔒 Terlalu banyak percobaan gagal. Akun diblokir sementara.<br>` +
            `<small style="font-weight:500;margin-top:4px;display:block;">` +
            `Coba lagi dalam: <strong id="lockoutTimer">${formatTime(totalSeconds)}</strong>` +
            `</small>`
        );
        errorCard.addClass('locked-out show').show();

        if (typeof window.showToast === 'function') {
            window.showToast(
                `Akun diblokir ${minutes} menit karena terlalu banyak percobaan gagal.`,
                'error',
                '🔒 Akun Terkunci'
            );
        }

        let remaining = totalSeconds;
        clearInterval(lockoutInterval);

        lockoutInterval = setInterval(() => {
            remaining--;
            const el = document.getElementById('lockoutTimer');
            if (el) el.textContent = formatTime(remaining);

            if (remaining <= 0) {
                clearInterval(lockoutInterval);
                submitBtn.removeClass('locked').prop('disabled', false);
                btnText.text('Masuk');
                errorCard.hide().removeClass('show locked-out');
                errorText.html('');
                if (typeof window.showToast === 'function') {
                    window.showToast('Akun Anda sudah dapat digunakan kembali.', 'success', '✅ Blokir Dicabut');
                }
            }
        }, 1000);
    }

    function showNormalError(errorCard, message) {
        $('#errorText').text(message);
        errorCard.removeClass('locked-out').addClass('show').show();
        if (typeof window.showToast === 'function') window.showToast(message, 'error');
    }

    // ─── AJAX Login ───────────────────────────────────────────────────────────
    const loginForm = document.getElementById('loginForm');
    if (!loginForm) return;

    loginForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const form      = $(this);
        const submitBtn = $('#submitBtn');
        const btnText   = submitBtn.find('.btn-text');
        const errorCard = $('.field-error-box');

        if (submitBtn.hasClass('locked')) return;

        // Loading state
        submitBtn.addClass('loading').prop('disabled', true);
        btnText.text('Sedang proses...');
        errorCard.hide().removeClass('show locked-out');
        $('#errorText').html('');

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

                submitBtn.removeClass('loading').prop('disabled', false);
                btnText.text('Masuk');

                const json   = xhr.responseJSON || {};
                const status = xhr.status;

                // ── 429 Too Many Requests (route-level throttle) ──
                if (status === 429) {
                    const retryAfter = parseInt(xhr.getResponseHeader('Retry-After') || '600', 10);
                    return startLockoutUI(retryAfter);
                }

                // ── 419 Session expired ──
                if (status === 419) {
                    return showNormalError(errorCard, 'Sesi telah berakhir. Silakan muat ulang halaman.');
                }

                // ── 200 OK Parser Error (Successful redirect / HTML response) ──
                if (status === 200) {
                    if (typeof window.showToast === 'function') {
                        window.showToast('Login berhasil! Mengalihkan...', 'success');
                    }
                    setTimeout(() => {
                        window.location.href = '/dashboard';
                    }, 1000);
                    return;
                }

                // ── 422 Validation / Auth failed ──
                if (status === 422) {
                    let msg = '';

                    if (json.errors && Object.keys(json.errors).length > 0) {
                        const firstKey = Object.keys(json.errors)[0];
                        const firstArr = json.errors[firstKey];
                        msg = Array.isArray(firstArr) ? firstArr[0] : firstArr;
                    } else if (json.message) {
                        msg = json.message;
                    }

                    if (!msg) msg = 'Terjadi kesalahan. Silakan coba lagi.';

                    // Detect throttle message
                    if (isThrottleMessage(msg)) {
                        return startLockoutUI(extractSeconds(msg));
                    }

                    return showNormalError(errorCard, msg);
                }

                // ── Generic fallback ──
                console.error('AJAX Login Error:', status, xhr.statusText, xhr.responseText);
                let fallbackMsg = `Terjadi kesalahan server (${status}).`;
                if (xhr.statusText) {
                    fallbackMsg += ` Detail: ${xhr.statusText}`;
                }
                if (xhr.responseText && !xhr.responseText.includes('<html') && !xhr.responseText.includes('<!DOCTYPE')) {
                    fallbackMsg += ` - ${xhr.responseText.substring(0, 80)}`;
                }
                showNormalError(errorCard, fallbackMsg);
            },
        });
    });
});
