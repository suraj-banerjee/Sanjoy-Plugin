<?php

if (!defined('ABSPATH')) {
    exit;
}

class SKD_PL_VDA_Profile
{
    public static function init()
    {
        // Profile update handlers
        add_action('wp_ajax_skd_update_profile_basic', [__CLASS__, 'update_profile_basic']);
        add_action('wp_ajax_skd_update_profile_skills', [__CLASS__, 'update_profile_skills']);
        add_action('wp_ajax_skd_update_profile_services', [__CLASS__, 'update_profile_services']);
        add_action('wp_ajax_skd_update_profile_specializations', [__CLASS__, 'update_profile_specializations']);
        add_action('wp_ajax_skd_update_profile_experience', [__CLASS__, 'update_profile_experience']);
        add_action('wp_ajax_skd_update_profile_rates', [__CLASS__, 'update_profile_rates']);
        add_action('wp_ajax_skd_update_profile_social', [__CLASS__, 'update_profile_social']);

        // Portfolio handlers
        add_action('wp_ajax_skd_add_portfolio_item', [__CLASS__, 'add_portfolio_item']);
        add_action('wp_ajax_skd_update_portfolio_item', [__CLASS__, 'update_portfolio_item']);
        add_action('wp_ajax_skd_delete_portfolio_item', [__CLASS__, 'delete_portfolio_item']);
        add_action('wp_ajax_skd_get_portfolio_items', [__CLASS__, 'get_portfolio_items']);
        add_action('wp_ajax_skd_get_portfolio_item', [__CLASS__, 'get_portfolio_item']);

        // Profile image upload
        add_action('wp_ajax_skd_upload_avatar', [__CLASS__, 'upload_avatar']);
        add_action('wp_ajax_skd_upload_cover_image', [__CLASS__, 'upload_cover_image']);

        // Account settings handlers
        add_action('wp_ajax_skd_update_notification_settings', [__CLASS__, 'update_notification_settings']);
        add_action('wp_ajax_skd_update_account_info', [__CLASS__, 'update_account_info']);
        add_action('wp_ajax_skd_update_privacy_settings', [__CLASS__, 'update_privacy_settings']);
        add_action('wp_ajax_skd_deactivate_account', [__CLASS__, 'deactivate_account']);
        add_action('wp_ajax_skd_delete_account', [__CLASS__, 'delete_account']);

        // Certification handlers
        add_action('wp_ajax_skd_add_certification', [__CLASS__, 'add_certification']);
        add_action('wp_ajax_skd_update_certification', [__CLASS__, 'update_certification']);
        add_action('wp_ajax_skd_remove_certification', [__CLASS__, 'remove_certification']);
    }

    /**
     * Update basic profile information
     */
    public static function update_profile_basic()
    {
        check_ajax_referer('skd_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'You must be logged in']);
        }

        global $wpdb;
        $user_id = get_current_user_id();
        $table = $wpdb->prefix . 'skd_pl_user_profiles';

        // Prepare data array with sanitization
        $data = [
            'tagline' => sanitize_text_field($_POST['tagline'] ?? ''),
            'short_description' => sanitize_textarea_field($_POST['short_description'] ?? ''),
            'bio' => wp_kses_post($_POST['bio'] ?? ''), // Allow HTML from wp_editor
            'what_i_offer' => wp_kses_post($_POST['what_i_offer'] ?? ''), // Allow HTML from wp_editor
            'company_name' => sanitize_text_field($_POST['company_name'] ?? ''),
            'country' => sanitize_text_field($_POST['country'] ?? ''),
            'city' => sanitize_text_field($_POST['city'] ?? ''),
            'timezone' => sanitize_text_field($_POST['timezone'] ?? ''),
        ];

        // Update WordPress user data
        $first_name = sanitize_text_field($_POST['first_name'] ?? '');
        $last_name = sanitize_text_field($_POST['last_name'] ?? '');
        $display_name = trim($first_name . ' ' . $last_name);

        if (!empty($first_name) || !empty($last_name)) {
            wp_update_user([
                'ID' => $user_id,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'display_name' => $display_name
            ]);
        }

        $result = $wpdb->update($table, $data, ['user_id' => $user_id]);

        if ($result !== false) {
            self::calculate_profile_completeness($user_id);
            wp_send_json_success([
                'message' => 'Profile updated successfully',
                'display_name' => $display_name,
                'tagline' => $data['tagline']
            ]);
        } else {
            wp_send_json_error(['message' => 'Failed to update profile']);
        }
    }

