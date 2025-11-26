<?php

if (!defined('ABSPATH')) {
    exit;
}

class SKD_PL_Portfolio_Categories
{
    public static function init()
    {
        add_action('wp_ajax_skd_pl_add_portfolio_category', [__CLASS__, 'ajax_add_category']);
        add_action('wp_ajax_skd_pl_update_portfolio_category', [__CLASS__, 'ajax_update_category']);
        add_action('wp_ajax_skd_pl_delete_portfolio_category', [__CLASS__, 'ajax_delete_category']);
        add_action('wp_ajax_skd_pl_reorder_portfolio_categories', [__CLASS__, 'ajax_reorder_categories']);
        add_action('wp_ajax_skd_pl_get_portfolio_category', [__CLASS__, 'ajax_get_category']);
    }

    public static function render_page()
    {
        include SKD_PL_PLUGIN_PATH . 'templates/admin/portfolio-categories/list.php';
    }

    /**
     * Get all portfolio categories
     */
    public static function get_categories($args = [])
    {
        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_portfolio_categories';

        $defaults = [
            'status' => '',
            'search' => '',
            'orderby' => 'sort_order',
            'order' => 'ASC',
        ];

        $args = wp_parse_args($args, $defaults);

        $where = ['1=1'];
        $values = [];

        if (!empty($args['status'])) {
            $where[] = 'status = %s';
            $values[] = $args['status'];
        }

        if (!empty($args['search'])) {
            $where[] = '(name LIKE %s OR description LIKE %s)';
            $values[] = '%' . $wpdb->esc_like($args['search']) . '%';
            $values[] = '%' . $wpdb->esc_like($args['search']) . '%';
        }

        $where_clause = implode(' AND ', $where);

        $query = "SELECT * FROM $table WHERE $where_clause ORDER BY {$args['orderby']} {$args['order']}";

        if (!empty($values)) {
            $query = $wpdb->prepare($query, $values);
        }

        return $wpdb->get_results($query);
    }

    /**
     * Add new category
     */
    public static function ajax_add_category()
    {
        check_ajax_referer('skd_pl_manage_portfolio_categories', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_portfolio_categories';

        $name = sanitize_text_field($_POST['name'] ?? '');
        $description = sanitize_textarea_field($_POST['description'] ?? '');
        $sort_order = intval($_POST['sort_order'] ?? 0);

        if (empty($name)) {
            wp_send_json_error(['message' => 'Category name is required']);
        }

        $slug = sanitize_title($name);

        // Check if slug exists
        $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table WHERE slug = %s", $slug));
        if ($exists) {
            wp_send_json_error(['message' => 'A category with this name already exists']);
        }

        $result = $wpdb->insert($table, [
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'sort_order' => $sort_order,
            'status' => 'active'
        ]);

        if ($result) {
            wp_send_json_success([
                'message' => 'Category added successfully',
                'id' => $wpdb->insert_id
            ]);
        } else {
            wp_send_json_error(['message' => 'Failed to add category']);
        }
    }

    /**
     * Update category
     */
    public static function ajax_update_category()
    {
        check_ajax_referer('skd_pl_manage_portfolio_categories', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        $id = intval($_POST['id'] ?? 0);
        if (!$id) {
            wp_send_json_error(['message' => 'Invalid category ID']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_portfolio_categories';

        $data = [
            'name' => sanitize_text_field($_POST['name'] ?? ''),
            'description' => sanitize_textarea_field($_POST['description'] ?? ''),
            'sort_order' => intval($_POST['sort_order'] ?? 0),
            'status' => sanitize_text_field($_POST['status'] ?? 'active')
        ];

        if (empty($data['name'])) {
            wp_send_json_error(['message' => 'Category name is required']);
        }

        $result = $wpdb->update($table, $data, ['id' => $id]);

        if ($result !== false) {
            wp_send_json_success(['message' => 'Category updated successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to update category']);
        }
    }

    /**
     * Get single category
     */
    public static function ajax_get_category()
    {
        $id = intval($_POST['id'] ?? 0);
        if (!$id) {
            wp_send_json_error(['message' => 'Invalid category ID']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_portfolio_categories';

        $category = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE id = %d",
            $id
        ));

        if ($category) {
            wp_send_json_success($category);
        } else {
            wp_send_json_error(['message' => 'Category not found']);
        }
    }

    /**
     * Delete category
     */
    public static function ajax_delete_category()
    {
        check_ajax_referer('skd_pl_manage_portfolio_categories', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        $id = intval($_POST['id'] ?? 0);
        if (!$id) {
            wp_send_json_error(['message' => 'Invalid category ID']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_portfolio_categories';

        $result = $wpdb->delete($table, ['id' => $id]);

        if ($result) {
            wp_send_json_success(['message' => 'Category deleted successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to delete category']);
        }
    }

    /**
     * Reorder categories
     */
    public static function ajax_reorder_categories()
    {
        check_ajax_referer('skd_pl_manage_portfolio_categories', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        $order = $_POST['order'] ?? [];

        if (empty($order) || !is_array($order)) {
            wp_send_json_error(['message' => 'Invalid order data']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_portfolio_categories';

        // Update sort order for each category
        foreach ($order as $index => $id) {
            $wpdb->update(
                $table,
                ['sort_order' => $index + 1],
                ['id' => intval($id)]
            );
        }

        wp_send_json_success(['message' => 'Order updated successfully']);
    }
}

SKD_PL_Portfolio_Categories::init();
