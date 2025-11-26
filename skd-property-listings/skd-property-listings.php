<?php

/**
 * Plugin Name: interiAssist - Digital Design Network
 * Description: Professional networking platform for Virtual Design Assistants, Studios, and Interior Designers.
 * Version: 2.0.0
 * Author: SKD
 * Text Domain: skd-property-listings
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define plugin constants
define('SKD_PL_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('SKD_PL_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SKD_PL_VERSION', '1.0.0');

if (!defined('SKD_MAP_API_KEY')) {
    $skd_map_key = get_option('skd_map_api_key');
    define('SKD_MAP_API_KEY', $skd_map_key ? $skd_map_key : '');
}

if (!defined('SKD_STRIPE_API_KEY')) {
    $stripe_api_key = get_option('skd_stripe_api_key');
    define('SKD_STRIPE_API_KEY', $stripe_api_key ? $stripe_api_key : '');
}

// Include the database class
require_once SKD_PL_PLUGIN_PATH . 'includes/class-skd-pl-database.php';

// Include the seed data class
require_once SKD_PL_PLUGIN_PATH . 'includes/class-skd-pl-seed-data.php';

// Hook for creating tables on plugin activation
// register_activation_hook(__FILE__, ['SKD_PL_Database', 'create_tables']);
register_activation_hook(__FILE__, function () {
    ob_start();
    // try {
    SKD_PL_Database::create_tables();
    SKD_PL_Seed_Data::insert_seed_data();
    // } catch (Exception $e) {
    //     error_log($e->getMessage());
    // }
    skd_create_single_pages();
    skd_flush_rewrite_rules();
    ob_end_clean();
});

// Hook for deleting tables on plugin uninstall
register_uninstall_hook(__FILE__, ['SKD_PL_Database', 'delete_tables']);


// Autoloader for classes
require_once SKD_PL_PLUGIN_PATH . 'includes/class-skd-pl-autoloader.php';

// Initialize the plugin
add_action('plugins_loaded', function () {
    require_once SKD_PL_PLUGIN_PATH . 'includes/class-skd-pl-main.php';
    require_once SKD_PL_PLUGIN_PATH . 'includes/web/class-skd-pl-registration.php';
    SKD_Plugin_Main::init();
});

function skd_register_rewrite_rules()
{
    // Professional Profile Rewrite Rule
    add_rewrite_rule(
        '^professional/([^/]+)/?$',
        'index.php?pagename=professional-profile&skd_professional_slug=$matches[1]',
        'top'
    );

    // Studio Profile Rewrite Rule
    add_rewrite_rule(
        '^studio/([^/]+)/?$',
        'index.php?pagename=professional-profile&skd_studio_slug=$matches[1]',
        'top'
    );

    // Job Details Rewrite Rule
    add_rewrite_rule(
        '^job/([^/]+)/?$',
        'index.php?pagename=job-details&skd_job_slug=$matches[1]',
        'top'
    );

    // Single Category Listings (Design Types)
    add_rewrite_rule(
        '^category/([^/]+)/?$',
        'index.php?pagename=find-assistants&skd_category_slug=$matches[1]',
        'top'
    );

    // Single Location Listings
    add_rewrite_rule(
        '^location/([^/]+)/?$',
        'index.php?pagename=find-assistants&skd_location_slug=$matches[1]',
        'top'
    );

    // Edit professional profile
    add_rewrite_rule(
        '^my-profile/edit/?$',
        'index.php?pagename=my-profile&action=edit',
        'top'
    );

    // Edit job posting
    add_rewrite_rule(
        '^my-jobs/edit/([0-9]+)/?$',
        'index.php?pagename=my-jobs&action=edit&job_id=$matches[1]',
        'top'
    );

    // Order confirmation
    add_rewrite_rule(
        '^order-confirmation/?$',
        'index.php?skd_order_confirmation=1',
        'top'
    );

    /**
     * =====================
     * Account Update Handler
     * =====================
     */
    if (isset($_POST['custom_save_account'])) {
        if (!isset($_POST['custom_edit_account_nonce']) || !wp_verify_nonce($_POST['custom_edit_account_nonce'], 'custom_edit_account_action')) {
            wp_die('Security check failed.');
        }
        $user_id = get_current_user_id();
        if (!$user_id) return;
        // Update user fields
        wp_update_user([
            'ID'           => $user_id,
            'first_name'   => sanitize_text_field($_POST['first_name']),
            'last_name'    => sanitize_text_field($_POST['last_name']),
            'display_name' => sanitize_text_field($_POST['display_name']),
            'user_email'   => sanitize_email($_POST['email']),
        ]);
        // Handle password change
        if (!empty($_POST['new_password'])) {
            if ($_POST['new_password'] === $_POST['confirm_password']) {
                wp_set_password($_POST['new_password'], $user_id);
                wp_set_auth_cookie($user_id); // keep user logged in
            }
        }
        // Redirect to prevent resubmission
        wp_redirect(add_query_arg('updated', 'true', wp_get_referer()));
        exit;
    }
}
add_action('init', 'skd_register_rewrite_rules');

