<?php

if (!defined('ABSPATH')) {
    exit;
}

class SKD_PL_VDA_Specializations
{
    public static function init()
    {
        // Menu is registered in class-skd-pl-admin-menu.php
        add_action('wp_ajax_skd_add_specialization', [__CLASS__, 'ajax_add_specialization']);
        add_action('wp_ajax_skd_update_specialization', [__CLASS__, 'ajax_update_specialization']);
        add_action('wp_ajax_skd_delete_specialization', [__CLASS__, 'ajax_delete_specialization']);
        add_action('wp_ajax_skd_get_specializations', [__CLASS__, 'ajax_get_specializations']);
        add_action('wp_ajax_skd_create_specialization_auto', [__CLASS__, 'ajax_create_specialization_auto']);
    }

    public static function render_page()
    {
        include SKD_PL_PLUGIN_PATH . 'templates/admin/specializations/list.php';
    }

    public static function get_specializations($args = [])
    {
        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_specializations';

        $defaults = [
            'status' => 'active',
            'search' => '',
            'orderby' => 'sort_order',
            'order' => 'ASC'
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

    public static function ajax_add_specialization()
    {
        check_ajax_referer('skd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_specializations';

        $name = sanitize_text_field($_POST['name'] ?? '');
        $slug = !empty($_POST['slug']) ? sanitize_title($_POST['slug']) : sanitize_title($name);
        $description = sanitize_textarea_field($_POST['description'] ?? '');

        if (empty($name)) {
            wp_send_json_error(['message' => 'Specialization name is required']);
        }

        // Check if slug exists
        $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table WHERE slug = %s", $slug));
        if ($exists) {
            wp_send_json_error(['message' => 'A specialization with this name already exists']);
        }

        $result = $wpdb->insert($table, [
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'status' => 'active'
        ]);

        if ($result) {
            wp_send_json_success([
                'message' => 'Specialization added successfully',
                'id' => $wpdb->insert_id
            ]);
        } else {
            wp_send_json_error(['message' => 'Failed to add specialization']);
        }
    }

    public static function ajax_update_specialization()
    {
        check_ajax_referer('skd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_specializations';

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
            wp_send_json_error(['message' => 'A specialization with this name already exists']);
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
            wp_send_json_success(['message' => 'Specialization updated successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to update specialization']);
        }
    }

    public static function ajax_delete_specialization()
    {
        check_ajax_referer('skd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_specializations';

        $id = intval($_POST['id'] ?? 0);

        $result = $wpdb->delete($table, ['id' => $id]);

        if ($result) {
            wp_send_json_success(['message' => 'Specialization deleted successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to delete specialization']);
        }
    }

    public static function ajax_get_specializations()
    {
        // Check if requesting a single specialization by ID (for edit)
        $id = isset($_POST['id']) ? intval($_POST['id']) : (isset($_GET['id']) ? intval($_GET['id']) : 0);

        if ($id > 0) {
            check_ajax_referer('skd_admin_nonce', 'nonce');

            if (!current_user_can('manage_options')) {
                wp_send_json_error(['message' => 'Permission denied']);
            }

            global $wpdb;
            $table = $wpdb->prefix . 'skd_pl_specializations';
            $spec = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));

            if ($spec) {
                wp_send_json_success(['specializations' => [$spec]]);
            } else {
                wp_send_json_error(['message' => 'Specialization not found']);
            }
            return;
        }

        // Otherwise, return list for Select2
        $search = sanitize_text_field($_GET['q'] ?? '');

        $args = [
            'status' => 'active',
            'search' => $search
        ];

        $specializations = self::get_specializations($args);

        $results = [];
        foreach ($specializations as $spec) {
            $results[] = [
                'id' => $spec->id,
                'text' => $spec->name
            ];
        }

        wp_send_json(['results' => $results]);
    }

    public static function ajax_create_specialization_auto()
    {
        check_ajax_referer('skd_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'You must be logged in']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_specializations';

        $name = sanitize_text_field($_POST['name'] ?? '');

        if (empty($name)) {
            wp_send_json_error(['message' => 'Specialization name is required']);
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
            wp_send_json_error(['message' => 'Failed to create specialization']);
        }
    }
}

SKD_PL_VDA_Specializations::init();
