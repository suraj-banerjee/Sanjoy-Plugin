<?php
class SKD_Shortcodes
{
    public function __construct()
    {
        // Registration & Authentication Shortcodes
        add_shortcode('skd_registration_form', [$this, 'skd_registration_form_shortcode']);
        add_shortcode('skd_login_form', [$this, 'skd_login_form_shortcode']);
        add_shortcode('skd_forgot_password', [$this, 'skd_forgot_password_shortcode']);
        add_shortcode('skd_reset_password', [$this, 'skd_reset_password_shortcode']);
        add_shortcode('skd_change_password', [$this, 'skd_change_password_shortcode']);

        // Professional Directory Shortcodes
        add_shortcode('skd_find_assistants', [$this, 'skd_find_assistants_shortcode']);
        add_shortcode('skd_find_studios', [$this, 'skd_find_studios_shortcode']);
        add_shortcode('skd_professional_profile', [$this, 'skd_professional_profile_shortcode']);

        // Search and Filter AJAX
        add_action('wp_ajax_skd_filter_professionals', [$this, 'skd_filter_professionals']);
        add_action('wp_ajax_nopriv_skd_filter_professionals', [$this, 'skd_filter_professionals']);

        // Job Board Shortcodes
        add_shortcode('skd_job_board', [$this, 'skd_job_board_shortcode']);
        add_shortcode('skd_post_job_form', [$this, 'skd_post_job_form_shortcode']);

        // Job Board AJAX handlers
        add_action('wp_ajax_skd_fetch_jobs', [$this, 'skd_fetch_jobs']);
        add_action('wp_ajax_nopriv_skd_fetch_jobs', [$this, 'skd_fetch_jobs']);
        add_action('wp_ajax_skd_get_job_details', [$this, 'skd_get_job_details']);
        add_action('wp_ajax_nopriv_skd_get_job_details', [$this, 'skd_get_job_details']);
        add_action('wp_ajax_skd_submit_job_application', [$this, 'skd_submit_job_application']);
        add_action('wp_ajax_nopriv_skd_submit_job_application', [$this, 'skd_submit_job_application']);
        add_action('wp_ajax_skd_submit_job_post', [$this, 'skd_submit_job_post']);
        add_action('wp_ajax_skd_save_job', [$this, 'skd_save_job']);
        add_action('wp_ajax_skd_set_job_alerts', [$this, 'skd_set_job_alerts']);

        // Academy and Resources
        add_shortcode('skd_academy_resources', [$this, 'skd_academy_resources_shortcode']);

        // VDA Profile Management
        add_shortcode('skd_edit_vda_profile', [$this, 'skd_edit_vda_profile_shortcode']);
        add_shortcode('skd_vda_dashboard', [$this, 'skd_vda_dashboard_shortcode']);
        add_shortcode('skd_studio_dashboard', [$this, 'skd_studio_dashboard_shortcode']);
        add_shortcode('skd_employer_dashboard', [$this, 'skd_employer_dashboard_shortcode']);
        add_shortcode('skd_vda_profile', [$this, 'skd_vda_profile_shortcode']); // Public VDA profile page

        // VDA Dashboard AJAX handlers
        add_action('wp_ajax_skd_update_basic_info', [$this, 'skd_update_basic_info']);
        add_action('wp_ajax_skd_change_password', [$this, 'skd_change_password_ajax']);
        add_action('wp_ajax_skd_logout_user', [$this, 'skd_logout_user']);

        // Messages AJAX handlers
        add_action('wp_ajax_skd_get_contact_info', [$this, 'skd_get_contact_info']);
        add_action('wp_ajax_skd_get_conversation_messages', [$this, 'skd_get_conversation_messages']);
        add_action('wp_ajax_skd_send_message', [$this, 'skd_send_message']);
        add_action('wp_ajax_skd_mark_messages_read', [$this, 'skd_mark_messages_read']);
        add_action('wp_ajax_skd_search_contacts', [$this, 'skd_search_contacts']);
        add_action('wp_ajax_skd_start_conversation', [$this, 'skd_start_conversation']);
        add_action('wp_ajax_skd_archive_conversation', [$this, 'skd_archive_conversation']);

        // Legacy shortcodes (keeping for backward compatibility)
        add_shortcode('skd_listing_carousel', [$this, 'skd_listing_carousel']);
        add_shortcode('skd_category_list', [$this, 'skd_category_list']);
        add_shortcode('skd_location_list', [$this, 'skd_location_list']);

        add_shortcode('skd_single_location_breadcrumb', [$this, 'skd_location_breadcrumb_shortcode']);
        add_shortcode('skd_location_listings', [$this, 'skd_location_listings_shortcode']);
        add_action('wp_ajax_skd_fetch_location_listings', [$this, 'skd_fetch_location_listings']);
        add_action('wp_ajax_nopriv_skd_fetch_location_listings', [$this, 'skd_fetch_location_listings']);

        add_shortcode('skd_single_category_breadcrumb', [$this, 'skd_category_breadcrumb_shortcode']);
        add_shortcode('skd_category_listings', [$this, 'skd_category_listings_shortcode']);
        add_action('wp_ajax_skd_fetch_category_listings', [$this, 'skd_fetch_category_listings']);
        add_action('wp_ajax_nopriv_skd_fetch_category_listings', [$this, 'skd_fetch_category_listings']);

        add_shortcode('skd_search_listings', [$this, 'skd_search_listings_shortcode']);
        add_action('wp_ajax_skd_fetch_search_listings', [$this, 'skd_fetch_search_listings']);
        add_action('wp_ajax_nopriv_skd_fetch_search_listings', [$this, 'skd_fetch_search_listings']);
        add_shortcode('skd_search_home', [$this, 'skd_search_home_shortcode']);

        add_shortcode('skd_add_listing_form', [$this, 'skd_add_listing_form_shortcode']);
        add_action('wp_ajax_skd_pl_add_listing', [$this, 'skd_pl_add_listing']);
        add_action('wp_ajax_nopriv_skd_pl_add_listing', [$this, 'skd_pl_add_listing']);

        add_shortcode('skd_pricing_plans', [$this, 'skd_price_plan_shortcode']);

        add_shortcode('skd_plan_checkout', [$this, 'skd_checkout_shortcode']);
        add_action('wp_ajax_skd_apply_coupon', [$this, 'skd_apply_coupon']);
        add_action('wp_ajax_nopriv_skd_apply_coupon', [$this, 'skd_apply_coupon']);
        add_action('wp_ajax_skd_process_free_plan', [$this, 'skd_process_free_plan']);
        add_action('wp_ajax_nopriv_skd_process_free_plan', [$this, 'skd_process_free_plan']);
        add_action('wp_ajax_skd_process_paid_order', [$this, 'skd_process_paid_order']);
        add_action('wp_ajax_nopriv_skd_process_paid_order', [$this, 'skd_process_paid_order']);

        add_shortcode('skd_user_listings', [$this, 'skd_user_listings_shortcode']);

        add_shortcode('skd_user_orders', [$this, 'skd_user_orders_shortcode']);

        add_shortcode('skd_listing_details', [$this, 'skd_listing_details_shortcode']);
        add_action('wp_ajax_skd_submit_list_contact_form', [$this, 'skd_submit_list_contact_form']);
        add_action('wp_ajax_nopriv_skd_submit_list_contact_form', [$this, 'skd_submit_list_contact_form']);

        add_action('wp_ajax_skd_cancel_subscription', [$this, 'skd_cancel_subscription']);
        add_action('wp_ajax_nopriv_skd_cancel_subscription', [$this, 'skd_cancel_subscription']);

        add_action('wp_ajax_skd_check_order_active', [$this, 'skd_check_order_active']);
        add_action('wp_ajax_nopriv_skd_check_order_active', [$this, 'skd_check_order_active']);

        add_shortcode('custom_edit_account', [$this, 'custom_edit_account_form']);
    }

    // === Custom Edit Account Shortcode ===
    function custom_edit_account_form()
    {
        if (!is_user_logged_in()) {
            return '<p>You need to <a href="' . wp_login_url() . '">login</a> to edit your account.</p>';
        }
        $user_id = get_current_user_id();
        $user    = get_userdata($user_id);
        ob_start();
        // Success message
        if (isset($_GET['updated']) && $_GET['updated'] === 'true') {
            echo '<div class="updated"><p>:white_check_mark: Account details updated successfully.</p></div>';
        }
?>
<form method="post" action="">
    <?php wp_nonce_field('custom_edit_account_action', 'custom_edit_account_nonce'); ?>
    <p>
        <label for="first_name">First name <span class="required">*</span></label>
        <input type="text" name="first_name" id="first_name" value="<?php echo esc_attr($user->first_name); ?>"
            required>
    </p>
    <p>
        <label for="last_name">Last name <span class="required">*</span></label>
        <input type="text" name="last_name" id="last_name" value="<?php echo esc_attr($user->last_name); ?>" required>
    </p>
    <p>
        <label for="display_name">Display name <span class="required">*</span></label>
        <input type="text" name="display_name" id="display_name" value="<?php echo esc_attr($user->display_name); ?>"
            required>
        <small>This will be how your name is displayed publicly.</small>
    </p>
    <p>
        <label for="email">Email address <span class="required">*</span></label>
        <input type="email" name="email" id="email" value="<?php echo esc_attr($user->user_email); ?>" required>
    </p>
    <fieldset>
        <legend>Password change</legend>
        <p>
            <label for="current_password">Current password</label>
            <input type="password" name="current_password" id="current_password">
        </p>
        <p>
            <label for="new_password">New password</label>
            <input type="password" name="new_password" id="new_password">
        </p>
        <p>
            <label for="confirm_password">Confirm new password</label>
            <input type="password" name="confirm_password" id="confirm_password">
        </p>
    </fieldset>
    <p>
        <button type="submit" name="custom_save_account" class="button">Save changes</button>
    </p>
</form>
<?php
        return ob_get_clean();
    }

    // home page featured listings carousel shortcode
    public function skd_listing_carousel($atts)
    {
        global $wpdb;

        // Default attributes
        $atts = shortcode_atts([
            'items'    => 6,
            'featured' => 'yes',
            'category' => '',
            'location' => '',
            'plan'     => '',
            'autoplay' => 'yes',
            'listing_type' => 'random_listings',
            'home_page' => 'no',
        ], $atts);

        $table_name = $wpdb->prefix . 'skd_pl_listings';

        // Build query conditions
        $conditions = ["listing_status = 'publish'"];

        if ($atts['featured'] && $atts['featured'] == 'yes') {
            $conditions[] = "is_feature = 1";
        }

        if ($atts['home_page'] == 'yes') {
            $conditions[] = "plan_id = 4";
        }

        // Handle multiple categories
        if (!empty($atts['category'])) {
            $category_conditions = [];
            $categories = explode(',', $atts['category']);
            foreach ($categories as $category) {
                $category = trim($category);
                $category_conditions[] = "JSON_CONTAINS(category_ids, '\"{$category}\"')";
            }
            $conditions[] = '(' . implode(' OR ', $category_conditions) . ')';
        }

        // Handle multiple locations
        if (!empty($atts['location'])) {
            $location_conditions = [];
            $locations = explode(',', $atts['location']);
            foreach ($locations as $location) {
                $location = trim($location);
                $location_conditions[] = "JSON_CONTAINS(location_ids, '\"{$location}\"')";
            }
            $conditions[] = '(' . implode(' OR ', $location_conditions) . ')';
        } else if (get_query_var('skd_location_slug')) {
            $location_slug = sanitize_text_field(get_query_var('skd_location_slug'));
            $location_table = $wpdb->prefix . 'skd_pl_locations';
            $location = $wpdb->get_row($wpdb->prepare("SELECT id FROM $location_table WHERE slug = %s", $location_slug));
            if ($location) {
                $conditions[] = "JSON_CONTAINS(location_ids, '\"{$location->id}\"')";
            }
        }

        // Handle multiple plans
        if (!empty($atts['plan'])) {
            $plan_conditions = [];
            $plans = explode(',', $atts['plan']);
            foreach ($plans as $plan) {
                $plan_conditions[] = "plan_id = " . intval($plan);
            }
            $conditions[] = '(' . implode(' OR ', $plan_conditions) . ')';
        }

        // Handle listing type
        if ($atts['listing_type'] == 'random_listings') {
            $conditions[] = "RAND()";
        }

        // SQL Query
        $query = "SELECT id, slug, listing_title, skd_logo, created_at FROM $table_name WHERE " . implode(' AND ', $conditions) . " ORDER BY created_at DESC LIMIT %d";
        $listings = $wpdb->get_results($wpdb->prepare($query, $atts['items']));

        // Load Template
        ob_start();
        include SKD_PL_PLUGIN_PATH . 'templates/shortcodes/listing-carousel.php';
        return ob_get_clean();
    }
    // [skd_listing_carousel items="10" featured="yes" autoplay="true" category="" location="" plan="" listing_type="random_listings"]
    // home page featured listings carousel shortcode

