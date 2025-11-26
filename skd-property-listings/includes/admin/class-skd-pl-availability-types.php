<?php

if (!defined('ABSPATH')) {
    exit;
}

class SKD_PL_Availability_Types
{
    public static function init()
    {
        add_action('admin_menu', [__CLASS__, 'add_menu_page'], 35);
        add_action('wp_ajax_skd_add_availability_type', [__CLASS__, 'ajax_add_availability_type']);
        add_action('wp_ajax_skd_update_availability_type', [__CLASS__, 'ajax_update_availability_type']);
        add_action('wp_ajax_skd_delete_availability_type', [__CLASS__, 'ajax_delete_availability_type']);
        add_action('wp_ajax_skd_get_availability_types', [__CLASS__, 'ajax_get_availability_types']);
    }

    public static function add_menu_page()
    {
        add_submenu_page(
            'skd-pl-all-listings',
            'Availability Types',
            'Availability Types',
            'manage_options',
            'skd-availability-types',
            [__CLASS__, 'render_page']
        );
    }

    public static function render_page()
    {
        include SKD_PL_PLUGIN_PATH . 'templates/admin/availability-types/list.php';
    }

    public static function get_availability_types($args = [])
    {
        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_availability_types';

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

    public static function ajax_add_availability_type()
    {
        check_ajax_referer('skd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_availability_types';

        $data = [
            'name' => sanitize_text_field($_POST['name']),
            'slug' => !empty($_POST['slug']) ? sanitize_title($_POST['slug']) : sanitize_title($_POST['name']),
            'description' => sanitize_textarea_field($_POST['description'] ?? ''),
            'sort_order' => intval($_POST['sort_order'] ?? 0),
            'status' => sanitize_text_field($_POST['status'] ?? 'active'),
        ];

        $result = $wpdb->insert($table, $data);

        if ($result) {
            wp_send_json_success(['message' => 'Availability type added successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to add availability type']);
        }
    }

    public static function ajax_update_availability_type()
    {
        check_ajax_referer('skd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_availability_types';

        $id = intval($_POST['id']);
        $data = [
            'name' => sanitize_text_field($_POST['name']),
            'slug' => !empty($_POST['slug']) ? sanitize_title($_POST['slug']) : sanitize_title($_POST['name']),
            'description' => sanitize_textarea_field($_POST['description'] ?? ''),
            'sort_order' => intval($_POST['sort_order'] ?? 0),
            'status' => sanitize_text_field($_POST['status'] ?? 'active'),
        ];

        $result = $wpdb->update($table, $data, ['id' => $id]);

        if ($result !== false) {
            wp_send_json_success(['message' => 'Availability type updated successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to update availability type']);
        }
    }

    public static function ajax_delete_availability_type()
    {
        check_ajax_referer('skd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_availability_types';
        $id = intval($_POST['id']);

        $result = $wpdb->delete($table, ['id' => $id]);

        if ($result) {
            wp_send_json_success(['message' => 'Availability type deleted successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to delete availability type']);
        }
    }

    public static function ajax_get_availability_types()
    {
        check_ajax_referer('skd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        if ($id > 0) {
            $availability_types = self::get_availability_types(['status' => '']);
            $availability_types = array_filter($availability_types, function ($item) use ($id) {
                return $item->id == $id;
            });
            $availability_types = array_values($availability_types);
        } else {
            $availability_types = self::get_availability_types();
        }

        wp_send_json_success(['availability_types' => $availability_types]);
    }
}

SKD_PL_Availability_Types::init();
