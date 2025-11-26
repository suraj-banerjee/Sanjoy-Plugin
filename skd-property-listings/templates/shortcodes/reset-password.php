<?php

/**
 * Template for Reset Password Page
 * Allows users to set a new password using reset link
 */

if (!defined('ABSPATH')) {
    exit;
}

// Redirect if already logged in
if (is_user_logged_in()) {
    $user = wp_get_current_user();
    $user_type = get_user_meta($user->ID, 'skd_user_type', true);
    $dashboard_url = SKD_PL_Registration::get_dashboard_url($user_type);
    wp_redirect($dashboard_url);
    exit;
}

// Check if reset key and login are present
$reset_key = isset($_GET['key']) ? sanitize_text_field($_GET['key']) : '';
$user_login = isset($_GET['login']) ? sanitize_text_field($_GET['login']) : '';

if (empty($reset_key) || empty($user_login)) {
    echo '<div class="skd-alert skd-alert-error">Invalid password reset link.</div>';
    return;
}
?>

<div class="skd-reset-password-container">
    <div class="skd-reset-password-wrapper">
        <div class="skd-reset-password-card">
            <div class="skd-reset-password-header">
                <h2>Reset Password</h2>
                <p>Enter your new password below.</p>
            </div>

            <div id="skd-reset-message"></div>

            <form id="skd-reset-password-form" class="skd-form">
                <div class="skd-form-group">
                    <label for="reset_password">New Password</label>
                    <div class="skd-password-wrapper">
                        <input
                            type="password"
                            id="reset_password"
                            name="password"
                            required
                            placeholder="Minimum 8 characters"
                            class="skd-form-control"
                            minlength="8">
                        <button type="button" class="skd-toggle-password" aria-label="Toggle password visibility">
                            <svg class="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg class="eye-slash-icon" style="display:none;" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </button>
                    </div>
                    <small class="skd-form-text">Password must be at least 8 characters long</small>
                </div>

                <div class="skd-form-group">
                    <label for="reset_confirm_password">Confirm New Password</label>
                    <div class="skd-password-wrapper">
                        <input
                            type="password"
                            id="reset_confirm_password"
                            name="confirm_password"
                            required
                            placeholder="Re-enter your password"
                            class="skd-form-control"
                            minlength="8">
                        <button type="button" class="skd-toggle-password" aria-label="Toggle password visibility">
                            <svg class="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg class="eye-slash-icon" style="display:none;" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="skd-btn skd-btn-primary skd-btn-block">
                    Reset Password
                </button>
            </form>

            <div class="skd-reset-password-footer">
                <p>Remember your password? <a href="<?php echo home_url('/login/'); ?>">Back to Login</a></p>
            </div>
        </div>
    </div>
</div>

<style>
    .skd-reset-password-container {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .skd-reset-password-wrapper {
        width: 100%;
        max-width: 450px;
    }

    .skd-reset-password-card {
        background: white;
        border-radius: 12px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    }

    .skd-reset-password-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .skd-reset-password-header h2 {
        margin: 0 0 10px;
        font-size: 28px;
        color: #2c3e50;
    }

    .skd-reset-password-header p {
        margin: 0;
        color: #7f8c8d;
        font-size: 14px;
    }

    .skd-form-group {
        margin-bottom: 20px;
    }

    .skd-form-group label {
        display: block;
        margin-bottom: 8px;
        color: #2c3e50;
        font-weight: 500;
        font-size: 14px;
    }

    .skd-password-wrapper {
        position: relative;
        width: 100%;
    }

    .skd-password-wrapper input {
        width: 100% !important;
        padding: 12px 45px 12px 16px !important;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 15px;
        transition: all 0.3s ease;
        box-sizing: border-box;
    }

    .skd-password-wrapper input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .skd-toggle-password {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        padding: 5px;
        color: #666;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
    }

    .skd-toggle-password:hover {
        color: #333;
    }

    .skd-toggle-password svg {
        width: 20px;
        height: 20px;
        stroke-width: 2;
    }

    .skd-form-control {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 15px;
        transition: all 0.3s ease;
        box-sizing: border-box;
    }

    .skd-form-control:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .skd-form-text {
        display: block;
        margin-top: 5px;
        color: #7f8c8d;
        font-size: 12px;
    }

    .skd-btn {
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
    }

    .skd-btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .skd-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
    }

    .skd-btn-block {
        width: 100%;
        display: block;
    }

    .skd-reset-password-footer {
        margin-top: 25px;
        text-align: center;
    }

    .skd-reset-password-footer p {
        margin: 10px 0;
        color: #7f8c8d;
        font-size: 14px;
    }

    .skd-reset-password-footer a {
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
    }

    .skd-reset-password-footer a:hover {
        text-decoration: underline;
    }

    .skd-alert {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .skd-alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .skd-alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    @media (max-width: 576px) {
        .skd-reset-password-card {
            padding: 30px 20px;
        }

        .skd-reset-password-header h2 {
            font-size: 24px;
        }
    }
</style>