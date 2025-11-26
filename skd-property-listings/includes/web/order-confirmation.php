<?php
require_once SKD_PL_PLUGIN_PATH . 'includes/web/class-skd-shortcodes.php';

global $wpdb;
if (!class_exists('\Stripe\Stripe')) {
    require_once SKD_PL_PLUGIN_PATH . 'vendor/autoload.php';
}

\Stripe\Stripe::setApiKey(SKD_STRIPE_API_KEY);

$session_id = isset($_GET['session_id']) ? sanitize_text_field($_GET['session_id']) : '';

if (!$session_id) {
    echo "<h2>Invalid Payment Session</h2>";
    exit;
}

try {
    $session = \Stripe\Checkout\Session::retrieve($session_id);

    if ($session->payment_status === 'paid') {
        $user_id = $session->metadata->user_id;
        $plan_id = $session->metadata->plan_id;
        $coupon_id = $session->metadata->coupon_id;
        $final_discount = $session->metadata->final_discount;
        $is_subscription = $session->metadata->is_subscription;
        $amount = $session->amount_total / 100;

        $subscription_id = isset($session->subscription) ? $session->subscription : null;

        // Check if the order already exists
        // $existing_order = $wpdb->get_var($wpdb->prepare(
        //     "SELECT id FROM {$wpdb->prefix}skd_pl_orders WHERE payment_transaction_id = %s",
        //     $session->id
        // ));

        // if (!$existing_order) {
        // Create the order
        $order_id = SKD_Shortcodes::skd_create_order(
            $plan_id,
            $amount,
            'completed',
            $final_discount,
            $coupon_id,
            'stripe',
            'completed',
            $session->id,
            json_encode($session),
            $is_subscription,
            $subscription_id
        );

        // echo "<h2>Payment Successful!</h2>";
        // echo "<p>Your order ID: #" . esc_html($order_id) . "</p>";
        //redirect to the order confirmation page
        wp_redirect(site_url('dashboard'));
        exit;
        // } else {
        //     echo "<h2>Order Already Processed</h2>";
        // }
    } else {
        echo "<h2>Payment Failed or Pending</h2>";
    }
} catch (\Exception $e) {
    echo "<h2>Error: " . esc_html($e->getMessage()) . "</h2>";
}
