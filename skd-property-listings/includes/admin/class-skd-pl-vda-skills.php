<?php

if (!defined('ABSPATH')) {
    exit;
}

class SKD_PL_VDA_Skills
{
    public static function init()
    {
        // Menu is registered in class-skd-pl-admin-menu.php
        add_action('wp_ajax_skd_add_skill', [__CLASS__, 'ajax_add_skill']);
        add_action('wp_ajax_skd_update_skill', [__CLASS__, 'ajax_update_skill']);
        add_action('wp_ajax_skd_delete_skill', [__CLASS__, 'ajax_delete_skill']);
        add_action('wp_ajax_skd_get_skills', [__CLASS__, 'ajax_get_skills']);

        // Frontend AJAX for auto-create
        add_action('wp_ajax_skd_create_skill_auto', [__CLASS__, 'ajax_create_skill_auto']);
    }

    public static function render_page()
    {
        include SKD_PL_PLUGIN_PATH . 'templates/admin/skills/list.php';
    }

    /**
     * Get all skills
     */
    public static function get_skills($args = [])
    {
        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_skills';

        $defaults = [
            'status' => 'active',
            'category' => '',
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

        if (!empty($args['category'])) {
            $where[] = 'category = %s';
            $values[] = $args['category'];
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
     * Add new skill
     */
    public static function ajax_add_skill()
    {
        check_ajax_referer('skd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_skills';

        $name = sanitize_text_field($_POST['name'] ?? '');
        $slug = !empty($_POST['slug']) ? sanitize_title($_POST['slug']) : sanitize_title($name);
        $description = sanitize_textarea_field($_POST['description'] ?? '');

        if (empty($name)) {
            wp_send_json_error(['message' => 'Software name is required']);
        }

        // Check if slug exists
        $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table WHERE slug = %s", $slug));
        if ($exists) {
            wp_send_json_error(['message' => 'A software with this name already exists']);
        }

        $result = $wpdb->insert($table, [
            'name' => $name,
            'slug' => $slug,
            'category' => 'software',
            'description' => $description,
            'is_featured' => 0,
            'sort_order' => 0,
            'status' => 'active'
        ]);

        if ($result) {
            wp_send_json_success([
                'message' => 'Software added successfully',
                'id' => $wpdb->insert_id
            ]);
        } else {
            wp_send_json_error(['message' => 'Failed to add software']);
        }
    }

    /**
     * Update skill
     */
    public static function ajax_update_skill()
    {
        check_ajax_referer('skd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_skills';

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
            wp_send_json_error(['message' => 'A software with this name already exists']);
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
            wp_send_json_success(['message' => 'Software updated successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to update software']);
        }
    }

    /**
     * Delete skill
     */
    public static function ajax_delete_skill()
    {
        check_ajax_referer('skd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_skills';

        $id = intval($_POST['id'] ?? 0);

        if (empty($id)) {
            wp_send_json_error(['message' => 'Invalid ID']);
        }

        $result = $wpdb->delete($table, ['id' => $id]);

        if ($result) {
            wp_send_json_success(['message' => 'Skill deleted successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to delete skill']);
        }
    }

    /**
     * Get skills for AJAX (used in Select2 and Edit)
     */
    public static function ajax_get_skills()
    {
        // Check if requesting a single skill by ID (for edit)
        $id = isset($_POST['id']) ? intval($_POST['id']) : (isset($_GET['id']) ? intval($_GET['id']) : 0);

        if ($id > 0) {
            check_ajax_referer('skd_admin_nonce', 'nonce');

            if (!current_user_can('manage_options')) {
                wp_send_json_error(['message' => 'Permission denied']);
            }

            global $wpdb;
            $table = $wpdb->prefix . 'skd_pl_skills';
            $skill = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));

            if ($skill) {
                wp_send_json_success(['skills' => [$skill]]);
            } else {
                wp_send_json_error(['message' => 'Software not found']);
            }
            return;
        }

        // Otherwise, return list for Select2
        $search = sanitize_text_field($_GET['q'] ?? '');
        $category = sanitize_text_field($_GET['category'] ?? '');

        $args = [
            'status' => 'active',
            'search' => $search
        ];

        if (!empty($category)) {
            $args['category'] = $category;
        }

        $skills = self::get_skills($args);

        $results = [];
        foreach ($skills as $skill) {
            $results[] = [
                'id' => $skill->id,
                'text' => $skill->name
            ];
        }

        wp_send_json(['results' => $results]);
    }

    /**
     * Auto-create skill from frontend (for Select2)
     */
    public static function ajax_create_skill_auto()
    {
        check_ajax_referer('skd_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'You must be logged in']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_skills';

        $name = sanitize_text_field($_POST['name'] ?? '');
        $category = sanitize_text_field($_POST['category'] ?? 'software');

        if (empty($name)) {
            wp_send_json_error(['message' => 'Skill name is required']);
        }

        $slug = sanitize_title($name);

        // Check if already exists
        $existing = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE slug = %s", $slug));
        if ($existing) {
            wp_send_json_success([
                'id' => $existing->id,
                'name' => $existing->name,
                'exists' => true
            ]);
            return;
        }

        // Create new skill
        $result = $wpdb->insert($table, [
            'name' => $name,
            'slug' => $slug,
            'category' => $category,
            'status' => 'active',
            'sort_order' => 999 // Put auto-created at end
        ]);

        if ($result) {
            wp_send_json_success([
                'id' => $wpdb->insert_id,
                'name' => $name,
                'exists' => false
            ]);
        } else {
            wp_send_json_error(['message' => 'Failed to create skill']);
        }
    }
}

SKD_PL_VDA_Skills::init();
