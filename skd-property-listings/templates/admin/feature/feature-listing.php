<?php if (!defined('ABSPATH')) {
    exit;
} ?>

<div class="wrap">
    <h1><?php esc_html_e('Feature Listing', 'skd-property-listings'); ?></h1>

    <a href="<?php echo esc_url(add_query_arg(['action' => 'add'], admin_url('admin.php?page=skd-pl-feature-listing'))); ?>" class="page-title-action"><?php esc_html_e('Add New Feature', 'skd-property-listings'); ?></a>

    <?php if (!empty($_GET['message'])): ?>
        <div class="notice notice-<?php echo $_GET['message'] === 'duplicate' ? 'error' : 'updated'; ?> is-dismissible">
            <p>
                <?php
                if ($_GET['message'] === 'success') {
                    esc_html_e('Feature saved successfully.', 'skd-property-listings');
                } elseif ($_GET['message'] === 'deleted') {
                    esc_html_e('Feature deleted successfully.', 'skd-property-listings');
                } elseif ($_GET['message'] === 'duplicate') {
                    esc_html_e('Duplicate slug detected. Please use a unique slug.', 'skd-property-listings');
                }
                ?>
            </p>
        </div>
    <?php endif; ?>

    <h2><?php echo $action === 'edit' ? esc_html__('Edit Feature', 'skd-property-listings') : esc_html__('Add New Feature', 'skd-property-listings'); ?></h2>

    <form method="post">
        <table class="form-table">
            <tr>
                <th><label for="name"><?php esc_html_e('Name', 'skd-property-listings'); ?></label></th>
                <td><input type="text" name="name" id="name" value="<?php echo $action === 'edit' && $feature ? esc_attr($feature->name) : ''; ?>" required></td>
            </tr>
            <tr>
                <th><label for="slug"><?php esc_html_e('Slug', 'skd-property-listings'); ?></label></th>
                <td><input type="text" name="slug" id="slug" value="<?php echo $action === 'edit' && $feature ? esc_attr($feature->slug) : ''; ?>"></td>
            </tr>
            <tr>
                <th><label for="description"><?php esc_html_e('Description', 'skd-property-listings'); ?></label></th>
                <td><textarea name="description" id="description"><?php echo $action === 'edit' && $feature ? esc_textarea($feature->description) : ''; ?></textarea></td>
            </tr>
        </table>
        <?php submit_button($action === 'edit' ? __('Update Feature', 'skd-property-listings') : __('Add Feature', 'skd-property-listings')); ?>
    </form>

    <h2><?php esc_html_e('Existing Features', 'skd-property-listings'); ?></h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php esc_html_e('ID', 'skd-property-listings'); ?></th>
                <th><?php esc_html_e('Name', 'skd-property-listings'); ?></th>
                <th><?php esc_html_e('Slug', 'skd-property-listings'); ?></th>
                <th><?php esc_html_e('Description', 'skd-property-listings'); ?></th>
                <th><?php esc_html_e('Actions', 'skd-property-listings'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($features)) : ?>
                <?php foreach ($features as $feature) : ?>
                    <tr>
                        <td><?php echo esc_html($feature->id); ?></td>
                        <td><?php echo esc_html($feature->name); ?></td>
                        <td><?php echo esc_html($feature->slug); ?></td>
                        <td><?php echo esc_html($feature->description); ?></td>
                        <td>
                            <a href="<?php echo esc_url(add_query_arg(['action' => 'edit', 'id' => $feature->id], admin_url('admin.php?page=skd-pl-feature-listing'))); ?>"><?php esc_html_e('Edit', 'skd-property-listings'); ?></a> |
                            <a href="<?php echo esc_url(add_query_arg(['action' => 'delete', 'id' => $feature->id], admin_url('admin.php?page=skd-pl-feature-listing'))); ?>" onclick="return confirm('<?php esc_html_e('Are you sure you want to delete this feature?', 'skd-property-listings'); ?>');"><?php esc_html_e('Delete', 'skd-property-listings'); ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="5"><?php esc_html_e('No features found.', 'skd-property-listings'); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>