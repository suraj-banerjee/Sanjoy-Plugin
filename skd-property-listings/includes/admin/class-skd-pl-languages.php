<?php

if (!defined('ABSPATH')) {
    exit;
}

class SKD_PL_Languages
{
    public static function init()
    {
        add_action('wp_ajax_skd_add_language', [__CLASS__, 'ajax_add_language']);
        add_action('wp_ajax_skd_update_language', [__CLASS__, 'ajax_update_language']);
        add_action('wp_ajax_skd_delete_language', [__CLASS__, 'ajax_delete_language']);
        add_action('wp_ajax_skd_get_languages', [__CLASS__, 'ajax_get_languages']);
    }

    public static function render_page()
    {
        include SKD_PL_PLUGIN_PATH . 'templates/admin/languages/list.php';
    }

    public static function get_languages($args = [])
    {
        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_languages';

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

    public static function ajax_add_language()
    {
        check_ajax_referer('skd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_languages';

        $name = sanitize_text_field($_POST['name'] ?? '');
        $code = sanitize_text_field($_POST['code'] ?? '');
        $native_name = sanitize_text_field($_POST['native_name'] ?? '');

        if (empty($name) || empty($code)) {
            wp_send_json_error(['message' => 'Language name and code are required']);
        }

        // Check if code exists
        $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table WHERE code = %s", $code));
        if ($exists) {
            wp_send_json_error(['message' => 'A language with this code already exists']);
        }

        $result = $wpdb->insert($table, [
            'name' => $name,
            'code' => $code,
            'native_name' => $native_name,
            'status' => 'active'
        ]);

        if ($result) {
            wp_send_json_success([
                'message' => 'Language added successfully',
                'id' => $wpdb->insert_id
            ]);
        } else {
            wp_send_json_error(['message' => 'Failed to add language']);
        }
    }

    public static function ajax_update_language()
    {
        check_ajax_referer('skd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_languages';

        $id = intval($_POST['id'] ?? 0);
        $name = sanitize_text_field($_POST['name'] ?? '');
        $code = sanitize_text_field($_POST['code'] ?? '');
        $native_name = sanitize_text_field($_POST['native_name'] ?? '');
        $status = sanitize_text_field($_POST['status'] ?? 'active');

        if (empty($id) || empty($name) || empty($code)) {
            wp_send_json_error(['message' => 'Invalid data']);
        }

        // Check if code exists for other records
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table WHERE code = %s AND id != %d",
            $code,
            $id
        ));
        if ($exists) {
            wp_send_json_error(['message' => 'A language with this code already exists']);
        }

        $result = $wpdb->update(
            $table,
            [
                'name' => $name,
                'code' => $code,
                'native_name' => $native_name,
                'status' => $status
            ],
            ['id' => $id]
        );

        if ($result !== false) {
            wp_send_json_success(['message' => 'Language updated successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to update language']);
        }
    }

    public static function ajax_delete_language()
    {
        check_ajax_referer('skd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_languages';

        $id = intval($_POST['id'] ?? 0);

        $result = $wpdb->delete($table, ['id' => $id]);

        if ($result) {
            wp_send_json_success(['message' => 'Language deleted successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to delete language']);
        }
    }

    public static function ajax_get_languages()
    {
        // Check if requesting a single language by ID (for edit)
        $id = isset($_POST['id']) ? intval($_POST['id']) : (isset($_GET['id']) ? intval($_GET['id']) : 0);

        if ($id > 0) {
            check_ajax_referer('skd_admin_nonce', 'nonce');

            if (!current_user_can('manage_options')) {
                wp_send_json_error(['message' => 'Permission denied']);
            }

            global $wpdb;
            $table = $wpdb->prefix . 'skd_pl_languages';
            $language = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));

            if ($language) {
                wp_send_json_success(['languages' => [$language]]);
            } else {
                wp_send_json_error(['message' => 'Language not found']);
            }
            return;
        }

        // Otherwise, return list for Select2
        $search = sanitize_text_field($_GET['q'] ?? '');

        $args = [
            'status' => 'active',
            'search' => $search
        ];

        $languages = self::get_languages($args);

        $results = [];
        foreach ($languages as $lang) {
            $results[] = [
                'id' => $lang->id,
                'text' => $lang->name
            ];
        }

        wp_send_json(['results' => $results]);
    }
}

SKD_PL_Languages::init();
