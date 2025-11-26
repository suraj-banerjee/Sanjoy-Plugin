<?php

/**
 * Template for Forgot Password Page
 * Allows users to request password reset link
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
?>

<div class="skd-forgot-password-container">
    <div class="skd-forgot-password-wrapper">
        <div class="skd-forgot-password-card">
            <div class="skd-forgot-password-header">
                <h2>Forgot Password?</h2>
                <p>Enter your email address and we'll send you a link to reset your password.</p>
            </div>

            <div id="skd-forgot-message"></div>

            <form id="skd-forgot-password-form" class="skd-form">
                <div class="skd-form-group">
                    <label for="forgot_email">Email Address</label>
                    <input
                        type="email"
                        id="forgot_email"
                        name="email"
                        required
                        placeholder="your@email.com"
                        class="skd-form-control">
                </div>

                <button type="submit" class="skd-btn skd-btn-primary skd-btn-block">
                    Send Reset Link
                </button>
            </form>

            <div class="skd-forgot-password-footer">
                <p>Remember your password? <a href="<?php echo home_url('/login/'); ?>">Back to Login</a></p>
                <p>Don't have an account? <a href="<?php echo home_url('/register/'); ?>">Register Now</a></p>
            </div>
        </div>
    </div>
</div>

<style>
    .skd-forgot-password-container {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .skd-forgot-password-wrapper {
        width: 100%;
        max-width: 450px;
    }

    .skd-forgot-password-card {
        background: white;
        border-radius: 12px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    }

    .skd-forgot-password-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .skd-forgot-password-header h2 {
        margin: 0 0 10px;
        font-size: 28px;
        color: #2c3e50;
    }

    .skd-forgot-password-header p {
        margin: 0;
        color: #7f8c8d;
        font-size: 14px;
        line-height: 1.6;
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

    .skd-forgot-password-footer {
        margin-top: 25px;
        text-align: center;
    }

    .skd-forgot-password-footer p {
        margin: 10px 0;
        color: #7f8c8d;
        font-size: 14px;
    }

    .skd-forgot-password-footer a {
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
    }

    .skd-forgot-password-footer a:hover {
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
        .skd-forgot-password-card {
            padding: 30px 20px;
        }

        .skd-forgot-password-header h2 {
            font-size: 24px;
        }
    }
</style>