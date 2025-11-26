<?php

if (!defined('ABSPATH')) {
    exit;
}

class SKD_PL_Experience_Levels
{
    public static function init()
    {
        add_action('wp_ajax_skd_add_experience_level', [__CLASS__, 'ajax_add_experience_level']);
        add_action('wp_ajax_skd_update_experience_level', [__CLASS__, 'ajax_update_experience_level']);
        add_action('wp_ajax_skd_delete_experience_level', [__CLASS__, 'ajax_delete_experience_level']);
        add_action('wp_ajax_skd_get_experience_levels', [__CLASS__, 'ajax_get_experience_levels']);
    }

    public static function render_page()
    {
        include SKD_PL_PLUGIN_PATH . 'templates/admin/experience-levels/list.php';
    }

    public static function get_experience_levels($args = [])
    {
        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_experience_levels';

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

    /**
     * Get experience level based on years of experience
     */
    public static function get_level_by_years($years)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_experience_levels';

        $years = intval($years);

        $query = $wpdb->prepare(
            "SELECT * FROM $table 
            WHERE status = 'active' 
            AND years_min <= %d 
            AND (years_max IS NULL OR years_max >= %d)
            ORDER BY sort_order ASC
            LIMIT 1",
            $years,
            $years
        );

        return $wpdb->get_row($query);
    }

    public static function ajax_add_experience_level()
    {
        check_ajax_referer('skd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_experience_levels';

        $name = sanitize_text_field($_POST['name'] ?? '');
        $slug = !empty($_POST['slug']) ? sanitize_title($_POST['slug']) : sanitize_title($name);
        $description = sanitize_textarea_field($_POST['description'] ?? '');
        $years_min = isset($_POST['years_min']) ? intval($_POST['years_min']) : 0;
        $years_max = !empty($_POST['years_max']) ? intval($_POST['years_max']) : null;

        if (empty($name)) {
            wp_send_json_error(['message' => 'Experience level name is required']);
        }

        // Check if slug exists
        $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table WHERE slug = %s", $slug));
        if ($exists) {
            wp_send_json_error(['message' => 'An experience level with this name already exists']);
        }

        $result = $wpdb->insert($table, [
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'years_min' => $years_min,
            'years_max' => $years_max,
            'status' => 'active'
        ]);

        if ($result) {
            wp_send_json_success([
                'message' => 'Experience level added successfully',
                'id' => $wpdb->insert_id
            ]);
        } else {
            wp_send_json_error(['message' => 'Failed to add experience level']);
        }
    }

    public static function ajax_update_experience_level()
    {
        check_ajax_referer('skd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_experience_levels';

        $id = intval($_POST['id'] ?? 0);
        $name = sanitize_text_field($_POST['name'] ?? '');
        $slug = !empty($_POST['slug']) ? sanitize_title($_POST['slug']) : sanitize_title($name);
        $description = sanitize_textarea_field($_POST['description'] ?? '');
        $status = sanitize_text_field($_POST['status'] ?? 'active');
        $years_min = isset($_POST['years_min']) ? intval($_POST['years_min']) : 0;
        $years_max = !empty($_POST['years_max']) ? intval($_POST['years_max']) : null;

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
            wp_send_json_error(['message' => 'An experience level with this name already exists']);
        }

        $result = $wpdb->update(
            $table,
            [
                'name' => $name,
                'slug' => $slug,
                'description' => $description,
                'years_min' => $years_min,
                'years_max' => $years_max,
                'status' => $status
            ],
            ['id' => $id]
        );

        if ($result !== false) {
            wp_send_json_success(['message' => 'Experience level updated successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to update experience level']);
        }
    }

    public static function ajax_delete_experience_level()
    {
        check_ajax_referer('skd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_experience_levels';

        $id = intval($_POST['id'] ?? 0);

        $result = $wpdb->delete($table, ['id' => $id]);

        if ($result) {
            wp_send_json_success(['message' => 'Experience level deleted successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to delete experience level']);
        }
    }

    public static function ajax_get_experience_levels()
    {
        // Check if requesting a single experience level by ID (for edit)
        $id = isset($_POST['id']) ? intval($_POST['id']) : (isset($_GET['id']) ? intval($_GET['id']) : 0);

        if ($id > 0) {
            check_ajax_referer('skd_admin_nonce', 'nonce');

            if (!current_user_can('manage_options')) {
                wp_send_json_error(['message' => 'Permission denied']);
            }

            global $wpdb;
            $table = $wpdb->prefix . 'skd_pl_experience_levels';
            $level = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));

            if ($level) {
                wp_send_json_success(['experience_levels' => [$level]]);
            } else {
                wp_send_json_error(['message' => 'Experience level not found']);
            }
            return;
        }

        // Otherwise, return list for Select2
        $search = sanitize_text_field($_GET['q'] ?? '');

        $args = [
            'status' => 'active',
            'search' => $search
        ];

        $levels = self::get_experience_levels($args);

        $results = [];
        foreach ($levels as $level) {
            $results[] = [
                'id' => $level->id,
                'text' => $level->name
            ];
        }

        wp_send_json(['results' => $results]);
    }
}

SKD_PL_Experience_Levels::init();