    /**
     * Update skills and specializations
     */
    public static function update_profile_skills()
    {
        check_ajax_referer('skd_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'You must be logged in']);
        }

        global $wpdb;
        $user_id = get_current_user_id();
        $table = $wpdb->prefix . 'skd_pl_user_profiles';

        // Get skills - only accept integers from master table
        $skills = isset($_POST['skills']) ? array_map('intval', array_filter((array)$_POST['skills'], 'is_numeric')) : [];
        $skills_json = json_encode($skills);

        // Get specializations - only accept integers from master table
        $specializations = isset($_POST['specializations']) ? array_map('intval', array_filter((array)$_POST['specializations'], 'is_numeric')) : [];
        $specs_json = json_encode($specializations);

        // Get project types - only accept integers from master table
        $project_types = isset($_POST['project_types']) ? array_map('intval', array_filter((array)$_POST['project_types'], 'is_numeric')) : [];
        $project_types_json = json_encode($project_types);

        // Get service types - only accept integers from master table
        $service_types = isset($_POST['service_types']) ? array_map('intval', array_filter((array)$_POST['service_types'], 'is_numeric')) : [];
        $service_types_json = json_encode($service_types);

        // Update all fields
        $data = [
            'skills' => $skills_json,
            'specializations' => $specs_json,
            'project_types' => $project_types_json,
            'service_types' => $service_types_json
        ];

        $result = $wpdb->update(
            $table,
            $data,
            ['user_id' => $user_id]
        );

        if ($result !== false) {
            self::calculate_profile_completeness($user_id);
            wp_send_json_success(['message' => 'Skills, specializations, and service types updated successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to update profile']);
        }
    }

    /**
     * Update services
     */
    public static function update_profile_services()
    {
        check_ajax_referer('skd_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'You must be logged in']);
        }

        global $wpdb;
        $user_id = get_current_user_id();
        $table = $wpdb->prefix . 'skd_pl_user_profiles';

        $services = isset($_POST['services']) ? array_map('intval', (array)$_POST['services']) : [];
        $services_json = json_encode($services);

        $result = $wpdb->update(
            $table,
            ['services_offered' => $services_json],
            ['user_id' => $user_id]
        );

        if ($result !== false) {
            self::calculate_profile_completeness($user_id);
            wp_send_json_success(['message' => 'Services updated successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to update services']);
        }
    }

    /**
     * Update specializations
     */
    public static function update_profile_specializations()
    {
        check_ajax_referer('skd_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'You must be logged in']);
        }

        global $wpdb;
        $user_id = get_current_user_id();
        $table = $wpdb->prefix . 'skd_pl_user_profiles';

        $specializations = isset($_POST['specializations']) ? array_map('intval', (array)$_POST['specializations']) : [];
        $specs_json = json_encode($specializations);

        $result = $wpdb->update(
            $table,
            ['specializations' => $specs_json],
            ['user_id' => $user_id]
        );

        if ($result !== false) {
            self::calculate_profile_completeness($user_id);
            wp_send_json_success(['message' => 'Specializations updated successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to update specializations']);
        }
    }

    /**
     * Update experience
     */
    public static function update_profile_experience()
    {
        check_ajax_referer('skd_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'You must be logged in']);
        }

        global $wpdb;
        $user_id = get_current_user_id();
        $table = $wpdb->prefix . 'skd_pl_user_profiles';

        $languages = isset($_POST['languages']) ? array_map('sanitize_text_field', (array)$_POST['languages']) : [];

        $data = [
            'years_experience' => intval($_POST['years_experience'] ?? 0),
            'experience_level' => sanitize_text_field($_POST['experience_level'] ?? ''),
            'education_level' => sanitize_text_field($_POST['education_level'] ?? ''),
            'languages_spoken' => json_encode($languages)
        ];

        $result = $wpdb->update($table, $data, ['user_id' => $user_id]);

        if ($result !== false) {
            self::calculate_profile_completeness($user_id);
            wp_send_json_success(['message' => 'Experience updated successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to update experience']);
        }
    }

