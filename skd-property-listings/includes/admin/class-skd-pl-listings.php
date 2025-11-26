<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class SKD_Plugin_Listings
{
    private $table_name;

    public function __construct()
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'skd_pl_listings';

        add_action('wp_ajax_generateListingCode', [$this, 'generate_listing_code']);
        add_action('wp_ajax_nopriv_generateListingCode', [$this, 'generate_listing_code']);
        add_action('wp_ajax_skd_add_new_category', [$this, 'skd_add_new_category']);
        add_action('wp_ajax_skd_add_new_location', [$this, 'skd_add_new_location']);
        add_action('wp_ajax_skd_search_tags', [$this, 'skd_search_tags']);
        add_action('wp_ajax_nopriv_skd_search_tags', [$this, 'skd_search_tags']);
        add_action('wp_ajax_skd_search_features', [$this, 'skd_search_features']);
        add_action('wp_ajax_nopriv_skd_search_features', [$this, 'skd_search_features']);
    }

    public function render_listings_page()
    {
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';

        if ($action === 'add' || $action === 'edit') {
            $this->render_add_edit_listing_page($action);
        } else {
            $this->render_listing_listing_page();
        }
    }

    public function render_add_edit_listing_page($action)
    {
        global $wpdb;
        $current_user_id = get_current_user_id();

        $listing_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $listing = null;

        if ($action === 'edit' && $listing_id) {
            $listing = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id = %d", $listing_id));
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handle_form_submission($action);
        }

        $price_plans_table = $wpdb->prefix . 'skd_pl_price_plans';
        $pricePlanList = $wpdb->get_results("SELECT * FROM $price_plans_table WHERE plan_status = 'published'", ARRAY_A);

        $plan_usage = [];
        foreach ($pricePlanList as $plan) {
            $plan_id = $plan['id'];

            // // Count how many listings this user has added under this plan
            // $used_regular = $wpdb->get_var($wpdb->prepare(
            //     "SELECT COUNT(*) FROM skd_property_listings WHERE user_id = %d AND plan_id = %d AND listing_type = 'regular'",
            //     $current_user_id,
            //     $plan_id
            // ));

            // $used_featured = $wpdb->get_var($wpdb->prepare(
            //     "SELECT COUNT(*) FROM skd_property_listings WHERE user_id = %d AND plan_id = %d AND listing_type = 'featured'",
            //     $current_user_id,
            //     $plan_id
            // ));

            // $plan_usage[$plan_id] = [
            //     'used_regular' => $used_regular ?: 0,
            //     'used_featured' => $used_featured ?: 0
            // ];

            $plan_usage[$plan_id] = [
                'used_regular' => 2,
                'used_featured' => 1
            ];
        }

        //need to show a dropdown of active users in the listing form like username(in bracket name if there)
        $users = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}users ORDER BY user_login ASC", ARRAY_A);
        $user_list = [];
        foreach ($users as $user) {
            $user_list[$user['ID']] = $user['user_login'] . ' (' . $user['display_name'] . ')';
        }

        include SKD_PL_PLUGIN_PATH . 'templates/admin/listing/listing-add-edit.php';
    }

    private function render_listing_listing_page()
    {
        global $wpdb;

        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';
        $listing_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if ($action === 'delete' && $listing_id) {
            $this->delete_listing($listing_id);
        }

        $listings = $wpdb->get_results("
            SELECT l.*, 
                p.plan_name, p.plan_type, 
                u.display_name as author_name 
            FROM {$this->table_name} l
            LEFT JOIN {$wpdb->prefix}users u ON l.user_id = u.ID
            LEFT JOIN {$wpdb->prefix}skd_pl_price_plans p ON l.plan_id = p.id
        ");

        include SKD_PL_PLUGIN_PATH . 'templates/admin/listing/listing-listing.php';
    }

    private function get_categories($category_ids_json)
    {
        global $wpdb;
        $category_ids = json_decode($category_ids_json, true);

        if (empty($category_ids)) {
            return __('No Categories', 'skd-property-listings');
        }

        $placeholders = implode(',', array_fill(0, count($category_ids), '%d'));
        $query = $wpdb->prepare("SELECT name FROM {$wpdb->prefix}skd_pl_categories WHERE id IN ($placeholders)", ...$category_ids);
        $categories = $wpdb->get_col($query);

        return !empty($categories) ? implode(', ', $categories) : __('No Categories', 'skd-property-listings');
    }

    private function get_locations($location_ids_json)
    {
        global $wpdb;
        $location_ids = json_decode($location_ids_json, true);

        if (empty($location_ids)) {
            return __('No Locations', 'skd-property-listings');
        }

        $placeholders = implode(',', array_fill(0, count($location_ids), '%d'));
        $query = $wpdb->prepare("SELECT name FROM {$wpdb->prefix}skd_pl_locations WHERE id IN ($placeholders)", ...$location_ids);
        $locations = $wpdb->get_col($query);

        return !empty($locations) ? implode(', ', $locations) : __('No Locations', 'skd-property-listings');
    }

    private function handle_form_submission($action)
    {
        global $wpdb;
        $listing_id = isset($_POST['listing_id']) ? intval($_POST['listing_id']) : 0;
        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : get_current_user_id();
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

        $skd_gallery = [];
        if (!empty($_POST['skd_gallery'])) {
            $skd_gallery = explode(',', $_POST['skd_gallery']);
        }
        $skd_gallery_json = json_encode($skd_gallery);

        $skd_category = [];
        if (isset($_POST['skd_category']) && !empty($_POST['skd_category'])) {
            $skd_category = $_POST['skd_category'];
        }
        $skd_category_json = json_encode($skd_category);

        $skd_location = [];
        if (isset($_POST['skd_location']) && !empty($_POST['skd_location'])) {
            $skd_location = $_POST['skd_location'];
        }
        $skd_location_json = json_encode($skd_location);

        $skd_features = [];
        if (isset($_POST['skd_features']) && !empty($_POST['skd_features'])) {
            $skd_features = explode(',', $_POST['skd_features']);
            $features_table = $wpdb->prefix . 'skd_pl_features';
            foreach ($skd_features as $feature) {
                $existing_feature = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $features_table WHERE name = %s", $feature));
                if (!$existing_feature) {
                    $wpdb->insert($features_table, ['name' => $feature, 'slug' => sanitize_title($feature)]);
                }
            }
        }
        $skd_features_json = json_encode($skd_features);

        $skd_tags = [];
        if (isset($_POST['skd_tags']) && !empty($_POST['skd_tags'])) {
            $skd_tags = explode(',', $_POST['skd_tags']);
            $tags_table = $wpdb->prefix . 'skd_pl_tags';
            foreach ($skd_tags as $tag) {
                $existing_tag = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $tags_table WHERE name = %s", $tag));
                if (!$existing_tag) {
                    $wpdb->insert($tags_table, ['name' => $tag, 'slug' => sanitize_title($tag)]);
                }
            }
        }
        $skd_tags_json = json_encode($skd_tags);

        // Check for duplicate slug
        $existing_listing = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE slug = %s AND id != %d",
            $slug,
            $listing_id
        ));

        if ($existing_listing) {
            wp_redirect(add_query_arg(['message' => 'duplicate'], admin_url('admin.php?page=skd-pl-all-listings')));
            exit;
        }

        //need to check plan_type from skd_pl_price_plans table
        $plan_id = intval($_POST['plan_id']);
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
            'plan_id' => intval($_POST['plan_id']),
            'order_id' => isset($_POST['order_id']) ? intval($_POST['order_id']) : 0,
            'listing_type' => $listing_type,
            'is_feature' => $is_feature,
            'expiration_date' => sanitize_text_field($_POST['expiration_date']),
            'listing_title' => $listing_title,
            'slug' => $slug,
            'listing_description' => sanitize_textarea_field($_POST['listing_description']),
            'tagline' => sanitize_text_field($_POST['tagline']),
            'price' => floatval($_POST['price']),
            'view_count' => isset($_POST['view_count']) ? intval($_POST['view_count']) : 0,
            'contact_details' => sanitize_textarea_field($_POST['contact_details']),
            'contact_phone' => sanitize_text_field($_POST['contact_phone']),
            'contact_phone2' => sanitize_text_field($_POST['contact_phone2']),
            'contact_email' => sanitize_email($_POST['contact_email']),
            'contact_zip' => sanitize_text_field($_POST['contact_zip']),
            'contact_website' => esc_url_raw($_POST['contact_website']),
            'hide_owner_form' => isset($_POST['hide_owner_form']) ? 1 : 0,
            'social_info' => $social_info_json,
            'list_address' => sanitize_text_field($_POST['list_address']),
            'latitude' => floatval($_POST['latitude']),
            'longitude' => floatval($_POST['longitude']),
            'is_online_only' => isset($_POST['is_online_only']) ? 1 : 0,
            'skd_logo' => esc_url_raw($_POST['skd_logo']),
            'skd_gallery' => $skd_gallery_json,
            'video' => esc_url_raw($_POST['video']),
            'privacy_policy' => isset($_POST['privacy_policy']) ? 1 : 0,
            'listing_status' => sanitize_text_field($_POST['listing_status']),
            'skd_header_image' => esc_url_raw($_POST['skd_header_image']),
            'category_ids' => $skd_category_json,
            'location_ids' => $skd_location_json,
            'features' => $skd_features_json,
            'tags' => $skd_tags_json,
        ];

        if ($action === 'edit' && $listing_id) {
            $wpdb->update($this->table_name, $formData, ['id' => $listing_id]);

            wp_redirect(add_query_arg(['message' => 'success'], admin_url('admin.php?page=skd-pl-all-listings&action=edit&id=' . $listing_id)));
        } else {
            $wpdb->insert($this->table_name, $formData);

            wp_redirect(add_query_arg(['message' => 'success'], admin_url('admin.php?page=skd-pl-all-listings')));
        }
        exit;
    }

    private function delete_listing($listing_id)
    {
        global $wpdb;

        $wpdb->delete($this->table_name, ['id' => $listing_id]);

        wp_redirect(admin_url('admin.php?page=skd-pl-all-listings'));
        exit;
    }

    public function get_all_listings()
    {
        global $wpdb;

        return $wpdb->get_results("SELECT * FROM {$this->table_name}");
    }

    function skd_add_new_category()
    {
        global $wpdb;

        // Security check
        check_ajax_referer('skd_category_nonce', 'security');

        $category_table = $wpdb->prefix . 'skd_pl_categories';
        $category_name = sanitize_text_field($_POST['category_name']);
        $parent_id = intval($_POST['parent_id']);

        if (empty($category_name)) {
            wp_send_json_error(['message' => __('Category name cannot be empty.', 'skd-property-listings')]);
        }

        // Check if category exists
        $existing = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $category_table WHERE name = %s", $category_name));
        if ($existing) {
            wp_send_json_error(['message' => __('Category already exists.', 'skd-property-listings')]);
        }

        // Insert category
        $wpdb->insert($category_table, [
            'name' => $category_name,
            'slug' => sanitize_title($category_name),
            'parent_id' => $parent_id,
        ]);
        $new_category_id = $wpdb->insert_id;

        // Fetch updated category list and dropdown
        $updated_list = $this->render_category_hierarchy();
        $updated_dropdown = $this->get_category_dropdown_options();

        wp_send_json_success([
            'new_category_id' => $new_category_id,
            'updated_list' => $updated_list,
            'updated_dropdown' => $updated_dropdown
        ]);
    }

    public function render_category_hierarchy($parent_id = 0, $level = 0, $selected_categories = [])
    {
        global $wpdb;
        $category_table = $wpdb->prefix . 'skd_pl_categories';
        $categories = $wpdb->get_results($wpdb->prepare("SELECT * FROM $category_table WHERE parent_id = %d ORDER BY name", $parent_id));

        if (!$categories) {
            return '';
        }

        $output = '<ul style="margin-left:' . ($level * 20) . 'px;">';
        foreach ($categories as $category) {
            $checked = in_array($category->id, $selected_categories) ? 'checked' : '';
            $output .= '<li><input type="checkbox" name="skd_category[]" value="' . esc_attr($category->id) . '" ' . $checked . '> ' . esc_html($category->name);
            $output .= $this->render_category_hierarchy($category->id, $level + 1, $selected_categories);
            $output .= '</li>';
        }
        $output .= '</ul>';

        return $output;
    }


    public function get_category_dropdown_options($parent_id = 0, $level = 0)
    {
        global $wpdb;
        $category_table = $wpdb->prefix . 'skd_pl_categories';
        $categories = $wpdb->get_results($wpdb->prepare("SELECT * FROM $category_table WHERE parent_id = %d ORDER BY name", $parent_id));

        if (!$categories) {
            return '';
        }

        $output = '';
        foreach ($categories as $category) {
            $output .= '<option value="' . esc_attr($category->id) . '">' . str_repeat('- ', $level) . esc_html($category->name) . '</option>';
            $output .= $this->get_category_dropdown_options($category->id, $level + 1);
        }

        return $output;
    }

    // location
    function skd_add_new_location()
    {
        global $wpdb;

        // Security check
        check_ajax_referer('skd_location_nonce', 'security');

        $location_table = $wpdb->prefix . 'skd_pl_locations';
        $location_name = sanitize_text_field($_POST['location_name']);
        $parent_id = intval($_POST['parent_id']);

        if (empty($location_name)) {
            wp_send_json_error(['message' => __('Location name cannot be empty.', 'skd-property-listings')]);
        }

        // Check if location exists
        $existing = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $location_table WHERE name = %s", $location_name));
        if ($existing) {
            wp_send_json_error(['message' => __('Location already exists.', 'skd-property-listings')]);
        }

        // Insert location
        $wpdb->insert($location_table, [
            'name' => $location_name,
            'slug' => sanitize_title($location_name),
            'parent_id' => $parent_id,
        ]);
        $new_location_id = $wpdb->insert_id;

        // Fetch updated location list and dropdown
        $updated_list = $this->render_location_hierarchy();
        $updated_dropdown = $this->get_location_dropdown_options();

        wp_send_json_success([
            'new_location_id' => $new_location_id,
            'updated_list' => $updated_list,
            'updated_dropdown' => $updated_dropdown
        ]);
    }

    public function render_location_hierarchy($parent_id = 0, $level = 0, $selected_locations = [])
    {
        global $wpdb;
        $location_table = $wpdb->prefix . 'skd_pl_locations';
        $locations = $wpdb->get_results($wpdb->prepare("SELECT * FROM $location_table WHERE parent_id = %d ORDER BY name", $parent_id));

        if (!$locations) {
            return '';
        }

        $output = '<ul style="margin-left:' . ($level * 20) . 'px;">';
        foreach ($locations as $location) {
            $checked = in_array($location->id, $selected_locations) ? 'checked' : '';
            $output .= '<li><input type="checkbox" name="skd_location[]" value="' . esc_attr($location->id) . '" ' . $checked . '> ' . esc_html($location->name);
            $output .= $this->render_location_hierarchy($location->id, $level + 1, $selected_locations);
            $output .= '</li>';
        }
        $output .= '</ul>';

        return $output;
    }

    public function get_location_dropdown_options($parent_id = 0, $level = 0)
    {
        global $wpdb;
        $location_table = $wpdb->prefix . 'skd_pl_locations';
        $locations = $wpdb->get_results($wpdb->prepare("SELECT * FROM $location_table WHERE parent_id = %d ORDER BY name", $parent_id));

        if (!$locations) {
            return '';
        }

        $output = '';
        foreach ($locations as $location) {
            $output .= '<option value="' . esc_attr($location->id) . '">' . str_repeat('- ', $level) . esc_html($location->name) . '</option>';
            $output .= $this->get_location_dropdown_options($location->id, $level + 1);
        }

        return $output;
    }

    // tags
    function skd_search_tags()
    {
        global $wpdb;

        check_ajax_referer('skd_tag_nonce', 'security');

        $term = sanitize_text_field($_POST['term']);
        $tags_table = $wpdb->prefix . 'skd_pl_tags';

        $tags = $wpdb->get_col($wpdb->prepare("SELECT name FROM $tags_table WHERE name LIKE %s LIMIT 10", '%' . $term . '%'));

        wp_send_json($tags);
    }
    // feature
    function skd_search_features()
    {
        global $wpdb;

        check_ajax_referer('skd_feature_nonce', 'security');

        $term = sanitize_text_field($_POST['term']);
        $features_table = $wpdb->prefix . 'skd_pl_features';

        $features = $wpdb->get_col($wpdb->prepare("SELECT name FROM $features_table WHERE name LIKE %s LIMIT 10", '%' . $term . '%'));

        wp_send_json($features);
    }
}
new SKD_Plugin_Listings();
