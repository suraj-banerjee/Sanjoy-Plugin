<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class SKD_PL_Registration
{
    /**
     * Initialize registration functionality
     */
    public static function init()
    {
        add_action('wp_ajax_nopriv_skd_register_user', [__CLASS__, 'handle_registration']);
        add_action('wp_ajax_nopriv_skd_login_user', [__CLASS__, 'handle_login']);
        add_action('wp_ajax_skd_logout_user', [__CLASS__, 'handle_logout']);
        add_action('wp_ajax_nopriv_skd_logout_user', [__CLASS__, 'handle_logout']);
        add_action('wp_ajax_skd_change_password', [__CLASS__, 'handle_change_password']);
        add_action('wp_ajax_nopriv_skd_forgot_password', [__CLASS__, 'handle_forgot_password']);
        add_action('wp_ajax_nopriv_skd_reset_password', [__CLASS__, 'handle_reset_password']);
        add_action('wp_ajax_skd_get_fresh_nonce', [__CLASS__, 'get_fresh_nonce']);
        add_action('user_register', [__CLASS__, 'after_user_registration'], 10, 1);

        // Block admin dashboard access for custom user roles
        add_action('admin_init', [__CLASS__, 'block_admin_access']);

        // Redirect after login to frontend dashboard
        add_filter('login_redirect', [__CLASS__, 'custom_login_redirect'], 10, 3);

        // Hide admin bar for custom user roles
        add_action('after_setup_theme', [__CLASS__, 'hide_admin_bar']);

        // Show error message on wp-login.php for frontend users
        add_filter('authenticate', [__CLASS__, 'check_login_attempt'], 30, 3);

        // Restrict dashboard access by user type
        add_action('template_redirect', [__CLASS__, 'restrict_dashboard_access']);
    }

    /**
     * Check login attempt and show error for frontend users trying to access wp-admin
     */
    public static function check_login_attempt($user, $username, $password)
    {
        // Only check on actual login attempts with credentials
        if (empty($username) || empty($password)) {
            return $user;
        }

        // If already an error, don't override it
        if (is_wp_error($user)) {
            return $user;
        }

        // Only block if user is trying to access wp-admin or wp-login.php directly
        // Don't block AJAX logins from frontend
        $is_admin_login = (
            (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'wp-login.php') !== false) ||
            (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'wp-login.php') !== false) ||
            (isset($_REQUEST['redirect_to']) && strpos($_REQUEST['redirect_to'], 'wp-admin') !== false)
        );

        // If this is a frontend AJAX login, allow it
        if (!$is_admin_login) {
            return $user;
        }

        // Get the user by username or email
        $user_obj = get_user_by('login', $username);
        if (!$user_obj) {
            $user_obj = get_user_by('email', $username);
        }

        if (!$user_obj) {
            return $user;
        }

        // Check if user has frontend-only role
        $blocked_roles = ['vda_user', 'studio_user', 'employer_user'];

        if (!empty(array_intersect($blocked_roles, $user_obj->roles))) {
            // Get user type for message customization
            $user_type = get_user_meta($user_obj->ID, 'skd_user_type', true);

            $role_name = '';
            if (in_array('vda_user', $user_obj->roles)) {
                $role_name = 'Virtual Design Assistant';
            } elseif (in_array('studio_user', $user_obj->roles)) {
                $role_name = 'Studio/Agency';
            } elseif (in_array('employer_user', $user_obj->roles)) {
                $role_name = 'Employer';
            }

            return new WP_Error(
                'frontend_user_login',
                sprintf(
                    '<strong>Access Denied:</strong> You are registered as a %s and do not have access to the WordPress admin area.<br><br>Please use the <a href="%s">Frontend Login</a> to access your dashboard.',
                    $role_name,
                    home_url('/login/')
                )
            );
        }

        return $user;
    }

    /**
     * Block admin dashboard access for VDA, Studio, and Employer users
     */
    public static function block_admin_access()
    {
        if (!defined('DOING_AJAX') || !DOING_AJAX) {
            $user = wp_get_current_user();

            $blocked_roles = ['vda_user', 'studio_user', 'employer_user'];

            if (!empty(array_intersect($blocked_roles, $user->roles))) {
                // Get user type
                $user_type = get_user_meta($user->ID, 'skd_user_type', true);

                // Redirect to frontend dashboard
                wp_redirect(self::get_dashboard_url($user_type));
                exit;
            }
        }
    }

    /**
     * Custom login redirect to frontend dashboard
     */
    public static function custom_login_redirect($redirect_to, $request, $user)
    {
        if (isset($user->roles) && is_array($user->roles)) {
            // Don't redirect administrators or if accessing admin area
            if (in_array('administrator', $user->roles) || strpos($redirect_to, 'wp-admin') !== false) {
                return $redirect_to;
            }

            $blocked_roles = ['vda_user', 'studio_user', 'employer_user'];

            if (!empty(array_intersect($blocked_roles, $user->roles))) {
                $user_type = get_user_meta($user->ID, 'skd_user_type', true);

                // Get dashboard page ID from options
                $page_option = 'skd_' . $user_type . '_dashboard_page_id';
                $dashboard_page_id = get_option($page_option);

                if ($dashboard_page_id && get_permalink($dashboard_page_id)) {
                    return get_permalink($dashboard_page_id);
                }

                return self::get_dashboard_url($user_type);
            }
        }

        return $redirect_to;
    }

    /**
     * Hide admin bar for VDA, Studio, and Employer users
     */
    public static function hide_admin_bar()
    {
        if (!is_user_logged_in()) {
            return;
        }

        $user = wp_get_current_user();
        $blocked_roles = ['vda_user', 'studio_user', 'employer_user'];

        if (!empty(array_intersect($blocked_roles, $user->roles))) {
            show_admin_bar(false);

            // Also add CSS to hide it completely in case show_admin_bar doesn't work
            add_action('wp_head', function () {
                echo '<style type="text/css">
                    #wpadminbar { display: none !important; }
                    html { margin-top: 0 !important; }
                    * html body { margin-top: 0 !important; }
                </style>';
            }, 99);
        }
    }

    /**
     * Handle user registration via AJAX
     */
    public static function handle_registration()
    {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'skd_registration_nonce')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }

        // Sanitize and validate input
        $user_type = sanitize_text_field($_POST['user_type'] ?? '');
        $email = sanitize_email($_POST['email'] ?? '');
        $username = sanitize_user($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $first_name = sanitize_text_field($_POST['first_name'] ?? '');
        $last_name = sanitize_text_field($_POST['last_name'] ?? '');

        // Validation
        $errors = [];

        if (empty($user_type) || !in_array($user_type, ['vda', 'employer'])) {
            $errors[] = 'Please select a valid user type.';
        }

        if (empty($email) || !is_email($email)) {
            $errors[] = 'Please provide a valid email address.';
        } elseif (email_exists($email)) {
            $errors[] = 'This email is already registered.';
        }

        if (empty($username)) {
            $errors[] = 'Username is required.';
        } elseif (username_exists($username)) {
            $errors[] = 'This username is already taken.';
        }

        if (empty($password) || strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long.';
        }

        if (!empty($errors)) {
            wp_send_json_error(['message' => implode('<br>', $errors)]);
            return;
        }

        // Create user
        $user_id = wp_create_user($username, $password, $email);

        if (is_wp_error($user_id)) {
            wp_send_json_error(['message' => $user_id->get_error_message()]);
            return;
        }

        // Update user meta
        wp_update_user([
            'ID' => $user_id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'display_name' => $first_name . ' ' . $last_name,
        ]);

        // Assign role based on user type
        $user = new WP_User($user_id);
        $user->remove_role('subscriber'); // Remove default role

        switch ($user_type) {
            case 'vda':
                $user->add_role('vda_user');
                break;
            case 'studio':
                $user->add_role('studio_user');
                break;
            case 'employer':
                $user->add_role('employer_user');
                break;
        }

        // Store user type in meta
        update_user_meta($user_id, 'skd_user_type', $user_type);

        // Create user profile entry
        self::create_user_profile($user_id, $user_type, $_POST);

        // Auto-login user
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);

        // Send welcome email
        self::send_welcome_email($user_id, $email, $first_name);

        wp_send_json_success([
            'message' => 'Registration successful! Redirecting...',
            'redirect' => self::get_dashboard_url($user_type)
        ]);
    }

    /**
     * Create initial user profile in custom table
     */
    private static function create_user_profile($user_id, $user_type, $data)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_user_profiles';

        $profile_data = [
            'user_id' => $user_id,
            'user_type' => $user_type,
            'company_name' => sanitize_text_field($data['company_name'] ?? ''),
            'tagline' => sanitize_text_field($data['tagline'] ?? ''),
            'timezone' => sanitize_text_field($data['timezone'] ?? ''),
            'availability_status' => 'available',
            'profile_completeness' => 20, // Basic info completed
            'joined_date' => current_time('mysql'),
            'last_active' => current_time('mysql'),
        ];

        $wpdb->insert($table, $profile_data);
    }

    /**
     * Handle user login via AJAX
     */
    public static function handle_login()
    {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'skd_login_nonce')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }

        $username = sanitize_text_field($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']) && $_POST['remember'] === 'true';

        if (empty($username) || empty($password)) {
            wp_send_json_error(['message' => 'Please provide both username and password.']);
            return;
        }

        $credentials = [
            'user_login' => $username,
            'user_password' => $password,
            'remember' => $remember,
        ];

        $user = wp_signon($credentials, is_ssl());

        if (is_wp_error($user)) {
            wp_send_json_error(['message' => 'Invalid username or password.']);
            return;
        }

        // Update last active
        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_user_profiles';
        $wpdb->update(
            $table,
            ['last_active' => current_time('mysql')],
            ['user_id' => $user->ID]
        );

        // Get user type
        $user_type = get_user_meta($user->ID, 'skd_user_type', true);

        wp_send_json_success([
            'message' => 'Login successful! Redirecting...',
            'redirect' => self::get_dashboard_url($user_type)
        ]);
    }

    /**
     * Handle user logout
     */
    public static function handle_logout()
    {
        wp_logout();
        wp_send_json_success([
            'message' => 'Logged out successfully',
            'redirect' => home_url('/login/')
        ]);
    }

    /**
     * Handle password change
     */
    public static function handle_change_password()
    {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'skd_ajax_nonce')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }

        // Check if user is logged in
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'You must be logged in to change password']);
            return;
        }

        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';

        $user = wp_get_current_user();

        // Validation
        if (empty($current_password) || empty($new_password)) {
            wp_send_json_error(['message' => 'All fields are required']);
            return;
        }

        // Verify current password
        if (!wp_check_password($current_password, $user->user_pass, $user->ID)) {
            wp_send_json_error(['message' => 'Current password is incorrect']);
            return;
        }

        // Validate password strength
        if (strlen($new_password) < 8) {
            wp_send_json_error(['message' => 'Password must be at least 8 characters long']);
            return;
        }

        // Update password
        wp_set_password($new_password, $user->ID);

        // Re-authenticate user
        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID);

        wp_send_json_success(['message' => 'Password changed successfully']);
    }

    /**
     * Get fresh nonce after password change
     */
    public static function get_fresh_nonce()
    {
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Not logged in']);
            return;
        }

        wp_send_json_success(['nonce' => wp_create_nonce('skd_ajax_nonce')]);
    }

    /**
     * Handle forgot password request
     */
    public static function handle_forgot_password()
    {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'skd_ajax_nonce')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }

        $email = sanitize_email($_POST['email'] ?? '');

        if (empty($email) || !is_email($email)) {
            wp_send_json_error(['message' => 'Please provide a valid email address']);
            return;
        }

        // Check if email exists
        $user = get_user_by('email', $email);

        if (!$user) {
            wp_send_json_error(['message' => 'No account found with this email address']);
            return;
        }

        // Generate reset key
        $reset_key = get_password_reset_key($user);

        if (is_wp_error($reset_key)) {
            wp_send_json_error(['message' => 'Failed to generate reset key. Please try again']);
            return;
        }

        // Send reset email
        $reset_url = home_url('/reset-password/?key=' . $reset_key . '&login=' . rawurlencode($user->user_login));

        $subject = 'Password Reset Request - interiAssist';
        $message = sprintf(
            "Hi %s,\n\nYou requested a password reset for your interiAssist account.\n\nClick the link below to reset your password:\n%s\n\nThis link will expire in 24 hours.\n\nIf you didn't request this, please ignore this email.\n\nBest regards,\nThe interiAssist Team",
            $user->display_name,
            $reset_url
        );

        $sent = wp_mail($email, $subject, $message);

        if ($sent) {
            wp_send_json_success(['message' => 'Password reset link has been sent to your email']);
        } else {
            wp_send_json_error(['message' => 'Failed to send email. Please try again']);
        }

        // // For development: Log the reset URL
        // error_log('Password Reset URL: ' . $reset_url);

        // // Always show success (email might fail on local dev without SMTP)
        // wp_send_json_success([
        //     'message' => 'Password reset link has been sent to your email',
        //     'reset_url' => $reset_url // For development only - remove in production
        // ]);
    }

    /**
     * Handle password reset
     */
    public static function handle_reset_password()
    {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'skd_ajax_nonce')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }

        $reset_key = sanitize_text_field($_POST['key'] ?? '');
        $user_login = sanitize_text_field($_POST['login'] ?? '');
        $new_password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (empty($reset_key) || empty($user_login) || empty($new_password) || empty($confirm_password)) {
            wp_send_json_error(['message' => 'All fields are required']);
            return;
        }

        // Check if passwords match
        if ($new_password !== $confirm_password) {
            wp_send_json_error(['message' => 'Passwords do not match']);
            return;
        }

        // Validate password strength
        if (strlen($new_password) < 8) {
            wp_send_json_error(['message' => 'Password must be at least 8 characters long']);
            return;
        }

        // Get user
        $user = check_password_reset_key($reset_key, $user_login);

        if (is_wp_error($user)) {
            wp_send_json_error(['message' => 'Invalid or expired reset link']);
            return;
        }

        // Reset password
        reset_password($user, $new_password);

        wp_send_json_success([
            'message' => 'Password reset successful! You can now login with your new password',
            'redirect' => home_url('/login/')
        ]);
    }

    /**
     * Restrict dashboard access by user type
     */
    public static function restrict_dashboard_access()
    {
        global $post;

        // Check if we're on a dashboard page
        if (!$post || !in_array($post->post_name, ['vda-dashboard', 'studio-dashboard', 'employer-dashboard'])) {
            return;
        }

        // Redirect to login if user is not logged in
        if (!is_user_logged_in()) {
            wp_redirect(home_url('/login/'));
            exit;
        }

        $user = wp_get_current_user();
        $user_type = get_user_meta($user->ID, 'skd_user_type', true);
        $current_page = $post->post_name;

        // Define allowed dashboards for each user type
        $allowed_dashboards = [
            'vda' => 'vda-dashboard',
            'studio' => 'studio-dashboard',
            'employer' => 'employer-dashboard'
        ];

        // Check if user is trying to access wrong dashboard
        if (isset($allowed_dashboards[$user_type]) && $current_page !== $allowed_dashboards[$user_type]) {
            wp_redirect(self::get_dashboard_url($user_type));
            exit;
        }
    }

    /**
     * Actions after user registration
     */
    public static function after_user_registration($user_id)
    {
        // Additional actions can be added here
        do_action('skd_after_user_registration', $user_id);
    }

    /**
     * Send welcome email
     */
    private static function send_welcome_email($user_id, $email, $first_name)
    {
        $subject = 'Welcome to interiAssist!';

        $message = sprintf(
            "Hi %s,\n\nWelcome to interiAssist - Your Digital Design Network!\n\nYour account has been successfully created. You can now:\n\n• Build your professional profile\n• Browse and apply to jobs\n• Connect with other design professionals\n• Access exclusive resources and training\n\nGet started by completing your profile: %s\n\nBest regards,\nThe interiAssist Team",
            $first_name,
            self::get_dashboard_url(get_user_meta($user_id, 'skd_user_type', true))
        );

        wp_mail($email, $subject, $message);
    }

    /**
     * Get dashboard URL based on user type
     */
    public static function get_dashboard_url($user_type)
    {
        $dashboard_slug = 'dashboard';

        switch ($user_type) {
            case 'vda':
                $dashboard_slug = 'vda-dashboard';
                break;
            case 'studio':
                $dashboard_slug = 'studio-dashboard';
                break;
            case 'employer':
                $dashboard_slug = 'employer-dashboard';
                break;
        }

        return home_url('/' . $dashboard_slug . '/');
    }

    /**
     * Get registration fields by user type
     */
    public static function get_registration_fields($user_type)
    {
        $common_fields = [
            'first_name' => [
                'label' => 'First Name',
                'type' => 'text',
                'required' => true,
                'placeholder' => 'Enter your first name'
            ],
            'last_name' => [
                'label' => 'Last Name',
                'type' => 'text',
                'required' => true,
                'placeholder' => 'Enter your last name'
            ],
            'email' => [
                'label' => 'Email Address',
                'type' => 'email',
                'required' => true,
                'placeholder' => 'your@email.com'
            ],
            'username' => [
                'label' => 'Username',
                'type' => 'text',
                'required' => true,
                'placeholder' => 'Choose a username'
            ],
            'password' => [
                'label' => 'Password',
                'type' => 'password',
                'required' => true,
                'placeholder' => 'Minimum 8 characters'
            ],
        ];

        $type_specific_fields = [];

        switch ($user_type) {
            case 'vda':
                $type_specific_fields = [
                    'tagline' => [
                        'label' => 'Professional Tagline',
                        'type' => 'text',
                        'required' => false,
                        'placeholder' => 'e.g., 3D Rendering Specialist'
                    ],
                    'timezone' => [
                        'label' => 'Timezone',
                        'type' => 'select',
                        'required' => false,
                        'options' => self::get_timezones()
                    ],
                ];
                break;

            case 'studio':
                $type_specific_fields = [
                    'company_name' => [
                        'label' => 'Studio/Agency Name',
                        'type' => 'text',
                        'required' => true,
                        'placeholder' => 'Your studio name'
                    ],
                    'tagline' => [
                        'label' => 'Company Tagline',
                        'type' => 'text',
                        'required' => false,
                        'placeholder' => 'Brief description of your studio'
                    ],
                    'timezone' => [
                        'label' => 'Timezone',
                        'type' => 'select',
                        'required' => false,
                        'options' => self::get_timezones()
                    ],
                ];
                break;

            case 'employer':
                $type_specific_fields = [
                    'company_name' => [
                        'label' => 'Company Name',
                        'type' => 'text',
                        'required' => false,
                        'placeholder' => 'Your company name (optional)'
                    ],
                    'timezone' => [
                        'label' => 'Timezone',
                        'type' => 'select',
                        'required' => false,
                        'options' => self::get_timezones()
                    ],
                ];
                break;
        }

        return array_merge($common_fields, $type_specific_fields);
    }

    /**
     * Get timezone options
     */
    public static function get_timezones()
    {
        $timezones = SKD_PL_Timezones::get_timezones(['status' => 'active']);
        $options = [];
        foreach ($timezones as $tz) {
            $options[$tz->id] = $tz->name . ' - ' . $tz->offset;
        }
        return $options;
    }
}

// Initialize
SKD_PL_Registration::init();
