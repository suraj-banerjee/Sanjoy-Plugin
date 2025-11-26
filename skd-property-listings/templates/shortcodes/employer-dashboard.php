<?php

/**
 * Employer Dashboard Template
 * Dashboard for Employers/Recruiters
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!is_user_logged_in()) {
    echo '<p>Please <a href="' . home_url('/login/') . '">login</a> to access your dashboard.</p>';
    return;
}

global $wpdb;
$user_id = get_current_user_id();
$user = wp_get_current_user();
$user_type = get_user_meta($user_id, 'skd_user_type', true);

if ($user_type !== 'employer') {
    echo '<p>This dashboard is only for Employer users.</p>';
    return;
}

// Check if user has active pricing plan
$active_plan = $wpdb->get_row($wpdb->prepare(
    "SELECT o.*, pp.plan_name, pp.plan_features 
     FROM {$wpdb->prefix}skd_pl_orders o
     INNER JOIN {$wpdb->prefix}skd_pl_price_plans pp ON o.plan_id = pp.id
     WHERE o.user_id = %d 
     AND o.payment_status = 'completed'
     AND (o.plan_duration = 'lifetime' OR DATE_ADD(o.created_at, INTERVAL 
         CASE o.plan_duration 
             WHEN 'monthly' THEN 30
             WHEN 'quarterly' THEN 90
             WHEN 'yearly' THEN 365
         END DAY) > NOW())
     ORDER BY o.created_at DESC
     LIMIT 1",
    $user_id
));

$has_active_plan = !empty($active_plan);

echo '<div class="vda-dashboard-wrapper">';
echo '<h1>Employer Dashboard Coming Soon!</h1>';
echo '<p>Welcome, ' . esc_html($user->display_name) . '</p>';
echo '<p>Employer-specific features will be available here.</p>';
echo '<a href="#" id="vda-logout-btn" class="vda-btn vda-btn-primary">Logout</a>';
echo '</div>';
?>

<script>
    jQuery(document).ready(function($) {
        const nonce = '<?php echo wp_create_nonce('skd_ajax_nonce'); ?>';

        $('#vda-logout-btn').click(function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Logout',
                text: 'Are you sure you want to logout?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Logout',
                confirmButtonColor: '#f44336'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(skd_ajax_object.ajax_url, {
                        action: 'skd_logout_user',
                        nonce: nonce
                    }, function(response) {
                        if (response.success) {
                            window.location.href = '<?php echo home_url('/login/'); ?>';
                        }
                    });
                }
            });
        });
    });
</script>