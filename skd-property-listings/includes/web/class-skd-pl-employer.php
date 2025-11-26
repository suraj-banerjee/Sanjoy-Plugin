<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class SKD_PL_Employer
{
    /**
     * Initialize employer functionality
     */
    public static function init()
    {
        // Employer profile AJAX handlers
        add_action('wp_ajax_skd_update_employer_profile', [__CLASS__, 'update_employer_profile']);
        add_action('wp_ajax_skd_update_employer_password', [__CLASS__, 'update_employer_password']);
        add_action('wp_ajax_skd_update_employer_account_info', [__CLASS__, 'update_employer_account_info']);

        // Job management AJAX handlers
        add_action('wp_ajax_skd_get_employer_jobs', [__CLASS__, 'get_employer_jobs']);
        add_action('wp_ajax_skd_post_job', [__CLASS__, 'post_job']);
        add_action('wp_ajax_skd_update_job', [__CLASS__, 'update_job']);
        add_action('wp_ajax_skd_delete_job', [__CLASS__, 'delete_job']);
        add_action('wp_ajax_skd_toggle_job_status', [__CLASS__, 'toggle_job_status']);
        add_action('wp_ajax_skd_get_job_details', [__CLASS__, 'get_job_details']);

        // VDA search and save AJAX handlers
        add_action('wp_ajax_skd_search_vdas', [__CLASS__, 'search_vdas']);
        add_action('wp_ajax_skd_save_vda', [__CLASS__, 'save_vda']);
        add_action('wp_ajax_skd_unsave_vda', [__CLASS__, 'unsave_vda']);
        add_action('wp_ajax_skd_get_saved_vdas', [__CLASS__, 'get_saved_vdas']);

        // Application management AJAX handlers
        add_action('wp_ajax_skd_get_employer_applications', [__CLASS__, 'get_employer_applications']);
        add_action('wp_ajax_skd_update_application_status', [__CLASS__, 'update_application_status']);
    }

    /**
     * Update employer password
     */
    public static function update_employer_password()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'skd_ajax_nonce')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Please log in']);
            return;
        }

        $user_id = get_current_user_id();
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';

        // Validate inputs
        if (empty($current_password) || empty($new_password)) {
            wp_send_json_error(['message' => 'All fields are required']);
            return;
        }

        $user = get_userdata($user_id);

        // Verify current password
        if (!wp_check_password($current_password, $user->user_pass, $user_id)) {
            wp_send_json_error(['message' => 'Current password is incorrect']);
            return;
        }

        // Validate password strength
        if (strlen($new_password) < 8) {
            wp_send_json_error(['message' => 'Password must be at least 8 characters long']);
            return;
        }

        // Update password
        wp_set_password($new_password, $user_id);

        wp_send_json_success(['message' => 'Password updated successfully']);
    }

    /**
     * Update employer account information
     */
    public static function update_employer_account_info()
    {
        // Log the incoming request for debugging
        error_log('Account info update request received');
        error_log('POST data: ' . print_r($_POST, true));

        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'skd_ajax_nonce')) {
            error_log('Nonce verification failed');
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }

        if (!is_user_logged_in()) {
            error_log('User not logged in');
            wp_send_json_error(['message' => 'Please log in']);
            return;
        }

        $user_id = get_current_user_id();
        error_log('User ID: ' . $user_id);

        $first_name = sanitize_text_field($_POST['first_name'] ?? '');
        $last_name = sanitize_text_field($_POST['last_name'] ?? '');
        $display_name = sanitize_text_field($_POST['display_name'] ?? '');
        $phone = sanitize_text_field($_POST['phone'] ?? '');

        error_log("First name: $first_name, Last name: $last_name, Display: $display_name");

        // Validate required fields
        if (empty($first_name) || empty($last_name) || empty($display_name)) {
            error_log('Validation failed - missing required fields');
            wp_send_json_error(['message' => 'First name, last name, and display name are required']);
            return;
        }

        // Update user data
        $updated = wp_update_user([
            'ID' => $user_id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'display_name' => $display_name
        ]);

        if (is_wp_error($updated)) {
            error_log('wp_update_user failed: ' . $updated->get_error_message());
            wp_send_json_error(['message' => 'Failed to update account information']);
            return;
        }

        // Update phone number
        if (!empty($phone)) {
            update_user_meta($user_id, 'phone', $phone);
        }

        error_log('Account info updated successfully');
        wp_send_json_success(['message' => 'Account information updated successfully']);
    }

    /**
     * Update employer profile
     */
    public static function update_employer_profile()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'skd_ajax_nonce')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Please log in']);
            return;
        }

        global $wpdb;
        $user_id = get_current_user_id();
        $user_type = get_user_meta($user_id, 'skd_user_type', true);

        if ($user_type !== 'employer') {
            wp_send_json_error(['message' => 'Access denied']);
            return;
        }

        $company_name = sanitize_text_field($_POST['company_name'] ?? '');
        $company_size = sanitize_text_field($_POST['company_size'] ?? '');
        $industry = sanitize_text_field($_POST['industry'] ?? '');
        $website = esc_url_raw($_POST['website'] ?? '');
        $phone = sanitize_text_field($_POST['phone'] ?? '');
        $bio = wp_kses_post($_POST['bio'] ?? '');

        // Check if profile exists
        $profile_exists = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}skd_pl_employer_profiles WHERE user_id = %d",
            $user_id
        ));

        if ($profile_exists) {
            // Update existing profile
            $updated = $wpdb->update(
                $wpdb->prefix . 'skd_pl_employer_profiles',
                [
                    'company_name' => $company_name,
                    'company_size' => $company_size,
                    'industry' => $industry,
                    'website' => $website,
                    'phone' => $phone,
                    'bio' => $bio,
                    'updated_at' => current_time('mysql')
                ],
                ['user_id' => $user_id],
                ['%s', '%s', '%s', '%s', '%s', '%s', '%s'],
                ['%d']
            );
        } else {
            // Insert new profile
            $updated = $wpdb->insert(
                $wpdb->prefix . 'skd_pl_employer_profiles',
                [
                    'user_id' => $user_id,
                    'company_name' => $company_name,
                    'company_size' => $company_size,
                    'industry' => $industry,
                    'website' => $website,
                    'phone' => $phone,
                    'bio' => $bio,
                    'created_at' => current_time('mysql')
                ],
                ['%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s']
            );
        }

        if ($updated !== false) {
            wp_send_json_success(['message' => 'Profile updated successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to update profile']);
        }
    }

    /**
     * Update employer settings (account info and password)
     */
    public static function update_employer_settings()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'skd_ajax_nonce')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Please log in']);
            return;
        }

        $user_id = get_current_user_id();
        $display_name = sanitize_text_field($_POST['display_name'] ?? '');
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Update display name
        if (!empty($display_name)) {
            wp_update_user([
                'ID' => $user_id,
                'display_name' => $display_name
            ]);
        }

        // Update password if provided
        if (!empty($current_password) && !empty($new_password)) {
            $user = get_userdata($user_id);

            if (!wp_check_password($current_password, $user->user_pass, $user_id)) {
                wp_send_json_error(['message' => 'Current password is incorrect']);
                return;
            }

            if ($new_password !== $confirm_password) {
                wp_send_json_error(['message' => 'Passwords do not match']);
                return;
            }

            if (strlen($new_password) < 8) {
                wp_send_json_error(['message' => 'Password must be at least 8 characters']);
                return;
            }

            wp_set_password($new_password, $user_id);
        }

        wp_send_json_success(['message' => 'Settings updated successfully']);
    }

    /**
     * Get employer's job listings
     */
    public static function get_employer_jobs()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'skd_ajax_nonce')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Please log in']);
            return;
        }

        global $wpdb;
        $user_id = get_current_user_id();
        $limit = isset($_POST['limit']) ? intval($_POST['limit']) : null;

        $query = $wpdb->prepare(
            "SELECT j.*, 
             (SELECT COUNT(*) FROM {$wpdb->prefix}skd_pl_job_applications WHERE job_id = j.id) as applications_count
             FROM {$wpdb->prefix}skd_pl_jobs j
             WHERE j.employer_id = %d
             ORDER BY j.created_at DESC",
            $user_id
        );

        if ($limit) {
            $query .= $wpdb->prepare(" LIMIT %d", $limit);
        }

        $jobs = $wpdb->get_results($query);

        if (empty($jobs)) {
            $html = '<div class="employer-empty-state">
                <iconify-icon icon="material-symbols:work-outline" style="font-size: 80px; color: #ccc;"></iconify-icon>
                <h3>No Jobs Posted Yet</h3>
                <p>Start attracting talented VDAs by posting your first job.</p>
                <button class="employer-btn employer-btn-primary" id="post-job-btn">
                    <iconify-icon icon="material-symbols:add"></iconify-icon>
                    Post Your First Job
                </button>
            </div>';
        } else {
            $html = '<div class="jobs-grid">';
            foreach ($jobs as $job) {
                $status_class = $job->status === 'open' ? 'status-open' : 'status-closed';
                $status_text = ucfirst($job->status);
                $toggle_text = $job->status === 'open' ? 'Close' : 'Reopen';

                $html .= '<div class="job-card" data-job-id="' . $job->id . '">
                    <div class="job-card-header">
                        <div>
                            <h3>' . esc_html($job->title) . '</h3>
                            <span class="job-status ' . $status_class . '">' . $status_text . '</span>
                        </div>
                        <button class="toggle-job-status" data-id="' . $job->id . '" data-status="' . $job->status . '" title="' . $toggle_text . ' Applications">
                            <iconify-icon icon="' . ($job->status === 'open' ? 'material-symbols:lock-open' : 'material-symbols:lock') . '"></iconify-icon>
                        </button>
                    </div>
                    <div class="job-card-body">
                        <p>' . esc_html(wp_trim_words($job->description, 20)) . '</p>
                        <div class="job-meta">
                            <span><iconify-icon icon="material-symbols:calendar-month-outline"></iconify-icon> ' .
                    human_time_diff(strtotime($job->created_at), current_time('timestamp')) . ' ago</span>
                            <span><iconify-icon icon="material-symbols:description-outline"></iconify-icon> ' .
                    $job->applications_count . ' applications</span>
                        </div>
                    </div>
                    <div class="job-card-actions">
                        <button class="employer-btn employer-btn-sm employer-btn-primary view-job" data-id="' . $job->id . '">
                            <iconify-icon icon="material-symbols:visibility-outline"></iconify-icon> View
                        </button>
                        <button class="employer-btn employer-btn-sm edit-job" data-id="' . $job->id . '">
                            <iconify-icon icon="material-symbols:edit-outline"></iconify-icon> Edit
                        </button>
                        <button class="employer-btn employer-btn-sm employer-btn-danger delete-job" data-id="' . $job->id . '">
                            <iconify-icon icon="material-symbols:delete-outline"></iconify-icon> Delete
                        </button>
                    </div>
                </div>';
            }
            $html .= '</div>';
        }

        wp_send_json_success(['html' => $html, 'count' => count($jobs)]);
    }

    /**
     * Search VDAs with filters
     */
    public static function search_vdas()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'skd_ajax_nonce')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }

        global $wpdb;
        $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
        $specialization = isset($_POST['specialization']) ? intval($_POST['specialization']) : 0;
        $skill = isset($_POST['skill']) ? intval($_POST['skill']) : 0;

        $query = "SELECT u.ID, u.display_name, u.user_email,
                  p.tagline, p.hourly_rate, p.avatar_url, p.rating, p.total_reviews,
                  p.years_experience, p.availability_status, p.city, p.country,
                  p.is_verified, p.is_featured, p.is_top_rated
                  FROM {$wpdb->users} u
                  INNER JOIN {$wpdb->prefix}skd_pl_user_profiles p ON u.ID = p.user_id
                  WHERE p.user_type = 'vda'";

        if (!empty($search)) {
            $query .= $wpdb->prepare(
                " AND (u.display_name LIKE %s OR p.tagline LIKE %s)",
                '%' . $wpdb->esc_like($search) . '%',
                '%' . $wpdb->esc_like($search) . '%'
            );
        }

        $query .= " ORDER BY p.is_featured DESC, p.rating DESC, p.total_reviews DESC LIMIT 20";

        $vdas = $wpdb->get_results($query);

        if (empty($vdas)) {
            $html = '<div class="employer-empty-state">
                <iconify-icon icon="material-symbols:search" style="font-size: 80px; color: #ccc;"></iconify-icon>
                <h3>No VDAs Found</h3>
                <p>Try adjusting your search filters.</p>
            </div>';
        } else {
            $html = '';
            foreach ($vdas as $vda) {
                $avatar_html = !empty($vda->avatar_url)
                    ? '<img src="' . esc_url($vda->avatar_url) . '" alt="' . esc_attr($vda->display_name) . '">'
                    : '<div class="vda-avatar-placeholder">' . strtoupper(substr($vda->display_name, 0, 1)) . '</div>';

                $badges = '';
                if ($vda->is_verified) $badges .= '<span class="badge verified"><iconify-icon icon="mdi:check-decagram"></iconify-icon></span>';
                if ($vda->is_featured) $badges .= '<span class="badge featured"><iconify-icon icon="mdi:star"></iconify-icon></span>';
                if ($vda->is_top_rated) $badges .= '<span class="badge top-rated"><iconify-icon icon="mdi:trophy"></iconify-icon></span>';

                $html .= '<div class="vda-card">
                    <div class="vda-card-header">
                        <div class="vda-avatar">' . $avatar_html . '</div>
                        <div class="vda-info">
                            <h4>' . esc_html($vda->display_name) . ' ' . $badges . '</h4>
                            <p class="vda-tagline">' . esc_html($vda->tagline ?: 'Virtual Design Assistant') . '</p>
                            <div class="vda-rating">
                                <span class="stars">★★★★★</span>
                                <span>' . number_format($vda->rating, 1) . ' (' . $vda->total_reviews . ')</span>
                            </div>
                        </div>
                    </div>
                    <div class="vda-card-body">
                        <div class="vda-meta">
                            <span><iconify-icon icon="mdi:map-marker"></iconify-icon> ' .
                    esc_html($vda->city ? $vda->city . ', ' . $vda->country : 'Remote') . '</span>
                            <span><iconify-icon icon="mdi:currency-usd"></iconify-icon> $' .
                    number_format($vda->hourly_rate ?: 0, 0) . '/hr</span>
                        </div>
                    </div>
                    <div class="vda-card-actions">
                        <button class="employer-btn employer-btn-sm view-vda-profile" data-id="' . $vda->ID . '">View Profile</button>
                        <button class="employer-btn employer-btn-sm save-vda" data-id="' . $vda->ID . '">
                            <iconify-icon icon="mdi:bookmark-outline"></iconify-icon> Save
                        </button>
                    </div>
                </div>';
            }
        }

        wp_send_json_success(['html' => $html, 'count' => count($vdas)]);
    }

    /**
     * Save/bookmark a VDA
     */
    public static function save_vda()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'skd_ajax_nonce')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Please log in']);
            return;
        }

        global $wpdb;
        $employer_id = get_current_user_id();
        $vda_id = intval($_POST['vda_id'] ?? 0);

        if (!$vda_id) {
            wp_send_json_error(['message' => 'Invalid VDA ID']);
            return;
        }

        $result = $wpdb->insert(
            $wpdb->prefix . 'skd_pl_saved_vdas',
            [
                'employer_id' => $employer_id,
                'vda_id' => $vda_id,
                'saved_at' => current_time('mysql')
            ],
            ['%d', '%d', '%s']
        );

        if ($result) {
            wp_send_json_success(['message' => 'VDA saved successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to save VDA or already saved']);
        }
    }

    /**
     * Remove VDA from saved list
     */
    public static function unsave_vda()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'skd_ajax_nonce')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Please log in']);
            return;
        }

        global $wpdb;
        $employer_id = get_current_user_id();
        $vda_id = intval($_POST['vda_id'] ?? 0);

        $result = $wpdb->delete(
            $wpdb->prefix . 'skd_pl_saved_vdas',
            [
                'employer_id' => $employer_id,
                'vda_id' => $vda_id
            ],
            ['%d', '%d']
        );

        if ($result) {
            wp_send_json_success(['message' => 'VDA removed from saved list']);
        } else {
            wp_send_json_error(['message' => 'Failed to remove VDA']);
        }
    }

    /**
     * Get saved VDAs
     */
    public static function get_saved_vdas()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'skd_ajax_nonce')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Please log in']);
            return;
        }

        global $wpdb;
        $employer_id = get_current_user_id();

        // Debug logging
        error_log('Get Saved VDAs - Employer ID: ' . $employer_id);

        // First check if there are any saved VDAs for this employer
        $count_check = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}skd_pl_saved_vdas WHERE employer_id = %d",
            $employer_id
        ));
        error_log('Saved VDAs count check: ' . $count_check);

        $saved_vdas = $wpdb->get_results($wpdb->prepare(
            "SELECT u.ID as user_id, u.display_name, u.user_email,
             p.tagline, p.hourly_rate, p.avatar_url, p.rating, p.total_reviews,
             p.city, p.country, p.timezone, p.bio, p.skills, p.specializations, 
             p.project_types, s.saved_at, s.notes
             FROM {$wpdb->prefix}skd_pl_saved_vdas s
             INNER JOIN {$wpdb->users} u ON s.vda_id = u.ID
             INNER JOIN {$wpdb->prefix}skd_pl_user_profiles p ON u.ID = p.user_id
             WHERE s.employer_id = %d
             ORDER BY s.saved_at DESC",
            $employer_id
        ));

        error_log('Saved VDAs query result count: ' . count($saved_vdas));
        if ($wpdb->last_error) {
            error_log('SQL Error: ' . $wpdb->last_error);
        }

        if (empty($saved_vdas)) {
            $html = '<div class="employer-empty-state">
                <iconify-icon icon="material-symbols:bookmark-outline" style="font-size: 80px; color: #ccc;"></iconify-icon>
                <h3>No Saved VDAs</h3>
                <p>Browse VDAs and save your favorites for quick access.</p>
            </div>';
        } else {
            $html = '<div class="skd-professionals-grid">';
            foreach ($saved_vdas as $pro) {
                // Get timezone display
                $timezone_display = 'Remote';
                if (!empty($pro->timezone)) {
                    $tz = $wpdb->get_row($wpdb->prepare(
                        "SELECT name, offset FROM {$wpdb->prefix}skd_pl_timezones WHERE id = %d",
                        $pro->timezone
                    ));
                    if ($tz) {
                        $timezone_display = $tz->name . ' • ' . $tz->offset;
                    }
                }

                // Get skills
                $pro_skills = json_decode($pro->skills ?? '[]', true);
                $skills_html = '';
                if (!empty($pro_skills) && is_array($pro_skills)) {
                    $skills_to_show = array_slice($pro_skills, 0, 4);
                    foreach ($skills_to_show as $skill_id) {
                        $skill = $wpdb->get_row($wpdb->prepare(
                            "SELECT name FROM {$wpdb->prefix}skd_pl_skills WHERE id = %d",
                            $skill_id
                        ));
                        if ($skill) {
                            $skills_html .= '<span class="skd-skill-tag">' . esc_html($skill->name) . '</span>';
                        }
                    }
                }

                // Get specializations for featured skills
                $specializations = json_decode($pro->specializations ?? '[]', true);
                $featured_skills = '';
                if (!empty($specializations) && is_array($specializations)) {
                    $specs_to_show = array_slice($specializations, 0, 2);
                    foreach ($specs_to_show as $spec_id) {
                        $spec = $wpdb->get_row($wpdb->prepare(
                            "SELECT name FROM {$wpdb->prefix}skd_pl_specializations WHERE id = %d",
                            $spec_id
                        ));
                        if ($spec) {
                            $featured_skills .= '<span class="skd-skill-tag-featured"><iconify-icon icon="mdi:check"></iconify-icon> ' . esc_html($spec->name) . '</span>';
                        }
                    }
                }

                // Get project types for icons
                $project_types = json_decode($pro->project_types ?? '[]', true);
                $project_icons = '';
                if (!empty($project_types) && is_array($project_types)) {
                    $icons_map = [
                        1 => 'mdi:home', // Residential
                        2 => 'mdi:office-building', // Commercial
                        3 => 'mdi:silverware-fork-knife', // Hospitality
                        4 => 'mdi:store', // Retail
                        5 => 'mdi:pot-steam', // Kitchen & Bath
                    ];
                    $types_to_show = array_slice($project_types, 0, 3);
                    foreach ($types_to_show as $pt_id) {
                        $icon = $icons_map[$pt_id] ?? 'mdi:cube-outline';
                        $project_icons .= '<div class="skd-project-icon"><iconify-icon icon="' . $icon . '"></iconify-icon></div>';
                    }
                }

                $avatar_url = !empty($pro->avatar_url) ? $pro->avatar_url : get_avatar_url($pro->user_id, ['size' => 200]);
                $profile_url = home_url('/vda-profile/?vda_id=' . $pro->user_id);

                $html .= '<div class="skd-professional-card" data-pro-id="' . $pro->user_id . '">
                    <div class="skd-pro-avatar">';

                if (!empty($pro->avatar_url)) {
                    $html .= '<img src="' . esc_url($pro->avatar_url) . '" alt="' . esc_attr($pro->display_name) . '">';
                } else {
                    $html .= '<div class="skd-avatar-placeholder">' . strtoupper(substr($pro->display_name, 0, 1)) . '</div>';
                }

                $html .= '</div>
                    <div class="skd-pro-header">
                        <h3 class="skd-pro-name">
                            <a href="' . esc_url($profile_url) . '">' . esc_html($pro->display_name) . '</a>
                        </h3>
                        <p class="skd-pro-title">' . esc_html($pro->tagline ?: 'Virtual Design Assistant') . '</p>
                    </div>
                    <div class="skd-pro-meta">
                        <span class="skd-pro-location">
                            <iconify-icon icon="mdi:map-marker"></iconify-icon>
                            ' . esc_html($timezone_display) . '
                        </span>
                        <span class="skd-pro-rate">
                            <iconify-icon icon="mdi:currency-usd"></iconify-icon>
                            ' . number_format($pro->hourly_rate ?: 0, 0) . '/hr
                        </span>
                    </div>';

                if (!empty($pro->bio)) {
                    $html .= '<div class="skd-pro-bio">' . esc_html(wp_trim_words($pro->bio, 20, '...')) . '</div>';
                }

                if (!empty($skills_html)) {
                    $html .= '<div class="skd-pro-skills">' . $skills_html . '</div>';
                }

                if (!empty($featured_skills)) {
                    $html .= '<div class="skd-pro-featured-skills">' . $featured_skills . '</div>';
                }

                if (!empty($project_icons)) {
                    $html .= '<div class="skd-pro-project-types">' . $project_icons . '</div>';
                }

                $html .= '<div class="skd-pro-actions">
                        <a href="' . esc_url($profile_url) . '" class="skd-btn skd-btn-primary" target="_BLANK" >View Profile</a>
                        <button class="skd-btn skd-btn-secondary unsave-vda" data-id="' . $pro->user_id . '">
                            <iconify-icon icon="mdi:bookmark-remove"></iconify-icon> Remove
                        </button>
                    </div>
                </div>';
            }
            $html .= '</div>';
        }

        wp_send_json_success(['html' => $html, 'count' => count($saved_vdas)]);
    }

    /**
     * Get employer's job applications
     */
    public static function get_employer_applications()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'skd_ajax_nonce')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Please log in']);
            return;
        }

        global $wpdb;
        $user_id = get_current_user_id();

        $applications = $wpdb->get_results($wpdb->prepare(
            "SELECT a.*, j.title as job_title, u.display_name as applicant_name,
             p.avatar_url, p.tagline, p.hourly_rate
             FROM {$wpdb->prefix}skd_pl_job_applications a
             INNER JOIN {$wpdb->prefix}skd_pl_jobs j ON a.job_id = j.id
             INNER JOIN {$wpdb->users} u ON a.applicant_id = u.ID
             LEFT JOIN {$wpdb->prefix}skd_pl_user_profiles p ON a.applicant_id = p.user_id
             WHERE j.employer_id = %d
             ORDER BY a.created_at DESC",
            $user_id
        ));

        if (empty($applications)) {
            $html = '<div class="employer-empty-state">
                <iconify-icon icon="material-symbols:description-outline" style="font-size: 80px; color: #ccc;"></iconify-icon>
                <h3>No Applications Yet</h3>
                <p>Applications will appear here once VDAs start applying to your jobs.</p>
            </div>';
        } else {
            $html = '<div class="applications-list">';
            foreach ($applications as $app) {
                $status_class = 'status-' . $app->status;
                $avatar_html = !empty($app->avatar_url)
                    ? '<img src="' . esc_url($app->avatar_url) . '" alt="' . esc_attr($app->applicant_name) . '">'
                    : '<div class="vda-avatar-placeholder">' . strtoupper(substr($app->applicant_name, 0, 1)) . '</div>';

                $html .= '<div class="application-card">
                    <div class="application-header">
                        <div class="applicant-info">
                            <div class="vda-avatar">' . $avatar_html . '</div>
                            <div>
                                <h4>' . esc_html($app->applicant_name) . '</h4>
                                <p class="job-title">Applied for: ' . esc_html($app->job_title) . '</p>
                            </div>
                        </div>
                        <span class="application-status ' . $status_class . '">' . ucfirst($app->status) . '</span>
                    </div>
                    <div class="application-body">
                        <p>' . esc_html(wp_trim_words($app->cover_letter, 30)) . '</p>
                        <div class="application-meta">
                            <span>Rate: $' . number_format($app->proposed_rate ?: $app->hourly_rate, 0) . '/hr</span>
                            <span>Applied ' . human_time_diff(strtotime($app->created_at), current_time('timestamp')) . ' ago</span>
                        </div>
                    </div>
                    <div class="application-actions">
                        <button class="employer-btn employer-btn-sm view-application" data-id="' . $app->id . '">View Full Application</button>
                        <button class="employer-btn employer-btn-sm shortlist-application" data-id="' . $app->id . '">Shortlist</button>
                    </div>
                </div>';
            }
            $html .= '</div>';
        }

        wp_send_json_success(['html' => $html, 'count' => count($applications)]);
    }

    /**
     * Update application status
     */
    public static function update_application_status()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'skd_ajax_nonce')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Please log in']);
            return;
        }

        global $wpdb;
        $application_id = intval($_POST['application_id'] ?? 0);
        $status = sanitize_text_field($_POST['status'] ?? '');

        if (!in_array($status, ['pending', 'shortlisted', 'rejected', 'hired'])) {
            wp_send_json_error(['message' => 'Invalid status']);
            return;
        }

        $updated = $wpdb->update(
            $wpdb->prefix . 'skd_pl_job_applications',
            ['status' => $status, 'updated_at' => current_time('mysql')],
            ['id' => $application_id],
            ['%s', '%s'],
            ['%d']
        );

        if ($updated !== false) {
            wp_send_json_success(['message' => 'Application status updated']);
        } else {
            wp_send_json_error(['message' => 'Failed to update status']);
        }
    }

    /**
     * Post a new job
     */
    public static function post_job()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'skd_ajax_nonce')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Please log in']);
            return;
        }

        global $wpdb;
        $user_id = get_current_user_id();

        $title = sanitize_text_field($_POST['title'] ?? '');
        $description = wp_kses_post($_POST['description'] ?? '');
        $requirements = wp_kses_post($_POST['requirements'] ?? '');
        $budget_min = floatval($_POST['budget_min'] ?? 0);
        $budget_max = floatval($_POST['budget_max'] ?? 0);
        $budget_type = sanitize_text_field($_POST['budget_type'] ?? 'project');
        $job_type = sanitize_text_field($_POST['job_type'] ?? 'one-time');
        $location = sanitize_text_field($_POST['location'] ?? '');

        if (empty($title) || empty($description)) {
            wp_send_json_error(['message' => 'Title and description are required']);
            return;
        }

        $slug = sanitize_title($title);

        $result = $wpdb->insert(
            $wpdb->prefix . 'skd_pl_jobs',
            [
                'employer_id' => $user_id,
                'title' => $title,
                'slug' => $slug,
                'description' => $description,
                'requirements' => $requirements,
                'budget_min' => $budget_min,
                'budget_max' => $budget_max,
                'budget_type' => $budget_type,
                'job_type' => $job_type,
                'location' => $location,
                'status' => 'open',
                'created_at' => current_time('mysql')
            ],
            ['%d', '%s', '%s', '%s', '%s', '%f', '%f', '%s', '%s', '%s', '%s', '%s']
        );

        if ($result) {
            wp_send_json_success(['message' => 'Job posted successfully', 'job_id' => $wpdb->insert_id]);
        } else {
            wp_send_json_error(['message' => 'Failed to post job']);
        }
    }

    /**
     * Update an existing job
     */
    public static function update_job()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'skd_ajax_nonce')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Please log in']);
            return;
        }

        global $wpdb;
        $user_id = get_current_user_id();
        $job_id = intval($_POST['job_id'] ?? 0);

        // Verify ownership
        $job = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}skd_pl_jobs WHERE id = %d AND employer_id = %d",
            $job_id,
            $user_id
        ));

        if (!$job) {
            wp_send_json_error(['message' => 'Job not found or access denied']);
            return;
        }

        $title = sanitize_text_field($_POST['title'] ?? '');
        $description = wp_kses_post($_POST['description'] ?? '');
        $status = sanitize_text_field($_POST['status'] ?? 'open');

        $updated = $wpdb->update(
            $wpdb->prefix . 'skd_pl_jobs',
            [
                'title' => $title,
                'description' => $description,
                'status' => $status,
                'updated_at' => current_time('mysql')
            ],
            ['id' => $job_id],
            ['%s', '%s', '%s', '%s'],
            ['%d']
        );

        if ($updated !== false) {
            wp_send_json_success(['message' => 'Job updated successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to update job']);
        }
    }

    /**
     * Delete a job
     */
    public static function delete_job()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'skd_ajax_nonce')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Please log in']);
            return;
        }

        global $wpdb;
        $user_id = get_current_user_id();
        $job_id = intval($_POST['job_id'] ?? 0);

        // Verify ownership
        $job = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}skd_pl_jobs WHERE id = %d AND employer_id = %d",
            $job_id,
            $user_id
        ));

        if (!$job) {
            wp_send_json_error(['message' => 'Job not found or access denied']);
            return;
        }

        $deleted = $wpdb->delete(
            $wpdb->prefix . 'skd_pl_jobs',
            ['id' => $job_id],
            ['%d']
        );

        if ($deleted) {
            wp_send_json_success(['message' => 'Job deleted successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to delete job']);
        }
    }

    /**
     * Toggle job status (open/closed)
     */
    public static function toggle_job_status()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'skd_ajax_nonce')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Please log in']);
            return;
        }

        global $wpdb;
        $user_id = get_current_user_id();
        $job_id = intval($_POST['job_id'] ?? 0);

        // Verify ownership
        $job = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}skd_pl_jobs WHERE id = %d AND employer_id = %d",
            $job_id,
            $user_id
        ));

        if (!$job) {
            wp_send_json_error(['message' => 'Job not found or access denied']);
            return;
        }

        // Toggle status
        $new_status = $job->status === 'open' ? 'closed' : 'open';

        $updated = $wpdb->update(
            $wpdb->prefix . 'skd_pl_jobs',
            ['status' => $new_status, 'updated_at' => current_time('mysql')],
            ['id' => $job_id],
            ['%s', '%s'],
            ['%d']
        );

        if ($updated !== false) {
            wp_send_json_success([
                'message' => 'Job status updated successfully',
                'new_status' => $new_status
            ]);
        } else {
            wp_send_json_error(['message' => 'Failed to update job status']);
        }
    }

    /**
     * Get job details for viewing/editing
     */
    public static function get_job_details()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'skd_ajax_nonce')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Please log in']);
            return;
        }

        global $wpdb;
        $user_id = get_current_user_id();
        $job_id = intval($_POST['job_id'] ?? 0);

        // Verify ownership
        $job = $wpdb->get_row($wpdb->prepare(
            "SELECT j.*,
             (SELECT COUNT(*) FROM {$wpdb->prefix}skd_pl_job_applications WHERE job_id = j.id) as applications_count,
             (SELECT COUNT(*) FROM {$wpdb->prefix}skd_pl_job_applications WHERE job_id = j.id AND status = 'pending') as pending_count
             FROM {$wpdb->prefix}skd_pl_jobs j
             WHERE j.id = %d AND j.employer_id = %d",
            $job_id,
            $user_id
        ));

        if (!$job) {
            wp_send_json_error(['message' => 'Job not found or access denied']);
            return;
        }

        wp_send_json_success(['job' => $job]);
    }
}
