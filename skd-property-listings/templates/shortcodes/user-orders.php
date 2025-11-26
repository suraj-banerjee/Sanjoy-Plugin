<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<style>
    .skd-user-orders table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .skd-user-orders th,
    .skd-user-orders td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
    }

    .skd-user-orders th {
        background: #f4f4f4;
    }

    .skd-add-listing-btn {
        background: #28a745;
        color: #fff !important;
        padding: 6px 12px;
        text-decoration: none;
        border-radius: 4px;
        font-size: 0.9em;
        border-color: #28a745;
        line-height: 1.1;
    }

    .skd-add-listing-btn:hover,
    .skd-add-listing-btn:focus {
        background: #218838;
        color: #fff;
    }

    .skd-cancel-subscription {
        padding: 6px 12px;
        border-radius: 4px;
        font-size: .9em;
        margin-left: 5px;
        line-height: 1.1;
    }

    .skd-disabled-btn {
        background: #ccc;
        padding: 6px 12px;
        border-radius: 4px;
        color: #666;
    }

    .skd-add-listing-btn.disabled,
    .skd-cancel-subscription:disabled {
        /* background: #ccc;
        color: #666 !important; */
        cursor: not-allowed;
    }
</style>
<div class="skd-user-orders">
    <table>
        <thead>
            <tr>
                <th>Plan Name</th>
                <th>Final Price</th>
                <th>Status</th>
                <th>Payment Method</th>
                <th>Subscription</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($orders)) : ?>
                <tr>
                    <td colspan="7">No orders found.</td>
                </tr>
            <?php endif; ?>
            <?php foreach ($orders as $order) : ?>
                <tr>
                    <td>
                        <?php echo esc_html($order->plan_name); ?>
                        <?php if ($order->is_active == 'Active') { ?>
                            <span class="skd-disabled-btn" style="margin-left: 5px; color:green;">Active</span>
                        <?php } else { ?>
                            <span class="skd-disabled-btn" style="margin-left: 5px; color:red;">Inactive</span>
                        <?php } ?>
                    </td>
                    <td><?php echo esc_html(number_format($order->final_price, 2)); ?></td>
                    <td><?php echo ucfirst($order->order_status); ?></td>
                    <td><?php echo ucfirst($order->payment_method); ?></td>
                    <td>
                        <?php echo $order->is_subscription == 'true' ? 'Yes' : 'No'; ?>
                        <?php if ($order->subscription_cancel) { ?>
                            <span class="skd-disabled-btn" style="margin-left: 5px; color:red;">Cancelled</span>
                        <?php } elseif ($order->is_subscription == 'true') { ?>
                            <span class="skd-disabled-btn" style="margin-left: 5px; color:green;">Active</span>
                        <?php } ?>
                    </td>
                    <td><?php echo date('d-m-Y', strtotime($order->created_at)); ?></td>
                    <td>
                        <?php if ($order->order_status === 'completed') : ?>
                            <button class="skd-add-listing-btn skdAddListingBtn" data-order-id="<?php echo esc_attr($order->id); ?>">
                                Add Listing
                            </button>
                            <?php if (!$order->subscription_cancel && $order->is_subscription && $order->subscription_id): ?>
                                <button class="skd-cancel-subscription" data-order-id="<?php echo esc_attr($order->id); ?>">
                                    Cancel Subscription
                                </button>
                            <?php endif; ?>
                        <?php else : ?>
                            <span class="skd-disabled-btn">Pending</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    jQuery(document).ready(function($) {
        var skd_ajax_object = {
            ajax_url: '<?php echo admin_url('admin-ajax.php'); ?>'
        };
        $('.skd-cancel-subscription').on('click', function() {
            let $btn = $(this);
            let order_id = $btn.data('order-id');

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, cancel it!',
                cancelButtonText: 'No',
                confirmButtonColor: '#92d509',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Disable button and show spinner
                    $btn.prop('disabled', true);
                    let originalText = $btn.html();
                    $btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Cancelling...');

                    $.post(skd_ajax_object.ajax_url, {
                        action: 'skd_cancel_subscription',
                        order_id: order_id
                    }, function(res) {
                        // Restore button
                        $btn.prop('disabled', false).html(originalText);

                        Swal.fire({
                            title: res.success ? 'Cancelled!' : 'Error!',
                            text: res.data.message,
                            icon: res.success ? 'success' : 'error'
                        }).then(() => {
                            if (res.success) location.reload();
                        });
                    });
                }
            });
        });

        $('.skdAddListingBtn').on('click', function() {
            let $btn = $(this);
            let order_id = $btn.data('order-id');

            // Disable & add spinner
            $btn.addClass('disabled');
            let originalHtml = $btn.html();
            $btn.html('<span class="spinner-border spinner-border-sm" role="status"></span> Checking...');

            $.post(skd_ajax_object.ajax_url, {
                action: 'skd_check_order_active',
                order_id: order_id
            }, function(res) {
                if (res.success) {
                    // console.log(res);

                    // Redirect if active
                    window.location.href = res.data.redirect_url;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Access Denied',
                        text: res.data.message || 'This order has expired or the subscription is inactive.',
                    });
                    $btn.removeClass('disabled').html(originalHtml);
                }
            });
        });

    });
</script>