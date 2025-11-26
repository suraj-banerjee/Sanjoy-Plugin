<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class SKD_Plugin_Orders
{
    private $table_name;

    public function __construct()
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'skd_pl_orders';
    }

    public function render_orders_page()
    {
        $this->render_order_list_page();
    }

    private function render_order_list_page()
    {
        include SKD_PL_PLUGIN_PATH . 'templates/admin/orders/order-list.php';
    }

    function skd_get_admin_orders($paged = 1, $search = '')
    {
        global $wpdb;
        $order_table = $wpdb->prefix . 'skd_pl_orders';
        $users_table = $wpdb->prefix . 'users';

        $limit = 20;
        $offset = ($paged - 1) * $limit;
        $where = "WHERE 1=1";

        if (!empty($search)) {
            $like = '%' . $wpdb->esc_like($search) . '%';
            $where .= $wpdb->prepare(" AND (o.id LIKE %s OR u.display_name LIKE %s OR u.user_email LIKE %s)", $like, $like, $like);
        }

        $orders = $wpdb->get_results($wpdb->prepare(
            "SELECT o.*, u.display_name, u.user_email
            FROM $order_table o
            LEFT JOIN $users_table u ON o.user_id = u.ID
            $where
            ORDER BY o.created_at DESC
            LIMIT %d OFFSET %d",
            $limit,
            $offset
        ));

        $total_orders = $wpdb->get_var("SELECT COUNT(*) FROM $order_table o LEFT JOIN $users_table u ON o.user_id = u.ID $where");
        $total_pages = ceil($total_orders / $limit);

        return [
            'orders' => $orders,
            'total_pages' => $total_pages,
            'current_page' => $paged,
        ];
    }

    // Get single plan
    function skd_get_plan_by_id($plan_id)
    {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}skd_pl_price_plans WHERE id = %d", $plan_id));
    }

    // Check Stripe subscription
    function skd_get_stripe_subscription($subscription_id)
    {
        try {
            \Stripe\Stripe::setApiKey(SKD_STRIPE_API_KEY);
            return \Stripe\Subscription::retrieve($subscription_id);
        } catch (\Exception $e) {
            return false;
        }
    }

    // Plan expiration
    function skd_calculate_expiration_date($created_date, $plan)
    {
        if ($plan->never_expire == 1) return '9999-12-31 23:59:59';
        $duration_map = [
            'days' => 1,
            'weeks' => 7,
            'months' => 30,
            'years' => 365
        ];
        $days = isset($duration_map[$plan->duration_unit]) ? $plan->listing_duration * $duration_map[$plan->duration_unit] : 365;
        return date('Y-m-d H:i:s', strtotime("+$days days", strtotime($created_date)));
    }
}

new SKD_Plugin_Orders();