function skd_flush_rewrite_rules()
{
    skd_register_rewrite_rules();
    flush_rewrite_rules();

    // Ensure pretty permalinks are set
    skd_ensure_pretty_permalinks();
}
register_activation_hook(__FILE__, 'skd_flush_rewrite_rules');

/**
 * Ensure WordPress is using pretty permalinks
 */
function skd_ensure_pretty_permalinks()
{
    global $wp_rewrite;

    // Check if permalinks are set to default (ugly URLs with index.php)
    $current_structure = get_option('permalink_structure');

    if (empty($current_structure)) {
        // Set to Post name structure (most common and SEO-friendly)
        update_option('permalink_structure', '/%postname%/');

        // Flush rewrite rules to apply changes
        $wp_rewrite->init();
        $wp_rewrite->flush_rules();
    }
}

function skd_add_query_vars($vars)
{
    $vars[] = 'skd_professional_slug';
    $vars[] = 'skd_studio_slug';
    $vars[] = 'skd_job_slug';
    $vars[] = 'skd_location_slug';
    $vars[] = 'skd_category_slug';
    $vars[] = 'job_id';
    $vars[] = 'skd_order_confirmation';
    $vars[] = 'action';
    return $vars;
}
add_filter('query_vars', 'skd_add_query_vars');

function skd_handle_order_confirmation()
{
    if (get_query_var('skd_order_confirmation') == 1) {
        include SKD_PL_PLUGIN_PATH . 'includes/web/order-confirmation.php';
        exit;
    }
}
add_action('template_redirect', 'skd_handle_order_confirmation');

/**
 * Enqueue AJAX variables for frontend
 */
function skd_enqueue_ajax_vars()
{
    // Only enqueue on frontend
    if (!is_admin()) {
        wp_localize_script('jquery', 'skd_ajax_object', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('skd_ajax_nonce')
        ]);
    }
}
add_action('wp_enqueue_scripts', 'skd_enqueue_ajax_vars');

