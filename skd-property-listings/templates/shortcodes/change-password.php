<?php

/**
 * Template for Password Change Form
 * To be included in user dashboard
 */

if (!defined('ABSPATH')) {
    exit;
}

// Check if user is logged in
if (!is_user_logged_in()) {
    echo '<p>Please login to change your password.</p>';
    return;
}
?>

<div class="skd-password-change-section">
    <div class="skd-section-header">
        <h3>Change Password</h3>
        <p>Update your account password</p>
    </div>

    <div id="skd-password-message"></div>

    <form id="skd-change-password-form" class="skd-form">
        <div class="skd-form-row">
            <div class="skd-form-group">
                <label for="current_password">Current Password <span class="required">*</span></label>
                <div class="skd-password-wrapper">
                    <input
                        type="password"
                        id="current_password"
                        name="current_password"
                        required
                        placeholder="Enter current password"
                        class="skd-form-control"
                        autocomplete="current-password">
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
        </div>

        <div class="skd-form-row">
            <div class="skd-form-group">
                <label for="new_password">New Password <span class="required">*</span></label>
                <div class="skd-password-wrapper">
                    <input
                        type="password"
                        id="new_password"
                        name="new_password"
                        required
                        placeholder="Minimum 8 characters"
                        class="skd-form-control"
                        minlength="8"
                        autocomplete="new-password">
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
        </div>

        <div class="skd-form-row">
            <div class="skd-form-group">
                <label for="confirm_password">Confirm New Password <span class="required">*</span></label>
                <div class="skd-password-wrapper">
                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        required
                        placeholder="Re-enter new password"
                        class="skd-form-control"
                        minlength="8"
                        autocomplete="new-password">
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
        </div>

        <div class="skd-form-actions">
            <button type="submit" class="skd-btn skd-btn-primary">
                Change Password
            </button>
        </div>
    </form>
</div>

<style>
    .skd-password-change-section {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        max-width: 600px;
    }

    .skd-section-header {
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f0f0;
    }

    .skd-section-header h3 {
        margin: 0 0 5px;
        font-size: 24px;
        color: #2c3e50;
    }

    .skd-section-header p {
        margin: 0;
        color: #7f8c8d;
        font-size: 14px;
    }

    .skd-form-row {
        margin-bottom: 20px;
    }

    .skd-form-group {
        width: 100%;
    }

    .skd-form-group label {
        display: block;
        margin-bottom: 8px;
        color: #2c3e50;
        font-weight: 500;
        font-size: 14px;
    }

    .required {
        color: #e74c3c;
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

    .skd-form-actions {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #f0f0f0;
    }

    .skd-btn {
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .skd-btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .skd-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
    }

    .skd-btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
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

    @media (max-width: 768px) {
        .skd-password-change-section {
            padding: 20px;
        }
    }
</style>