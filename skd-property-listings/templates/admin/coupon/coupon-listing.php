<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Coupons', 'skd-property-listings'); ?></h1>
    <a href="<?php echo admin_url('admin.php?page=skd-pl-coupons&action=add'); ?>" class="page-title-action">
        <?php _e('Add New', 'skd-property-listings'); ?>
    </a>
    <hr class="wp-header-end">

    <?php if (!empty($_GET['message']) && $_GET['message'] === 'success') : ?>
        <div class="updated notice is-dismissible">
            <p><?php _e('Coupon saved successfully!', 'skd-property-listings'); ?></p>
        </div>
    <?php elseif (!empty($_GET['message']) && $_GET['message'] === 'deleted') : ?>
        <div class="updated notice is-dismissible">
            <p><?php _e('Coupon deleted successfully!', 'skd-property-listings'); ?></p>
        </div>
    <?php elseif (!empty($_GET['message']) && $_GET['message'] === 'duplicate') : ?>
        <div class="error notice is-dismissible">
            <p><?php _e('Duplicate code. Please use a unique code.', 'skd-property-listings'); ?></p>
        </div>
    <?php endif; ?>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php _e('Title', 'skd-property-listings'); ?></th>
                <th><?php _e('Coupon Code', 'skd-property-listings'); ?></th>
                <th><?php _e('Discount Amount', 'skd-property-listings'); ?></th>
                <th><?php _e('Coupon Type', 'skd-property-listings'); ?></th>
                <th><?php _e('Usage Limit Per User', 'skd-property-listings'); ?></th>
                <th><?php _e('Expiry Date', 'skd-property-listings'); ?></th>
                <th><?php _e('Actions', 'skd-property-listings'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($coupons)) : ?>
                <?php foreach ($coupons as $coupon) : ?>
                    <tr>
                        <td><?php echo $coupon->title; ?></td>
                        <td><?php echo $coupon->coupon_code; ?></td>
                        <td><?php echo $coupon->discount_amount; ?></td>
                        <td><?php echo $coupon->discount_type; ?></td>
                        <td><?php echo $coupon->usage_limit; ?></td>
                        <td><?php echo $coupon->expiry_date; ?></td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=skd-pl-coupons&action=edit&id=' . $coupon->id); ?>">
                                <?php _e('Edit', 'skd-property-listings'); ?>
                            </a>
                            |
                            <a href="<?php echo admin_url('admin.php?page=skd-pl-coupons&action=delete&id=' . $coupon->id); ?>" onclick="return confirm('<?php _e('Are you sure you want to delete this coupon?', 'skd-property-listings'); ?>');">
                                <?php _e('Delete', 'skd-property-listings'); ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="5"><?php _e('No coupons found.', 'skd-property-listings'); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>