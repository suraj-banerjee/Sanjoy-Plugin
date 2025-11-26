<?php

/**
 * Template: Registration Form
 * Description: Multi-role registration form for interiAssist
 */

if (!defined('ABSPATH')) {
    exit;
}

$user_type = isset($_GET['type']) ? sanitize_text_field($_GET['type']) : 'vda';
if (!in_array($user_type, ['vda', 'employer'])) {
    $user_type = 'vda';
}

$fields = SKD_PL_Registration::get_registration_fields($user_type);
?>

<div class="skd-registration-wrapper">
    <div class="skd-registration-container">
        <!-- Role Selection Tabs -->
        <div class="skd-role-tabs">
            <a href="?type=vda" class="skd-role-tab <?php echo $user_type === 'vda' ? 'active' : ''; ?>">
                <span class="role-icon">🧑‍🎨</span>
                <span class="role-title">Virtual Design Assistant</span>
                <span class="role-desc">Join as a freelance VDA</span>
            </a>
            <a href="?type=employer" class="skd-role-tab <?php echo $user_type === 'employer' ? 'active' : ''; ?>">
                <span class="role-icon">🛋️</span>
                <span class="role-title">Interior Designer/Employer</span>
                <span class="role-desc">Find and hire talent</span>
            </a>
        </div>

        <!-- Registration Form -->
        <div class="skd-registration-form-wrapper">
            <div class="skd-form-header">
                <h2>Create Your Account</h2>
                <p>Join interiAssist and start connecting with design professionals</p>
            </div>

            <div id="skd-registration-messages"></div>

            <form id="skd-registration-form" class="skd-form">
                <input type="hidden" name="user_type" value="<?php echo esc_attr($user_type); ?>">
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('skd_registration_nonce'); ?>">

                <div class="skd-form-grid">
                    <?php foreach ($fields as $field_name => $field): ?>
                        <div class="skd-form-group <?php echo $field['required'] ? 'required' : ''; ?>">
                            <label for="<?php echo esc_attr($field_name); ?>">
                                <?php echo esc_html($field['label']); ?>
                                <?php if ($field['required']): ?>
                                    <span class="required-asterisk">*</span>
                                <?php endif; ?>
                            </label>

                            <?php if ($field['type'] === 'select'): ?>
                                <select
                                    name="<?php echo esc_attr($field_name); ?>"
                                    id="<?php echo esc_attr($field_name); ?>"
                                    <?php echo $field['required'] ? 'required' : ''; ?>>
                                    <option value="">Select <?php echo esc_html($field['label']); ?></option>
                                    <?php foreach ($field['options'] as $value => $label): ?>
                                        <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php elseif ($field['type'] === 'textarea'): ?>
                                <textarea
                                    name="<?php echo esc_attr($field_name); ?>"
                                    id="<?php echo esc_attr($field_name); ?>"
                                    placeholder="<?php echo esc_attr($field['placeholder'] ?? ''); ?>"
                                    <?php echo $field['required'] ? 'required' : ''; ?>></textarea>
                            <?php elseif ($field['type'] === 'password'): ?>
                                <div class="skd-password-wrapper">
                                    <input
                                        type="password"
                                        name="<?php echo esc_attr($field_name); ?>"
                                        id="<?php echo esc_attr($field_name); ?>"
                                        placeholder="<?php echo esc_attr($field['placeholder'] ?? ''); ?>"
                                        <?php echo $field['required'] ? 'required' : ''; ?>>
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
                            <?php else: ?>
                                <input
                                    type="<?php echo esc_attr($field['type']); ?>"
                                    name="<?php echo esc_attr($field_name); ?>"
                                    id="<?php echo esc_attr($field_name); ?>"
                                    placeholder="<?php echo esc_attr($field['placeholder'] ?? ''); ?>"
                                    <?php echo $field['required'] ? 'required' : ''; ?>>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="skd-form-group">
                    <label class="skd-checkbox-wrapper">
                        <input type="checkbox" name="terms" id="terms" required>
                        <span class="skd-checkbox-custom"></span>
                        <span class="skd-checkbox-label-text">I agree to the <a href="<?php echo home_url('/terms-of-service/'); ?>" target="_blank">Terms of Service</a> and <a href="<?php echo home_url('/privacy-policy/'); ?>" target="_blank">Privacy Policy</a></span>
                    </label>
                </div>

                <button type="submit" class="skd-btn skd-btn-primary skd-btn-block" id="skd-register-btn">
                    <span class="btn-text">Create Account</span>
                    <span class="btn-loader" style="display: none;">
                        <span class="spinner"></span> Creating account...
                    </span>
                </button>
            </form>

            <div class="skd-form-footer">
                <p>Already have an account? <a href="<?php echo home_url('/login/'); ?>">Login here</a></p>
            </div>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function($) {
        // Initialize jQuery Validation
        $('#skd-registration-form').validate({
            rules: {
                first_name: {
                    required: true,
                    minlength: 2
                },
                last_name: {
                    required: true,
                    minlength: 2
                },
                email: {
                    required: true,
                    email: true
                },
                username: {
                    required: true,
                    minlength: 4,
                    pattern: /^[a-zA-Z0-9_]+$/
                },
                password: {
                    required: true,
                    minlength: 8,
                    pattern: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/
                },
                company_name: {
                    minlength: 2
                }
            },
            messages: {
                first_name: {
                    required: 'Please enter your first name',
                    minlength: 'First name must be at least 2 characters'
                },
                last_name: {
                    required: 'Please enter your last name',
                    minlength: 'Last name must be at least 2 characters'
                },
                email: {
                    required: 'Please enter your email address',
                    email: 'Please enter a valid email address'
                },
                username: {
                    required: 'Please choose a username',
                    minlength: 'Username must be at least 4 characters',
                    pattern: 'Username can only contain letters, numbers, and underscores'
                },
                password: {
                    required: 'Please enter a password',
                    minlength: 'Password must be at least 8 characters',
                    pattern: 'Password must contain uppercase, lowercase, and a number'
                },
                company_name: {
                    minlength: 'Company name must be at least 2 characters'
                }
            },
            errorElement: 'span',
            errorClass: 'error-message',
            errorPlacement: function(error, element) {
                if (element.parent().hasClass('skd-password-wrapper')) {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function(element) {
                $(element).addClass('error-field');
            },
            unhighlight: function(element) {
                $(element).removeClass('error-field');
            },
            submitHandler: function(form) {
                var $form = $(form);
                var $btn = $('#skd-register-btn');
                var $messages = $('#skd-registration-messages');

                // Disable button and show loader
                $btn.prop('disabled', true);
                $btn.find('.btn-text').hide();
                $btn.find('.btn-loader').show();
                $messages.html('');

                // Collect form data
                var formData = {
                    action: 'skd_register_user',
                    user_type: $form.find('[name="user_type"]').val(),
                    first_name: $form.find('[name="first_name"]').val(),
                    last_name: $form.find('[name="last_name"]').val(),
                    email: $form.find('[name="email"]').val(),
                    username: $form.find('[name="username"]').val(),
                    password: $form.find('[name="password"]').val(),
                    company_name: $form.find('[name="company_name"]').val(),
                    timezone: $form.find('[name="timezone"]').val(),
                    nonce: $form.find('[name="nonce"]').val()
                };

                $.ajax({
                    url: skd_ajax.ajax_url,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $messages.html('<div class="skd-message skd-message-success">' + response.data.message + '</div>');
                            setTimeout(function() {
                                window.location.href = response.data.redirect;
                            }, 1500);
                        } else {
                            $messages.html('<div class="skd-message skd-message-error">' + response.data.message + '</div>');
                            $btn.prop('disabled', false);
                            $btn.find('.btn-text').show();
                            $btn.find('.btn-loader').hide();
                        }
                    },
                    error: function() {
                        $messages.html('<div class="skd-message skd-message-error">An error occurred. Please try again.</div>');
                        $btn.prop('disabled', false);
                        $btn.find('.btn-text').show();
                        $btn.find('.btn-loader').hide();
                    }
                });
            }
        });

        // Add custom validation method for pattern
        $.validator.addMethod('pattern', function(value, element, param) {
            if (this.optional(element)) {
                return true;
            }
            if (typeof param === 'string') {
                param = new RegExp('^(?:' + param + ')$');
            }
            return param.test(value);
        }, 'Invalid format');
    });
</script>