    /**
     * Update rates and availability
     */
    public static function update_profile_rates()
    {
        check_ajax_referer('skd_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'You must be logged in']);
        }

        global $wpdb;
        $user_id = get_current_user_id();
        $table = $wpdb->prefix . 'skd_pl_user_profiles';

        // Get languages and sanitize as integers
        $languages = isset($_POST['languages_spoken']) ? array_map('intval', (array)$_POST['languages_spoken']) : [];
        $languages_json = json_encode($languages);

        // Calculate experience level based on years (using dynamic database levels)
        $years_experience = intval($_POST['years_experience'] ?? 0);
        $experience_level_obj = SKD_PL_Experience_Levels::get_level_by_years($years_experience);
        $experience_level = $experience_level_obj ? $experience_level_obj->slug : 'not-set';

        $data = [
            'hourly_rate' => floatval($_POST['hourly_rate'] ?? 0),
            'availability_status' => intval($_POST['availability_status'] ?? 0),
            'years_experience' => $years_experience,
            'experience_level' => $experience_level,
            'response_time' => intval($_POST['response_time'] ?? 0),
            'languages_spoken' => $languages_json
        ];

        $result = $wpdb->update($table, $data, ['user_id' => $user_id]);

        if ($result !== false) {
            self::calculate_profile_completeness($user_id);
            wp_send_json_success([
                'message' => 'Rates and availability updated successfully',
                'experience_level' => $experience_level
            ]);
        } else {
            wp_send_json_error(['message' => 'Failed to update rates and availability']);
        }
    }

    /**
     * Update social links
     */
    public static function update_profile_social()
    {
        check_ajax_referer('skd_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'You must be logged in']);
        }

        global $wpdb;
        $user_id = get_current_user_id();
        $table = $wpdb->prefix . 'skd_pl_user_profiles';

        $data = [
            'website_url' => esc_url_raw($_POST['website_url'] ?? ''),
            'linkedin_url' => esc_url_raw($_POST['linkedin_url'] ?? ''),
            'behance_url' => esc_url_raw($_POST['behance_url'] ?? ''),
            'instagram_url' => esc_url_raw($_POST['instagram_url'] ?? ''),
            'pinterest_url' => esc_url_raw($_POST['pinterest_url'] ?? ''),
            'portfolio_url' => esc_url_raw($_POST['portfolio_url'] ?? '')
        ];

        $result = $wpdb->update($table, $data, ['user_id' => $user_id]);

        if ($result !== false) {
            self::calculate_profile_completeness($user_id);
            wp_send_json_success(['message' => 'Social links updated successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to update social links']);
        }
    }

    /**
     * Add portfolio item
     */
    public static function add_portfolio_item()
    {
        check_ajax_referer('skd_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'You must be logged in']);
        }

        global $wpdb;
        $user_id = get_current_user_id();
        $table = $wpdb->prefix . 'skd_pl_user_portfolio';

        // Handle multi-image upload
        $uploaded_images = [];
        $featured_image = '';

        if (!empty($_FILES['images']['name'][0])) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');

            $files = $_FILES['images'];
            $max_size = 1 * 1024 * 1024; // 1MB

            foreach ($files['name'] as $key => $value) {
                if ($files['name'][$key]) {
                    // Validate file size
                    if ($files['size'][$key] > $max_size) {
                        wp_send_json_error(['message' => 'Image ' . $files['name'][$key] . ' exceeds 1MB limit']);
                    }

                    $file = [
                        'name' => $files['name'][$key],
                        'type' => $files['type'][$key],
                        'tmp_name' => $files['tmp_name'][$key],
                        'error' => $files['error'][$key],
                        'size' => $files['size'][$key]
                    ];

                    $upload = wp_handle_upload($file, ['test_form' => false]);

                    if (isset($upload['url'])) {
                        $uploaded_images[] = $upload['url'];
                        if (empty($featured_image)) {
                            $featured_image = $upload['url'];
                        }
                    } else if (isset($upload['error'])) {
                        wp_send_json_error(['message' => 'Upload error: ' . $upload['error']]);
                    }
                }
            }
        }

        // Process tags - convert comma-separated to JSON array
        $tags_json = null;
        if (!empty($_POST['tags'])) {
            $tags_array = array_map('trim', explode(',', $_POST['tags']));
            $tags_array = array_filter($tags_array); // Remove empty values
            $tags_json = json_encode(array_values($tags_array));
        }

