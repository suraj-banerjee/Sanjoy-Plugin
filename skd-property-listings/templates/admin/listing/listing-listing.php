<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Listings', 'skd-property-listings'); ?></h1>
    <a href="<?php echo admin_url('admin.php?page=skd-pl-all-listings&action=add'); ?>" class="page-title-action">
        <?php _e('Add New', 'skd-property-listings'); ?>
    </a>
    <hr class="wp-header-end">

    <?php if (!empty($_GET['message']) && $_GET['message'] === 'success') : ?>
        <div class="updated notice is-dismissible">
            <p><?php _e('Listing saved successfully!', 'skd-property-listings'); ?></p>
        </div>
    <?php elseif (!empty($_GET['message']) && $_GET['message'] === 'deleted') : ?>
        <div class="updated notice is-dismissible">
            <p><?php _e('Listing deleted successfully!', 'skd-property-listings'); ?></p>
        </div>
    <?php endif; ?>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php _e('Title', 'skd-property-listings'); ?></th>
                <th><?php _e('Location', 'skd-property-listings'); ?></th>
                <th><?php _e('Categories', 'skd-property-listings'); ?></th>
                <th><?php _e('Author', 'skd-property-listings'); ?></th>
                <th><?php _e('Featured', 'skd-property-listings'); ?></th>
                <th><?php _e('Date', 'skd-property-listings'); ?></th>
                <th><?php _e('Plan', 'skd-property-listings'); ?></th>
                <th><?php _e('Actions', 'skd-property-listings'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($listings)) : ?>
                <?php foreach ($listings as $listing) : ?>
                    <tr>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=skd-pl-all-listings&action=edit&id=' . $listing->id); ?>">
                                <?php echo esc_html($listing->listing_title); ?>
                            </a>
                            <br>
                            <b><?php _e('Status:', 'skd-property-listings'); ?></b>
                            <?php if ($listing->listing_status == 'publish') { ?>
                                <span class="dashicons dashicons-yes" style="color: green;"></span>
                                <span style="color: green;"><?php _e('Published', 'skd-property-listings'); ?></span>
                            <?php } elseif ($listing->listing_status == 'draft') { ?>
                                <span class="dashicons dashicons-no" style="color: red;"></span>
                                <span style="color: red;"><?php _e('Draft', 'skd-property-listings'); ?></span>
                            <?php } elseif ($listing->listing_status == 'pending') { ?>
                                <span class="dashicons dashicons-clock" style="color: orange;"></span>
                                <span style="color: orange;"><?php _e('Pending', 'skd-property-listings'); ?></span>
                            <?php } ?>
                        </td>
                        <td><?php echo esc_html($this->get_locations($listing->location_ids)); ?></td>
                        <td><?php echo esc_html($this->get_categories($listing->category_ids)); ?></td>
                        <td><?php echo esc_html($listing->author_name); ?></td>
                        <td><?php echo $listing->is_feature ? __('Yes', 'skd-property-listings') : __('No', 'skd-property-listings'); ?></td>
                        <td>
                            <b><?php _e('Created Date:', 'skd-property-listings'); ?></b>
                            <?php echo date('F j, Y', strtotime($listing->created_at)); ?> <br>
                            <b><?php _e('Expiry Date:', 'skd-property-listings'); ?></b>
                            <?php echo date('F j, Y', strtotime($listing->expiration_date)); ?>
                        </td>
                        <td><?php echo esc_html($listing->plan_name) . ' - ' . esc_html($listing->plan_type); ?></td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=skd-pl-all-listings&action=edit&id=' . $listing->id); ?>">
                                <?php _e('Edit', 'skd-property-listings'); ?>
                            </a>
                            |
                            <a href="<?php echo admin_url('admin.php?page=skd-pl-all-listings&action=delete&id=' . $listing->id); ?>" onclick="return confirm('<?php _e('Are you sure you want to delete this listing?', 'skd-property-listings'); ?>');">
                                <?php _e('Delete', 'skd-property-listings'); ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="8"><?php _e('No listings found.', 'skd-property-listings'); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>