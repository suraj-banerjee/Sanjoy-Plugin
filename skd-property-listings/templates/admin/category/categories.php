<?php if (!defined('ABSPATH')) {
    exit;
} ?>

<div class="wrap">
    <h1><?php esc_html_e('Categories', 'skd-property-listings'); ?></h1>

    <a href="<?php echo esc_url(add_query_arg(['action' => 'add'], admin_url('admin.php?page=skd-pl-categories'))); ?>" class="page-title-action"><?php esc_html_e('Add New Category', 'skd-property-listings'); ?></a>

    <?php if (!empty($_GET['message'])): ?>
        <div class="notice notice-<?php echo esc_attr($_GET['message'] === 'duplicate' ? 'error' : 'updated'); ?> is-dismissible">
            <p>
                <?php
                switch ($_GET['message']) {
                    case 'success':
                        esc_html_e('Category saved successfully.', 'skd-property-listings');
                        break;
                    case 'deleted':
                        esc_html_e('Category deleted successfully.', 'skd-property-listings');
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
                <td><input type="text" name="name" id="name" value="<?php echo esc_attr($category->name ?? ''); ?>" required></td>
            </tr>
            <tr>
                <th><label for="slug"><?php esc_html_e('Slug', 'skd-property-listings'); ?></label></th>
                <td><input type="text" name="slug" id="slug" value="<?php echo esc_attr($category->slug ?? ''); ?>"></td>
            </tr>
            <tr>
                <th><label for="parent_id"><?php esc_html_e('Parent Category', 'skd-property-listings'); ?></label></th>
                <td>
                    <select name="parent_id" id="parent_id">
                        <option value="0"><?php esc_html_e('None', 'skd-property-listings'); ?></option>
                        <?php
                        echo $this->get_category_hierarchy_options(
                            $categories,          // List of all categories
                            0,                    // Root level starts from 0
                            $category ? $category->id : 0, // Exclude the current category being edited
                            0,                    // Initial depth
                            $category ? $category->parent_id : 0 // Pre-select the current category's parent
                        );
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="description"><?php esc_html_e('Description', 'skd-property-listings'); ?></label></th>
                <td><textarea name="description" id="description"><?php echo esc_textarea($category->description ?? ''); ?></textarea></td>
            </tr>
            <tr>
                <th><label for="category_icon"><?php esc_html_e('Category Icon', 'skd-property-listings'); ?></label></th>
                <td>
                    <input type="text" name="icon_url" id="category_icon" value="<?php echo esc_url($category->icon_url ?? ''); ?>" readonly>
                    <button type="button" class="button skd-upload-media" data-target="#category_icon"><?php esc_html_e('Select Icon', 'skd-property-listings'); ?></button>
                </td>
            </tr>
            <tr>
                <th><label for="category_image"><?php esc_html_e('Category Image', 'skd-property-listings'); ?></label></th>
                <td>
                    <input type="text" name="image_url" id="category_image" value="<?php echo esc_url($category->image_url ?? ''); ?>" readonly>
                    <button type="button" class="button skd-upload-media" data-target="#category_image"><?php esc_html_e('Select Image', 'skd-property-listings'); ?></button>
                </td>
            </tr>
        </table>
        <?php submit_button($actionCat === 'edit' ? __('Update Category', 'skd-property-listings') : __('Add Category', 'skd-property-listings')); ?>
    </form>

    <h2><?php esc_html_e('All Categories', 'skd-property-listings'); ?></h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php esc_html_e('ID', 'skd-property-listings'); ?></th>
                <th><?php esc_html_e('Name', 'skd-property-listings'); ?></th>
                <th><?php esc_html_e('Slug', 'skd-property-listings'); ?></th>
                <th><?php esc_html_e('Description', 'skd-property-listings'); ?></th>
                <th><?php esc_html_e('Icon', 'skd-property-listings'); ?></th>
                <th><?php esc_html_e('Image', 'skd-property-listings'); ?></th>
                <th><?php esc_html_e('Actions', 'skd-property-listings'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($categories)) : ?>
                <?php echo $this->render_hierarchical_category_list($categories); ?>
            <?php else : ?>
                <tr>
                    <td colspan="7"><?php esc_html_e('No categories found.', 'skd-property-listings'); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>