    //==================== Category shortcode ============================
    public function skd_category_list($atts)
    {
        global $wpdb;

        // Default attributes
        $atts = shortcode_atts([
            'columns'    => 4,         // Number of columns
            'items'      => 10,        // Max categories to display
            'orderby'    => 'name',    // Order by name
            'order'      => 'ASC',     // Sorting order
            'show_count' => true,      // Show listing count
            'parent_only' => true,
        ], $atts);

        $category_table = $wpdb->prefix . 'skd_pl_categories';
        $listing_table  = $wpdb->prefix . 'skd_pl_listings';

        $where = $atts['parent_only'] ? "WHERE parent_id = 0" : "";

        $limit = $atts['items'] > 0 ? "LIMIT " . intval($atts['items']) : "";

        // Get parent categories (top-level only)
        $categories = $wpdb->get_results("
            SELECT id, name, slug, icon_url, image_url
            FROM $category_table 
            $where
            ORDER BY {$atts['orderby']} {$atts['order']}
            $limit
        ");

        // Recursive function to get all subcategory IDs
        function skd_get_all_subcategories($parent_id, $category_table, $wpdb)
        {
            $subcategories = $wpdb->get_col($wpdb->prepare("
            SELECT id FROM $category_table WHERE parent_id = %d
        ", $parent_id));

            $all_subcategories = $subcategories; // Store initial child categories

            // Recursively fetch deeper levels
            foreach ($subcategories as $sub_id) {
                $all_subcategories = array_merge($all_subcategories, skd_get_all_subcategories($sub_id, $category_table, $wpdb));
            }

            return $all_subcategories;
        }

        // Count listings for each parent category, including all levels of subcategories
        foreach ($categories as $category) {
            // Get all descendant categories
            $all_category_ids = skd_get_all_subcategories($category->id, $category_table, $wpdb);
            $all_category_ids[] = $category->id; // Include parent category

            // Build JSON_CONTAINS conditions
            $json_conditions = array_map(function ($id) {
                return "JSON_CONTAINS(category_ids, '\"$id\"')";
            }, $all_category_ids);

            // Count listings in parent + all subcategories
            $category->listing_count = $wpdb->get_var(
                "
            SELECT COUNT(*) FROM $listing_table
            WHERE " . implode(" OR ", $json_conditions)
            );
        }

        // Load template
        ob_start();
        include SKD_PL_PLUGIN_PATH . 'templates/shortcodes/category-list.php';
        return ob_get_clean();
    }
    // [skd_category_list items="" orderby="id" order="DESC" columns="3" show_count="false"]

    function skd_category_breadcrumb_shortcode()
    {
        if (!get_query_var('skd_category_slug')) {
            return ''; // If no category slug, return empty
        }

        global $wpdb;
        $category_slug = sanitize_text_field(get_query_var('skd_category_slug'));
        $category_table = $wpdb->prefix . 'skd_pl_categories';

        // Get category details
        $category = $wpdb->get_row($wpdb->prepare("SELECT name FROM $category_table WHERE slug = %s", $category_slug));

        if (!$category) {
            return ''; // If category not found, return empty
        }

        ob_start();
        include SKD_PL_PLUGIN_PATH . 'templates/shortcodes/category-breadcrumb.php';
        return ob_get_clean();
    }

    function skd_category_listings_shortcode()
    {
        ob_start();
        include SKD_PL_PLUGIN_PATH . 'templates/shortcodes/category-listings.php';
        return ob_get_clean();
    }

    function skd_fetch_category_listings()
    {
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $category_slug = sanitize_text_field($_POST['category_slug']);

        ob_start();
        $this->skd_load_category_listings($page, $category_slug);
        $listings_html = ob_get_clean();

        wp_send_json([
            'listings'   => $listings_html
        ]);
    }

    function skd_load_category_listings($page = 1, $category_slug = '')
    {
        global $wpdb;
        $listing_table = $wpdb->prefix . 'skd_pl_listings';
        $category_table = $wpdb->prefix . 'skd_pl_categories';
        $per_page = 9;
        $offset = ($page - 1) * $per_page;

        // Get category ID from slug
        $category_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM $category_table WHERE slug = %s", $category_slug));
        if (!$category_id) return;

        // Get total listings count
        $total_listings = $wpdb->get_var("
            SELECT COUNT(*) FROM $listing_table WHERE JSON_CONTAINS(category_ids, '\"$category_id\"')
        ");

        $total_pages = ceil($total_listings / $per_page);
        $current_page = $page;

        // Get listings
        $listings = $wpdb->get_results($wpdb->prepare("
            SELECT * FROM $listing_table
            WHERE JSON_CONTAINS(category_ids, '\"$category_id\"')
            ORDER BY created_at DESC
            LIMIT %d OFFSET %d
        ", $per_page, $offset));

        $category_name = $wpdb->get_var($wpdb->prepare("SELECT name FROM $category_table WHERE id = %d", $category_id));
        $current_page = $page;

        include SKD_PL_PLUGIN_PATH . 'templates/shortcodes/single-category-listings.php';
    }
    //==================== Category shortcode ============================

    //============ Location shortcode ===========================
    public function skd_location_list($atts)
    {
        global $wpdb;

        $atts = shortcode_atts([
            'columns'     => 3,
            'items'       => 10,  // 0 means all locations
            'orderby'     => 'name',
            'order'       => 'ASC',
            'show_count'  => true,
            'layout'      => 'grid',
            'parent_only' => true, // Show only parent locations
        ], $atts);

        $location_table = $wpdb->prefix . 'skd_pl_locations';
        $listing_table = $wpdb->prefix . 'skd_pl_listings';

        // Build query condition
        $where = $atts['parent_only'] ? "WHERE parent_id = 0" : "";

        // Limit clause
        $limit = $atts['items'] > 0 ? "LIMIT " . intval($atts['items']) : "";

        // Fetch locations
        $locations = $wpdb->get_results("
            SELECT id, name, slug, image_url
            FROM $location_table 
            $where 
            ORDER BY {$atts['orderby']} {$atts['order']}
            $limit
        ");

        // Recursive function to get all sub-location IDs
        function skd_get_all_sublocations($parent_id, $location_table, $wpdb)
        {
            $sublocations = $wpdb->get_col($wpdb->prepare("
            SELECT id FROM $location_table WHERE parent_id = %d
        ", $parent_id));

            $all_sublocations = $sublocations; // Store initial child locations

            // Recursively fetch deeper levels
            foreach ($sublocations as $sub_id) {
                $all_sublocations = array_merge($all_sublocations, skd_get_all_sublocations($sub_id, $location_table, $wpdb));
            }

            return $all_sublocations;
        }

        // Count listings for each location
        foreach ($locations as $location) {
            if ($atts['show_count']) {
                // Get all descendant locations
                $all_location_ids = skd_get_all_sublocations($location->id, $location_table, $wpdb);
                $all_location_ids[] = $location->id; // Include parent location

                // Build JSON_CONTAINS conditions
                $json_conditions = array_map(function ($id) {
                    return "JSON_CONTAINS(location_ids, '\"$id\"')";
                }, $all_location_ids);

                // Count listings in parent + all sub-locations
                $location->listing_count = $wpdb->get_var(
                    "
                SELECT COUNT(*) FROM $listing_table
                WHERE " . implode(" OR ", $json_conditions)
                );
            } else {
                $location->listing_count = 0;
            }
        }

        // Load template
        ob_start();
        include SKD_PL_PLUGIN_PATH . 'templates/shortcodes/location-list.php';
        return ob_get_clean();
    }
    // [skd_location_list columns="4" items="6" orderby="created_at" order="DESC" show_count="true" layout="grid"]

    function skd_location_breadcrumb_shortcode()
    {
        if (!get_query_var('skd_location_slug')) {
            return ''; // If no location slug, return empty
        }

        global $wpdb;
        $location_slug = sanitize_text_field(get_query_var('skd_location_slug'));
        $location_table = $wpdb->prefix . 'skd_pl_locations';

        // Get location details
        $location = $wpdb->get_row($wpdb->prepare("SELECT name FROM $location_table WHERE slug = %s", $location_slug));

        if (!$location) {
            return ''; // If location not found, return empty
        }

        ob_start();
        include SKD_PL_PLUGIN_PATH . 'templates/shortcodes/location-breadcrumb.php';
        return ob_get_clean();
    }

    function skd_location_listings_shortcode()
    {
        ob_start();
        include SKD_PL_PLUGIN_PATH . 'templates/shortcodes/location-listings.php';
        return ob_get_clean();
    }

    function skd_fetch_location_listings()
    {
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $location_slug = sanitize_text_field($_POST['location_slug']);

        ob_start();
        $this->skd_load_location_listings($page, $location_slug);
        $listings_html = ob_get_clean();

        wp_send_json([
            'listings'   => $listings_html
        ]);
    }

    function skd_load_location_listings($page = 1, $location_slug = '')
    {
        global $wpdb;
        $listing_table = $wpdb->prefix . 'skd_pl_listings';
        $location_table = $wpdb->prefix . 'skd_pl_locations';
        $per_page = 9;
        $offset = ($page - 1) * $per_page;

        // Get location ID from slug
        $location_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM $location_table WHERE slug = %s", $location_slug));
        if (!$location_id) return;

        // Get total listings count
        $total_listings = $wpdb->get_var("
            SELECT COUNT(*) FROM $listing_table WHERE JSON_CONTAINS(location_ids, '\"$location_id\"')
        ");

        $total_pages = ceil($total_listings / $per_page);

        // Get listings
        $listings = $wpdb->get_results($wpdb->prepare("
            SELECT * FROM $listing_table
            WHERE JSON_CONTAINS(location_ids, '\"$location_id\"')
            ORDER BY created_at DESC
            LIMIT %d OFFSET %d
        ", $per_page, $offset));

        $location_name = $wpdb->get_var($wpdb->prepare("SELECT name FROM $location_table WHERE id = %d", $location_id));

        $current_page = $page;

        // Include the template file
        include SKD_PL_PLUGIN_PATH . 'templates/shortcodes/single-location-listings.php';
    }
    //============ Location shortcode ===========================

    //============ Search filter shortcode ===========================
    // search-result/?list_type=general&search=&category=&zipcode=&lat=&long=&range=0-100&phone=&email=&website=&sort_by=created_at&page=1&rating=5,4&tags=213,86
    //?search=&category=&lat=&long=&range=100&phone=04585881&email=&website=&sort_by=Latest%20listings
    function skd_search_listings_shortcode()
    {
        global $wpdb;

        // Get unique tag names from published listings
        $listing_table = $wpdb->prefix . 'skd_pl_listings';
        $tags_table = $wpdb->prefix . 'skd_pl_tags';

        $published_tags = $wpdb->get_col("
            SELECT DISTINCT tags FROM $listing_table 
            WHERE listing_status = 'publish' AND tags IS NOT NULL
        ");

        $tag_list = [];
        foreach ($published_tags as $tag_json) {
            $decoded_tags = json_decode($tag_json, true);
            if (is_array($decoded_tags)) {
                $tag_list = array_merge($tag_list, $decoded_tags);
            }
        }
        $tag_list = array_unique($tag_list); // Remove duplicates

        // Get tag IDs from the skd_pl_tags table
        $tag_objects = [];
        if (!empty($tag_list)) {
            $tag_placeholders = implode(',', array_fill(0, count($tag_list), '%s'));
            $query = $wpdb->prepare("
                SELECT id, name FROM $tags_table 
                WHERE name IN ($tag_placeholders)
            ", ...$tag_list);

            $tag_objects = $wpdb->get_results($query);
        }

        $category_table = $wpdb->prefix . 'skd_pl_categories';
        $categories = $wpdb->get_results("SELECT id, name, slug, parent_id FROM $category_table ORDER BY name ASC");

        ob_start();
        include SKD_PL_PLUGIN_PATH . 'templates/shortcodes/search-listings.php';
        return ob_get_clean();
    }

    function skd_build_category_dropdown($categories, $parent_id = 0, $level = 0, $categoryId = '')
    {
        $output = '';
        foreach ($categories as $category) {
            if ($category->parent_id == $parent_id) {
                $indent = str_repeat('&nbsp;&nbsp;&nbsp;', $level); // Indentation for hierarchy
                $output .= '<option value="' . esc_attr($category->id) . '"' . selected($categoryId, $category->id, false) . '>' . $indent . esc_html($category->name) . '</option>';
                $output .= $this->skd_build_category_dropdown($categories, $category->id, $level + 1, $categoryId); // Recursion for subcategories
            }
        }
        return $output;
    }

    function skd_fetch_search_listings()
    {
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $filters = $_POST;

        ob_start();
        $this->skd_load_search_listings($page, $filters);
        $listings_html = ob_get_clean();

        wp_send_json([
            'listings'   => $listings_html
        ]);
    }

    function skd_load_search_listings($page = 1, $filters = [])
    {
        global $wpdb;
        $listing_table = $wpdb->prefix . 'skd_pl_listings';
        $per_page = 9;
        $offset = ($page - 1) * $per_page;

        $where_clauses = [];
        $query_params = [];

        $where_clauses[] = "listing_status = 'publish'";

        // Search text
        if (!empty($filters['search'])) {
            $where_clauses[] = "(listing_title LIKE %s OR listing_description LIKE %s)";
            $query_params[] = '%' . $filters['search'] . '%';
            $query_params[] = '%' . $filters['search'] . '%';
        }

        // Category filter
        if (!empty($filters['category'])) {
            $where_clauses[] = "JSON_CONTAINS(category_ids, '\"{$filters['category']}\"')";
        }

        //postal code filter
        if (!empty($filters['postcode'])) {
            $where_clauses[] = "contact_zip = %s";
            $query_params[] = $filters['postcode'];
        } else if (!empty($filters['lat']) && !empty($filters['long']) && !empty($filters['range'])) {
            // $latitude = floatval($filters['lat']);
            // $longitude = floatval($filters['long']);
            // $range = intval($filters['range']);

            // $where_clauses[] = "(6371 * ACOS(COS(RADIANS($latitude)) * COS(RADIANS(latitude)) * 
            //                COS(RADIANS(longitude) - RADIANS($longitude)) + 
            //                SIN(RADIANS($latitude)) * SIN(RADIANS(latitude)))) <= %d";
            // $query_params[] = intval($range);
        }

        // Phone filter
        if (!empty($filters['phone'])) {
            $where_clauses[] = "(contact_phone LIKE %s OR contact_phone2 LIKE %s)";
            $query_params[] = '%' . $filters['phone'] . '%';
            $query_params[] = '%' . $filters['phone'] . '%';
        }

        // Email filter
        if (!empty($filters['email'])) {
            $where_clauses[] = "contact_email LIKE %s";
            $query_params[] = '%' . $filters['email'] . '%';
        }

        // Website filter
        if (!empty($filters['website'])) {
            $where_clauses[] = "contact_website LIKE %s";
            $query_params[] = '%' . $filters['website'] . '%';
        }

        // Tags filter (Multiple tags)
        if (!empty($filters['tag'])) {
            $tags = explode(',', $filters['tag']);
            $tag_conditions = [];
            foreach ($tags as $tag) {
                $tag_conditions[] = "JSON_CONTAINS(tags, '\"$tag\"')";
            }
            $where_clauses[] = "(" . implode(" OR ", $tag_conditions) . ")";
        }

        // Review filter
        if (!empty($filters['rating'])) {
            $ratings = explode(',', $filters['rating']);
            $rating_conditions = [];
            foreach ($ratings as $rating) {
                $rating_conditions[] = "average_rating = " . intval($rating);
            }
            $where_clauses[] = "(" . implode(" OR ", $rating_conditions) . ")";
        }

        // Sorting
        $sort_by = "created_at";
        $sort_order = "DESC";

        if (!empty($filters['sort_by'])) {
            switch ($filters['sort_by']) {
                case "A to Z (title)":
                    $sort_by = "listing_title";
                    $sort_order = "ASC";
                    break;
                case "Z to A (title)":
                    $sort_by = "listing_title";
                    $sort_order = "DESC";
                    break;
                case "Latest listings":
                    $sort_by = "created_at";
                    $sort_order = "DESC";
                    break;
                case "Oldest listings":
                    $sort_by = "created_at";
                    $sort_order = "ASC";
                    break;
                case "Popular listings":
                    $sort_by = "view_count";
                    $sort_order = "DESC";
                    break;
                case "Price (low to high)":
                    $sort_by = "price";
                    $sort_order = "ASC";
                    break;
                case "Price (high to low)":
                    $sort_by = "price";
                    $sort_order = "DESC";
                    break;
                case "Random listings":
                    $sort_by = "RAND()";
                    break;
            }
        }

        // Construct query
        $where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";
        $sql = $wpdb->prepare("SELECT * FROM $listing_table $where_sql ORDER BY $sort_by $sort_order LIMIT %d OFFSET %d", ...array_merge($query_params, [$per_page, $offset]));
        $listings = $wpdb->get_results($sql);

        //pagination
        $total_listings = $wpdb->get_var("SELECT COUNT(*) FROM $listing_table $where_sql");
        $total_pages = ceil($total_listings / $per_page);
        $current_page = $page;

        include SKD_PL_PLUGIN_PATH . 'templates/shortcodes/search-listings-results.php';
    }

    function skd_search_home_shortcode()
    {
        global $wpdb;

        // Get unique tag names from published listings
        $listing_table = $wpdb->prefix . 'skd_pl_listings';
        $tags_table = $wpdb->prefix . 'skd_pl_tags';

        $published_tags = $wpdb->get_col("
            SELECT DISTINCT tags FROM $listing_table 
            WHERE listing_status = 'publish' AND tags IS NOT NULL
        ");

        $tag_list = [];
        foreach ($published_tags as $tag_json) {
            $decoded_tags = json_decode($tag_json, true);
            if (is_array($decoded_tags)) {
                $tag_list = array_merge($tag_list, $decoded_tags);
            }
        }
        $tag_list = array_unique($tag_list); // Remove duplicates

        // Get tag IDs from the skd_pl_tags table
        $tag_objects = [];
        if (!empty($tag_list)) {
            $tag_placeholders = implode(',', array_fill(0, count($tag_list), '%s'));
            $query = $wpdb->prepare("
                SELECT id, name FROM $tags_table 
                WHERE name IN ($tag_placeholders)
            ", ...$tag_list);

            $tag_objects = $wpdb->get_results($query);
        }

        $category_table = $wpdb->prefix . 'skd_pl_categories';
        $categories = $wpdb->get_results("SELECT id, name, slug, parent_id FROM $category_table ORDER BY name ASC");

        ob_start();
        include SKD_PL_PLUGIN_PATH . 'templates/shortcodes/search-home.php';
        return ob_get_clean();
    }
    //============ Search filter shortcode ===========================

    //============ Add new listing shortcode ===========================
    public function skd_add_listing_form_shortcode()
    {
        if (!is_user_logged_in()) {
            return '<p>You must be logged in to submit a listing.</p>';
        }

        global $wpdb;
        $user_id = get_current_user_id();
        $is_admin = current_user_can('administrator');

        // Check if it's an edit request
        $edit_listing_id = get_query_var('edit_listing_id', false);
        $order_id = isset($_GET['order']) ? intval($_GET['order']) : 0;

        // Initialize variables
        $listing_data = null;
        $is_edit_mode = false;

        $order_table = $wpdb->prefix . 'skd_pl_orders';
        $listing_table = $wpdb->prefix . 'skd_pl_listings';

        // If editing, fetch listing details
        if ($edit_listing_id) {
            $listing_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $listing_table WHERE id = %d AND user_id = %d", $edit_listing_id, $user_id));

            if (!$listing_data) {
                return '<p>Invalid listing or you do not have permission to edit this listing.</p>';
            }

            $is_edit_mode = true;
            $order_id = $listing_data->order_id; // Fetch order_id from listing
        }

        // If adding a new listing, validate order_id
        if (!$is_edit_mode) {
            if ($order_id > 0) {
                $orderDetails = $wpdb->get_row($wpdb->prepare("SELECT * FROM $order_table WHERE id = %d AND user_id = %d AND order_status = 'completed'", $order_id, $user_id));
                if (!$orderDetails) {
                    return '<p>Invalid order ID.</p>';
                }
            } else {
                return '<p>Invalid order ID.</p>';
            }
        }

        $price_plans_table = $wpdb->prefix . 'skd_pl_price_plans';
        $planDetails = $wpdb->get_row($wpdb->prepare("SELECT pp.* FROM $price_plans_table pp JOIN $order_table o ON pp.id = o.plan_id WHERE o.id = %d", $order_id));

        if (!$is_edit_mode && $planDetails->plan_type == 'pay_per_listing') {
            $listing_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}skd_pl_listings WHERE order_id = %d", $order_id));
            if ($listing_count >= 1) {
                return '<p>You have no remaining listings under this plan.</p>';
            }
        }

        if (!$edit_listing_id && $planDetails && !$planDetails->never_expire && $planDetails->plan_type != 'pay_per_listing') {
            $expiry_date = strtotime("+{$planDetails->listing_duration} {$planDetails->duration_unit}", strtotime($orderDetails->created_at));
            if (current_time('timestamp') <= $expiry_date) {
            } else {
                return '<p>Your plan has expired.</p>';
            }
        }

        //max file count
        $max_file_count = '';
        if ($planDetails->images_fld_limit_unlimited == 1) {
            $max_file_count = 0;
        } else {
            $max_file_count = $planDetails->images_fld_limit;
        }

        // Count how many listings this user has added under this plan
        $featured_listing_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $listing_table WHERE user_id = %d AND plan_id = %d AND order_id = %d AND listing_type = 'featured'", $user_id, $planDetails->id, $order_id));
        $regular_listing_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $listing_table WHERE user_id = %d AND plan_id = %d AND order_id = %d AND listing_type = 'regular'", $user_id, $planDetails->id, $order_id));

        // Calculate remaining listings
        $remainingRegular = $planDetails->mark_as_unlimited == 1 ? "Unlimited" : max(0, $planDetails->no_of_listing - $regular_listing_count);
        $remainingFeatured = $planDetails->mark_feature_unlimited == 1 ? "Unlimited" : max(0, $planDetails->no_of_feature_listing - $featured_listing_count);

        // **Check if both are 0 remaining**
        if ($planDetails->plan_type != 'pay_per_listing' && $remainingRegular === 0 && $remainingFeatured === 0) {
            return '<p>You have no remaining listings under this plan1.</p>';
        }

        // Fetch locations, categories, and tags
        $location_table = $wpdb->prefix . 'skd_pl_locations';
        $locations = $wpdb->get_results("
            SELECT l.*, p.name AS parent_name 
            FROM {$location_table} l
            LEFT JOIN {$location_table} p ON l.parent_id = p.id
            ORDER BY l.parent_id ASC, l.name ASC
        ");

        $category_table = $wpdb->prefix . 'skd_pl_categories';
        $categories = $wpdb->get_results("
            SELECT c.*, p.name AS parent_name 
            FROM {$category_table} c
            LEFT JOIN {$category_table} p ON c.parent_id = p.id
            ORDER BY c.parent_id ASC, c.name ASC
        ");

        $tag_table = $wpdb->prefix . 'skd_pl_tags';
        $tags = $wpdb->get_results("SELECT * FROM $tag_table ORDER BY id DESC");

        ob_start();
        include SKD_PL_PLUGIN_PATH . 'templates/shortcodes/add-listing-form.php';
        return ob_get_clean();
    }

    public function get_location_hierarchy_options($locations, $current_location_id = 0, $exclude_id = 0, $depth = 0, $selected_parent_id = 0)
    {
        $parent_map = [];
        foreach ($locations as $location) {
            $parent_map[$location->parent_id][] = $location;
        }

        $excluded_ids = $exclude_id ? $this->get_descendants($locations, $exclude_id) : [];
        $excluded_ids[] = $exclude_id;

        return $this->build_hierarchy($parent_map, $current_location_id, $excluded_ids, $depth, $selected_parent_id);
    }

    public function build_hierarchy($parent_map, $current_location_id, $excluded_ids, $depth, $selected_parent_id)
    {
        $output = '';

        if (!isset($parent_map[$current_location_id])) {
            return $output;
        }

        foreach ($parent_map[$current_location_id] as $location) {
            if (in_array($location->id, $excluded_ids)) {
                continue;
            }

            $indent = str_repeat('&nbsp;&nbsp;', $depth);

            $output .= sprintf(
                '<option value="%d"%s>%s%s</option>',
                $location->id,
                selected($location->id, $selected_parent_id, false),
                $indent,
                esc_html($location->name)
            );

            $output .= $this->build_hierarchy($parent_map, $location->id, $excluded_ids, $depth + 1, $selected_parent_id);
        }

        return $output;
    }

    public function get_descendants($locations, $parent_id)
    {
        $descendants = [];
        foreach ($locations as $location) {
            if ($location->parent_id == $parent_id) {
                $descendants[] = $location->id;
                $descendants = array_merge($descendants, $this->get_descendants($locations, $location->id));
            }
        }
        return $descendants;
    }

    public function get_category_hierarchy_options($categories, $current_category_id = 0, $exclude_id = 0, $depth = 0, $selected_parent_id = 0)
    {
        // Build a parent-child relationship map
        $parent_map = [];
        foreach ($categories as $category) {
            $parent_map[$category->parent_id][] = $category;
        }

        // Exclude the current category and its descendants
        $excluded_ids = $exclude_id ? $this->get_descendants($categories, $exclude_id) : [];
        $excluded_ids[] = $exclude_id; // Include the exclude_id itself

        // Generate dropdown options
        return $this->build_cat_hierarchy($parent_map, $current_category_id, $excluded_ids, $depth, $selected_parent_id);
    }

    // Build the dropdown recursively from the parent map
    public function build_cat_hierarchy($parent_map, $current_category_id, $excluded_ids, $depth, $selected_parent_id)
    {
        $output = '';

        if (!isset($parent_map[$current_category_id])) {
            return $output; // No children for this parent
        }

        foreach ($parent_map[$current_category_id] as $category) {
            // Skip excluded categories
            if (in_array($category->id, $excluded_ids)) {
                continue;
            }

            // Indentation for hierarchy
            $indent = str_repeat('&nbsp;&nbsp;', $depth);

            // Add option
            $output .= sprintf(
                '<option value="%d"%s>%s%s</option>',
                $category->id,
                selected($category->id, $selected_parent_id, false), // Pre-select the parent category
                $indent,
                esc_html($category->name)
            );

            // Recursively add child categories
            $output .= $this->build_cat_hierarchy($parent_map, $category->id, $excluded_ids, $depth + 1, $selected_parent_id);
        }

        return $output;
    }

    // Get all descendant IDs of a category
    public function get_cat_descendants($categories, $parent_id)
    {
        $descendants = [];
        foreach ($categories as $category) {
            if ($category->parent_id == $parent_id) {
                $descendants[] = $category->id;
                $descendants = array_merge($descendants, $this->get_cat_descendants($categories, $category->id));
            }
        }
        return $descendants;
    }

    public function skd_pl_add_listing()
    {
        // echo '<pre>';
        // print_r($_FILES);
        // echo '</pre>';
        // die();
        global $wpdb;

        //check nonce
        if (!isset($_POST['skd_pl_add_listing_nonce']) || !wp_verify_nonce($_POST['skd_pl_add_listing_nonce'], 'skd_pl_add_listing')) {
            wp_send_json_error(['message' => 'Nonce verification failed.']);
        }

        // Get current user ID
        $user_id = get_current_user_id();
        $listing_id = isset($_POST['listing_id']) ? intval($_POST['listing_id']) : 0;

        $order_table = $wpdb->prefix . 'skd_pl_orders';
        $listing_table = $wpdb->prefix . 'skd_pl_listings';

        $is_edit = 'no';
        // If editing, fetch listing details
        if ($listing_id) {
            $listing_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $listing_table WHERE id = %d AND user_id = %d", $listing_id, $user_id));

            if (!$listing_data) {
                wp_send_json_error(['message' => 'Invalid listing or you do not have permission to edit this listing.']);
            }

            $order_id = $listing_data->order_id; // Fetch order_id from listing
            $is_edit = 'yes';
        } else {
            $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
        }

        // Get plan ID from the order ID
        $plan_id = $wpdb->get_var($wpdb->prepare("SELECT plan_id FROM $order_table WHERE id = %d AND user_id = %d AND order_status = 'completed'", $order_id, $user_id));

        if (!$plan_id) {
            wp_send_json_error(['message' => 'Invalid order ID.']);
        }

        $price_plans_table = $wpdb->prefix . 'skd_pl_price_plans';
        $planDetails = $wpdb->get_row($wpdb->prepare("SELECT pp.* FROM $price_plans_table pp JOIN $order_table o ON pp.id = o.plan_id WHERE o.id = %d", $order_id));
        // Calculate expiration date
        $orderDetails = $wpdb->get_row($wpdb->prepare("SELECT * FROM $order_table WHERE id = %d AND user_id = %d", $order_id, $user_id));
        if (!$orderDetails) {
            wp_send_json_error(['message' => 'Invalid order ID.']);
        }
        $created_date = $orderDetails->created_at;
        $expiration_date = $this->skd_calculate_expiration_date($created_date, $planDetails);

        // Upload Logo Image (if exists)
        $logo_url = $listing_id && !empty($_POST['skd_logo']) ? $_POST['skd_logo'] : '';
        if (!empty($_FILES['skd_logo_file']['name'])) {
            $logo_upload = $this->skd_pl_upload_image($_FILES['skd_logo_file']);
            if (is_wp_error($logo_upload)) {
                // wp_send_json_error(['message' => 'Logo upload failed.']);
                $logo_url = '';
            } else {
                $logo_url = $logo_upload;

                if ($listing_id && $listing_data->skd_logo) {
                    $this->skd_pl_delete_image($listing_data->skd_logo);
                }
            }
        }

        // Upload Gallery Images (if exists)
        $gallery_urls = $listing_id && !empty($_POST['skd_gallery']) ? json_decode(stripslashes($_POST['skd_gallery'])) : [];
        if ($listing_id && !empty($listing_data->skd_gallery)) {
            $old_gallery_images = json_decode($listing_data->skd_gallery);
            foreach ($old_gallery_images as $old_image) {
                if (!in_array($old_image, $gallery_urls)) {
                    $this->skd_pl_delete_image($old_image);
                }
            }
        }
        if (!empty($_FILES['skd_gallery_files']['name'][0])) {
            foreach ($_FILES['skd_gallery_files']['name'] as $key => $value) {
                $file = [
                    'name' => $_FILES['skd_gallery_files']['name'][$key],
                    'type' => $_FILES['skd_gallery_files']['type'][$key],
                    'tmp_name' => $_FILES['skd_gallery_files']['tmp_name'][$key],
                    'error' => $_FILES['skd_gallery_files']['error'][$key],
                    'size' => $_FILES['skd_gallery_files']['size'][$key],
                ];

                $upload = $this->skd_pl_upload_image($file);
                if (!is_wp_error($upload)) {
                    $gallery_urls[] = $upload;
                }
            }
        }

        $listing_title = sanitize_text_field($_POST['listing_title']);
        $slug = sanitize_title($listing_title);

        // Handle social info (convert to JSON)
        $social_info = [];
        if (!empty($_POST['social_networks']) && !empty($_POST['social_urls'])) {
            foreach ($_POST['social_networks'] as $index => $network) {
                $social_info[] = [
                    'network' => sanitize_text_field($network),
                    'url' => esc_url_raw($_POST['social_urls'][$index])
                ];
            }
        }
        $social_info_json = json_encode($social_info);

        $skd_category = [];
        if (isset($_POST['skd_categories']) && !empty($_POST['skd_categories'])) {
            $skd_category = $_POST['skd_categories'];
        }
        $skd_category_json = json_encode($skd_category);

        $skd_location = [];
        if (isset($_POST['skd_locations']) && !empty($_POST['skd_locations'])) {
            $skd_location = $_POST['skd_locations'];
        }
        $skd_location_json = json_encode($skd_location);

        $skd_tags = isset($_POST['skd_tags']) ? $_POST['skd_tags'] : [];
        $skd_tags_json = json_encode($skd_tags);

        $table_list = $wpdb->prefix . 'skd_pl_listings';
        $existingListing = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_list WHERE slug = %s AND id != %d",
            $slug,
            $listing_id
        ));
        if ($existingListing) {
            wp_send_json_error(['message' => 'A listing with this title already exists.']);
        }

        $plan_details = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}skd_pl_price_plans WHERE id = %d",
            $plan_id
        ));
        $plan_type = $plan_details ? $plan_details->plan_type : '';
        $is_feature = 0;
        if ($plan_type === 'pay_per_listing') {
            $listing_type = 'regular';
            if ($plan_details->featured_the_list === 'yes') {
                $listing_type = 'featured';
                $is_feature = 1;
            }
        } else {
            $listing_type = isset($_POST['listing_type']) ? sanitize_text_field($_POST['listing_type']) : 'regular';
            $is_feature = isset($_POST['is_feature']) ? 1 : ($_POST['listing_type'] === 'featured' ? 1 : 0);
        }

        $formData = [
            'user_id' => $user_id,
            'plan_id' => $plan_id,
            'order_id' => $order_id,
            'listing_type' => $listing_type,
            'is_feature' => $is_feature,
            'expiration_date' => $expiration_date,
            'listing_title' => $listing_title,
            'slug' => $slug,
            'listing_description' => sanitize_textarea_field($_POST['listing_description']),
            'tagline' => sanitize_text_field($_POST['tagline']),
            'price' => floatval($_POST['price']),
            'view_count' => 0,
            'contact_details' => sanitize_textarea_field($_POST['contact_details']),
            'contact_phone' => sanitize_text_field($_POST['contact_phone']),
            'contact_phone2' => sanitize_text_field($_POST['contact_phone2']),
            'contact_email' => sanitize_email($_POST['contact_email']),
            'contact_zip' => sanitize_text_field($_POST['contact_zip']),
            'contact_website' => esc_url_raw($_POST['contact_website']),
            'hide_owner_form' => 0,
            'social_info' => $social_info_json,
            'list_address' => sanitize_text_field($_POST['list_address']),
            'latitude' => floatval($_POST['latitude']),
            'longitude' => floatval($_POST['longitude']),
            'is_online_only' => isset($_POST['is_online_only']) ? 1 : 0,
            'skd_logo' => $logo_url,
            'skd_gallery' => json_encode($gallery_urls),
            'video' => esc_url_raw($_POST['video']),
            'privacy_policy' => isset($_POST['privacy_policy']) ? 1 : 0,
            'listing_status' => 'publish',
            'skd_header_image' => $logo_url,
            'category_ids' => $skd_category_json,
            'location_ids' => $skd_location_json,
            'tags' => $skd_tags_json,
        ];

        if ($listing_id > 0) {
            $existing_listing = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_list WHERE id = %d AND user_id = %d", $listing_id, $user_id));

            if (!$existing_listing) {
                wp_send_json_error(['message' => 'Invalid listing or you do not have permission to edit this listing.']);
            }

            // Update listing
            $wpdb->update(
                $table_list,
                $formData,
                ['id' => $listing_id]
            );
            $sccsMsg = 'Listing updated successfully.';
        } else {
            $wpdb->insert($table_list, $formData);
            $listing_id = $wpdb->insert_id;
            $sccsMsg = 'Listing added successfully.';
        }

        //================== Send Email =========================================
        $mailSite_icon_url = get_site_icon_url();
        $listing_details = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_list WHERE id = %d", $listing_id));
        $listingUserDetails = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}users WHERE ID = %d", $listing_details->user_id));
        $listing_contact_email = $listingUserDetails->user_email;
        // $listing_contact_email = 'sanjoy.websadroit@gmail.com';
        $listing_contact_name = $listingUserDetails->display_name;
        $listing_url = site_url('/single-detail/' . $listing_details->slug);

        $mailContactName = $listing_contact_name ? $listing_contact_name : 'User';
        $emailBodyHtml = '';
        $mailTitleTxt = '';
        if ($is_edit == 'yes') {
            $mailTitleTxt = 'Listing "' . $listing_details->listing_title . '" Updated';
            $emailBodyHtml = "
                <p>Dear {$mailContactName},</p>
                <p>Congratulations! Your listing '{$listing_details->listing_title}' has been edited. It is publicly available at <a href='{$listing_url}'>{$listing_url}</a></p>            
                <br>
                Thank you for joining us!,
                <br>
            ";
        } else {
            $mailTitleTxt = 'Listing "' . $listing_details->listing_title . '" Published';
            $emailBodyHtml = "
                <p>Congratulations! Your listing '{$listing_details->listing_title}' has been published. Now it is publicly available at <a href='{$listing_url}'>{$listing_url}</a></p>            
                <br>
                Thank you for joining us!,
                <br>
            ";
        }

        ob_start();
        include SKD_PL_PLUGIN_PATH . 'templates/mail-template.php';
        $email_body = ob_get_clean();

        $subject = $mailTitleTxt ? $mailTitleTxt : 'Listing Published';
        $headers = ["Content-Type: text/html; charset=UTF-8"];

        try {
            wp_mail($listing_contact_email, $subject, $email_body, $headers);
        } catch (Exception $e) {
            // error_log("Exception while sending email: " . $e->getMessage());
        }
        //================== Send Email =========================================

        // Redirect to same page with success message
        $listing_url = site_url('/single-detail/' . $slug);
        wp_send_json_success(['message' => $sccsMsg, 'redirect_url' => $listing_url]);
    }

    public function skd_pl_upload_image($file)
    {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        $upload = wp_handle_upload($file, ['test_form' => false]);

        if (isset($upload['error'])) {
            return new WP_Error('upload_error', $upload['error']);
        }

        // Insert into WordPress media library
        $attachment = [
            'post_mime_type' => $upload['type'],
            'post_title' => sanitize_file_name($file['name']),
            'post_content' => '',
            'post_status' => 'inherit'
        ];

        $attach_id = wp_insert_attachment($attachment, $upload['file']);
        if (!is_wp_error($attach_id)) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            wp_update_attachment_metadata($attach_id, wp_generate_attachment_metadata($attach_id, $upload['file']));
            return wp_get_attachment_url($attach_id);
        }

        return new WP_Error('attachment_error', 'Failed to insert media.');
    }

    public function skd_pl_delete_image($image_url)
    {
        $attachment_id = attachment_url_to_postid($image_url);
        if ($attachment_id) {
            wp_delete_attachment($attachment_id, true);
        }
    }

    public function skd_calculate_expiration_date($created_date, $plan)
    {
        if ($plan->never_expire == 1) {
            return "9999-12-31 23:59:59"; // Never expires
        }

        $new_expiration = new DateTime($created_date);
        $duration_multiplier = [
            "days" => 1,
            "weeks" => 7,
            "months" => 30,
            "years" => 365
        ];

        if ($plan->listing_duration == 0) {
            $new_expiration->modify("+1 year");
        } elseif (isset($duration_multiplier[$plan->duration_unit])) {
            $days_to_add = $plan->listing_duration * $duration_multiplier[$plan->duration_unit];
            $new_expiration->modify("+{$days_to_add} days");
        }

        return $new_expiration->format('Y-m-d H:i:s');
    }
    //============ Add new listing shortcode ===========================

    //============ Pricing shortcode ===========================
    function skd_price_plan_shortcode()
    {
        global $wpdb;
        $price_plans_table = $wpdb->prefix . 'skd_pl_price_plans';
        $orders_table = $wpdb->prefix . 'skd_pl_orders';

        // Fetch active price plans
        $plans = $wpdb->get_results("SELECT * FROM $price_plans_table WHERE plan_status = 'published' ORDER BY listing_sorting_order ASC");

        if (!$plans) {
            return '<p>No price plans available.</p>';
        }

        // Get purchased plans
        $purchased_plans = [];
        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            $purchased_orders = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $orders_table WHERE user_id = %d AND order_status = 'completed'",
                $user_id
            ));

            foreach ($purchased_orders as $order) {
                if ($this->skd_is_plan_active_for_user($order)) {
                    $purchased_plans[$order->plan_id] = $order->id;
                }
            }
        }

        ob_start();
        include SKD_PL_PLUGIN_PATH . 'templates/shortcodes/pricing-plan.php';
        return ob_get_clean();
    }

    function skd_is_plan_active_for_user($order)
    {
        global $wpdb;

        $plan = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}skd_pl_price_plans WHERE id = %d",
            $order->plan_id
        ));

        if (!$plan) return false;

        // Pay per listing → check if listing exists for this order
        if ($plan->plan_type === 'pay_per_listing') {
            $count = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}skd_pl_listings WHERE order_id = %d",
                $order->id
            ));
            return $count < 1;
        }

        // Subscription check
        if ($order->is_subscription === 'true' && !empty($order->subscription_id)) {
            if (!class_exists('\Stripe\Stripe')) {
                require_once SKD_PL_PLUGIN_PATH . 'vendor/autoload.php';
            }

            \Stripe\Stripe::setApiKey(SKD_STRIPE_API_KEY);

            try {
                $subscription = \Stripe\Subscription::retrieve($order->subscription_id);
                return $subscription && $subscription->status === 'active';
            } catch (\Exception $e) {
                return false;
            }
        }

        // Regular plan expiration
        if ($plan->never_expire === '1') return true;

        $expiry_date = strtotime("+{$plan->listing_duration} {$plan->duration_unit}", strtotime($order->created_at));
        return current_time('timestamp') <= $expiry_date;
    }
    //============ Pricing shortcode ===========================

    //============ Checkout shortcode ===========================
    function skd_checkout_shortcode()
    {
        if (!is_user_logged_in()) {
            return '<p>Please <a href="' . site_url('/good-services-suppliers-register') . '">log in</a> to proceed to checkout.</p>';
        }

        if (!isset($_GET['plan']) || empty($_GET['plan'])) {
            return '<p>Invalid checkout request.</p>';
        }

        global $wpdb;
        $price_plans_table = $wpdb->prefix . 'skd_pl_price_plans';
        $plan_id = intval($_GET['plan']);

        // Fetch plan details
        $plan = $wpdb->get_row($wpdb->prepare("SELECT * FROM $price_plans_table WHERE id = %d AND plan_status = 'published'", $plan_id));

        if (!$plan) {
            return '<p>Selected price plan not found.</p>';
        }

        $user_id = get_current_user_id();
        $orders_table = $wpdb->prefix . 'skd_pl_orders';
        $order = $wpdb->get_row($wpdb->prepare("SELECT * FROM $orders_table WHERE user_id = %d AND plan_id = %d AND order_status = 'completed'", $user_id, $plan_id));

        // if ($order) {
        //     return '<p>You have already purchased this plan.</p>';
        // }

        ob_start();
        include SKD_PL_PLUGIN_PATH . 'templates/shortcodes/checkout.php';
        return ob_get_clean();
    }

    function skd_apply_coupon()
    {
        global $wpdb;

        $coupon_code = sanitize_text_field($_POST['coupon_code']);
        $plan_id = intval($_POST['plan_id']);

        // Fetch the coupon
        $coupon = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}skd_pl_coupons WHERE coupon_code = %s AND coupon_status = 'publish'",
            $coupon_code
        ));

        if (!$coupon) {
            wp_send_json_error(['message' => 'Invalid coupon code.']);
        }

        // Check if the coupon is expired
        $current_date = date('Y-m-d');
        if ($coupon->expiry_date && $coupon->expiry_date < $current_date) {
            wp_send_json_error(['message' => 'This coupon has expired.']);
        }

        // Fetch the plan price
        $plan = $wpdb->get_row($wpdb->prepare(
            "SELECT price, add_gst_rate, gst_type, gst_rate, is_free FROM {$wpdb->prefix}skd_pl_price_plans WHERE id = %d",
            $plan_id
        ));

        if (!$plan) {
            wp_send_json_error(['message' => 'Plan not found.']);
        }

        // Check if the coupon is applicable for this plan (for 'fixed_product' discount type)
        if ($coupon->discount_type === 'fixed_product') {
            $product_ids = explode(',', $coupon->product_ids);
            if (!in_array($plan_id, $product_ids)) {
                wp_send_json_error(['message' => 'Coupon is not applicable for this plan.']);
            }
        }

        $original_price = $plan->price;
        $discount = 0;

        // Calculate discount
        if ($coupon->discount_type === 'percentage') {
            $discount = ($original_price * $coupon->discount_amount) / 100;
        } elseif ($coupon->discount_type === 'fixed_cart') {
            $discount = $coupon->discount_amount;
        } elseif ($coupon->discount_type === 'fixed_product') {
            $discount = $coupon->discount_amount;
        }

        // Ensure discount does not exceed the original price
        $discount = min($discount, $original_price);

        // Calculate GST amount based on the original price
        $gst_amount = 0;
        if ($plan->gst_type === 'flat') {
            $gst_amount = $plan->gst_rate;
        } elseif ($plan->gst_type === 'percentage') {
            $gst_amount = ($original_price * $plan->gst_rate / 100);
        }

        // Calculate subtotal (Base Price + GST)
        $subtotal = $original_price + $gst_amount;

        // Determine final price
        if ($coupon->gst_exemption === 'exempt_gst') {
            // If GST is exempt, total price should be the subtotal before GST
            $total_price = $original_price - $discount;
            $gst_amount = 0;
        } else {
            // If GST applies, calculate total price normally
            $total_price = max(0, ($original_price - $discount) + $gst_amount);
        }

        wp_send_json_success([
            'message' => 'Coupon applied successfully!',
            'subtotal' => number_format($subtotal, 2),
            'total_price' => number_format($total_price, 2),
            'discount' => number_format($discount, 2),
            'gst_amount' => number_format($gst_amount, 2),
            'total_discount' => number_format(($subtotal - $total_price), 2),
            'coupon_id' => $coupon->id,
        ]);
    }

    function skd_process_free_plan()
    {
        $plan_id = isset($_POST['plan_id']) ? intval($_POST['plan_id']) : 0;

        if (!$plan_id) {
            wp_send_json_error(['message' => 'Invalid plan.']);
        }

        $coupon_id = isset($_POST['coupon_id']) ? intval($_POST['coupon_id']) : 0;
        $final_total_price = isset($_POST['final_total_price']) ? floatval($_POST['final_total_price']) : 0;
        $final_discount = isset($_POST['final_discount']) ? floatval($_POST['final_discount']) : 0;
        $payment_method = 'free';
        $payment_status = '';

        // Create order in database
        $order_id = $this->skd_create_order($plan_id, $final_total_price, 'completed', $final_discount, $coupon_id, $payment_method, $payment_status);

        if ($order_id) {
            wp_send_json_success(['redirect' => site_url('dashboard')]);
        } else {
            wp_send_json_error(['message' => 'Failed to create order.']);
        }
    }

    function skd_process_paid_order()
    {
        $plan_id = isset($_POST['plan_id']) ? intval($_POST['plan_id']) : 0;
        $coupon_id = isset($_POST['coupon_id']) ? intval($_POST['coupon_id']) : 0;
        $final_total_price = isset($_POST['final_total_price']) ? floatval($_POST['final_total_price']) : 0;
        $final_discount = isset($_POST['final_discount']) ? floatval($_POST['final_discount']) : 0;

        if (!$plan_id || $final_total_price <= 0) {
            wp_send_json_error(['message' => 'Invalid request.']);
        }

        // Create Stripe Checkout Session
        $session = $this->skd_create_stripe_session($plan_id, $final_total_price, $final_discount, $coupon_id);

        if ($session) {
            wp_send_json_success(['payment_url' => $session->url]);
        } else {
            wp_send_json_error(['message' => 'Failed to process payment.']);
        }
    }

    // Function to create an order
    public static function skd_create_order($plan_id, $amount, $status, $final_discount, $coupon_id, $payment_method, $payment_status, $transaction_id = '', $payment_details = '', $is_subscription = '', $subscription_id = '')
    {
        global $wpdb;
        $order_table = $wpdb->prefix . 'skd_pl_orders';
        $coupon_table = $wpdb->prefix . 'skd_pl_coupons';
        $coupon_usage_table = $wpdb->prefix . 'skd_pl_coupon_usage';
        $user_id = get_current_user_id();
        if ($coupon_id) {
            $couponDetails = $wpdb->get_row($wpdb->prepare("SELECT * FROM $coupon_table WHERE id = %d", $coupon_id));
        } else {
            $couponDetails = null;
        }

        $wpdb->insert(
            $order_table,
            [
                'user_id' => $user_id,
                'plan_id' => $plan_id,
                'coupon_id' => $coupon_id,
                'coupon_details' => $couponDetails ? json_encode($couponDetails) : null,
                'discount_amount' => $final_discount,
                'final_price' => $amount,
                'order_status' => $status,
                'payment_method' => $payment_method,
                'payment_status' => $payment_method === 'free' ? '' : $payment_status,
                'payment_date' => $payment_method === 'free' ? null : current_time('mysql'),
                'payment_amount' => $payment_method === 'free' ? 0 : $amount,
                'payment_currency' => 'AUD',
                'payment_transaction_id' => $transaction_id,
                'payment_details' => $payment_details,
                'is_subscription' => $is_subscription,
                'subscription_id' => $subscription_id,
                'created_at' => current_time('mysql'),
            ]
        );
        $order_id = $wpdb->insert_id;

        if ($coupon_id) {
            $wpdb->query($wpdb->prepare(
                "INSERT INTO {$coupon_usage_table} (user_id, coupon_id, usage_count)
                    VALUES (%d, %d, 1) ON DUPLICATE KEY UPDATE usage_count = usage_count + 1",
                $user_id,
                $coupon_id
            ));
        }

        //================== Send Email =========================================
        $mailSite_icon_url = get_site_icon_url();
        $orderUserDetails = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}users WHERE ID = %d", $user_id));
        $listing_contact_email = $orderUserDetails->user_email;
        // $listing_contact_email = 'sanjoy.websadroit@gmail.com';
        $listing_contact_name = $orderUserDetails->display_name;
        $order_list_url = site_url('/dashboard/');

        $mailContactName = $listing_contact_name ? $listing_contact_name : 'User';
        $mailTitleTxt = "Congratulation! Your Order #{$order_id} Completed";
        $emailBodyHtml = "
                <p>Congratulations! This email is to notify you that your order #6766 has been completed.</p>            
                <br>
                <p>You can check your order details by clicking the link below.</p>
                <p>Order Details Page: <a href='{$order_list_url}'>View order</a></p> 
                <br>
                <table style='border-collapse: collapse; width: 100%;'>
                <tr>
                    <th style='border: 1px solid #ddd; padding: 8px;'>Item(s)</th>
                    <th style='border: 1px solid #ddd; padding: 8px;'>Price [AUD]</th>
                </tr>
                <tr>
                    <td style='border: 1px solid #ddd; padding: 8px;'>Total amount [AUD]</td>
                    <td style='border: 1px solid #ddd; padding: 8px;'>{$amount}</td>
                </tr>
                </table>
                <br><br><br>
                <p>NB. You need to be logged in your account to access the order details page.</p>
                <br>
                Thank you for being a part of the our team and we look forward to a successful year!
                <br>
            ";

        ob_start();
        include SKD_PL_PLUGIN_PATH . 'templates/mail-template.php';
        $email_body = ob_get_clean();

        $subject = $mailTitleTxt ? $mailTitleTxt : 'Congratulation! Your Order Completed';
        $headers = ["Content-Type: text/html; charset=UTF-8"];

        try {
            wp_mail($listing_contact_email, $subject, $email_body, $headers);
        } catch (Exception $e) {
            // error_log("Exception while sending email: " . $e->getMessage());
        }
        //================== Send Email =========================================

        return $order_id;
    }

    // Function to create Stripe Checkout Session
    function skd_create_stripe_session($plan_id, $total_price, $final_discount, $coupon_id)
    {
        // Ensure Stripe is loaded
        if (!class_exists('\Stripe\Stripe')) {
            require_once SKD_PL_PLUGIN_PATH . 'vendor/autoload.php';
        }
        \Stripe\Stripe::setApiKey(SKD_STRIPE_API_KEY);

        global $wpdb;
        $price_plans_table = $wpdb->prefix . 'skd_pl_price_plans';
        $plan = $wpdb->get_row($wpdb->prepare("SELECT * FROM $price_plans_table WHERE id = %d", $plan_id));
        if (!$plan) return false;

        $plan_name = $plan ? $plan->plan_name : "Plan #$plan_id";

        if ($plan->never_expire) {
            $is_subscription = false;
            $listing_duration = 0;
            $duration_unit = 'day';
        } else {
            $is_subscription = $plan->enable_subscription === 'yes';
            $listing_duration = $plan->listing_duration;
            $duration_unit = rtrim($plan->duration_unit, 's');
        }

        if ($is_subscription) {
            $product = \Stripe\Product::create(['name' => $plan_name]);

            $price = \Stripe\Price::create([
                'unit_amount' => $total_price * 100,
                'currency' => 'AUD',
                'recurring' => [
                    'interval' => $duration_unit,
                    'interval_count' => $listing_duration,
                    // 'interval' => 'day',
                    // 'interval_count' => 1,
                ], //'day', 'week', 'month', 'year'
                'product' => $product->id,
            ]);

            return \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'mode' => 'subscription',
                'line_items' => [[
                    'price' => $price->id,
                    'quantity' => 1,
                ]],
                'success_url' => site_url('/order-confirmation?session_id={CHECKOUT_SESSION_ID}'),
                'cancel_url' => site_url('/checkout?plan=' . $plan_id),
                'metadata' => [
                    'user_id' => get_current_user_id(),
                    'plan_id' => $plan_id,
                    'coupon_id' => $coupon_id,
                    'final_discount' => $final_discount,
                    'is_subscription' => $is_subscription
                ]
            ]);
        } else {
            return \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'AUD',
                        'product_data' => ['name' => $plan_name],
                        'unit_amount' => $total_price * 100,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => site_url('/order-confirmation?session_id={CHECKOUT_SESSION_ID}'),
                'cancel_url' => site_url('/checkout?plan=' . $plan_id),
                'metadata' => [
                    'user_id' => get_current_user_id(),
                    'plan_id' => $plan_id,
                    'coupon_id' => $coupon_id,
                    'final_discount' => $final_discount,
                    'is_subscription' => $is_subscription
                ]
            ]);
        }
    }
    //============ Checkout shortcode ===========================

    function skd_user_listings_shortcode()
    {
        if (!is_user_logged_in()) {
            return '<p>You need to <a href="' . site_url('/good-services-suppliers-register') . '">log in</a> to view your listings.</p>';
        }

        global $wpdb;
        $current_user_id = get_current_user_id();
        $listing_table = $wpdb->prefix . 'skd_pl_listings';

        // Fetch listings of logged-in user
        // $listings = $wpdb->get_results(
        //     $wpdb->prepare(
        //         "SELECT id, listing_title, slug, listing_status, created_at FROM $listing_table WHERE user_id = %d ORDER BY created_at DESC",
        //         $current_user_id
        //     )
        // );

        $order_table = $wpdb->prefix . 'skd_pl_orders';
        $is_admin = current_user_can('administrator');

        // if ($is_admin) {
        //     // Admin sees all listings, no need to join orders
        //     $listings = $wpdb->get_results(
        //         "SELECT id, listing_title, slug, listing_status, created_at 
        //         FROM $listing_table 
        //         ORDER BY created_at DESC"
        //     );
        // } else {
        //     // Regular user – join with orders to filter
        //     $listings = $wpdb->get_results(
        //         $wpdb->prepare(
        //             "SELECT l.id, l.listing_title, l.slug, l.listing_status, l.created_at, o.plan_id, o.order_status 
        //             FROM $listing_table l
        //             JOIN $order_table o ON l.order_id = o.id
        //             WHERE l.user_id = %d
        //             ORDER BY l.created_at DESC",
        //             $current_user_id
        //         )
        //     );
        // }

        $listings = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT l.id, l.listing_title, l.slug, l.listing_status, l.created_at, o.plan_id, o.order_status 
                    FROM $listing_table l
                    JOIN $order_table o ON l.order_id = o.id
                    WHERE l.user_id = %d
                    ORDER BY l.created_at DESC",
                $current_user_id
            )
        );

        if (!$listings) {
            return '<p>No listings found.</p>';
        }

        ob_start();
        include SKD_PL_PLUGIN_PATH . 'templates/shortcodes/user-listings.php';
        return ob_get_clean();
    }

    function skd_user_orders_shortcode()
    {
        if (!is_user_logged_in()) {
            return '<p>You need to <a href="' . site_url('/good-services-suppliers-register') . '">log in</a> to view your orders.</p>';
        }

        global $wpdb;
        $current_user_id = get_current_user_id();
        $order_table = $wpdb->prefix . 'skd_pl_orders';
        $plan_table = $wpdb->prefix . 'skd_pl_price_plans'; // Assuming plan details are stored in this table

        // Fetch user orders
        $orders = $wpdb->get_results(
            $wpdb->prepare("
            SELECT o.*, p.plan_name
            FROM $order_table o
            LEFT JOIN $plan_table p ON o.plan_id = p.id
            WHERE o.user_id = %d
            ORDER BY o.created_at DESC
        ", $current_user_id)
        );

        // Append active status
        foreach ($orders as $oKey => $order) {
            $orders[$oKey]->is_active = $this->skd_is_plan_active_for_user($order) ? 'Active' : 'Inactive';
        }

        ob_start();
        include SKD_PL_PLUGIN_PATH . 'templates/shortcodes/user-orders.php';
        return ob_get_clean();
    }

    function skd_cancel_subscription()
    {
        if (!isset($_POST['order_id'])) {
            wp_send_json_error(['message' => 'Invalid request.']);
        }

        require_once SKD_PL_PLUGIN_PATH . 'vendor/autoload.php';
        \Stripe\Stripe::setApiKey(SKD_STRIPE_API_KEY);

        //fetch subscription id from order table
        global $wpdb;
        $order_id = intval($_POST['order_id']);
        $order_table = $wpdb->prefix . 'skd_pl_orders';
        $order = $wpdb->get_row($wpdb->prepare("SELECT * FROM $order_table WHERE id = %d", $order_id));
        if (!$order) {
            wp_send_json_error(['message' => 'Order not found.']);
        }
        if (empty($order->subscription_id)) {
            wp_send_json_error(['message' => 'No subscription found for this order.']);
        }
        $subscription_id = $order->subscription_id;

        try {
            \Stripe\Subscription::update($subscription_id, ['cancel_at_period_end' => true]);

            // Update order status in your database if needed
            $wpdb->update(
                $order_table,
                ['subscription_cancel' => 'yes'],
                ['id' => $order_id]
            );

            wp_send_json_success(['message' => 'Subscription cancelled successfully.']);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => 'Error: ' . $e->getMessage()]);
        }
    }

    function skd_check_order_active()
    {
        global $wpdb;

        $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;

        if (!$order_id) {
            wp_send_json_error(['message' => 'Invalid Order ID']);
        }

        $order = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}skd_pl_orders WHERE id = %d", $order_id));
        if (!$order) {
            wp_send_json_error(['message' => 'Order not found']);
        }

        $is_subscription = $order->is_subscription == 'true' ? true : false;

        $planDetais = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}skd_pl_price_plans WHERE id = %d", $order->plan_id));
        if (!$planDetais) {
            wp_send_json_error(['message' => 'Plan not found']);
        }

        //if plan_type is pay_per_listing then check listing count for this order id
        if ($planDetais->plan_type == 'pay_per_listing') {
            $listing_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}skd_pl_listings WHERE order_id = %d", $order_id));
            if ($listing_count >= 1) {
                wp_send_json_error(['message' => 'You have no remaining listings under this plan.']);
            }
        }

        // If it's a subscription
        if ($is_subscription && $order->subscription_id) {
            if (!class_exists('\Stripe\Stripe')) {
                require_once SKD_PL_PLUGIN_PATH . 'vendor/autoload.php';
            }

            \Stripe\Stripe::setApiKey(SKD_STRIPE_API_KEY);

            try {
                $subscription = \Stripe\Subscription::retrieve($order->subscription_id);
                // echo '<pre>';
                // print_r($subscription);
                // exit;
                if ($subscription->status === 'active') {
                    wp_send_json_success([
                        'redirect_url' => site_url('/add-new-listing/?order=' . $order->id)
                    ]);
                } else {
                    wp_send_json_error(['message' => 'Subscription is not active']);
                }
            } catch (Exception $e) {
                wp_send_json_error(['message' => 'Stripe error: ' . $e->getMessage()]);
            }
        }

        // If it's a one-time plan, check expiry
        $plan = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}skd_pl_price_plans WHERE id = %d", $order->plan_id));
        if ($plan && $planDetais->plan_type != 'pay_per_listing') {
            if ($plan->never_expire) {
                wp_send_json_success([
                    'redirect_url' => site_url('/add-new-listing/?order=' . $order->id)
                ]);
            }
            $expiry_date = strtotime("+{$plan->listing_duration} {$plan->duration_unit}", strtotime($order->created_at));
            if (current_time('timestamp') <= $expiry_date) {
                wp_send_json_success([
                    'redirect_url' => site_url('/add-new-listing/?order=' . $order->id)
                ]);
            } else {
                wp_send_json_error(['message' => 'Your plan has expired']);
            }
        } else {
            wp_send_json_success([
                'redirect_url' => site_url('/add-new-listing/?order=' . $order->id)
            ]);
        }

        wp_send_json_error(['message' => 'Unable to validate order']);
    }

    function skd_listing_details_shortcode()
    {
        global $wpdb;

        $slug = get_query_var('skd_listing_slug');
        if (!$slug) {
            return '<p>Listing not found.</p>';
        }

        $table_name = $wpdb->prefix . 'skd_pl_listings';
        $listing = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE slug = %s AND listing_status = 'publish'", $slug));

        if (!$listing) {
            return '<p>Listing not found.</p>';
        }

        $gallery_images = json_decode($listing->skd_gallery, true);

        // Decode category IDs from JSON
        $category_ids = json_decode($listing->category_ids, true);

        $related_listings = [];
        if (!empty($category_ids)) {
            $conditions = [];
            foreach ($category_ids as $cat_id) {
                $conditions[] = "JSON_CONTAINS(category_ids, '\"{$cat_id}\"')";
            }
            $category_condition = implode(' OR ', $conditions);

            $related_listings = $wpdb->get_results("
                SELECT * FROM $table_name
                WHERE listing_status = 'publish'
                AND id != {$listing->id}
                AND ($category_condition)
                LIMIT 4
            ");
        }


        ob_start();
        include SKD_PL_PLUGIN_PATH . 'templates/shortcodes/listing-details.php';
        return ob_get_clean();
    }

    function skd_get_embeddable_video_url($url)
    {
        // YouTube long format
        if (strpos($url, 'youtube.com/watch') !== false) {
            parse_str(parse_url($url, PHP_URL_QUERY), $query_vars);
            if (!empty($query_vars['v'])) {
                return 'https://www.youtube.com/embed/' . esc_attr($query_vars['v']);
            }
        }

        // YouTube short format
        if (strpos($url, 'youtu.be/') !== false) {
            $video_id = basename(parse_url($url, PHP_URL_PATH));
            return 'https://www.youtube.com/embed/' . esc_attr($video_id);
        }

        // Vimeo format
        if (strpos($url, 'vimeo.com/') !== false) {
            $video_id = basename(parse_url($url, PHP_URL_PATH));
            return 'https://player.vimeo.com/video/' . esc_attr($video_id);
        }

        // If it's already an embed URL or another video platform, return as is
        return esc_url($url);
    }

    function skd_submit_list_contact_form()
    {
        global $wpdb;

        // Sanitize input
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $message = sanitize_textarea_field($_POST['message']);
        $listing_id = intval($_POST['listing_id']);
        $listing_url = esc_url($_POST['listing_url']);
        $site_name = get_bloginfo('name');
        $now = current_time('mysql');

        // Validate inputs
        if (empty($name) || empty($email) || empty($message)) {
            wp_send_json_error("All fields are required.");
        }

        // Get listing contact email
        $listing_details = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}skd_pl_listings WHERE id = %d",
            $listing_id
        ));

        if (!$listing_details) {
            wp_send_json_error("Listing not found.");
        }
        $listing_contact_email = $listing_details->contact_email;

        if (empty($listing_contact_email)) {
            wp_send_json_error("Listing contact email not found.");
        }

        $listing_contact_name = '';
        $listingUserId = $listing_details->user_id;
        $listingUser = get_userdata($listingUserId);
        if ($listingUser) {
            $listing_contact_name = $listingUser->display_name;
        }

        // Insert into database skd_pl_listing_contact_form
        $contact_table = $wpdb->prefix . 'skd_pl_listing_contact_form';
        $user_id = get_current_user_id();
        $wpdb->insert($contact_table, [
            'listing_id' => $listing_id,
            'user_id' => $user_id,
            'name' => $name,
            'email' => $email,
            'message' => $message,
            'created_at' => $now
        ]);

        //================== Send Email =========================================
        $mailSite_icon_url = get_site_icon_url();
        $mailContactName = $listing_contact_name ? $listing_contact_name : 'User';
        $mailTitleTxt = 'New Message from Your Listing "' . $listing_details->listing_title . '"';
        $emailBodyHtml = "
            <p>You have received a message from your listing at <a href='{$listing_url}'>{$listing_url}</a>.</p>
            <table style='border-collapse: collapse; width: 100%;'>
            <tr>
                <td style='border: 1px solid #ddd; padding: 8px;'>Name</td>
                <td style='border: 1px solid #ddd; padding: 8px;'>{$name}</td>
            </tr>
            <tr>
                <td style='border: 1px solid #ddd; padding: 8px;'>Email</td>
                <td style='border: 1px solid #ddd; padding: 8px;'>{$email}</td>
            </tr>
            <tr>
                <td style='border: 1px solid #ddd; padding: 8px;'>Message</td>
                <td style='border: 1px solid #ddd; padding: 8px;'>{$message}</td>
            </tr>
            <tr>
                <td style='border: 1px solid #ddd; padding: 8px;'>Time</td>
                <td style='border: 1px solid #ddd; padding: 8px;'>{$now}</td>
            </tr>
            </table>
            <br><br>
            Thank you,
            <br>
        ";

        ob_start();
        include SKD_PL_PLUGIN_PATH . 'templates/mail-template.php';
        $email_body = ob_get_clean();

        $subject = "New Message from Your Listing";
        $headers = ["Content-Type: text/html; charset=UTF-8"];

        try {
            $mail_sent = wp_mail($listing_contact_email, $subject, $email_body, $headers);

            // if (!$mail_sent) {
            //     error_log("Mail not sent to {$listing_contact_email} regarding listing: {$listing_details->listing_title}");
            // }
        } catch (Exception $e) {
            // error_log("Exception while sending email: " . $e->getMessage());
        }
        //================== Send Email =========================================

        wp_send_json_success();
    }

    // === NEW INTERIASSIST SHORTCODE METHODS ===

    /**
     * Find Assistants Directory Shortcode
     */
    public function skd_find_assistants_shortcode($atts)
    {
        $atts = shortcode_atts([
            'per_page' => 12,
            'featured_first' => true,
            'show_filters' => true
        ], $atts);

        ob_start();
        include SKD_PL_PLUGIN_PATH . 'templates/shortcodes/find-assistants.php';
        return ob_get_clean();
    }

    /**
     * Find Studios Directory Shortcode
     */
    public function skd_find_studios_shortcode($atts)
    {
        $atts = shortcode_atts([
            'per_page' => 12,
            'featured_first' => true,
            'show_filters' => true
        ], $atts);

        ob_start();
        include SKD_PL_PLUGIN_PATH . 'templates/shortcodes/find-studios.php';
        return ob_get_clean();
    }

    /**
     * Professional Profile Shortcode
     */
    public function skd_professional_profile_shortcode($atts)
    {
        $professional_slug = get_query_var('skd_professional_slug');
        $studio_slug = get_query_var('skd_studio_slug');

        if (!$professional_slug && !$studio_slug) {
            return '<p>Professional not found.</p>';
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_listings';

        $slug = $professional_slug ?: $studio_slug;
        $professional = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE slug = %s AND listing_status = 'publish'",
            $slug
        ));

        if (!$professional) {
            return '<p>Professional not found.</p>';
        }

        ob_start();
        include SKD_PL_PLUGIN_PATH . 'templates/shortcodes/professional-profile.php';
        return ob_get_clean();
    }

    /**
     * Job Board Shortcode
     */
    public function skd_job_board_shortcode($atts)
    {
        $atts = shortcode_atts([
            'per_page' => 10,
            'show_expired' => false
        ], $atts);

        ob_start();
        include SKD_PL_PLUGIN_PATH . 'templates/shortcodes/job-board.php';
        return ob_get_clean();
    }

    /**
     * Post Job Form Shortcode
     */
    public function skd_post_job_form_shortcode($atts)
    {
        if (!is_user_logged_in()) {
            return '<p>Please <a href="' . wp_login_url() . '">login</a> to post a job.</p>';
        }

        ob_start();
        include SKD_PL_PLUGIN_PATH . 'templates/shortcodes/post-job-form.php';
        return ob_get_clean();
    }

    /**
     * Academy Shortcode
     */
    public function skd_academy_shortcode($atts)
    {
        ob_start();
        include SKD_PL_PLUGIN_PATH . 'templates/shortcodes/academy.php';
        return ob_get_clean();
    }

    /**
     * Resources Shortcode
     */
    public function skd_resources_shortcode($atts)
    {
        ob_start();
        include SKD_PL_PLUGIN_PATH . 'templates/shortcodes/resources.php';
        return ob_get_clean();
    }

    /**
     * Community Shortcode
     */
    public function skd_community_shortcode($atts)
    {
        ob_start();
        include SKD_PL_PLUGIN_PATH . 'templates/shortcodes/community.php';
        return ob_get_clean();
    }

    /**
     * User Dashboard Shortcode
     */
    /**
     * VDA Profile Edit Shortcode
     */
    public function skd_edit_vda_profile_shortcode($atts)
    {
        if (!is_user_logged_in()) {
            return '<p>Please <a href="' . home_url('/login/') . '">login</a> to edit your profile.</p>';
        }

        ob_start();
        include SKD_PL_PLUGIN_PATH . 'templates/shortcodes/edit-vda-profile.php';
        return ob_get_clean();
    }

    /**
     * VDA Dashboard Shortcode
     */
    public function skd_vda_dashboard_shortcode($atts)
    {
        if (!is_user_logged_in()) {
            return '<p>Please <a href="' . home_url('/login/') . '">login</a> to access your dashboard.</p>';
        }

        ob_start();
        include SKD_PL_PLUGIN_PATH . 'templates/shortcodes/vda-dashboard.php';
        return ob_get_clean();
    }

    /**
     * VDA Public Profile Shortcode
     */
    public function skd_vda_profile_shortcode($atts)
    {
        ob_start();
        include SKD_PL_PLUGIN_PATH . 'templates/shortcodes/vda-profile-public.php';
        return ob_get_clean();
    }

    /**
     * Studio Dashboard Shortcode
     */
    public function skd_studio_dashboard_shortcode($atts)
    {
        if (!is_user_logged_in()) {
            return '<p>Please <a href="' . home_url('/login/') . '">login</a> to access your dashboard.</p>';
        }

        ob_start();
        include SKD_PL_PLUGIN_PATH . 'templates/shortcodes/studio-dashboard.php';
        return ob_get_clean();
    }

    /**
     * Employer Dashboard Shortcode
     */
    public function skd_employer_dashboard_shortcode($atts)
    {
        if (!is_user_logged_in()) {
            return '<p>Please <a href="' . home_url('/login/') . '">login</a> to access your dashboard.</p>';
        }

        ob_start();
        include SKD_PL_PLUGIN_PATH . 'templates/shortcodes/employer-dashboard.php';
        return ob_get_clean();
    }

    /**
     * Update Basic Info AJAX Handler
     */
    public function skd_update_basic_info()
    {
        check_ajax_referer('skd_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'User not logged in']);
        }

        $user_id = get_current_user_id();
        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);
        $phone = sanitize_text_field($_POST['phone']);

        wp_update_user([
            'ID' => $user_id,
            'first_name' => $first_name,
            'last_name' => $last_name
        ]);

        update_user_meta($user_id, 'skd_phone', $phone);

        wp_send_json_success(['message' => 'Information updated successfully']);
    }

    /**
     * Change Password AJAX Handler
     */
    public function skd_change_password_ajax()
    {
        check_ajax_referer('skd_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'User not logged in']);
        }

        $user_id = get_current_user_id();
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];

        $user = wp_get_current_user();

        // Verify current password
        if (!wp_check_password($current_password, $user->user_pass, $user_id)) {
            wp_send_json_error(['message' => 'Current password is incorrect']);
        }

        // Update password
        wp_set_password($new_password, $user_id);

        wp_send_json_success(['message' => 'Password updated successfully']);
    }

    /**
     * Logout User AJAX Handler
     */
    public function skd_logout_user()
    {
        check_ajax_referer('skd_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'User not logged in']);
        }

        wp_logout();
        wp_send_json_success(['message' => 'Logged out successfully']);
    }

    /**
     * AJAX Filter Professionals
     */
    public function skd_filter_professionals()
    {
        global $wpdb;

        // Parse filter data from serialized form
        parse_str($_POST['filters'] ?? '', $filters);
        $sort_by = sanitize_text_field($_POST['sort_by'] ?? 'newest');
        $per_page = 12;
        $current_page = isset($_POST['paged']) ? max(1, intval($_POST['paged'])) : 1;
        $offset = ($current_page - 1) * $per_page;

        // Build query using user_profiles table
        $profiles_table = $wpdb->prefix . 'skd_pl_user_profiles';
        $users_table = $wpdb->prefix . 'users';
        $usermeta_table = $wpdb->prefix . 'usermeta';

        $query = "SELECT p.*, u.display_name 
                  FROM {$profiles_table} p
                  LEFT JOIN {$users_table} u ON p.user_id = u.ID
                  LEFT JOIN {$usermeta_table} um ON (u.ID = um.user_id AND um.meta_key = '{$wpdb->prefix}capabilities')
                  WHERE p.user_type = 'vda'
                  AND um.meta_value LIKE '%\"vda_user\"%'";

        $where_conditions = [];

        // Skills filter - convert slugs to IDs, then check JSON array in skills column
        if (!empty($filters['software']) || !empty($filters['skills'])) {
            $skill_slugs = !empty($filters['software']) ? array_map('sanitize_text_field', $filters['software']) : array_map('sanitize_text_field', $filters['skills']);

            // Convert slugs to IDs
            $slug_placeholder = implode(',', array_fill(0, count($skill_slugs), '%s'));
            $skill_ids = $wpdb->get_col($wpdb->prepare(
                "SELECT id FROM {$wpdb->prefix}skd_pl_skills WHERE slug IN ($slug_placeholder) AND status = 'active'",
                ...$skill_slugs
            ));

            if (!empty($skill_ids)) {
                $skill_conditions = [];
                foreach ($skill_ids as $skill_id) {
                    // Match skill ID in JSON array
                    $skill_conditions[] = $wpdb->prepare(
                        "(p.skills LIKE %s OR p.skills LIKE %s OR p.skills LIKE %s OR p.skills LIKE %s)",
                        '[' . $skill_id . ']',
                        '[' . $skill_id . ',%',
                        '%,' . $skill_id . ',%',
                        '%,' . $skill_id . ']'
                    );
                }
                $where_conditions[] = '(' . implode(' OR ', $skill_conditions) . ')';
            }
        }

        // Experience Level filter - match ENUM value
        if (!empty($filters['experience_level'])) {
            $experience_levels = array_map('sanitize_text_field', $filters['experience_level']);
            $exp_placeholder = implode(',', array_fill(0, count($experience_levels), '%s'));
            $where_conditions[] = $wpdb->prepare("p.experience_level IN ($exp_placeholder)", ...$experience_levels);
        }

        // Hourly Rate filter
        if (!empty($filters['hourly_rate'])) {
            $rate_ranges = $filters['hourly_rate'];
            $rate_conditions = [];
            foreach ($rate_ranges as $range) {
                switch ($range) {
                    case '0-10':
                        $rate_conditions[] = "p.hourly_rate < 10";
                        break;
                    case '10-15':
                        $rate_conditions[] = "(p.hourly_rate >= 10 AND p.hourly_rate < 15)";
                        break;
                    case '15-25':
                        $rate_conditions[] = "(p.hourly_rate >= 15 AND p.hourly_rate < 25)";
                        break;
                    case '25-40':
                        $rate_conditions[] = "(p.hourly_rate >= 25 AND p.hourly_rate < 40)";
                        break;
                    case '40-60':
                        $rate_conditions[] = "(p.hourly_rate >= 40 AND p.hourly_rate < 60)";
                        break;
                    case '60+':
                        $rate_conditions[] = "p.hourly_rate >= 60";
                        break;
                }
            }
            if (!empty($rate_conditions)) {
                $where_conditions[] = '(' . implode(' OR ', $rate_conditions) . ')';
            }
        }

        // Availability filter - convert slugs to match availability_status column
        if (!empty($filters['availability'])) {
            $availability_slugs = array_map('sanitize_text_field', $filters['availability']);

            // Convert slugs to IDs
            $slug_placeholder = implode(',', array_fill(0, count($availability_slugs), '%s'));
            $availability_ids = $wpdb->get_col($wpdb->prepare(
                "SELECT id FROM {$wpdb->prefix}skd_pl_availability_types WHERE slug IN ($slug_placeholder) AND status = 'active'",
                ...$availability_slugs
            ));

            if (!empty($availability_ids)) {
                $avail_placeholder = implode(',', array_fill(0, count($availability_ids), '%d'));
                $where_conditions[] = $wpdb->prepare("p.availability_status IN ($avail_placeholder)", ...$availability_ids);
            }
        }

        // Timezone filter - convert timezone value to ID
        if (!empty($filters['timezone'])) {
            $timezone_value = sanitize_text_field($filters['timezone']);
            $timezone_id = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM {$wpdb->prefix}skd_pl_timezones WHERE value = %s AND status = 'active'",
                $timezone_value
            ));

            if ($timezone_id) {
                $where_conditions[] = $wpdb->prepare("p.timezone = %d", $timezone_id);
            }
        }

        // Project Type filter - convert slugs to IDs, then check JSON array
        if (!empty($filters['project_type'])) {
            $pt_slugs = array_map('sanitize_text_field', $filters['project_type']);

            $slug_placeholder = implode(',', array_fill(0, count($pt_slugs), '%s'));
            $pt_ids = $wpdb->get_col($wpdb->prepare(
                "SELECT id FROM {$wpdb->prefix}skd_pl_project_types WHERE slug IN ($slug_placeholder) AND status = 'active'",
                ...$pt_slugs
            ));

            if (!empty($pt_ids)) {
                $pt_conditions = [];
                foreach ($pt_ids as $pt_id) {
                    $pt_conditions[] = $wpdb->prepare(
                        "(p.project_types LIKE %s OR p.project_types LIKE %s OR p.project_types LIKE %s OR p.project_types LIKE %s)",
                        '[' . $pt_id . ']',
                        '[' . $pt_id . ',%',
                        '%,' . $pt_id . ',%',
                        '%,' . $pt_id . ']'
                    );
                }
                $where_conditions[] = '(' . implode(' OR ', $pt_conditions) . ')';
            }
        }

        // Service Type filter - convert slugs to IDs, then check JSON array
        if (!empty($filters['service_type'])) {
            $st_slugs = array_map('sanitize_text_field', $filters['service_type']);

            $slug_placeholder = implode(',', array_fill(0, count($st_slugs), '%s'));
            $st_ids = $wpdb->get_col($wpdb->prepare(
                "SELECT id FROM {$wpdb->prefix}skd_pl_service_types WHERE slug IN ($slug_placeholder) AND status = 'active'",
                ...$st_slugs
            ));

            if (!empty($st_ids)) {
                $st_conditions = [];
                foreach ($st_ids as $st_id) {
                    $st_conditions[] = $wpdb->prepare(
                        "(p.service_types LIKE %s OR p.service_types LIKE %s OR p.service_types LIKE %s OR p.service_types LIKE %s)",
                        '[' . $st_id . ']',
                        '[' . $st_id . ',%',
                        '%,' . $st_id . ',%',
                        '%,' . $st_id . ']'
                    );
                }
                $where_conditions[] = '(' . implode(' OR ', $st_conditions) . ')';
            }
        }

        // Badges filter - convert slugs to IDs, then check certifications table
        if (!empty($filters['badges'])) {
            $badge_slugs = array_map('sanitize_text_field', $filters['badges']);

            $slug_placeholder = implode(',', array_fill(0, count($badge_slugs), '%s'));
            $cert_ids = $wpdb->get_col($wpdb->prepare(
                "SELECT id FROM {$wpdb->prefix}skd_pl_certifications WHERE slug IN ($slug_placeholder) AND status = 'active'",
                ...$badge_slugs
            ));

            if (!empty($cert_ids)) {
                $cert_conditions = [];
                foreach ($cert_ids as $cert_id) {
                    $cert_conditions[] = $wpdb->prepare(
                        "p.user_id IN (SELECT user_id FROM {$wpdb->prefix}skd_pl_user_certifications WHERE certification_id = %d AND status = 'approved')",
                        $cert_id
                    );
                }
                $where_conditions[] = '(' . implode(' OR ', $cert_conditions) . ')';
            }
        }

        // Ratings filter
        if (!empty($filters['rating'])) {
            $min_rating = floatval($filters['rating']);
            $where_conditions[] = $wpdb->prepare("p.rating >= %f", $min_rating);
        }

        // Add WHERE conditions
        if (!empty($where_conditions)) {
            $query .= ' AND ' . implode(' AND ', $where_conditions);
        }

        // Get total count for pagination (before LIMIT)
        $count_query = str_replace('SELECT p.*, u.display_name', 'SELECT COUNT(DISTINCT p.id)', $query);
        $total_count = $wpdb->get_var($count_query);
        $total_pages = ceil($total_count / $per_page);

        // Sorting
        switch ($sort_by) {
            case 'newest':
                $query .= " ORDER BY p.created_at DESC";
                break;
            case 'rating':
                $query .= " ORDER BY p.rating DESC, p.created_at DESC";
                break;
            case 'rate_low':
                $query .= " ORDER BY p.hourly_rate ASC";
                break;
            case 'rate_high':
                $query .= " ORDER BY p.hourly_rate DESC";
                break;
            default:
                $query .= " ORDER BY p.is_featured DESC, p.is_verified DESC, p.rating DESC, p.created_at DESC";
        }

        // Add pagination
        $query .= $wpdb->prepare(" LIMIT %d OFFSET %d", $per_page, $offset);

        $professionals = $wpdb->get_results($query);

        if (!$professionals) {
            wp_send_json_error(['message' => 'No professionals found']);
            return;
        }

        // Build HTML for professional cards
        ob_start();
        foreach ($professionals as $professional) {
            // Get timezone display
            $timezone_display = 'Remote';
            if (!empty($professional->timezone)) {
                $tz = $wpdb->get_row($wpdb->prepare(
                    "SELECT name, offset FROM {$wpdb->prefix}skd_pl_timezones WHERE id = %d",
                    $professional->timezone
                ));
                if ($tz) {
                    $timezone_display = $tz->name . ' • ' . $tz->offset;
                }
            }

            // Get skills
            $pro_skills = json_decode($professional->skills ?? '[]', true);
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
            $specializations = json_decode($professional->specializations ?? '[]', true);
            $featured_skills = '';
            if (!empty($specializations) && is_array($specializations)) {
                $specs_to_show = array_slice($specializations, 0, 2);
                foreach ($specs_to_show as $spec_id) {
                    $spec = $wpdb->get_row($wpdb->prepare(
                        "SELECT name FROM {$wpdb->prefix}skd_pl_specializations WHERE id = %d",
                        $spec_id
                    ));
                    if ($spec) {
                        $featured_skills .= '<span class="skd-skill-tag-featured"><iconify-icon icon="noto:graduation-cap"></iconify-icon> ' . esc_html($spec->name) . '</span>';
                    }
                }
            }

            // Get project types for icons
            $project_types = json_decode($professional->project_types ?? '[]', true);
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

            $avatar_url = !empty($professional->avatar_url) ? $professional->avatar_url : get_avatar_url($professional->user_id, ['size' => 200]);
            $profile_url = home_url('/vda-profile/?vda_id=' . $professional->user_id);
        ?>
<div class="skd-professional-card" data-pro-id="<?php echo $professional->user_id; ?>">
    <!-- Avatar -->
    <div class="skd-pro-avatar">
        <?php if (!empty($professional->avatar_url)): ?>
        <img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($professional->display_name); ?>">
        <?php else: ?>
        <div class="skd-avatar-placeholder">
            <?php echo strtoupper(substr($professional->display_name, 0, 1)); ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Name & Title -->
    <div class="skd-pro-header">
        <h5 class="skd-pro-name">
            <a href="<?php echo esc_url($profile_url); ?>">
                <?php echo esc_html($professional->display_name); ?>
            </a>
            <?php if ($professional->is_verified): ?>
            <iconify-icon icon="mdi:verified-user" class="skd-icon-verified"></iconify-icon>
            <?php endif; ?>
        </h5>
        <p class="skd-pro-title"><?php echo esc_html($professional->tagline ?? 'Individual Designer'); ?></p>
    </div>

    <!-- Location & Rate -->
    <div class="skd-pro-meta">
        <span class="skd-pro-location">
            <iconify-icon icon="mdi:map-marker"></iconify-icon>
            <?php echo esc_html($timezone_display); ?>
        </span>
        <span class="skd-pro-rate">
            <iconify-icon icon="solar:money-bag-bold"></iconify-icon>
            <span> $<?php echo number_format($professional->hourly_rate ?? 0, 0); ?></span> /hour
        </span>
    </div>

    <!-- Description -->
    <div class="skd-pro-bio">
        <?php echo wp_trim_words($professional->bio ?? 'No description available.', 20); ?>
    </div>
    <div class="skd-pro-skills-Featureds-wrapper">
        <!-- Skills Tags -->
        <div class="skd-pro-skills">
            <?php echo $skills_html; ?>
        </div>

        <!-- Featured Skills -->
        <?php if (!empty($featured_skills)): ?>
        <div class="skd-pro-featured-skills">
            <?php echo $featured_skills; ?>
        </div>
        <?php endif; ?>
    </div>
    <!-- Project Type Icons -->
    <?php if (!empty($project_icons)): ?>
    <div class="skd-pro-project-types">
        <?php echo $project_icons; ?>
    </div>
    <?php endif; ?>

    <!-- Action Buttons -->
    <div class="skd-pro-actions">
        <a href="<?php echo esc_url($profile_url); ?>" class="skd-btn skd-btn-primary">View Profile</a>
        <a href="<?php echo esc_url(home_url('/hire-vda/?vda_id=' . $professional->user_id)); ?>"
            class="skd-btn vda-btn-outline skd-btn-secondary">Hire / Message</a>
    </div>
</div>
<?php
        }
        $html = ob_get_clean();

        // Build pagination HTML
        $pagination_html = '';
        if ($total_pages > 1) {
            $pagination_html .= '<div class="skd-pagination">';

            // Previous button
            if ($current_page > 1) {
                $pagination_html .= '<a href="#" class="skd-page-link skd-page-prev" data-page="' . ($current_page - 1) . '"><iconify-icon icon="mdi:chevron-left"></iconify-icon> Previous</a>';
            }

            $pagination_html .= '<div class="skd-page-numbers">';

            // Page numbers (show first 5)
            for ($i = 1; $i <= min($total_pages, 5); $i++) {
                $active = $i === $current_page ? 'active' : '';
                $pagination_html .= '<a href="#" class="skd-page-link ' . $active . '" data-page="' . $i . '">' . $i . '</a>';
            }

            // Show dots and last page if more than 5 pages
            if ($total_pages > 5) {
                $pagination_html .= '<span class="skd-page-dots">...</span>';
                $pagination_html .= '<a href="#" class="skd-page-link" data-page="' . $total_pages . '">' . $total_pages . '</a>';
            }

            $pagination_html .= '</div>';

            // Next button
            if ($current_page < $total_pages) {
                $pagination_html .= '<a href="#" class="skd-page-link skd-page-next" data-page="' . ($current_page + 1) . '">Next <iconify-icon icon="mdi:chevron-right"></iconify-icon></a>';
            }

            $pagination_html .= '</div>';
        }

        wp_send_json_success([
            'html' => $html,
            'count' => $total_count,
            'pagination' => $pagination_html,
            'current_page' => $current_page,
            'total_pages' => $total_pages
        ]);
    }
    /**
     * Academy Resources Shortcode
     */
    public function skd_academy_resources_shortcode($atts)
    {
        $atts = shortcode_atts([
            'default_tab' => 'academy'
        ], $atts);

        ob_start();
        include SKD_PL_PLUGIN_PATH . 'templates/shortcodes/academy-resources.php';
        return ob_get_clean();
    }

    /**
     * AJAX: Fetch Jobs
     */
    public function skd_fetch_jobs()
    {
        global $wpdb;

        $page = intval($_POST['page'] ?? 1);
        $per_page = intval($_POST['per_page'] ?? 10);
        $offset = ($page - 1) * $per_page;

        $jobs_table = $wpdb->prefix . 'skd_pl_jobs';

        // Build WHERE clause based on filters
        $where_conditions = ["status = 'active'"];
        $where_params = [];

        // Search filter
        if (!empty($_POST['search'])) {
            $where_conditions[] = "(title LIKE %s OR description LIKE %s OR company_name LIKE %s)";
            $search_term = '%' . $wpdb->esc_like($_POST['search']) . '%';
            $where_params[] = $search_term;
            $where_params[] = $search_term;
            $where_params[] = $search_term;
        }

        // Location filter
        if (!empty($_POST['location'])) {
            $where_conditions[] = "location LIKE %s";
            $where_params[] = '%' . $wpdb->esc_like($_POST['location']) . '%';
        }

        // Remote only filter
        if (!empty($_POST['remote_only'])) {
            $where_conditions[] = "is_remote = 1";
        }

        // Job types filter
        if (!empty($_POST['job_types']) && is_array($_POST['job_types'])) {
            $placeholders = implode(',', array_fill(0, count($_POST['job_types']), '%s'));
            $where_conditions[] = "job_type IN ($placeholders)";
            $where_params = array_merge($where_params, $_POST['job_types']);
        }

        // Experience levels filter
        if (!empty($_POST['experience_levels']) && is_array($_POST['experience_levels'])) {
            $placeholders = implode(',', array_fill(0, count($_POST['experience_levels']), '%s'));
            $where_conditions[] = "experience_level IN ($placeholders)";
            $where_params = array_merge($where_params, $_POST['experience_levels']);
        }

        // Date posted filter
        if (!empty($_POST['date_posted']) && $_POST['date_posted'] !== 'any') {
            $date_condition = "";
            switch ($_POST['date_posted']) {
                case '24h':
                    $date_condition = "created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
                    break;
                case '7d':
                    $date_condition = "created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
                    break;
                case '30d':
                    $date_condition = "created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
                    break;
            }
            if ($date_condition) {
                $where_conditions[] = $date_condition;
            }
        }

        $where_clause = implode(' AND ', $where_conditions);

        // Build ORDER BY clause
        $order_by = "created_at DESC";
        if (!empty($_POST['sort'])) {
            switch ($_POST['sort']) {
                case 'newest':
                    $order_by = "created_at DESC";
                    break;
                case 'oldest':
                    $order_by = "created_at ASC";
                    break;
                case 'title':
                    $order_by = "title ASC";
                    break;
                case 'budget_high':
                    $order_by = "salary_max DESC, salary_min DESC";
                    break;
                case 'budget_low':
                    $order_by = "salary_min ASC, salary_max ASC";
                    break;
            }
        }

        // Get total count
        $count_query = "SELECT COUNT(*) FROM $jobs_table WHERE $where_clause";
        $total = $wpdb->get_var($wpdb->prepare($count_query, $where_params));

        // Get jobs
        $jobs_query = "SELECT * FROM $jobs_table WHERE $where_clause ORDER BY $order_by LIMIT %d OFFSET %d";
        $query_params = array_merge($where_params, [$per_page, $offset]);
        $jobs = $wpdb->get_results($wpdb->prepare($jobs_query, $query_params));

        // Generate HTML for jobs
        ob_start();
        foreach ($jobs as $job) {
            $this->render_job_card($job);
        }
        $jobs_html = ob_get_clean();

        wp_send_json_success([
            'html' => $jobs_html,
            'total' => intval($total),
            'has_more' => ($offset + $per_page) < $total
        ]);
    }

    /**
     * Render individual job card
     */
    private function render_job_card($job)
    {
        $posted_date = human_time_diff(strtotime($job->created_at), current_time('timestamp')) . ' ago';
        $salary_display = '';

        if ($job->salary_min && $job->salary_max) {
            $salary_display = '$' . number_format($job->salary_min) . ' - $' . number_format($job->salary_max);
            if ($job->compensation_type === 'hourly') $salary_display .= '/hr';
            elseif ($job->compensation_type === 'salary') $salary_display .= '/year';
        } elseif ($job->compensation_type === 'negotiate') {
            $salary_display = 'Negotiable';
        }

        ?>
<div class="skd-job-card" data-job-id="<?php echo $job->id; ?>">
    <div class="skd-job-header">
        <div class="skd-job-title-area">
            <h3><?php echo esc_html($job->title); ?></h3>
            <div class="skd-job-company"><?php echo esc_html($job->company_name); ?></div>
        </div>
        <div class="skd-job-badges">
            <?php if ($job->is_featured): ?>
            <span class="skd-badge featured">Featured</span>
            <?php endif; ?>
            <?php if ($job->is_remote): ?>
            <span class="skd-badge remote">Remote</span>
            <?php endif; ?>
            <?php if ($job->urgency === 'high' || $job->urgency === 'asap'): ?>
            <span class="skd-badge urgent">Urgent</span>
            <?php endif; ?>
        </div>
    </div>

    <div class="skd-job-meta">
        <span class="skd-job-type"><?php echo esc_html(ucwords(str_replace('-', ' ', $job->job_type))); ?></span>
        <span class="skd-job-location"><?php echo esc_html($job->location); ?></span>
        <span
            class="skd-job-experience"><?php echo esc_html(ucwords(str_replace('-', ' ', $job->experience_level))); ?></span>
        <?php if ($salary_display): ?>
        <span class="skd-job-salary"><?php echo esc_html($salary_display); ?></span>
        <?php endif; ?>
    </div>

    <div class="skd-job-summary">
        <p><?php echo esc_html(wp_trim_words($job->summary, 25)); ?></p>
    </div>

    <div class="skd-job-footer">
        <div class="skd-job-posted">
            <span class="dashicons dashicons-clock"></span>
            Posted <?php echo $posted_date; ?>
        </div>
        <div class="skd-job-actions">
            <button class="skd-btn skd-btn-outline skd-btn-small" onclick="saveJob(<?php echo $job->id; ?>, this)">
                <span class="dashicons dashicons-heart"></span>
                Save
            </button>
            <button class="skd-btn skd-btn-primary" onclick="applyForJob(<?php echo $job->id; ?>)">
                Apply Now
            </button>
        </div>
    </div>
</div>
<?php
    }

    /**
     * AJAX: Get Job Details
     */
    public function skd_get_job_details()
    {
        global $wpdb;

        $job_id = intval($_POST['job_id'] ?? 0);
        if (!$job_id) {
            wp_send_json_error('Invalid job ID');
        }

        $jobs_table = $wpdb->prefix . 'skd_pl_jobs';
        $job = $wpdb->get_row($wpdb->prepare("SELECT * FROM $jobs_table WHERE id = %d", $job_id));

        if (!$job) {
            wp_send_json_error('Job not found');
        }

        $summary_html = sprintf(
            '<div class="skd-job-summary-modal">
                <h4>%s</h4>
                <div class="skd-job-company">%s</div>
                <div class="skd-job-location">%s</div>
                <div class="skd-job-type">%s • %s</div>
            </div>',
            esc_html($job->title),
            esc_html($job->company_name),
            esc_html($job->location),
            esc_html(ucwords(str_replace('-', ' ', $job->job_type))),
            esc_html(ucwords(str_replace('-', ' ', $job->experience_level)))
        );

        wp_send_json_success(['summary_html' => $summary_html]);
    }

    /**
     * AJAX: Submit Job Application
     */
    public function skd_submit_job_application()
    {
        global $wpdb;

        // Validate required fields
        $required_fields = ['job_id', 'name', 'email', 'cover_letter'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                wp_send_json_error("$field is required");
            }
        }

        $applications_table = $wpdb->prefix . 'skd_pl_job_applications';

        $application_data = [
            'job_id' => intval($_POST['job_id']),
            'applicant_name' => sanitize_text_field($_POST['name']),
            'applicant_email' => sanitize_email($_POST['email']),
            'applicant_phone' => sanitize_text_field($_POST['phone'] ?? ''),
            'applicant_location' => sanitize_text_field($_POST['location'] ?? ''),
            'cover_letter' => sanitize_textarea_field($_POST['cover_letter']),
            'portfolio_link' => esc_url_raw($_POST['portfolio_link'] ?? ''),
            'expected_rate' => floatval($_POST['expected_rate'] ?? 0),
            'rate_type' => sanitize_text_field($_POST['rate_type'] ?? 'hourly'),
            'availability' => sanitize_text_field($_POST['availability'] ?? ''),
            'status' => 'pending',
            'applied_at' => current_time('mysql'),
            'user_id' => get_current_user_id()
        ];

        $result = $wpdb->insert($applications_table, $application_data);

        if ($result) {
            // Send notification email to employer
            $this->send_application_notification($application_data);
            wp_send_json_success('Application submitted successfully');
        } else {
            wp_send_json_error('Failed to submit application');
        }
    }

    /**
     * AJAX: Submit Job Post
     */
    public function skd_submit_job_post()
    {
        if (!is_user_logged_in()) {
            wp_send_json_error('Please login to post a job');
        }

        global $wpdb;

        // Validate required fields
        $required_fields = ['job_title', 'job_type', 'experience_level', 'location', 'company_name', 'job_summary', 'contact_email'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                wp_send_json_error("$field is required");
            }
        }

        $jobs_table = $wpdb->prefix . 'skd_pl_jobs';

        $job_data = [
            'title' => sanitize_text_field($_POST['job_title']),
            'slug' => sanitize_title($_POST['job_title']) . '-' . time(),
            'job_type' => sanitize_text_field($_POST['job_type']),
            'experience_level' => sanitize_text_field($_POST['experience_level']),
            'location' => sanitize_text_field($_POST['location']),
            'is_remote' => !empty($_POST['is_remote']) ? 1 : 0,
            'company_name' => sanitize_text_field($_POST['company_name']),
            'summary' => sanitize_textarea_field($_POST['job_summary']),
            'description' => wp_kses_post($_POST['job_description'] ?? ''),
            'requirements' => sanitize_textarea_field($_POST['job_requirements'] ?? ''),
            'specializations' => sanitize_text_field($_POST['specializations'] ?? ''),
            'required_skills' => sanitize_text_field($_POST['skills'] ?? ''),
            'project_duration' => sanitize_text_field($_POST['project_duration'] ?? ''),
            'urgency' => sanitize_text_field($_POST['job_urgency'] ?? 'medium'),
            'compensation_type' => sanitize_text_field($_POST['compensation_type'] ?? 'negotiate'),
            'salary_min' => floatval($_POST['salary_min'] ?? 0),
            'salary_max' => floatval($_POST['salary_max'] ?? 0),
            'benefits' => sanitize_text_field($_POST['benefits'] ?? ''),
            'additional_benefits' => sanitize_textarea_field($_POST['additional_benefits'] ?? ''),
            'contact_email' => sanitize_email($_POST['contact_email']),
            'contact_phone' => sanitize_text_field($_POST['contact_phone'] ?? ''),
            'application_deadline' => !empty($_POST['application_deadline']) ? sanitize_text_field($_POST['application_deadline']) : null,
            'application_method' => sanitize_text_field($_POST['application_method'] ?? 'platform'),
            'external_application_url' => esc_url_raw($_POST['external_application_url'] ?? ''),
            'application_instructions' => sanitize_textarea_field($_POST['application_instructions'] ?? ''),
            'screening_questions' => sanitize_text_field($_POST['screening_questions'] ?? ''),
            'is_featured' => ($_POST['publishing_plan'] ?? 'free') === 'featured' ? 1 : 0,
            'user_id' => get_current_user_id(),
            'status' => 'active',
            'expires_at' => date('Y-m-d H:i:s', strtotime(($_POST['publishing_plan'] ?? 'free') === 'featured' ? '+60 days' : '+30 days')),
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        $result = $wpdb->insert($jobs_table, $job_data);

        if ($result) {
            wp_send_json_success(['job_id' => $wpdb->insert_id]);
        } else {
            wp_send_json_error('Failed to post job');
        }
    }

    /**
     * AJAX: Save Job
     */
    public function skd_save_job()
    {
        if (!is_user_logged_in()) {
            wp_send_json_error('Please login to save jobs');
        }

        global $wpdb;

        $job_id = intval($_POST['job_id'] ?? 0);
        $user_id = get_current_user_id();

        if (!$job_id) {
            wp_send_json_error('Invalid job ID');
        }

        // Check if already saved
        $saved_jobs_table = $wpdb->prefix . 'skd_pl_saved_jobs';
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $saved_jobs_table WHERE user_id = %d AND job_id = %d",
            $user_id,
            $job_id
        ));

        if ($existing) {
            // Remove from saved
            $wpdb->delete($saved_jobs_table, ['user_id' => $user_id, 'job_id' => $job_id]);
            wp_send_json_success(['action' => 'removed']);
        } else {
            // Add to saved
            $wpdb->insert($saved_jobs_table, [
                'user_id' => $user_id,
                'job_id' => $job_id,
                'saved_at' => current_time('mysql')
            ]);
            wp_send_json_success(['action' => 'saved']);
        }
    }

    /**
     * AJAX: Set Job Alerts
     */
    public function skd_set_job_alerts()
    {
        global $wpdb;

        $email = sanitize_email($_POST['email'] ?? '');
        if (!$email) {
            wp_send_json_error('Valid email is required');
        }

        $alerts_table = $wpdb->prefix . 'skd_pl_job_alerts';

        $alert_data = [
            'email' => $email,
            'keywords' => sanitize_text_field($_POST['keywords'] ?? ''),
            'job_types' => sanitize_text_field($_POST['job_type'] ?? ''),
            'location' => sanitize_text_field($_POST['location'] ?? ''),
            'frequency' => sanitize_text_field($_POST['frequency'] ?? 'daily'),
            'is_active' => 1,
            'created_at' => current_time('mysql'),
            'user_id' => get_current_user_id()
        ];

        $result = $wpdb->insert($alerts_table, $alert_data);

        if ($result) {
            wp_send_json_success('Job alerts set up successfully');
        } else {
            wp_send_json_error('Failed to set up alerts');
        }
    }

    /**
     * Send application notification email
     */
    private function send_application_notification($application_data)
    {
        global $wpdb;

        // Get job details
        $jobs_table = $wpdb->prefix . 'skd_pl_jobs';
        $job = $wpdb->get_row($wpdb->prepare("SELECT * FROM $jobs_table WHERE id = %d", $application_data['job_id']));

        if ($job) {
            $subject = sprintf('New Application for %s', $job->title);
            $message = sprintf(
                "You have received a new application for your job posting: %s\n\n" .
                    "Applicant: %s\n" .
                    "Email: %s\n" .
                    "Phone: %s\n" .
                    "Location: %s\n\n" .
                    "Cover Letter:\n%s\n\n" .
                    "Portfolio: %s\n\n" .
                    "Expected Rate: $%s/%s\n" .
                    "Availability: %s\n\n" .
                    "You can review this application in your interiAssist dashboard.",
                $job->title,
                $application_data['applicant_name'],
                $application_data['applicant_email'],
                $application_data['applicant_phone'],
                $application_data['applicant_location'],
                $application_data['cover_letter'],
                $application_data['portfolio_link'],
                $application_data['expected_rate'],
                $application_data['rate_type'],
                $application_data['availability']
            );

            wp_mail($job->contact_email, $subject, $message);
        }
    }

    /**
     * Get contact information for messaging
     */
    function skd_get_contact_info()
    {
        global $wpdb;

        if (!is_user_logged_in()) {
            wp_send_json_error('User not logged in');
            return;
        }

        $contact_id = intval($_POST['contact_id']);
        if (!$contact_id) {
            wp_send_json_error('Invalid contact ID');
            return;
        }

        // Get contact details
        $listings_table = $wpdb->prefix . 'skd_pl_listings';
        $contact = $wpdb->get_row($wpdb->prepare(
            "SELECT u.ID, u.display_name, l.listing_title, l.skd_logo, l.user_role, l.listing_slug
             FROM {$wpdb->users} u
             LEFT JOIN $listings_table l ON u.ID = l.user_id
             WHERE u.ID = %d",
            $contact_id
        ));

        if (!$contact) {
            wp_send_json_error('Contact not found');
            return;
        }

        $role_display = '';
        switch ($contact->user_role) {
            case 'vda':
                $role_display = 'Virtual Design Assistant';
                break;
            case 'studio':
                $role_display = 'Design Studio';
                break;
            case 'employer':
                $role_display = 'Employer';
                break;
            default:
                $role_display = 'Professional';
        }

        $profile_url = '';
        if ($contact->listing_slug) {
            if ($contact->user_role === 'vda') {
                $profile_url = home_url('/professional/' . $contact->listing_slug);
            } elseif ($contact->user_role === 'studio') {
                $profile_url = home_url('/studio/' . $contact->listing_slug);
            }
        }

        wp_send_json_success([
            'id' => $contact->ID,
            'name' => $contact->listing_title ?: $contact->display_name,
            'role' => $role_display,
            'avatar' => $contact->skd_logo,
            'profile_url' => $profile_url
        ]);
    }

    /**
     * Get conversation messages
     */
    function skd_get_conversation_messages()
    {
        global $wpdb;

        if (!is_user_logged_in()) {
            wp_send_json_error('User not logged in');
            return;
        }

        $current_user_id = get_current_user_id();
        $contact_id = intval($_POST['contact_id']);

        if (!$contact_id) {
            wp_send_json_error('Invalid contact ID');
            return;
        }

        $messages_table = $wpdb->prefix . 'skd_pl_messages';
        $messages = $wpdb->get_results($wpdb->prepare(
            "SELECT m.*, u.display_name as sender_name, l.skd_logo as sender_avatar
             FROM $messages_table m
             LEFT JOIN {$wpdb->users} u ON m.sender_id = u.ID
             LEFT JOIN {$wpdb->prefix}skd_pl_listings l ON m.sender_id = l.user_id
             WHERE (m.sender_id = %d AND m.recipient_id = %d) 
                OR (m.sender_id = %d AND m.recipient_id = %d)
             ORDER BY m.created_at ASC",
            $current_user_id,
            $contact_id,
            $contact_id,
            $current_user_id
        ));

        ob_start();
        if (empty($messages)) {
            echo '<div class="skd-no-messages">No messages yet. Start the conversation!</div>';
        } else {
            $current_date = '';
            foreach ($messages as $message) {
                $message_date = date('Y-m-d', strtotime($message->created_at));

                // Show date separator
                if ($current_date !== $message_date) {
                    $current_date = $message_date;
                    echo '<div class="skd-date-separator">' . date('F j, Y', strtotime($message->created_at)) . '</div>';
                }

                $is_sent = $message->sender_id == $current_user_id;
                $message_class = $is_sent ? 'sent' : 'received';
        ?>
<div class="skd-message <?php echo $message_class; ?>">
    <?php if (!$is_sent): ?>
    <div class="skd-message-avatar">
        <?php if ($message->sender_avatar): ?>
        <img src="<?php echo esc_url($message->sender_avatar); ?>" alt="<?php echo esc_attr($message->sender_name); ?>">
        <?php else: ?>
        <div class="skd-avatar-placeholder">
            <?php echo strtoupper(substr($message->sender_name, 0, 2)); ?>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="skd-message-content">
        <div class="skd-message-text"><?php echo nl2br(esc_html($message->message_text)); ?></div>
        <div class="skd-message-time">
            <?php echo date('g:i A', strtotime($message->created_at)); ?>
            <?php if ($is_sent): ?>
            <span class="skd-message-status">
                <?php echo $message->is_read ? '✓✓' : '✓'; ?>
            </span>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
            }
        }
        $html = ob_get_clean();

        wp_send_json_success(['html' => $html]);
    }

    /**
     * Send a new message
     */
    function skd_send_message()
    {
        global $wpdb;

        if (!is_user_logged_in()) {
            wp_send_json_error('User not logged in');
            return;
        }

        $current_user_id = get_current_user_id();
        $recipient_id = intval($_POST['recipient_id']);
        $message_text = sanitize_textarea_field($_POST['message_text']);

        if (!$recipient_id || !$message_text) {
            wp_send_json_error('Missing required fields');
            return;
        }

        // Validate recipient exists
        $recipient = get_user_by('id', $recipient_id);
        if (!$recipient) {
            wp_send_json_error('Invalid recipient');
            return;
        }

        $messages_table = $wpdb->prefix . 'skd_pl_messages';
        $result = $wpdb->insert(
            $messages_table,
            [
                'sender_id' => $current_user_id,
                'recipient_id' => $recipient_id,
                'message_text' => $message_text,
                'is_read' => 0,
                'created_at' => current_time('mysql')
            ],
            ['%d', '%d', '%s', '%d', '%s']
        );

        if ($result === false) {
            wp_send_json_error('Failed to send message');
            return;
        }

        // Send email notification
        $sender = wp_get_current_user();
        $subject = sprintf('New message from %s on interiAssist', $sender->display_name);

        // Get recipient's dashboard URL
        $recipient_user_type = get_user_meta($recipient_id, 'skd_user_type', true) ?: 'vda';
        $dashboard_slug = $recipient_user_type . '-dashboard';

        $message_body = sprintf(
            "You have received a new message on interiAssist:\n\n" .
                "From: %s\n" .
                "Message: %s\n\n" .
                "Reply at: %s",
            $sender->display_name,
            $message_text,
            home_url('/' . $dashboard_slug . '/#messages')
        );

        wp_mail($recipient->user_email, $subject, $message_body);

        wp_send_json_success('Message sent successfully');
    }

    /**
     * Mark messages as read
     */
    function skd_mark_messages_read()
    {
        global $wpdb;

        if (!is_user_logged_in()) {
            wp_send_json_error('User not logged in');
            return;
        }

        $current_user_id = get_current_user_id();
        $contact_id = intval($_POST['contact_id']);

        if (!$contact_id) {
            wp_send_json_error('Invalid contact ID');
            return;
        }

        $messages_table = $wpdb->prefix . 'skd_pl_messages';
        $result = $wpdb->update(
            $messages_table,
            ['is_read' => 1],
            [
                'sender_id' => $contact_id,
                'recipient_id' => $current_user_id,
                'is_read' => 0
            ],
            ['%d'],
            ['%d', '%d', '%d']
        );

        if ($result !== false) {
            wp_send_json_success('Messages marked as read');
        } else {
            wp_send_json_error('Failed to mark messages as read');
        }
    }

    /**
     * Search for contacts to start new conversation
     */
    function skd_search_contacts()
    {
        global $wpdb;

        if (!is_user_logged_in()) {
            wp_send_json_error('User not logged in');
            return;
        }

        $current_user_id = get_current_user_id();
        $search_term = sanitize_text_field($_POST['search']);

        if (strlen($search_term) < 2) {
            wp_send_json_error('Search term too short');
            return;
        }

        $listings_table = $wpdb->prefix . 'skd_pl_listings';
        $contacts = $wpdb->get_results($wpdb->prepare(
            "SELECT u.ID as id, 
                    COALESCE(l.listing_title, u.display_name) as name,
                    CASE 
                        WHEN l.user_role = 'vda' THEN 'Virtual Design Assistant'
                        WHEN l.user_role = 'studio' THEN 'Design Studio'
                        WHEN l.user_role = 'employer' THEN 'Employer'
                        ELSE 'Professional'
                    END as role,
                    l.skd_logo as avatar
             FROM {$wpdb->users} u
             LEFT JOIN $listings_table l ON u.ID = l.user_id
             WHERE u.ID != %d 
               AND (u.display_name LIKE %s OR l.listing_title LIKE %s)
               AND l.listing_status = 'publish'
             ORDER BY l.listing_title, u.display_name
             LIMIT 10",
            $current_user_id,
            '%' . $search_term . '%',
            '%' . $search_term . '%'
        ));

        wp_send_json_success($contacts);
    }

    /**
     * Start a new conversation
     */
    function skd_start_conversation()
    {
        global $wpdb;

        if (!is_user_logged_in()) {
            wp_send_json_error('User not logged in');
            return;
        }

        $current_user_id = get_current_user_id();
        $recipient_id = intval($_POST['recipient_id']);
        $subject = sanitize_text_field($_POST['subject']);
        $message_text = sanitize_textarea_field($_POST['message_text']);

        if (!$recipient_id || !$message_text) {
            wp_send_json_error('Missing required fields');
            return;
        }

        // Validate recipient exists
        $recipient = get_user_by('id', $recipient_id);
        if (!$recipient) {
            wp_send_json_error('Invalid recipient');
            return;
        }

        // Send the message
        $messages_table = $wpdb->prefix . 'skd_pl_messages';
        $full_message = $subject ? "Subject: $subject\n\n$message_text" : $message_text;

        $result = $wpdb->insert(
            $messages_table,
            [
                'sender_id' => $current_user_id,
                'recipient_id' => $recipient_id,
                'message_text' => $full_message,
                'is_read' => 0,
                'created_at' => current_time('mysql')
            ],
            ['%d', '%d', '%s', '%d', '%s']
        );

        if ($result === false) {
            wp_send_json_error('Failed to start conversation');
            return;
        }

        // Send email notification
        $sender = wp_get_current_user();
        $email_subject = sprintf('New message from %s on interiAssist', $sender->display_name);
        if ($subject) {
            $email_subject .= ': ' . $subject;
        }

        // Get recipient's dashboard URL
        $recipient_user_type = get_user_meta($recipient_id, 'skd_user_type', true) ?: 'vda';
        $dashboard_slug = $recipient_user_type . '-dashboard';

        $message_body = sprintf(
            "You have received a new message on interiAssist:\n\n" .
                "From: %s\n" .
                "Subject: %s\n\n" .
                "Message: %s\n\n" .
                "Reply at: %s",
            $sender->display_name,
            $subject,
            $message_text,
            home_url('/' . $dashboard_slug . '/#messages')
        );

        wp_mail($recipient->user_email, $email_subject, $message_body);

        wp_send_json_success('Conversation started successfully');
    }

    /**
     * Archive conversation (soft delete)
     */
    function skd_archive_conversation()
    {
        global $wpdb;

        if (!is_user_logged_in()) {
            wp_send_json_error('User not logged in');
            return;
        }

        $current_user_id = get_current_user_id();
        $contact_id = intval($_POST['contact_id']);

        if (!$contact_id) {
            wp_send_json_error('Invalid contact ID');
            return;
        }

        // For now, we'll just mark messages as archived by adding a metadata field
        // In a full implementation, you might want a separate table for archived conversations
        $messages_table = $wpdb->prefix . 'skd_pl_messages';

        // Add archived_by field to messages (requires ALTER TABLE in production)
        // For now, we'll update a flag or delete the conversation
        $result = $wpdb->query($wpdb->prepare(
            "UPDATE $messages_table 
             SET message_text = CONCAT('[ARCHIVED BY USER] ', message_text)
             WHERE (sender_id = %d AND recipient_id = %d) 
                OR (sender_id = %d AND recipient_id = %d)
               AND message_text NOT LIKE '[ARCHIVED BY USER]%%'",
            $current_user_id,
            $contact_id,
            $contact_id,
            $current_user_id
        ));

        if ($result !== false) {
            wp_send_json_success('Conversation archived');
        } else {
            wp_send_json_error('Failed to archive conversation');
        }
    }

    // === Registration & Login Shortcodes ===

    /**
     * Registration form shortcode
     */
    public function skd_registration_form_shortcode($atts)
    {
        $atts = shortcode_atts([
            'redirect' => '',
        ], $atts);

        ob_start();
        include SKD_PL_PLUGIN_PATH . 'templates/web/registration-form.php';
        return ob_get_clean();
    }

    /**
     * Login form shortcode
     */
    public function skd_login_form_shortcode($atts)
    {
        $atts = shortcode_atts([
            'redirect' => '',
        ], $atts);

        ob_start();
        include SKD_PL_PLUGIN_PATH . 'templates/web/login-form.php';
        return ob_get_clean();
    }

    /**
     * Forgot password form shortcode
     */
    public function skd_forgot_password_shortcode($atts)
    {
        ob_start();
        include SKD_PL_PLUGIN_PATH . 'templates/shortcodes/forgot-password.php';
        return ob_get_clean();
    }

    /**
     * Reset password form shortcode
     */
    public function skd_reset_password_shortcode($atts)
    {
        ob_start();
        include SKD_PL_PLUGIN_PATH . 'templates/shortcodes/reset-password.php';
        return ob_get_clean();
    }

    /**
     * Change password form shortcode
     */
    public function skd_change_password_shortcode($atts)
    {
        ob_start();
        include SKD_PL_PLUGIN_PATH . 'templates/shortcodes/change-password.php';
        return ob_get_clean();
    }
}

new SKD_Shortcodes();