        $data = [
            'user_id' => $user_id,
            'title' => sanitize_text_field($_POST['title'] ?? ''),
            'description' => wp_kses_post($_POST['description'] ?? ''),
            'category_id' => intval($_POST['category_id'] ?? 0),
            'tags' => $tags_json,
            'images' => !empty($uploaded_images) ? json_encode($uploaded_images) : null,
            'featured_image' => $featured_image,
            'year' => sanitize_text_field($_POST['year'] ?? ''),
            'status' => 'published'
        ];

        error_log('Portfolio tags saved: ' . $tags_json);

        // Specify formats to prevent over-escaping
        $formats = ['%d', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s'];
        $result = $wpdb->insert($table, $data, $formats);

        if ($result) {
            self::calculate_profile_completeness($user_id);
            wp_send_json_success([
                'message' => 'Portfolio item added successfully',
                'id' => $wpdb->insert_id
            ]);
        } else {
            wp_send_json_error(['message' => 'Failed to add portfolio item: ' . $wpdb->last_error]);
        }
    }

    /**
     * Update portfolio item
     */
    public static function update_portfolio_item()
    {
        check_ajax_referer('skd_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'You must be logged in']);
        }

        global $wpdb;
        $user_id = get_current_user_id();
        $table = $wpdb->prefix . 'skd_pl_user_portfolio';
        $item_id = intval($_POST['id'] ?? 0);

        // Verify ownership
        $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d AND user_id = %d", $item_id, $user_id));
        if (!$item) {
            wp_send_json_error(['message' => 'Portfolio item not found']);
        }

        // Get existing images from hidden field
        $existing_images = [];
        $old_images = [];

        // Get old images from database for comparison
        if ($item->images) {
            $old_images = json_decode($item->images, true);
            if (!is_array($old_images)) {
                $old_images = [];
            }
        }

        if (!empty($_POST['existing_images'])) {
            $existing_images = json_decode(stripslashes($_POST['existing_images']), true);
            if (!is_array($existing_images)) {
                $existing_images = [];
            }
        } else {
            // Fallback to database if not provided
            $existing_images = $old_images;
        }

        // Delete removed images
        $removed_images = array_diff($old_images, $existing_images);
        if (!empty($removed_images)) {
            $upload_dir = wp_upload_dir();
            foreach ($removed_images as $image_url) {
                $file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $image_url);
                if (file_exists($file_path)) {
                    @unlink($file_path);
                }
            }
        }

        // Handle new image uploads
        $uploaded_images = [];
        $featured_image = $item->featured_image; // Keep existing featured image

        if (!empty($_FILES['images']['name'][0])) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');

            $files = $_FILES['images'];
            $max_size = 1 * 1024 * 1024; // 1MB

