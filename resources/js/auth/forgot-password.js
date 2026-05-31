document.addEventListener('DOMContentLoaded', () => {
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

    // Handle AJAX Forgot Password Form Submission
    const forgotPasswordForm = document.getElementById('forgotPasswordForm');
    if (forgotPasswordForm) {
        forgotPasswordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = $(this);
            const submitBtn = $('#submitBtn');
            const submitBtnText = submitBtn.find('.btn-text');
            const errorMessage = $('#errorMessage');
            const successMessage = $('#successMessage');

            // Set loading state on submit button
            submitBtn.addClass('loading').prop('disabled', true);
            submitBtnText.text('Sedang proses...');

            // Show loading dialog overlay
            if (typeof window.showLoadingDialog === 'function') {
                window.showLoadingDialog();
            }

            // Hide error & success messages
            errorMessage.hide().removeClass('show');
            successMessage.hide().removeClass('show');

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

                    // Reset button loading state
                    submitBtn.removeClass('loading').prop('disabled', false);
                    submitBtnText.text('Kirim Tautan Reset');

                    // Show success toast
                    if (typeof window.showToast === 'function') {
                        window.showToast(response.message, 'success');
                    }

                    // Show success block in page
                    $('#successText').text(response.message);
                    successMessage.css('display', 'block').addClass('show');
                    
                    // Clear form input
                    form.find('input[type="email"]').val('');
                },
                error: function(xhr) {
                    // Reset button loading state
                    submitBtn.removeClass('loading').prop('disabled', false);
                    submitBtnText.text('Kirim Tautan Reset');

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

                    // Show error in form
                    $('#errorText').text(errorMsgText);
                    errorMessage.css('display', 'flex').addClass('show');
                }
            });
        });
    }
});
