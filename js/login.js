$(document).ready(function() {
    // Form validation patterns
    const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d@$!%*?&]{6,}$/;

    // Real-time validation
    $('#email').on('blur', function() {
        validateEmail();
    });

    $('#password').on('blur', function() {
        validatePassword();
    });

    // Email validation function
    function validateEmail() {
        const email = $('#email').val().trim();
        const emailField = $('#email');
        
        if (email === '') {
            showFieldError(emailField, 'Email is required');
            return false;
        } else if (!emailPattern.test(email)) {
            showFieldError(emailField, 'Please enter a valid email address');
            return false;
        } else {
            showFieldSuccess(emailField);
            return true;
        }
    }

    // Password validation function
    function validatePassword() {
        const password = $('#password').val();
        const passwordField = $('#password');
        
        if (password === '') {
            showFieldError(passwordField, 'Password is required');
            return false;
        } else if (password.length < 6) {
            showFieldError(passwordField, 'Password must be at least 6 characters long');
            return false;
        } else {
            showFieldSuccess(passwordField);
            return true;
        }
    }

    // Show field error
    function showFieldError(field, message) {
        field.removeClass('is-valid').addClass('is-invalid');
        let feedback = field.siblings('.invalid-feedback');
        if (feedback.length === 0) {
            feedback = $('<div class="invalid-feedback"></div>');
            field.after(feedback);
        }
        feedback.text(message);
    }

    // Show field success
    function showFieldSuccess(field) {
        field.removeClass('is-invalid').addClass('is-valid');
        field.siblings('.invalid-feedback').remove();
    }

    // Clear validation on input
    $('#email, #password').on('input', function() {
        $(this).removeClass('is-valid is-invalid');
        $(this).siblings('.invalid-feedback').remove();
    });

    // Form submission
    $('#login-form').submit(function(e) {
        e.preventDefault();

        // Validate all fields
        const isEmailValid = validateEmail();
        const isPasswordValid = validatePassword();

        if (!isEmailValid || !isPasswordValid) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please fix the errors above before submitting.',
            });
            return;
        }

        // Get form data
        const email = $('#email').val().trim();
        const password = $('#password').val();

        // Show loading state
        const submitBtn = $('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Logging in...');

        // AJAX request
        $.ajax({
            url: '../actions/login_customer_action.php',
            type: 'POST',
            data: {
                email: email,
                password: password
            },
            success: function(response) {
                if (response.status === 'success') {
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Redirect to index page
                        window.location.href = response.redirect_url || '../index.php';
                    });
                } else {
                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Login Failed',
                        text: response.message,
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Connection Error',
                    text: 'Unable to connect to server. Please check your internet connection and try again.',
                });
            },
            complete: function() {
                // Reset button state
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Add loading animation to form
    function addLoadingAnimation() {
        $('.card').addClass('animate-pulse-custom');
        $('input, button').addClass('animate__animated animate__pulse');
    }

    function removeLoadingAnimation() {
        $('.card').removeClass('animate-pulse-custom');
        $('input, button').removeClass('animate__animated animate__pulse');
    }

    // Add some interactive effects
    $('.form-control').on('focus', function() {
        $(this).parent().addClass('animate__animated animate__pulse');
    });

    $('.form-control').on('blur', function() {
        $(this).parent().removeClass('animate__animated animate__pulse');
    });

    // Add enter key support for form submission
    $('#email, #password').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            $('#login-form').submit();
        }
    });
});
