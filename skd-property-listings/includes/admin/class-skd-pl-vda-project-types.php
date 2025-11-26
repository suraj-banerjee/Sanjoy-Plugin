<?php

if (!defined('ABSPATH')) {
    exit;
}

class SKD_PL_VDA_Project_Types
{
    public static function init()
    {
        add_action('admin_menu', [__CLASS__, 'add_menu_page'], 33);
        add_action('wp_ajax_skd_add_project_type', [__CLASS__, 'ajax_add_project_type']);
        add_action('wp_ajax_skd_update_project_type', [__CLASS__, 'ajax_update_project_type']);
        add_action('wp_ajax_skd_delete_project_type', [__CLASS__, 'ajax_delete_project_type']);
        add_action('wp_ajax_skd_get_project_types', [__CLASS__, 'ajax_get_project_types']);
    }

    public static function add_menu_page()
    {
        add_submenu_page(
            'skd-pl-all-listings',
            'Project Types',
            'Project Types',
            'manage_options',
            'skd-project-types',
            [__CLASS__, 'render_page']
        );
    }

    public static function render_page()
    {
        include SKD_PL_PLUGIN_PATH . 'templates/admin/project-types/list.php';
    }

    /**
     * Get all project types
     */
    public static function get_project_types($args = [])
    {
        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_project_types';

        $defaults = [
            'status' => 'active',
            'search' => '',
            'orderby' => 'sort_order',
            'order' => 'ASC',
            'limit' => -1,
            'offset' => 0
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

        if ($args['limit'] > 0) {
            $query .= $wpdb->prepare(" LIMIT %d OFFSET %d", $args['limit'], $args['offset']);
        }

        if (!empty($values)) {
            $query = $wpdb->prepare($query, $values);
        }

        return $wpdb->get_results($query);
    }

    /**
     * Add new project type
     */
    public static function ajax_add_project_type()
    {
        check_ajax_referer('skd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_project_types';

        $name = sanitize_text_field($_POST['name']);
        $slug = sanitize_title($_POST['slug']);
        $description = sanitize_textarea_field($_POST['description'] ?? '');
        $status = sanitize_text_field($_POST['status'] ?? 'active');
        $sort_order = intval($_POST['sort_order'] ?? 0);

        // Check if slug already exists
        $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE slug = %s", $slug));
        if ($exists) {
            wp_send_json_error(['message' => 'Project type with this slug already exists']);
        }

        $result = $wpdb->insert(
            $table,
            [
                'name' => $name,
                'slug' => $slug,
                'description' => $description,
                'status' => $status,
                'sort_order' => $sort_order
            ],
            ['%s', '%s', '%s', '%s', '%d']
        );

        if ($result) {
            wp_send_json_success(['message' => 'Project type added successfully', 'id' => $wpdb->insert_id]);
        } else {
            wp_send_json_error(['message' => 'Failed to add project type']);
        }
    }

    /**
     * Update project type
     */
    public static function ajax_update_project_type()
    {
        check_ajax_referer('skd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_project_types';

        $id = intval($_POST['id']);
        $name = sanitize_text_field($_POST['name']);
        $slug = sanitize_title($_POST['slug']);
        $description = sanitize_textarea_field($_POST['description'] ?? '');
        $status = sanitize_text_field($_POST['status'] ?? 'active');
        $sort_order = intval($_POST['sort_order'] ?? 0);

        // Check if slug already exists (excluding current record)
        $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE slug = %s AND id != %d", $slug, $id));
        if ($exists) {
            wp_send_json_error(['message' => 'Project type with this slug already exists']);
        }

        $result = $wpdb->update(
            $table,
            [
                'name' => $name,
                'slug' => $slug,
                'description' => $description,
                'status' => $status,
                'sort_order' => $sort_order
            ],
            ['id' => $id],
            ['%s', '%s', '%s', '%s', '%d'],
            ['%d']
        );

        if ($result !== false) {
            wp_send_json_success(['message' => 'Project type updated successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to update project type']);
        }
    }

    /**
     * Delete project type
     */
    public static function ajax_delete_project_type()
    {
        check_ajax_referer('skd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_project_types';

        $id = intval($_POST['id']);

        $result = $wpdb->delete($table, ['id' => $id], ['%d']);

        if ($result) {
            wp_send_json_success(['message' => 'Project type deleted successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to delete project type']);
        }
    }

    /**
     * Get project types for AJAX
     */
    public static function ajax_get_project_types()
    {
        check_ajax_referer('skd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        $project_types = self::get_project_types();
        wp_send_json_success(['project_types' => $project_types]);
    }
}

SKD_PL_VDA_Project_Types::init();
