<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class SKD_Plugin_Coupons
{
    private $table_name;

    public function __construct()
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'skd_pl_coupons';

        add_action('wp_ajax_generateCouponCode', [$this, 'generate_coupon_code']);
        add_action('wp_ajax_nopriv_generateCouponCode', [$this, 'generate_coupon_code']);
    }

    public function render_coupons_page()
    {
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';

        if ($action === 'add' || $action === 'edit') {
            $this->render_add_edit_coupon_page($action);
        } else {
            $this->render_coupon_listing_page();
        }
    }

    private function render_add_edit_coupon_page($action)
    {
        global $wpdb;

        $coupon_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $coupon = null;

        if ($action === 'edit' && $coupon_id) {
            $coupon = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id = %d", $coupon_id));
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handle_form_submission($action);
        }

        $price_plans_table = $wpdb->prefix . 'skd_pl_price_plans';
        $price_plans = $wpdb->get_results("SELECT id, plan_name FROM $price_plans_table WHERE plan_status = 'published'", ARRAY_A);

        include SKD_PL_PLUGIN_PATH . 'templates/admin/coupon/coupon-add-edit.php';
    }

    private function render_coupon_listing_page()
    {
        global $wpdb;

        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';
        $coupon_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if ($action === 'delete' && $coupon_id) {
            $this->delete_coupon($coupon_id);
        }

        $coupons = $this->get_all_coupons();

        include SKD_PL_PLUGIN_PATH . 'templates/admin/coupon/coupon-listing.php';
    }

    // Handle form submission for Add/Edit
    private function handle_form_submission($action)
    {
        if (!isset($_POST['coupon_nonce']) || !wp_verify_nonce($_POST['coupon_nonce'], 'save_coupon')) {
            wp_die(__('Security check failed', 'skd-property-listings'));
        }
        global $wpdb;

        // Sanitize input data
        $coupon_id = isset($_POST['coupon_id']) ? intval($_POST['coupon_id']) : 0;
        $coupon_title = sanitize_text_field($_POST['coupon_title']);
        $coupon_description = sanitize_textarea_field($_POST['coupon_description']);
        $coupon_code = sanitize_text_field($_POST['coupon_code']);
        $coupon_type = sanitize_text_field($_POST['coupon_type']);
        $coupon_amount = floatval($_POST['coupon_amount']);
        $expiry_date = !empty($_POST['expiry_date']) ? date('Y-m-d', strtotime($_POST['expiry_date'])) : null;
        $usage_limit = isset($_POST['usage_limit']) ? intval($_POST['usage_limit']) : null;
        $gst_exemption = sanitize_text_field($_POST['gst_exemption']);
        $coupon_status = sanitize_text_field($_POST['coupon_status']);

        // Handle selected price plans for 'fixed_product' discount
        $product_ids = ($coupon_type === 'fixed_product' && !empty($_POST['product_ids']))
            ? implode(',', array_map('intval', $_POST['product_ids']))
            : null;

        // Check if coupon code already exists (excluding current coupon in edit mode)
        $existing_coupon = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $this->table_name WHERE code = %s AND id != %d",
            $coupon_code,
            $coupon_id
        ));

        if ($existing_coupon) {
            wp_die(__('A coupon with this code already exists. Please use a different code.', 'skd-property-listings'));
        }

        $coupon_data = [
            'title' => $coupon_title,
            'description' => $coupon_description,
            'coupon_code' => $coupon_code,
            'discount_type' => $coupon_type,
            'discount_amount' => $coupon_amount,
            'expiry_date' => $expiry_date,
            'usage_limit' => $usage_limit,
            'gst_exemption' => $gst_exemption,
            'coupon_status' => $coupon_status,
            'product_ids' => $product_ids,
        ];

        if ($action === 'edit' && $coupon_id) {
            $wpdb->update($this->table_name, $coupon_data, ['id' => $coupon_id]);

            wp_redirect(admin_url('admin.php?page=skd-pl-coupons&action=edit&id=' . $coupon_id . '&message=success'));
        } else {
            $wpdb->insert($this->table_name, $coupon_data);

            wp_redirect(admin_url('admin.php?page=skd-pl-coupons&message=success'));
        }

        exit;
    }

    // Delete a coupon
    private function delete_coupon($coupon_id)
    {
        global $wpdb;
        $wpdb->delete($this->table_name, ['id' => $coupon_id]);
    }

    // Get all coupons
    private function get_all_coupons()
    {
        global $wpdb;
        return $wpdb->get_results("SELECT * FROM {$this->table_name}");
    }

    // Generate Unique Coupon Code via AJAX
    public function generate_coupon_code()
    {
        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_coupons';

        do {
            $coupon_code = strtoupper(wp_generate_password(10, false));
            $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE coupon_code = %s", $coupon_code));
        } while ($exists);

        wp_send_json_success(['coupon_code' => $coupon_code]);
    }
}

new SKD_Plugin_Coupons();
