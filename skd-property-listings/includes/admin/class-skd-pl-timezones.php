<?php

if (!defined('ABSPATH')) {
    exit;
}

class SKD_PL_Timezones
{
    public static function init()
    {
        add_action('wp_ajax_skd_add_timezone', [__CLASS__, 'ajax_add_timezone']);
        add_action('wp_ajax_skd_update_timezone', [__CLASS__, 'ajax_update_timezone']);
        add_action('wp_ajax_skd_delete_timezone', [__CLASS__, 'ajax_delete_timezone']);
        add_action('wp_ajax_skd_get_timezones', [__CLASS__, 'ajax_get_timezones']);
    }

    public static function render_page()
    {
        include SKD_PL_PLUGIN_PATH . 'templates/admin/timezones/list.php';
    }

    public static function get_timezones($args = [])
    {
        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_timezones';

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

    public static function ajax_add_timezone()
    {
        check_ajax_referer('skd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_timezones';

        $name = sanitize_text_field($_POST['name'] ?? '');
        $value = sanitize_text_field($_POST['value'] ?? '');
        $offset = sanitize_text_field($_POST['offset'] ?? '');

        if (empty($name) || empty($value)) {
            wp_send_json_error(['message' => 'Timezone name and value are required']);
        }

        // Check if value exists
        $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table WHERE value = %s", $value));
        if ($exists) {
            wp_send_json_error(['message' => 'A timezone with this value already exists']);
        }

        $result = $wpdb->insert($table, [
            'name' => $name,
            'value' => $value,
            'offset' => $offset,
            'status' => 'active'
        ]);

        if ($result) {
            wp_send_json_success([
                'message' => 'Timezone added successfully',
                'id' => $wpdb->insert_id
            ]);
        } else {
            wp_send_json_error(['message' => 'Failed to add timezone']);
        }
    }

    public static function ajax_update_timezone()
    {
        check_ajax_referer('skd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_timezones';

        $id = intval($_POST['id'] ?? 0);
        $name = sanitize_text_field($_POST['name'] ?? '');
        $value = sanitize_text_field($_POST['value'] ?? '');
        $offset = sanitize_text_field($_POST['offset'] ?? '');
        $status = sanitize_text_field($_POST['status'] ?? 'active');

        if (empty($id) || empty($name) || empty($value)) {
            wp_send_json_error(['message' => 'Invalid data']);
        }

        // Check if value exists for other records
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table WHERE value = %s AND id != %d",
            $value,
            $id
        ));
        if ($exists) {
            wp_send_json_error(['message' => 'A timezone with this value already exists']);
        }

        $result = $wpdb->update(
            $table,
            [
                'name' => $name,
                'value' => $value,
                'offset' => $offset,
                'status' => $status
            ],
            ['id' => $id]
        );

        if ($result !== false) {
            wp_send_json_success(['message' => 'Timezone updated successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to update timezone']);
        }
    }

    public static function ajax_delete_timezone()
    {
        check_ajax_referer('skd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_timezones';

        $id = intval($_POST['id'] ?? 0);

        $result = $wpdb->delete($table, ['id' => $id]);

        if ($result) {
            wp_send_json_success(['message' => 'Timezone deleted successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to delete timezone']);
        }
    }

    public static function ajax_get_timezones()
    {
        // Check if requesting a single timezone by ID (for edit)
        $id = isset($_POST['id']) ? intval($_POST['id']) : (isset($_GET['id']) ? intval($_GET['id']) : 0);

        if ($id > 0) {
            check_ajax_referer('skd_admin_nonce', 'nonce');

            if (!current_user_can('manage_options')) {
                wp_send_json_error(['message' => 'Permission denied']);
            }

            global $wpdb;
            $table = $wpdb->prefix . 'skd_pl_timezones';
            $timezone = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));

            if ($timezone) {
                wp_send_json_success(['timezones' => [$timezone]]);
            } else {
                wp_send_json_error(['message' => 'Timezone not found']);
            }
            return;
        }

        // Otherwise, return list for Select2
        $search = sanitize_text_field($_GET['q'] ?? '');

        $args = [
            'status' => 'active',
            'search' => $search
        ];

        $timezones = self::get_timezones($args);

        $results = [];
        foreach ($timezones as $tz) {
            $results[] = [
                'id' => $tz->id,
                'text' => $tz->name
            ];
        }

        wp_send_json(['results' => $results]);
    }
}

SKD_PL_Timezones::init();
