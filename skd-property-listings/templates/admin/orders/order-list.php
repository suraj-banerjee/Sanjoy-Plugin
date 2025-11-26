<?php
$search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
$paged = isset($_GET['paged']) ? intval($_GET['paged']) : 1;

$data = $this->skd_get_admin_orders($paged, $search);
$orders = $data['orders'];
$total_pages = $data['total_pages'];
$current_page = $data['current_page'];
?>

<div class="wrap">
    <h1 class="wp-heading-inline">Orders</h1>
    <form method="get">
        <input type="hidden" name="page" value="skd-pl-order-history">
        <input type="text" name="s" value="<?php echo esc_attr($search); ?>" placeholder="Search Orders">
        <input type="submit" class="button" value="Search">
    </form>

    <table class="widefat striped">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Amount</th>
                <th>Payment Type</th>
                <th>Payment Details</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Status</th>
                <th>Plan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $order): ?>
                    <?php
                    $plan = $this->skd_get_plan_by_id($order->plan_id);
                    $is_active = 'Inactive';

                    if ($order->is_subscription === 'true' && $order->subscription_id) {
                        $subscription = $this->skd_get_stripe_subscription($order->subscription_id);
                        $is_active = ($subscription && $subscription->status === 'active') ? '<span style="color: green;">Active</span>' : '<span style="color: red;">Inactive</span>';
                    } else {
                        if ($plan->plan_type == 'pay_per_listing') {
                            global $wpdb;
                            $listing_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}skd_pl_listings WHERE order_id = %d", $order->id));

                            $is_active = $listing_count >= 1 ? '<span style="color: red;">Inactive</span>' : '<span style="color: green;">Active</span>';
                        } else {
                            $expiry = $this->skd_calculate_expiration_date($order->created_at, $plan);
                            $is_active = strtotime($expiry) > time() ? '<span style="color: green;">Active</span>' : '<span style="color: red;">Inactive</span>';
                        }
                    }

                    $payment_type = $order->payment_method === 'free' ? 'Free Submission' : ucfirst($order->payment_method) . ' Payment';
                    $formatted_date = date('d/m/Y \a\t g:i a', strtotime($order->created_at));
                    ?>
                    <tr>
                        <td>Order #<?php echo esc_html($order->id); ?></td>
                        <td>$<?php echo number_format($order->final_price, 2); ?></td>
                        <td><?php echo esc_html($payment_type); ?></td>
                        <td>
                            <?php if ($order->payment_method === 'free'): ?>
                                N/A
                            <?php else: ?>
                                <?php if ($order->is_subscription === 'true' && $order->subscription_id): ?>
                                    <b>Subscription ID : </b> <?php echo esc_html($order->subscription_id); ?><br>
                                <?php else: ?>
                                    <b>Transaction ID : </b> <?php echo esc_html($order->payment_transaction_id); ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html($order->display_name); ?><br><?php echo esc_html($order->user_email); ?></td>
                        <td>Published<br><?php echo esc_html($formatted_date); ?></td>
                        <td><?php echo ucfirst($order->order_status); ?></td>
                        <td><?php echo strtoupper($plan->plan_name); ?> - <strong><?php echo $is_active; ?></strong></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">No orders found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if ($total_pages > 1): ?>
        <div class="tablenav">
            <div class="tablenav-pages">
                <?php if ($current_page > 1): ?>
                    <a class="prev page-numbers" href="?page=skd-pl-order-history&paged=<?php echo $current_page - 1; ?>&s=<?php echo urlencode($search); ?>">« Previous</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a class="page-numbers<?php echo ($i == $current_page) ? ' current' : ''; ?>" href="?page=skd-pl-order-history&paged=<?php echo $i; ?>&s=<?php echo urlencode($search); ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($current_page < $total_pages): ?>
                    <a class="next page-numbers" href="?page=skd-pl-order-history&paged=<?php echo $current_page + 1; ?>&s=<?php echo urlencode($search); ?>">Next »</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>