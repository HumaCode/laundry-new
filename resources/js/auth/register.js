document.addEventListener('DOMContentLoaded', () => {
    // Toggle Password Visibility for password field
    const toggleBtn = document.getElementById('passwordToggleBtn');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            const passwordInput = document.getElementById('password');
            const toggleIcon = toggleBtn.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        });
    }

    // Toggle Password Visibility for password confirmation field
    const confirmToggleBtn = document.getElementById('password_confirmationToggleBtn');
    if (confirmToggleBtn) {
        confirmToggleBtn.addEventListener('click', () => {
            const confirmInput = document.getElementById('password_confirmation');
            const toggleIcon = confirmToggleBtn.querySelector('i');
            
            if (confirmInput.type === 'password') {
                confirmInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                confirmInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        });
    }

    // Input Focus Animation
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
    });

    // Handle AJAX Register Form Submission
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = $(this);
            const submitBtn = $('#submitBtn');
            const submitBtnText = submitBtn.find('.btn-text');
            const errorMessage = $('#errorMessage');

            // Set loading state on submit button
            submitBtn.addClass('loading').prop('disabled', true);
            submitBtnText.text('Sedang proses...');

            // Show loading dialog overlay
            if (typeof window.showLoadingDialog === 'function') {
                window.showLoadingDialog();
            }

            // Hide error message if any
            errorMessage.removeClass('show');

            $.ajax({
                url: form.attr('action'),
                method: form.attr('method'),
                data: form.serialize(),
                dataType: 'json',
                headers: {
                    'Accept': 'application/json'
                },
                success: function(response) {
                    // Hide loading dialog
                    if (typeof window.hideLoadingDialog === 'function') {
                        window.hideLoadingDialog();
                    }

                    // Show success toast
                    if (typeof window.showToast === 'function') {
                        window.showToast(`Pendaftaran berhasil, selamat bergabung ${response.user.name}!`, 'success');
                    }

                    // Redirect after a short delay so user can see the toast
                    setTimeout(() => {
                        window.location.href = response.redirect;
                    }, 1500);
                },
                error: function(xhr) {
                    // Reset button loading state
                    submitBtn.removeClass('loading').prop('disabled', false);
                    submitBtnText.text('Daftar');

                    // Hide loading dialog
                    if (typeof window.hideLoadingDialog === 'function') {
                        window.hideLoadingDialog();
                    }

                    let errorMsgText = 'Terjadi kesalahan. Silakan coba lagi.';

                    if (xhr.status === 422) {
                        const response = xhr.responseJSON;
                        if (response && response.errors) {
                            // Get first error message
                            const firstErrorKey = Object.keys(response.errors)[0];
                            errorMsgText = response.errors[firstErrorKey][0];
                        }
                    } else if (xhr.status === 419) {
                        errorMsgText = 'Sesi telah berakhir. Silakan muat ulang halaman.';
                    }

                    // Show error toast
                    if (typeof window.showToast === 'function') {
                        window.showToast(errorMsgText, 'error');
                    }

                    // Show error in register form
                    errorMessage.find('#errorText').text(errorMsgText);
                    errorMessage.addClass('show');
                }
            });
        });
    }
});
