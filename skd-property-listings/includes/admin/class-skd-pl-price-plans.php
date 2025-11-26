<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

require_once SKD_PL_PLUGIN_PATH . 'includes/common/skd-utils.php';

class SKD_Plugin_Price_Plans
{
    private $table_name;

    public function __construct()
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'skd_pl_price_plans';
    }

    public function render_price_plans_page()
    {
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';

        if ($action === 'add' || $action === 'edit') {
            $this->render_add_price_plan_page(); // Load Add Price Plan Page
        } else {
            $this->render_price_plan_list_page(); // Show Price Plan List
        }
    }

    // Render the Price Plan Listing page
    public function render_price_plan_list_page()
    {
        global $wpdb;

        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';
        $encrypted_id = isset($_GET['id']) ? $_GET['id'] : 0;
        if ($encrypted_id) {
            $plan_id = skd_decrypt_id($encrypted_id);
        } else {
            $plan_id = 0;
        }

        // Handle delete action
        if ($action === 'delete' && $plan_id) {
            $this->delete_price_plan($plan_id);
        }

        // Get all price plans for listing
        $price_plans = $this->get_all_price_plans();

        // Pass the data to the template
        include SKD_PL_PLUGIN_PATH . 'templates/admin/price-plan/price-plans-listing.php';
    }

    public function render_add_price_plan_page()
    {
        global $wpdb;

        $planDetails = null;
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';

        $encrypted_id = isset($_GET['id']) ? $_GET['id'] : 0;
        if ($encrypted_id) {
            $plan_id = skd_decrypt_id($encrypted_id);
        } else {
            $plan_id = 0;
        }

        if ($action === 'edit' && !$plan_id) {
            wp_die(__('Invalid ID', 'skd-property-listings'));
        }

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handle_form_submission($action);
        }

        if ($action === 'edit' && $plan_id) {
            $planDetails = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id = %d", $plan_id));
        }

        include SKD_PL_PLUGIN_PATH . 'templates/admin/price-plan/add-edit-price-plan.php';
    }

    // Handle form submission for Add/Edit
    private function handle_form_submission($action)
    {
        // echo '<pre>';
        // print_r($_POST);
        // echo '</pre>';
        // exit;
        global $wpdb;

        // Sanitize input data
        $plan_id = isset($_POST['plan_id']) ? intval($_POST['plan_id']) : 0;
        $plan_name = sanitize_text_field($_POST['plan_name']);


        // Generate slug if not provided
        $slug = sanitize_title($plan_name);

        $formdata = [
            'plan_name' => $plan_name,
            'slug' => $slug,
            'plan_description' => sanitize_textarea_field($_POST['plan_description']),
            'plan_type' => sanitize_text_field($_POST['plan_type']),
            'price' => isset($_POST['price']) ? floatval($_POST['price']) : 0,
            'add_gst_rate' => isset($_POST['add_gst_rate']) ? 1 : 0,
            'gst_rate' => isset($_POST['gst_rate']) ? floatval($_POST['gst_rate']) : 0,
            'gst_type' => isset($_POST['gst_type']) ? sanitize_text_field($_POST['gst_type']) : '',
            'is_free' => isset($_POST['is_free']) ? 1 : 0,
            'listing_duration' => isset($_POST['listing_duration']) ? intval($_POST['listing_duration']) : 0,
            'duration_unit' => isset($_POST['duration_unit']) ? sanitize_text_field($_POST['duration_unit']) : 'days',
            'never_expire' => isset($_POST['never_expire']) ? 1 : 0,
            'featured_the_list' => isset($_POST['featured_the_list']) ? 'yes' : 'no',
            'featured_the_list_hide' => isset($_POST['featured_the_list_hide']) ? intval($_POST['featured_the_list_hide']) : 0,
            'enable_subscription' => isset($_POST['enable_subscription']) ? 'yes' : 'no',
            'enable_subscription_hide' => isset($_POST['enable_subscription_hide']) ? intval($_POST['enable_subscription_hide']) : 0,
            'no_of_listing' => isset($_POST['no_of_listing']) ? intval($_POST['no_of_listing']) : 0,
            'mark_as_unlimited' => isset($_POST['mark_as_unlimited']) ? 1 : 0,
            'no_of_feature_listing' => isset($_POST['no_of_feature_listing']) ? intval($_POST['no_of_feature_listing']) : 0,
            'mark_feature_unlimited' => isset($_POST['mark_feature_unlimited']) ? 1 : 0,
            'contact_owner' => isset($_POST['contact_owner']) ? 'yes' : 'no',
            'contact_owner_hide' => isset($_POST['contact_owner_hide']) ? 1 : 0,
            'customer_review' => isset($_POST['customer_review']) ? 'yes' : 'no',
            'customer_review_hide' => isset($_POST['customer_review_hide']) ? 1 : 0,
            'mark_as_sold' => isset($_POST['mark_as_sold']) ? 'yes' : 'no',
            'mark_as_sold_hide' => isset($_POST['mark_as_sold_hide']) ? 1 : 0,
            'recomend_plan' => isset($_POST['recomend_plan']) ? 'yes' : 'no',
            'hide_from_plan' => isset($_POST['hide_from_plan']) ? 'yes' : 'no',
            'listing_sorting_order' => isset($_POST['listing_sorting_order']) ? intval($_POST['listing_sorting_order']) : 0,
            'business_name' => isset($_POST['business_name']) ? 'yes' : 'no',
            'business_name_hide' => isset($_POST['business_name_hide']) ? 1 : 0,
            'pricing_fld' => isset($_POST['pricing_fld']) ? 'yes' : 'no',
            'pricing_fld_hide' => isset($_POST['pricing_fld_hide']) ? 1 : 0,
            'location_fld' => isset($_POST['location_fld']) ? 'yes' : 'no',
            'location_fld_hide' => isset($_POST['location_fld_hide']) ? 1 : 0,
            'location_fld_limit' => isset($_POST['location_fld_limit']) ? intval($_POST['location_fld_limit']) : 0,
            'location_fld_limit_unlimited' => isset($_POST['location_fld_limit_unlimited']) ? 1 : 0,
            'tag_fld' => isset($_POST['tag_fld']) ? 'yes' : 'no',
            'tag_fld_hide' => isset($_POST['tag_fld_hide']) ? 1 : 0,
            'tag_fld_limit' => isset($_POST['tag_fld_limit']) ? intval($_POST['tag_fld_limit']) : 0,
            'tag_fld_limit_unlimited' => isset($_POST['tag_fld_limit_unlimited']) ? 1 : 0,
            'category_fld' => isset($_POST['category_fld']) ? 'yes' : 'no',
            'category_fld_hide' => isset($_POST['category_fld_hide']) ? 1 : 0,
            'category_fld_limit' => isset($_POST['category_fld_limit']) ? intval($_POST['category_fld_limit']) : 0,
            'category_fld_limit_unlimited' => isset($_POST['category_fld_limit_unlimited']) ? 1 : 0,
            'phone_fld' => isset($_POST['phone_fld']) ? 'yes' : 'no',
            'phone_fld_hide' => isset($_POST['phone_fld_hide']) ? 1 : 0,
            'phone_2_fld' => isset($_POST['phone_2_fld']) ? 'yes' : 'no',
            'phone_2_fld_hide' => isset($_POST['phone_2_fld_hide']) ? 1 : 0,
            'email_fld' => isset($_POST['email_fld']) ? 'yes' : 'no',
            'email_fld_hide' => isset($_POST['email_fld_hide']) ? 1 : 0,
            'website_fld' => isset($_POST['website_fld']) ? 'yes' : 'no',
            'website_fld_hide' => isset($_POST['website_fld_hide']) ? 1 : 0,
            'map_fld' => isset($_POST['map_fld']) ? 'yes' : 'no',
            'map_fld_hide' => isset($_POST['map_fld_hide']) ? 1 : 0,
            'hide_owner_form_listing' => isset($_POST['hide_owner_form_listing']) ? 'yes' : 'no',
            'cntct_owner_price_pln_hide' => isset($_POST['cntct_owner_price_pln_hide']) ? 1 : 0,
            'tagline_fld' => isset($_POST['tagline_fld']) ? 'yes' : 'no',
            'tagline_fld_hide' => isset($_POST['tagline_fld_hide']) ? 1 : 0,
            'address_fld' => isset($_POST['address_fld']) ? 'yes' : 'no',
            'address_fld_hide' => isset($_POST['address_fld_hide']) ? 1 : 0,
            'social_info' => isset($_POST['social_info']) ? 'yes' : 'no',
            'social_info_hide' => isset($_POST['social_info_hide']) ? 1 : 0,
            'zip_post_code' => isset($_POST['zip_post_code']) ? 'yes' : 'no',
            'zip_post_code_hide' => isset($_POST['zip_post_code_hide']) ? 1 : 0,
            'description_fld' => isset($_POST['description_fld']) ? 'yes' : 'no',
            'description_fld_hide' => isset($_POST['description_fld_hide']) ? 1 : 0,
            'description_fld_limit' => isset($_POST['description_fld_limit']) ? intval($_POST['description_fld_limit']) : 0,
            'description_fld_limit_unlimited' => isset($_POST['description_fld_limit_unlimited']) ? 1 : 0,
            'video_fld' => isset($_POST['video_fld']) ? 'yes' : 'no',
            'video_fld_hide' => isset($_POST['video_fld_hide']) ? 1 : 0,
            'images_fld' => isset($_POST['images_fld']) ? 'yes' : 'no',
            'images_fld_hide' => isset($_POST['images_fld_hide']) ? 1 : 0,
            'images_fld_limit' => isset($_POST['images_fld_limit']) ? intval($_POST['images_fld_limit']) : 0,
            'images_fld_limit_unlimited' => isset($_POST['images_fld_limit_unlimited']) ? 1 : 0,
            'online_business' => isset($_POST['online_business']) ? 'yes' : 'no',
            'online_business_hide' => isset($_POST['online_business_hide']) ? 1 : 0,
            'upload_logo_images' => isset($_POST['upload_logo_images']) ? 'yes' : 'no',
            'upload_logo_images_hide' => isset($_POST['upload_logo_images_hide']) ? 1 : 0,
            'view_count' => isset($_POST['view_count']) ? 'yes' : 'no',
            'view_count_hide' => isset($_POST['view_count_hide']) ? 1 : 0,
            'contact_details' => isset($_POST['contact_details']) ? 'yes' : 'no',
            'contact_details_hide' => isset($_POST['contact_details_hide']) ? 1 : 0,
            'plan_status' => sanitize_text_field($_POST['plan_status']),
            'created_date' => isset($_POST['created_date']) ? sanitize_text_field($_POST['created_date']) : current_time('mysql')
        ];

        // Check for duplicate entries
        $existing_plan = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $this->table_name WHERE slug = %s AND id != %d",
            $slug,
            $plan_id
        ));

        if ($existing_plan) {
            wp_redirect(add_query_arg(['message' => 'duplicate'], admin_url('admin.php?page=skd-pl-price-plans')));
            exit;
        }

        if ($action === 'edit' && $plan_id) {
            $wpdb->update(
                $this->table_name,
                $formdata,
                ['id' => $plan_id]
            );

            $encrypted_id = skd_encrypt_id($plan_id);

            wp_redirect(add_query_arg(['message' => 'success'], admin_url('admin.php?page=skd-pl-price-plans&action=edit&id=' . $encrypted_id)));
        } else {
            $wpdb->insert(
                $this->table_name,
                $formdata
            );

            wp_redirect(add_query_arg(['message' => 'success'], admin_url('admin.php?page=skd-pl-price-plans')));
        }

        // if ($wpdb->last_error) {
        //     echo $wpdb->last_error;
        //     exit;
        // }

        exit;
    }

    // Delete a price plan
    private function delete_price_plan($plan_id)
    {
        global $wpdb;
        $plan_id = skd_decrypt_id($plan_id);
        if ($plan_id < 6) {
            wp_redirect(add_query_arg(['message' => 'error'], admin_url('admin.php?page=skd-pl-price-plans')));
            exit;
        }
        $wpdb->delete($this->table_name, ['id' => $plan_id]);
        wp_redirect(add_query_arg(['message' => 'deleted'], admin_url('admin.php?page=skd-pl-price-plans')));
        exit;
    }

    // Get all price plans
    private function get_all_price_plans()
    {
        global $wpdb;
        return $wpdb->get_results("SELECT * FROM $this->table_name ORDER BY id ASC");
    }
}
