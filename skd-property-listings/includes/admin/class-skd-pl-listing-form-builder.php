<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class SKD_Plugin_Listing_Form_Builder
{
    private $table_name;

    public function __construct()
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'skd_form_builder';
        add_action('wp_ajax_nopriv_save_form_builder_fields', [$this, 'save_form_builder_fields']);
        add_action('wp_ajax_save_form_builder_fields', [$this, 'save_form_builder_fields']);
        add_action('wp_ajax_nopriv_get_form_builder_fields', [$this, 'get_form_builder_fields']);
        add_action('wp_ajax_get_form_builder_fields', [$this, 'get_form_builder_fields']);
    }

    // Render the form builder page
    public function render_form_builder_page()
    {
        global $wpdb;
        $fields = $wpdb->get_results("SELECT * FROM {$this->table_name} ORDER BY sort_order ASC");
        include SKD_PL_PLUGIN_PATH . 'templates/admin/listing-form-builder/listing-form-builder.php';
    }

    // Save form fields via AJAX
    function save_form_builder_fields()
    {
        global $wpdb;
        $table_name = $this->table_name;

        $fields = isset($_POST['fields']) ? json_decode($_POST['fields']) : [];

        if (empty($fields)) {
            wp_send_json_error(['message' => 'No fields to save.']);
            return;
        }

        foreach ($fields as $field) {
            if ($field['id']) {
                // Update existing field
                $wpdb->update(
                    $table_name,
                    [
                        'field_name' => sanitize_text_field($field['field_name']),
                        'field_type' => sanitize_text_field($field['field_type']),
                        'field_placeholder' => sanitize_text_field($field['field_placeholder']),
                        'field_required' => intval($field['field_required']),
                        'sort_order' => intval($field['sort_order']),
                    ],
                    ['id' => intval($field['id'])]
                );
            } else {
                // Insert new field
                $wpdb->insert(
                    $table_name,
                    [
                        'field_name' => sanitize_text_field($field['field_name']),
                        'field_type' => sanitize_text_field($field['field_type']),
                        'field_placeholder' => sanitize_text_field($field['field_placeholder']),
                        'field_required' => intval($field['field_required']),
                        'sort_order' => intval($field['sort_order']),
                    ]
                );
            }
        }

        wp_send_json_success(['message' => 'Form saved successfully.']);
    }

    function get_form_builder_fields()
    {
        global $wpdb;
        $table_name = $this->table_name;

        $results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY sort_order ASC");

        wp_send_json_success($results);
    }
}