function skd_create_single_pages()
{
    $pages = [
        // Authentication Pages
        ['register', 'Register', '[skd_registration_form]'],
        ['login', 'Login', '[skd_login_form]'],
        ['forgot-password', 'Forgot Password', '[skd_forgot_password]'],
        ['reset-password', 'Reset Password', '[skd_reset_password]'],
        ['change-password', 'Change Password', '[skd_change_password]'],

        // Directory Pages
        ['find-assistants', 'Find Assistants', '[skd_find_assistants]'],
        ['find-studios', 'Find Studios', '[skd_find_studios]'],
        ['professional-profile', 'Professional Profile', '[skd_professional_profile]'],
        ['vda-profile', 'VDA Profile', '[skd_vda_profile]'],
        ['add-professional-profile', 'Create Professional Profile', '[skd_add_listing_form]'],

        // Job Board
        ['job-board', 'Job Board', '[skd_job_board]'],
        ['post-job', 'Post a Job', '[skd_post_job_form]'],

        // Resources
        ['academy', 'Academy', '[skd_academy_resources]'],
        ['resources', 'Resources', '[skd_resources]'],
        ['community', 'Community', '[skd_community]'],

        // Membership
        ['pricing', 'Membership Pricing', '[skd_pricing_plans]'],
        ['checkout', 'Checkout', '[skd_plan_checkout]'],

        // User Dashboards (Type-Specific)
        ['vda-dashboard', 'VDA Dashboard', '[skd_vda_dashboard]'],
        ['studio-dashboard', 'Studio Dashboard', '[skd_studio_dashboard]'],
        ['employer-dashboard', 'Employer Dashboard', '[skd_employer_dashboard]'],
        ['edit-vda-profile', 'Edit VDA Profile', '[skd_edit_vda_profile]']
    ];

    foreach ($pages as $page) {
        $page_slug = $page[0];
        $page_title = $page[1];
        $shortcode = $page[2];

        // Check if the page already exists
        $existing_page = get_page_by_path($page_slug);

        if (!$existing_page) {
            $page_id = wp_insert_post([
                'post_title'    => $page_title,
                'post_name'     => $page_slug,
                'post_content'  => $shortcode,
                'post_status'   => 'publish',
                'post_type'     => 'page',
            ]);

            // Store page IDs as options for easy reference
            if ($page_slug === 'vda-dashboard') {
                update_option('skd_vda_dashboard_page_id', $page_id);
            } elseif ($page_slug === 'studio-dashboard') {
                update_option('skd_studio_dashboard_page_id', $page_id);
            } elseif ($page_slug === 'employer-dashboard') {
                update_option('skd_employer_dashboard_page_id', $page_id);
            } elseif ($page_slug === 'login') {
                update_option('skd_login_page_id', $page_id);
            } elseif ($page_slug === 'vda-profile') {
                update_option('skd_vda_profile_page_id', $page_id);
            } elseif ($page_slug === 'find-assistants') {
                update_option('skd_find_assistants_page_id', $page_id);
            }
        } else {
            // Update existing page if shortcode is different (for migration from old shortcodes)
            if ($existing_page->post_content !== $shortcode) {
                wp_update_post([
                    'ID'           => $existing_page->ID,
                    'post_content' => $shortcode,
                ]);
            }

            // Store page IDs for existing pages too
            $page_id = $existing_page->ID;
            if ($page_slug === 'vda-dashboard') {
                update_option('skd_vda_dashboard_page_id', $page_id);
            } elseif ($page_slug === 'studio-dashboard') {
                update_option('skd_studio_dashboard_page_id', $page_id);
            } elseif ($page_slug === 'employer-dashboard') {
                update_option('skd_employer_dashboard_page_id', $page_id);
            } elseif ($page_slug === 'login') {
                update_option('skd_login_page_id', $page_id);
            } elseif ($page_slug === 'vda-profile') {
                update_option('skd_vda_profile_page_id', $page_id);
            } elseif ($page_slug === 'find-assistants') {
                update_option('skd_find_assistants_page_id', $page_id);
            }
        }
    }
}

/**
 * Redirect users to their dashboard after login
 */
add_filter('login_redirect', 'skd_login_redirect', 10, 3);
function skd_login_redirect($redirect_to, $request, $user)
{
    if (isset($user->ID)) {
        // Don't redirect administrators or if accessing admin area
        if (in_array('administrator', $user->roles) || strpos($redirect_to, 'wp-admin') !== false) {
            return $redirect_to;
        }

        $user_type = get_user_meta($user->ID, 'skd_user_type', true);

        switch ($user_type) {
            case 'vda':
                $dashboard_page_id = get_option('skd_vda_dashboard_page_id');
                if ($dashboard_page_id) {
                    return get_permalink($dashboard_page_id);
                }
                return home_url('/vda-dashboard/');

            case 'studio':
                $dashboard_page_id = get_option('skd_studio_dashboard_page_id');
                if ($dashboard_page_id) {
                    return get_permalink($dashboard_page_id);
                }
                return home_url('/studio-dashboard/');

            case 'employer':
                $dashboard_page_id = get_option('skd_employer_dashboard_page_id');
                if ($dashboard_page_id) {
                    return get_permalink($dashboard_page_id);
                }
                return home_url('/employer-dashboard/');

            default:
                return home_url('/vda-dashboard/');
        }
    }

    return $redirect_to;
}

// Include Stripe if it's not already included
if (!class_exists('\Stripe\Stripe')) {
    require_once SKD_PL_PLUGIN_PATH . 'vendor/autoload.php';
}
