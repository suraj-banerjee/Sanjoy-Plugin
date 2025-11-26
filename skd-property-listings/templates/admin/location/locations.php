<?php if (!defined('ABSPATH')) {
    exit;
} ?>

<div class="wrap">
    <h1><?php esc_html_e('Locations', 'skd-property-listings'); ?></h1>

    <a href="<?php echo esc_url(add_query_arg(['action' => 'add'], admin_url('admin.php?page=skd-pl-locations'))); ?>" class="page-title-action"><?php esc_html_e('Add New Location', 'skd-property-listings'); ?></a>

    <?php if (!empty($_GET['message'])): ?>
        <div class="notice notice-<?php echo esc_attr($_GET['message'] === 'duplicate' ? 'error' : 'updated'); ?> is-dismissible">
            <p>
                <?php
                switch ($_GET['message']) {
                    case 'success':
                        esc_html_e('Location saved successfully.', 'skd-property-listings');
                        break;
                    case 'deleted':
                        esc_html_e('Location deleted successfully.', 'skd-property-listings');
                        break;
                    case 'duplicate':
                        esc_html_e('Duplicate slug detected. Please use a unique slug.', 'skd-property-listings');
                        break;
                }
                ?>
            </p>
        </div>
    <?php endif; ?>

    <form method="post">
        <table class="form-table">
            <tr>
                <th><label for="name"><?php esc_html_e('Name', 'skd-property-listings'); ?></label></th>
                <td><input type="text" name="name" id="name" value="<?php echo esc_attr($location->name ?? ''); ?>" required></td>
            </tr>
            <tr>
                <th><label for="slug"><?php esc_html_e('Slug', 'skd-property-listings'); ?></label></th>
                <td><input type="text" name="slug" id="slug" value="<?php echo esc_attr($location->slug ?? ''); ?>"></td>
            </tr>
            <tr>
                <th><label for="parent_id"><?php esc_html_e('Parent Location', 'skd-property-listings'); ?></label></th>
                <td>
                    <select name="parent_id" id="parent_id">
                        <option value="0"><?php esc_html_e('None', 'skd-property-listings'); ?></option>
                        <?php
                        echo $this->get_location_hierarchy_options(
                            $locations,          // List of all locations
                            0,                    // Root level starts from 0
                            $location ? $location->id : 0, // Exclude the current location being edited
                            0,                    // Initial depth
                            $location ? $location->parent_id : 0 // Pre-select the current location's parent
                        );
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="description"><?php esc_html_e('Description', 'skd-property-listings'); ?></label></th>
                <td><textarea name="description" id="description"><?php echo esc_textarea($location->description ?? ''); ?></textarea></td>
            </tr>
            <tr>
                <th><label for="location_image"><?php esc_html_e('Location Image', 'skd-property-listings'); ?></label></th>
                <td>
                    <input type="text" name="image_url" id="location_image" value="<?php echo esc_url($location->image_url ?? ''); ?>" readonly>
                    <button type="button" class="button skd-upload-media" data-target="#location_image"><?php esc_html_e('Select Image', 'skd-property-listings'); ?></button>
                </td>
            </tr>
        </table>
        <?php submit_button($actionLoc === 'edit' ? __('Update Location', 'skd-property-listings') : __('Add Location', 'skd-property-listings')); ?>
    </form>

    <h2><?php esc_html_e('All Locations', 'skd-property-listings'); ?></h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php esc_html_e('ID', 'skd-property-listings'); ?></th>
                <th><?php esc_html_e('Name', 'skd-property-listings'); ?></th>
                <th><?php esc_html_e('Slug', 'skd-property-listings'); ?></th>
                <th><?php esc_html_e('Description', 'skd-property-listings'); ?></th>
                <th><?php esc_html_e('Image', 'skd-property-listings'); ?></th>
                <th><?php esc_html_e('Actions', 'skd-property-listings'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($locations)) : ?>
                <?php echo $this->render_hierarchical_location_list($locations); ?>
            <?php else : ?>
                <tr>
                    <td colspan="6"><?php esc_html_e('No locations found.', 'skd-property-listings'); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>