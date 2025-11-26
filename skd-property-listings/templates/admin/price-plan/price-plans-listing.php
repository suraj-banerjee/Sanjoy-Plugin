<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

require_once SKD_PL_PLUGIN_PATH . 'includes/common/skd-utils.php';
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Price Plans', 'skd-property-listings'); ?></h1>
    <a href="<?php echo admin_url('admin.php?page=skd-pl-price-plans&action=add'); ?>" class="page-title-action">
        <?php _e('Add New', 'skd-property-listings'); ?>
    </a>
    <hr class="wp-header-end">

    <?php if (!empty($_GET['message']) && $_GET['message'] === 'success') : ?>
        <div class="updated notice is-dismissible">
            <p><?php _e('Price Plan saved successfully!', 'skd-property-listings'); ?></p>
        </div>
    <?php elseif (!empty($_GET['message']) && $_GET['message'] === 'deleted') : ?>
        <div class="updated notice is-dismissible">
            <p><?php _e('Price Plan deleted successfully!', 'skd-property-listings'); ?></p>
        </div>
    <?php elseif (!empty($_GET['message']) && $_GET['message'] === 'duplicate') : ?>
        <div class="error notice is-dismissible">
            <p><?php _e('Duplicate slug. Please use a unique name.', 'skd-property-listings'); ?></p>
        </div>
    <?php endif; ?>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php _e('Plan Name', 'skd-property-listings'); ?></th>
                <th><?php _e('Plan Type', 'skd-property-listings'); ?></th>
                <th><?php _e('Price', 'skd-property-listings'); ?></th>
                <th><?php _e('Duration', 'skd-property-listings'); ?></th>
                <th><?php _e('Listing Limit', 'skd-property-listings'); ?></th>
                <th><?php _e('Status', 'skd-property-listings'); ?></th>
                <th><?php _e('Actions', 'skd-property-listings'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($price_plans)) : ?>
                <?php foreach ($price_plans as $plan) : ?>
                    <tr>
                        <td><?php echo esc_html($plan->plan_name); ?></td>
                        <td>
                            <?php
                            echo ($plan->plan_type === 'pay_per_listing') ? __('Pay per listing', 'skd-property-listings') : __('Package', 'skd-property-listings');
                            ?>
                        </td>
                        <td><?php echo '$' . esc_html(number_format($plan->price, 2)); ?></td>
                        <td><?php echo esc_html($plan->listing_duration) . ' ' . esc_html($plan->duration_unit); ?></td>
                        <td>
                            <?php if ($plan->plan_type === 'pay_per_listing') { ?>
                                <?php echo ($plan->featured_the_list == 'yes') ? '1 featured lisitng' : '1 regular lisitng'; ?>
                            <?php } else { ?>
                                <p>
                                    Number of Listings :
                                    <?php echo ($plan->mark_as_unlimited == 1) ? 'Unlimited' : $plan->no_of_listing; ?>
                                </p>
                                <p>
                                    Featured Listings :
                                    <?php echo ($plan->mark_feature_unlimited == 1) ? 'Unlimited' : $plan->no_of_feature_listing; ?>
                                </p>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if ($plan->plan_status == 'published') : ?>
                                <span style="color: green;"><?php _e('Published', 'skd-property-listings'); ?></span>
                            <?php elseif ($plan->plan_status == 'pending') : ?>
                                <span style="color: orange;"><?php _e('Pending', 'skd-property-listings'); ?></span>
                            <?php else : ?>
                                <span style="color: red;"><?php _e('Draft', 'skd-property-listings'); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=skd-pl-price-plans&action=edit&id=' . skd_encrypt_id($plan->id)); ?>"><?php _e('Edit', 'skd-property-listings'); ?></a> |
                            <a href="<?php echo admin_url('admin.php?page=skd-pl-price-plans&action=delete&id=' . skd_encrypt_id($plan->id)); ?>" style="color: red;" onclick="return confirm('<?php _e('Are you sure you want to delete this price plan?', 'skd-property-listings'); ?>');"><?php _e('Delete', 'skd-property-listings'); ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="5" style="text-align:center;"><?php _e('No Price Plans Found.', 'skd-property-listings'); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>