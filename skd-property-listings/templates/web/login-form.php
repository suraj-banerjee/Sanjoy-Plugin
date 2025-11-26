<?php

/**
 * Template: Login Form
 * Description: User login form for interiAssist
 */

if (!defined('ABSPATH')) {
    exit;
}

// Redirect if already logged in
if (is_user_logged_in()) {
    $user_type = get_user_meta(get_current_user_id(), 'skd_user_type', true);
    $redirect_url = SKD_PL_Registration::get_dashboard_url($user_type);
    wp_redirect($redirect_url);
    exit;
}
?>

<div class="skd-login-wrapper">
    <div class="skd-login-container">
        <div class="skd-form-header">
            <h2>Welcome Back!</h2>
            <p>Login to your interiAssist account</p>
        </div>

        <div id="skd-login-messages"></div>

        <form id="skd-login-form" class="skd-form">
            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('skd_login_nonce'); ?>">

            <div class="skd-form-group">
                <label for="username">Username or Email</label>
                <input type="text" name="username" id="username" required placeholder="Enter your username or email">
            </div>

            <div class="skd-form-group">
                <label for="password">Password</label>
                <div class="skd-password-wrapper">
                    <input type="password" name="password" id="password" required placeholder="Enter your password">
                    <button type="button" class="skd-toggle-password" aria-label="Toggle password visibility">
                        <svg class="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <svg class="eye-slash-icon" style="display:none;" width="20" height="20" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor">
                            <path
                                d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24">
                            </path>
                            <line x1="1" y1="1" x2="23" y2="23"></line>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="skd-form-group skd-form-row">
                <label class="skd-checkbox-wrapper">
                    <input type="checkbox" name="remember" value="true">
                    <span class="skd-checkbox-custom"></span>
                    <span class="skd-checkbox-label-text">Remember me</span>
                </label>
                <a href="<?php echo home_url('/forgot-password/'); ?>" class="skd-forgot-password">Forgot password?</a>
            </div>
            <div class=" skd-form-group-btn_Row">
                <button type="submit" class="skd-btn skd-btn-primary skd-btn-block" id="skd-login-btn">
                    <span class="btn-text">Login</span>
                    <span class="btn-loader" style="display: none;">
                        <span class="spinner"></span> Logging in...
                    </span>
                </button>
            </div>
        </form>

        <div class="skd-form-footer">
            <p>Don't have an account? <a href="<?php echo home_url('/register/'); ?>">Sign up now</a></p>
        </div>

        <div class="skd-social-login" style="display: none;">
            <div class="skd-divider">
                <span>Or continue with</span>
            </div>
            <div class="skd-social-buttons">
                <button class="skd-social-btn skd-google-btn">
                    <svg width="18" height="18" viewBox="0 0 18 18">
                        <path fill="#4285F4"
                            d="M16.51 8H8.98v3h4.3c-.18 1-.74 1.48-1.6 2.04v2.01h2.6a7.8 7.8 0 0 0 2.38-5.88c0-.57-.05-.66-.15-1.18z" />
                        <path fill="#34A853"
                            d="M8.98 17c2.16 0 3.97-.72 5.3-1.94l-2.6-2a4.8 4.8 0 0 1-7.18-2.54H1.83v2.07A8 8 0 0 0 8.98 17z" />
                        <path fill="#FBBC05"
                            d="M4.5 10.52a4.8 4.8 0 0 1 0-3.04V5.41H1.83a8 8 0 0 0 0 7.18l2.67-2.07z" />
                        <path fill="#EA4335"
                            d="M8.98 4.18c1.17 0 2.23.4 3.06 1.2l2.3-2.3A8 8 0 0 0 1.83 5.4L4.5 7.49a4.77 4.77 0 0 1 4.48-3.3z" />
                    </svg>
                    Google
                </button>
                <button class="skd-social-btn skd-linkedin-btn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#0077B5">
                        <path
                            d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                    </svg>
                    LinkedIn
                </button>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Initialize jQuery Validation
    $('#skd-login-form').validate({
        rules: {
            username: {
                required: true,
                minlength: 3
            },
            password: {
                required: true,
                minlength: 6
            }
        },
        messages: {
            username: {
                required: 'Please enter your username or email',
                minlength: 'Username must be at least 3 characters'
            },
            password: {
                required: 'Please enter your password',
                minlength: 'Password must be at least 6 characters'
            }
        },
        errorElement: 'span',
        errorClass: 'error-message',
        errorPlacement: function(error, element) {
            error.insertAfter(element.parent());
        },
        highlight: function(element) {
            $(element).addClass('error-field');
        },
        unhighlight: function(element) {
            $(element).removeClass('error-field');
        },
        submitHandler: function(form) {
            var $form = $(form);
            var $btn = $('#skd-login-btn');
            var $messages = $('#skd-login-messages');

            // Disable button and show loader
            $btn.prop('disabled', true);
            $btn.find('.btn-text').hide();
            $btn.find('.btn-loader').show();
            $messages.html('');

            // Collect form data
            var formData = {
                action: 'skd_login_user',
                username: $form.find('[name="username"]').val(),
                password: $form.find('[name="password"]').val(),
                remember: $form.find('[name="remember"]').is(':checked'),
                nonce: $form.find('[name="nonce"]').val()
            };

            $.ajax({
                url: skd_ajax.ajax_url,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $messages.html('<div class="skd-message skd-message-success">' +
                            response.data.message + '</div>');
                        setTimeout(function() {
                            window.location.href = response.data.redirect;
                        }, 1000);
                    } else {
                        $messages.html('<div class="skd-message skd-message-error">' +
                            response.data.message + '</div>');
                        $btn.prop('disabled', false);
                        $btn.find('.btn-text').show();
                        $btn.find('.btn-loader').hide();
                    }
                },
                error: function() {
                    $messages.html(
                        '<div class="skd-message skd-message-error">An error occurred. Please try again.</div>'
                    );
                    $btn.prop('disabled', false);
                    $btn.find('.btn-text').show();
                    $btn.find('.btn-loader').hide();
                }
            });
        }
    });
});
</script>