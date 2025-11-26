<?php

if (!defined('ABSPATH')) {
    exit;
}

class SKD_PL_Certifications
{
    public static function init()
    {
        add_action('wp_ajax_skd_add_certification_master', [__CLASS__, 'ajax_add_certification']);
        add_action('wp_ajax_skd_update_certification_master', [__CLASS__, 'ajax_update_certification']);
        add_action('wp_ajax_skd_delete_certification_master', [__CLASS__, 'ajax_delete_certification']);
        add_action('wp_ajax_skd_get_certifications_master', [__CLASS__, 'ajax_get_certifications']);
        add_action('wp_ajax_skd_reorder_certifications', [__CLASS__, 'ajax_reorder_certifications']);
        add_action('wp_ajax_skd_get_user_certifications', [__CLASS__, 'ajax_get_user_certifications']);
        add_action('wp_ajax_skd_approve_user_certification', [__CLASS__, 'ajax_approve_user_certification']);
        add_action('wp_ajax_skd_reject_user_certification', [__CLASS__, 'ajax_reject_user_certification']);
    }

    public static function render_page()
    {
        include SKD_PL_PLUGIN_PATH . 'templates/admin/certifications/list.php';
    }

    /**
     * Get all certifications
     */
    public static function get_certifications($args = [])
    {
        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_certifications';

        $defaults = [
            'status' => '',
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
            $where[] = '(name LIKE %s OR issuer LIKE %s OR description LIKE %s)';
            $values[] = '%' . $wpdb->esc_like($args['search']) . '%';
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
     * Add new certification
     */
    public static function ajax_add_certification()
    {
        check_ajax_referer('skd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_certifications';

        $name = sanitize_text_field($_POST['name'] ?? '');
        $issuer = sanitize_text_field($_POST['issuer'] ?? '');
        $description = sanitize_textarea_field($_POST['description'] ?? '');
        $verification_required = isset($_POST['verification_required']) ? 1 : 0;
        $sort_order = intval($_POST['sort_order'] ?? 0);

        if (empty($name)) {
            wp_send_json_error(['message' => 'Certification name is required']);
        }

        $slug = sanitize_title($name);

        // Check if slug exists
        $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table WHERE slug = %s", $slug));
        if ($exists) {
            wp_send_json_error(['message' => 'A certification with this name already exists']);
        }

        $data = [
            'name' => $name,
            'slug' => $slug,
            'issuer' => $issuer,
            'description' => $description,
            'verification_required' => $verification_required,
            'sort_order' => $sort_order,
            'status' => 'active'
        ];

        // Handle badge image upload - only if a file was actually selected
        if (!empty($_FILES['badge_image']) && !empty($_FILES['badge_image']['name'])) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');

            $upload = wp_handle_upload($_FILES['badge_image'], ['test_form' => false]);

            if (isset($upload['error'])) {
                wp_send_json_error(['message' => $upload['error']]);
            }

            $data['badge_image_url'] = $upload['url'];
        }

        $result = $wpdb->insert($table, $data);

        if ($result) {
            wp_send_json_success([
                'message' => 'Certification added successfully',
                'id' => $wpdb->insert_id
            ]);
        } else {
            wp_send_json_error(['message' => 'Failed to add certification']);
        }
    }

    /**
     * Update certification
     */
    public static function ajax_update_certification()
    {
        check_ajax_referer('skd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        $id = intval($_POST['id'] ?? 0);
        if (!$id) {
            wp_send_json_error(['message' => 'Invalid certification ID']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_certifications';

        $data = [
            'name' => sanitize_text_field($_POST['name'] ?? ''),
            'issuer' => sanitize_text_field($_POST['issuer'] ?? ''),
            'description' => sanitize_textarea_field($_POST['description'] ?? ''),
            'verification_required' => isset($_POST['verification_required']) ? 1 : 0,
            'sort_order' => intval($_POST['sort_order'] ?? 0),
            'status' => sanitize_text_field($_POST['status'] ?? 'active')
        ];

        if (empty($data['name'])) {
            wp_send_json_error(['message' => 'Certification name is required']);
        }

        // Handle badge image upload - only if a file was actually selected
        if (!empty($_FILES['badge_image']) && !empty($_FILES['badge_image']['name'])) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');

            $upload = wp_handle_upload($_FILES['badge_image'], ['test_form' => false]);

            if (isset($upload['error'])) {
                wp_send_json_error(['message' => $upload['error']]);
            }

            $data['badge_image_url'] = $upload['url'];
        }

        $result = $wpdb->update($table, $data, ['id' => $id]);

        if ($result !== false) {
            wp_send_json_success(['message' => 'Certification updated successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to update certification']);
        }
    }

    /**
     * Delete certification
     */
    public static function ajax_delete_certification()
    {
        check_ajax_referer('skd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        $id = intval($_POST['id'] ?? 0);
        if (!$id) {
            wp_send_json_error(['message' => 'Invalid certification ID']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_certifications';

        $result = $wpdb->delete($table, ['id' => $id]);

        if ($result) {
            wp_send_json_success(['message' => 'Certification deleted successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to delete certification']);
        }
    }

    /**
     * Get certifications via AJAX
     */
    public static function ajax_get_certifications()
    {
        check_ajax_referer('skd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        $certifications = self::get_certifications();
        wp_send_json_success(['certifications' => $certifications]);
    }

    /**
     * Reorder certifications
     */
    public static function ajax_reorder_certifications()
    {
        check_ajax_referer('skd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        $order = $_POST['order'] ?? [];

        if (empty($order) || !is_array($order)) {
            wp_send_json_error(['message' => 'Invalid order data']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_certifications';

        // Update sort order for each certification
        foreach ($order as $index => $id) {
            $wpdb->update(
                $table,
                ['sort_order' => $index + 1],
                ['id' => intval($id)]
            );
        }

        wp_send_json_success(['message' => 'Order updated successfully']);
    }

    /**
     * Get user certifications with user details
     */
    public static function ajax_get_user_certifications()
    {
        check_ajax_referer('skd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        global $wpdb;

        $status = sanitize_text_field($_POST['status'] ?? '');

        $query = "
            SELECT 
                uc.*,
                c.name as cert_name,
                c.issuer,
                u.display_name,
                u.user_email
            FROM {$wpdb->prefix}skd_pl_user_certifications uc
            LEFT JOIN {$wpdb->prefix}skd_pl_certifications c ON uc.certification_id = c.id
            LEFT JOIN {$wpdb->users} u ON uc.user_id = u.ID
            WHERE 1=1
        ";

        if ($status) {
            $query .= $wpdb->prepare(" AND uc.status = %s", $status);
        }

        $query .= " ORDER BY uc.created_at DESC";

        $user_certs = $wpdb->get_results($query);

        wp_send_json_success(['user_certifications' => $user_certs]);
    }

    /**
     * Approve user certification
     */
    public static function ajax_approve_user_certification()
    {
        check_ajax_referer('skd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        $id = intval($_POST['id'] ?? 0);
        if (!$id) {
            wp_send_json_error(['message' => 'Invalid certification ID']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_user_certifications';

        $result = $wpdb->update(
            $table,
            [
                'status' => 'approved',
                'verification_date' => current_time('mysql')
            ],
            ['id' => $id]
        );

        if ($result !== false) {
            wp_send_json_success(['message' => 'Certification approved successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to approve certification']);
        }
    }

    /**
     * Reject user certification
     */
    public static function ajax_reject_user_certification()
    {
        check_ajax_referer('skd_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        $id = intval($_POST['id'] ?? 0);
        if (!$id) {
            wp_send_json_error(['message' => 'Invalid certification ID']);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_user_certifications';

        $result = $wpdb->update(
            $table,
            ['status' => 'rejected'],
            ['id' => $id]
        );

        if ($result !== false) {
            wp_send_json_success(['message' => 'Certification rejected']);
        } else {
            wp_send_json_error(['message' => 'Failed to reject certification']);
        }
    }
}

SKD_PL_Certifications::init();
