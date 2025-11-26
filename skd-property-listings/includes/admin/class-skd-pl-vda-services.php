<?php

if (!defined('ABSPATH')) {
    exit;
}

class SKD_PL_VDA_Services
{
    public static function init()
    {
        // Menu is registered in class-skd-pl-admin-menu.php
        add_action('wp_ajax_skd_add_service', [__CLASS__, 'ajax_add_service']);
        add_action('wp_ajax_skd_update_service', [__CLASS__, 'ajax_update_service']);
        add_action('wp_ajax_skd_delete_service', [__CLASS__, 'ajax_delete_service']);
        add_action('wp_ajax_skd_get_services', [__CLASS__, 'ajax_get_services']);
        add_action('wp_ajax_skd_create_service_auto', [__CLASS__, 'ajax_create_service_auto']);
    }

    public static function render_page()
    {
        include SKD_PL_PLUGIN_PATH . 'templates/admin/services/list.php';
    }

    public static function get_services($args = [])
    {
        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_services';

        $defaults = [
            'status' => 'active',
            'category' => '',
            'search' => '',
            'orderby' => 'sort_order',
            'order' => 'ASC',
            'limit' => -1
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

    public static function ajax_add_service()
    {
        check_ajax_referer('skd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_services';

        $name = sanitize_text_field($_POST['name'] ?? '');
        $description = sanitize_textarea_field($_POST['description'] ?? '');
        $category = sanitize_text_field($_POST['category'] ?? '');

        if (empty($name)) {
            wp_send_json_error(['message' => 'Service name is required']);
        }

        $slug = sanitize_title($name);

        $result = $wpdb->insert($table, [
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'category' => $category,
            'status' => 'active'
        ]);

        if ($result) {
            wp_send_json_success([
                'message' => 'Service added successfully',
                'id' => $wpdb->insert_id
            ]);
        } else {
            wp_send_json_error(['message' => 'Failed to add service']);
        }
    }

    public static function ajax_update_service()
    {
        check_ajax_referer('skd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_services';

        $id = intval($_POST['id'] ?? 0);
        $name = sanitize_text_field($_POST['name'] ?? '');
        $description = sanitize_textarea_field($_POST['description'] ?? '');
        $status = sanitize_text_field($_POST['status'] ?? 'active');

        if (empty($id) || empty($name)) {
            wp_send_json_error(['message' => 'Invalid data']);
        }

        $result = $wpdb->update(
            $table,
            [
                'name' => $name,
                'description' => $description,
                'status' => $status
            ],
            ['id' => $id]
        );

        if ($result !== false) {
            wp_send_json_success(['message' => 'Service updated successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to update service']);
        }
    }

    public static function ajax_delete_service()
    {
        check_ajax_referer('skd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_services';

        $id = intval($_POST['id'] ?? 0);

        $result = $wpdb->delete($table, ['id' => $id]);

        if ($result) {
            wp_send_json_success(['message' => 'Service deleted successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to delete service']);
        }
    }

    public static function ajax_get_services()
    {
        $search = sanitize_text_field($_GET['q'] ?? '');

        $args = [
            'status' => 'active',
            'search' => $search
        ];

        $services = self::get_services($args);

        $results = [];
        foreach ($services as $service) {
            $results[] = [
                'id' => $service->id,
                'text' => $service->name
            ];
        }

        wp_send_json(['results' => $results]);
    }

    public static function ajax_create_service_auto()
    {
        check_ajax_referer('skd_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'You must be logged in']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_services';

        $name = sanitize_text_field($_POST['name'] ?? '');

        if (empty($name)) {
            wp_send_json_error(['message' => 'Service name is required']);
        }

        $slug = sanitize_title($name);

        $existing = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE slug = %s", $slug));
        if ($existing) {
            wp_send_json_success([
                'id' => $existing->id,
                'name' => $existing->name,
                'exists' => true
            ]);
            return;
        }

        $result = $wpdb->insert($table, [
            'name' => $name,
            'slug' => $slug,
            'status' => 'active',
            'sort_order' => 999
        ]);

        if ($result) {
            wp_send_json_success([
                'id' => $wpdb->insert_id,
                'name' => $name,
                'exists' => false
            ]);
        } else {
            wp_send_json_error(['message' => 'Failed to create service']);
        }
    }
}

SKD_PL_VDA_Services::init();