            foreach ($files['name'] as $key => $value) {
                if ($files['name'][$key]) {
                    // Validate file size
                    if ($files['size'][$key] > $max_size) {
                        wp_send_json_error(['message' => 'Image ' . $files['name'][$key] . ' exceeds 1MB limit']);
                    }

                    $file = [
                        'name' => $files['name'][$key],
                        'type' => $files['type'][$key],
                        'tmp_name' => $files['tmp_name'][$key],
                        'error' => $files['error'][$key],
                        'size' => $files['size'][$key]
                    ];

                    $upload = wp_handle_upload($file, ['test_form' => false]);

                    if (isset($upload['url'])) {
                        $uploaded_images[] = $upload['url'];
                    } else if (isset($upload['error'])) {
                        wp_send_json_error(['message' => 'Upload error: ' . $upload['error']]);
                    }
                }
            }
        }

        // Combine existing and newly uploaded images
        $all_images = array_merge($existing_images, $uploaded_images);

        // Set featured image to first image if available
        if (!empty($all_images)) {
            $featured_image = $all_images[0];
        }

        // Process tags - convert comma-separated to JSON array
        $tags_json = null;
        if (!empty($_POST['tags'])) {
            $tags_array = array_map('trim', explode(',', $_POST['tags']));
            $tags_array = array_filter($tags_array);
            $tags_json = json_encode(array_values($tags_array));
        }

        $data = [
            'title' => sanitize_text_field($_POST['title'] ?? ''),
            'description' => wp_kses_post($_POST['description'] ?? ''),
            'category_id' => intval($_POST['category_id'] ?? 0),
            'tags' => $tags_json,
            'year' => sanitize_text_field($_POST['year'] ?? '')
        ];

        // Update images if there are any (existing or new)
        if (!empty($all_images)) {
            $data['images'] = json_encode($all_images);
            $data['featured_image'] = $featured_image;
        }

        // Specify formats to prevent over-escaping
        $formats = ['%s', '%s', '%d', '%s', '%s'];
        $result = $wpdb->update($table, $data, ['id' => $item_id, 'user_id' => $user_id], $formats, ['%d', '%d']);

        if ($result !== false) {
            wp_send_json_success(['message' => 'Portfolio item updated successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to update portfolio item']);
        }
    }

    /**
     * Delete portfolio item
     */
    public static function delete_portfolio_item()
    {
        check_ajax_referer('skd_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'You must be logged in']);
        }

        global $wpdb;
        $user_id = get_current_user_id();
        $table = $wpdb->prefix . 'skd_pl_user_portfolio';
        $item_id = intval($_POST['item_id'] ?? 0);

        // Get portfolio item to delete its images
        $item = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE id = %d AND user_id = %d",
            $item_id,
            $user_id
        ));

        if (!$item) {
            wp_send_json_error(['message' => 'Portfolio item not found']);
        }

        // Delete all portfolio images
        if (!empty($item->images)) {
            $images = json_decode($item->images, true);
            if (is_array($images)) {
                $upload_dir = wp_upload_dir();
                foreach ($images as $image_url) {
                    $file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $image_url);
                    if (file_exists($file_path)) {
                        @unlink($file_path);
                    }
                }
            }
        }

        $result = $wpdb->delete($table, ['id' => $item_id, 'user_id' => $user_id]);

        if ($result) {
            self::calculate_profile_completeness($user_id);
            wp_send_json_success(['message' => 'Portfolio item deleted successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to delete portfolio item']);
        }
    }

    /**
     * Get portfolio items
     */
    public static function get_portfolio_items()
    {
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'You must be logged in']);
        }

        global $wpdb;
        $user_id = get_current_user_id();
        $portfolio_table = $wpdb->prefix . 'skd_pl_user_portfolio';
        $categories_table = $wpdb->prefix . 'skd_pl_portfolio_categories';

        $items = $wpdb->get_results($wpdb->prepare(
            "SELECT p.*, c.name as category_name 
             FROM $portfolio_table p 
             LEFT JOIN $categories_table c ON p.category_id = c.id 
             WHERE p.user_id = %d 
             ORDER BY p.sort_order ASC, p.created_at DESC",
            $user_id
        ));

        wp_send_json_success(['items' => $items]);
    }

    /**
     * Get single portfolio item
     */
    public static function get_portfolio_item()
    {
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'You must be logged in']);
        }

        global $wpdb;
        $user_id = get_current_user_id();
        $portfolio_table = $wpdb->prefix . 'skd_pl_user_portfolio';
        $categories_table = $wpdb->prefix . 'skd_pl_portfolio_categories';
        $item_id = intval($_POST['item_id'] ?? 0);

        $item = $wpdb->get_row($wpdb->prepare(
            "SELECT p.*, c.name as category_name 
             FROM $portfolio_table p 
             LEFT JOIN $categories_table c ON p.category_id = c.id 
             WHERE p.id = %d AND p.user_id = %d",
            $item_id,
            $user_id
        ));

        if ($item) {
            wp_send_json_success(['item' => $item]);
        } else {
            wp_send_json_error(['message' => 'Portfolio item not found']);
        }
    }

    /**
     * Upload avatar
     */
    public static function upload_avatar()
    {
        check_ajax_referer('skd_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'You must be logged in']);
        }

        if (empty($_FILES['avatar'])) {
            wp_send_json_error(['message' => 'No file uploaded']);
        }

        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        global $wpdb;
        $user_id = get_current_user_id();
        $table = $wpdb->prefix . 'skd_pl_user_profiles';

        // Get old avatar URL to delete later
        $old_avatar = $wpdb->get_var($wpdb->prepare(
            "SELECT avatar_url FROM {$table} WHERE user_id = %d",
            $user_id
        ));

        $file = $_FILES['avatar'];
        $upload = wp_handle_upload($file, ['test_form' => false]);

        if (isset($upload['error'])) {
            wp_send_json_error(['message' => $upload['error']]);
        }

        // Update database with new avatar URL
        $wpdb->update(
            $table,
            ['avatar_url' => $upload['url']],
            ['user_id' => $user_id]
        );

        // Delete old avatar file if it exists and is in uploads directory
        if (!empty($old_avatar)) {
            $upload_dir = wp_upload_dir();
            $old_file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $old_avatar);
            if (file_exists($old_file_path)) {
                @unlink($old_file_path);
            }
        }

        // Recalculate profile completion
        self::calculate_profile_completeness($user_id);

        wp_send_json_success([
            'message' => 'Avatar uploaded successfully',
            'url' => $upload['url']
        ]);
    }

    /**
     * Upload cover image
     */
    public static function upload_cover_image()
    {
        check_ajax_referer('skd_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'You must be logged in']);
        }

        if (empty($_FILES['cover_image'])) {
            wp_send_json_error(['message' => 'No file uploaded']);
        }

        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        global $wpdb;
        $user_id = get_current_user_id();
        $table = $wpdb->prefix . 'skd_pl_user_profiles';

        // Get old cover image URL to delete later
        $old_cover = $wpdb->get_var($wpdb->prepare(
            "SELECT cover_image_url FROM {$table} WHERE user_id = %d",
            $user_id
        ));

        $file = $_FILES['cover_image'];
        $upload = wp_handle_upload($file, ['test_form' => false]);

        if (isset($upload['error'])) {
            wp_send_json_error(['message' => $upload['error']]);
        }

        // Update database with new cover image URL
        $wpdb->update(
            $table,
            ['cover_image_url' => $upload['url']],
            ['user_id' => $user_id]
        );

        // Delete old cover image file if it exists and is in uploads directory
        if (!empty($old_cover)) {
            $upload_dir = wp_upload_dir();
            $old_file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $old_cover);
            if (file_exists($old_file_path)) {
                @unlink($old_file_path);
            }
        }

        wp_send_json_success([
            'message' => 'Cover image uploaded successfully',
            'url' => $upload['url']
        ]);
    }

    /**
     * Calculate profile completeness percentage
     */
    public static function calculate_profile_completeness($user_id)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_user_profiles';
        $portfolio_table = $wpdb->prefix . 'skd_pl_user_portfolio';
        $cert_table = $wpdb->prefix . 'skd_pl_user_certifications';

        $profile = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE user_id = %d", $user_id));

        if (!$profile) {
            return 0;
        }

        $score = 0;
        $total_fields = 14; // Based on actual available fields

        // Basic Info (8 points)
        if (!empty($profile->tagline)) $score++;
        if (!empty($profile->short_description)) $score++;
        if (!empty($profile->bio)) $score++;
        if (!empty($profile->what_i_offer)) $score++;
        if (!empty($profile->country)) $score++;
        if (!empty($profile->city)) $score++;
        if (!empty($profile->timezone)) $score++;
        if (!empty($profile->avatar_url)) $score++;

        // Skills & Specializations (2 points)
        if (!empty($profile->skills) && $profile->skills != '[]') $score++;
        if (!empty($profile->specializations) && $profile->specializations != '[]') $score++;

        // Portfolio (2 points)
        $portfolio_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $portfolio_table WHERE user_id = %d",
            $user_id
        ));
        if ($portfolio_count > 0) $score++;
        if ($portfolio_count >= 3) $score++;

        // Certifications (2 points)
        if ($wpdb->get_var("SHOW TABLES LIKE '$cert_table'") == $cert_table) {
            $cert_count = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $cert_table WHERE user_id = %d",
                $user_id
            ));
            if ($cert_count > 0) $score++;
            if ($cert_count >= 2) $score++;
        }

        $percentage = round(($score / $total_fields) * 100);

        $wpdb->update(
            $table,
            ['profile_completeness' => $percentage],
            ['user_id' => $user_id]
        );

        return $percentage;
    }

    /**
     * Get user profile
     */
    public static function get_user_profile($user_id)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_user_profiles';

        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE user_id = %d", $user_id));
    }

    /**
     * Update notification settings
     */
    public static function update_notification_settings()
    {
        check_ajax_referer('skd_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'You must be logged in']);
        }

        $user_id = get_current_user_id();

        update_user_meta($user_id, 'email_job_alerts', sanitize_text_field($_POST['email_job_alerts'] ?? 'no'));
        update_user_meta($user_id, 'email_messages', sanitize_text_field($_POST['email_messages'] ?? 'no'));
        update_user_meta($user_id, 'email_profile_views', sanitize_text_field($_POST['email_profile_views'] ?? 'no'));
        update_user_meta($user_id, 'email_marketing', sanitize_text_field($_POST['email_marketing'] ?? 'no'));

        wp_send_json_success(['message' => 'Notification preferences updated']);
    }

    /**
     * Update account information
     */
    public static function update_account_info()
    {
        check_ajax_referer('skd_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'You must be logged in']);
        }

        $user_id = get_current_user_id();

        $user_data = [
            'ID' => $user_id,
            'first_name' => sanitize_text_field($_POST['first_name'] ?? ''),
            'last_name' => sanitize_text_field($_POST['last_name'] ?? ''),
            'display_name' => sanitize_text_field($_POST['display_name'] ?? ''),
        ];

        $result = wp_update_user($user_data);

        if (is_wp_error($result)) {
            wp_send_json_error(['message' => $result->get_error_message()]);
        }

        // Update phone
        update_user_meta($user_id, 'phone', sanitize_text_field($_POST['phone'] ?? ''));

        wp_send_json_success(['message' => 'Account information updated']);
    }

    /**
     * Update privacy settings
     */
    public static function update_privacy_settings()
    {
        check_ajax_referer('skd_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'You must be logged in']);
        }

        $user_id = get_current_user_id();

        update_user_meta($user_id, 'profile_visibility', sanitize_text_field($_POST['profile_visibility'] ?? 'public'));
        update_user_meta($user_id, 'show_contact_info', sanitize_text_field($_POST['show_contact_info'] ?? 'no'));

        wp_send_json_success(['message' => 'Privacy settings updated']);
    }

    /**
     * Deactivate account
     */
    public static function deactivate_account()
    {
        check_ajax_referer('skd_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'You must be logged in']);
        }

        $user_id = get_current_user_id();
        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_user_profiles';

        $wpdb->update(
            $table,
            ['is_active' => 0],
            ['user_id' => $user_id]
        );

        wp_send_json_success(['message' => 'Account deactivated']);
    }

    /**
     * Delete account
     */
    public static function delete_account()
    {
        check_ajax_referer('skd_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'You must be logged in']);
        }

        $user_id = get_current_user_id();

        // Only allow VDA users to delete their own accounts
        $user_type = get_user_meta($user_id, 'skd_user_type', true);
        if ($user_type !== 'vda') {
            wp_send_json_error(['message' => 'Invalid user type']);
        }

        // Delete from custom tables
        global $wpdb;
        $wpdb->delete($wpdb->prefix . 'skd_pl_user_profiles', ['user_id' => $user_id]);
        $wpdb->delete($wpdb->prefix . 'skd_pl_portfolio', ['user_id' => $user_id]);
        $wpdb->delete($wpdb->prefix . 'skd_pl_certifications', ['user_id' => $user_id]);
        $wpdb->delete($wpdb->prefix . 'skd_pl_job_applications', ['applicant_id' => $user_id]);

        // Delete WordPress user
        require_once(ABSPATH . 'wp-admin/includes/user.php');
        wp_delete_user($user_id);

        wp_send_json_success(['message' => 'Account permanently deleted']);
    }

    /**
     * Add certification to user profile
     */
    public static function add_certification()
    {
        check_ajax_referer('skd_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'You must be logged in']);
        }

        $user_id = get_current_user_id();
        $certification_id = intval($_POST['certification_id'] ?? 0);

        if (!$certification_id) {
            wp_send_json_error(['message' => 'Invalid certification']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_user_certifications';

        // Check if already exists
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table WHERE user_id = %d AND certification_id = %d",
            $user_id,
            $certification_id
        ));

        if ($exists) {
            wp_send_json_error(['message' => 'You already have this certification']);
        }

        // Get certification details to check if verification required
        $cert_table = $wpdb->prefix . 'skd_pl_certifications';
        $cert = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $cert_table WHERE id = %d",
            $certification_id
        ));

        if (!$cert) {
            wp_send_json_error(['message' => 'Certification not found']);
        }

        $data = [
            'user_id' => $user_id,
            'certification_id' => $certification_id,
            'status' => $cert->verification_required ? 'pending' : 'approved',
            'created_at' => current_time('mysql')
        ];

        // Handle file upload if provided
        if (!empty($_FILES['certificate_file'])) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');

            $upload = wp_handle_upload($_FILES['certificate_file'], ['test_form' => false]);

            if (isset($upload['error'])) {
                wp_send_json_error(['message' => $upload['error']]);
            }

            $data['certificate_file'] = $upload['url'];
        }

        // Add optional fields
        if (!empty($_POST['expiry_date'])) {
            $data['expiry_date'] = sanitize_text_field($_POST['expiry_date']);
        }

        if (!empty($_POST['notes'])) {
            $data['notes'] = sanitize_textarea_field($_POST['notes']);
        }

        if (!empty($_POST['verification_date'])) {
            $data['verification_date'] = sanitize_text_field($_POST['verification_date']);
        }

        $result = $wpdb->insert($table, $data);

        if ($result) {
            $user_cert_id = $wpdb->insert_id;

            // Recalculate profile completeness
            self::calculate_profile_completeness($user_id);

            wp_send_json_success([
                'message' => $cert->verification_required ? 'Certification submitted for review' : 'Certification added successfully',
                'requires_verification' => $cert->verification_required,
                'user_cert_id' => $user_cert_id,
                'certificate_file' => $data['certificate_file'] ?? null
            ]);
        } else {
            wp_send_json_error(['message' => 'Failed to add certification']);
        }
    }

    /**
     * Update certification
     */
    public static function update_certification()
    {
        check_ajax_referer('skd_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'You must be logged in']);
        }

        $user_id = get_current_user_id();
        $user_cert_id = intval($_POST['user_cert_id'] ?? 0);

        if (!$user_cert_id) {
            wp_send_json_error(['message' => 'Invalid certification']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_user_certifications';

        // Verify ownership
        $cert = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE id = %d AND user_id = %d",
            $user_cert_id,
            $user_id
        ));

        if (!$cert) {
            wp_send_json_error(['message' => 'Certification not found or you do not have permission']);
        }

        $data = [
            'updated_at' => current_time('mysql'),
            'status' => 'pending' // Reset to pending when updated
        ];

        // Handle file upload if provided
        if (!empty($_FILES['certificate_file'])) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');

            $upload = wp_handle_upload($_FILES['certificate_file'], ['test_form' => false]);

            if (isset($upload['error'])) {
                wp_send_json_error(['message' => $upload['error']]);
            }

            // Delete old certificate file if exists
            if (!empty($cert->certificate_file)) {
                $upload_dir = wp_upload_dir();
                $old_file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $cert->certificate_file);
                if (file_exists($old_file_path)) {
                    @unlink($old_file_path);
                }
            }

            $data['certificate_file'] = $upload['url'];
        }

        // Update optional fields
        if (isset($_POST['expiry_date'])) {
            $data['expiry_date'] = sanitize_text_field($_POST['expiry_date']);
        }

        if (isset($_POST['notes'])) {
            $data['notes'] = sanitize_textarea_field($_POST['notes']);
        }

        $result = $wpdb->update(
            $table,
            $data,
            ['id' => $user_cert_id]
        );

        if ($result !== false) {
            // Recalculate profile completeness
            self::calculate_profile_completeness($user_id);

            wp_send_json_success(['message' => 'Certification updated successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to update certification']);
        }
    }

    /**
     * Remove certification from user profile
     */
    public static function remove_certification()
    {
        check_ajax_referer('skd_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'You must be logged in']);
        }

        $user_id = get_current_user_id();
        $user_cert_id = intval($_POST['user_cert_id'] ?? 0);

        if (!$user_cert_id) {
            wp_send_json_error(['message' => 'Invalid certification']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_user_certifications';

        // Verify ownership
        $cert = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE id = %d AND user_id = %d",
            $user_cert_id,
            $user_id
        ));

        if (!$cert) {
            wp_send_json_error(['message' => 'Certification not found or you do not have permission']);
        }

        // Delete certificate file if exists
        if (!empty($cert->certificate_file)) {
            $upload_dir = wp_upload_dir();
            $file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $cert->certificate_file);
            if (file_exists($file_path)) {
                @unlink($file_path);
            }
        }

        $result = $wpdb->delete(
            $table,
            ['id' => $user_cert_id, 'user_id' => $user_id]
        );

        if ($result) {
            // Recalculate profile completeness
            self::calculate_profile_completeness($user_id);

            wp_send_json_success(['message' => 'Certification removed successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to remove certification']);
        }
    }
}

SKD_PL_VDA_Profile::init();
