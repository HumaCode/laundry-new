document.addEventListener('DOMContentLoaded', () => {
    // Password toggle visibility
    document.querySelectorAll('.password-toggle').forEach(btn => {
        btn.addEventListener('click', function () {
            const input = this.closest('.input-wrapper').querySelector('input');
            const icon  = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    });

    // Password strength meter
    const passwordInput   = document.getElementById('password');
    const strengthBar     = document.getElementById('strengthBar');
    const strengthLabel   = document.getElementById('strengthLabel');

    if (passwordInput && strengthBar) {
        passwordInput.addEventListener('input', function () {
            const val      = this.value;
            let   strength = 0;

            if (val.length >= 8)                         strength++;
            if (/[A-Z]/.test(val))                       strength++;
            if (/[0-9]/.test(val))                       strength++;
            if (/[^A-Za-z0-9]/.test(val))                strength++;

            const levels = [
                { pct: 0,   color: '#e2e8f0', label: '' },
                { pct: 25,  color: '#f43f5e', label: 'Lemah' },
                { pct: 50,  color: '#fb923c', label: 'Cukup' },
                { pct: 75,  color: '#facc15', label: 'Kuat' },
                { pct: 100, color: '#10b981', label: 'Sangat Kuat' },
            ];

            const level = levels[strength];
            strengthBar.style.width        = level.pct + '%';
            strengthBar.style.background   = level.color;
            strengthLabel.textContent      = level.label;
            strengthLabel.style.color      = level.color;
        });
    }

    // AJAX form submit
    const resetForm = document.getElementById('resetPasswordForm');
    if (!resetForm) return;

    resetForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const form        = $(this);
        const submitBtn   = $('#submitBtn');
        const btnText     = submitBtn.find('.btn-text');
        const errorCard   = $('#errorMessage');
        const successCard = $('#successMessage');

        // Reset UI
        errorCard.hide().removeClass('show');
        successCard.hide().removeClass('show');

        // Loading state
        submitBtn.addClass('loading').prop('disabled', true);
        btnText.text('Memproses...');

        if (typeof window.showLoadingDialog === 'function') window.showLoadingDialog();

        $.ajax({
            url    : form.attr('action'),
            method : form.attr('method'),
            data   : form.serialize(),
            dataType: 'json',
            headers: { 'Accept': 'application/json' },

            success(response) {
                if (typeof window.hideLoadingDialog === 'function') window.hideLoadingDialog();

                submitBtn.removeClass('loading').prop('disabled', false);
                btnText.text('Simpan Kata Sandi Baru');

                if (typeof window.showToast === 'function') {
                    window.showToast(response.message ?? 'Kata sandi berhasil diubah!', 'success', 'Berhasil!');
                }

                // Show success card then redirect
                $('#successText').text(response.message ?? 'Kata sandi berhasil diubah!');
                successCard.css('display', 'block').addClass('show');

                setTimeout(() => {
                    window.location.href = response.redirect ?? '/login';
                }, 2500);
            },

            error(xhr) {
                if (typeof window.hideLoadingDialog === 'function') window.hideLoadingDialog();

                submitBtn.removeClass('loading').prop('disabled', false);
                btnText.text('Simpan Kata Sandi Baru');

                let msg = 'Terjadi kesalahan. Silakan coba lagi.';

                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    const firstKey = Object.keys(xhr.responseJSON.errors)[0];
                    msg = xhr.responseJSON.errors[firstKey][0];
                } else if (xhr.status === 419) {
                    msg = 'Sesi telah berakhir. Silakan muat ulang halaman.';
                }

                if (typeof window.showToast === 'function') window.showToast(msg, 'error');

                $('#errorText').text(msg);
                errorCard.css('display', 'flex').addClass('show');
            },
        });
    });
});
