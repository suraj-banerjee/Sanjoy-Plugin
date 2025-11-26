<?php

if (!defined('ABSPATH')) {
    exit;
}

class SKD_PL_Response_Times
{
    public static function init()
    {
        add_action('wp_ajax_skd_add_response_time', [__CLASS__, 'ajax_add_response_time']);
        add_action('wp_ajax_skd_update_response_time', [__CLASS__, 'ajax_update_response_time']);
        add_action('wp_ajax_skd_delete_response_time', [__CLASS__, 'ajax_delete_response_time']);
        add_action('wp_ajax_skd_get_response_times', [__CLASS__, 'ajax_get_response_times']);
    }

    public static function render_page()
    {
        include SKD_PL_PLUGIN_PATH . 'templates/admin/response-times/list.php';
    }

    public static function get_response_times($args = [])
    {
        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_response_times';

        $defaults = [
            'status' => 'active',
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

    public static function ajax_add_response_time()
    {
        check_ajax_referer('skd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_response_times';

        $name = sanitize_text_field($_POST['name'] ?? '');
        $slug = !empty($_POST['slug']) ? sanitize_title($_POST['slug']) : sanitize_title($name);
        $description = sanitize_textarea_field($_POST['description'] ?? '');

        if (empty($name)) {
            wp_send_json_error(['message' => 'Response time name is required']);
        }

        // Check if slug exists
        $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table WHERE slug = %s", $slug));
        if ($exists) {
            wp_send_json_error(['message' => 'A response time with this name already exists']);
        }

        $result = $wpdb->insert($table, [
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'status' => 'active'
        ]);

        if ($result) {
            wp_send_json_success([
                'message' => 'Response time added successfully',
                'id' => $wpdb->insert_id
            ]);
        } else {
            wp_send_json_error(['message' => 'Failed to add response time']);
        }
    }

    public static function ajax_update_response_time()
    {
        check_ajax_referer('skd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_response_times';

        $id = intval($_POST['id'] ?? 0);
        $name = sanitize_text_field($_POST['name'] ?? '');
        $slug = !empty($_POST['slug']) ? sanitize_title($_POST['slug']) : sanitize_title($name);
        $description = sanitize_textarea_field($_POST['description'] ?? '');
        $status = sanitize_text_field($_POST['status'] ?? 'active');

        if (empty($id) || empty($name)) {
            wp_send_json_error(['message' => 'Invalid data']);
        }

        // Check if slug exists for other records
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table WHERE slug = %s AND id != %d",
            $slug,
            $id
        ));
        if ($exists) {
            wp_send_json_error(['message' => 'A response time with this name already exists']);
        }

        $result = $wpdb->update(
            $table,
            [
                'name' => $name,
                'slug' => $slug,
                'description' => $description,
                'status' => $status
            ],
            ['id' => $id]
        );

        if ($result !== false) {
            wp_send_json_success(['message' => 'Response time updated successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to update response time']);
        }
    }

    public static function ajax_delete_response_time()
    {
        check_ajax_referer('skd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_response_times';

        $id = intval($_POST['id'] ?? 0);

        $result = $wpdb->delete($table, ['id' => $id]);

        if ($result) {
            wp_send_json_success(['message' => 'Response time deleted successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to delete response time']);
        }
    }

    public static function ajax_get_response_times()
    {
        // Check if requesting a single response time by ID (for edit)
        $id = isset($_POST['id']) ? intval($_POST['id']) : (isset($_GET['id']) ? intval($_GET['id']) : 0);

        if ($id > 0) {
            check_ajax_referer('skd_admin_nonce', 'nonce');

            if (!current_user_can('manage_options')) {
                wp_send_json_error(['message' => 'Permission denied']);
            }

            global $wpdb;
            $table = $wpdb->prefix . 'skd_pl_response_times';
            $time = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));

            if ($time) {
                wp_send_json_success(['response_times' => [$time]]);
            } else {
                wp_send_json_error(['message' => 'Response time not found']);
            }
            return;
        }

        // Otherwise, return list for Select2
        $search = sanitize_text_field($_GET['q'] ?? '');

        $args = [
            'status' => 'active',
            'search' => $search
        ];

        $times = self::get_response_times($args);

        $results = [];
        foreach ($times as $time) {
            $results[] = [
                'id' => $time->id,
                'text' => $time->name
            ];
        }

        wp_send_json(['results' => $results]);
    }
}

SKD_PL_Response_Times::init();
