<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class SKD_Plugin_Feature_Listing
{
    private $table_name;

    public function __construct()
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'skd_pl_features';
    }

    // Render the Feature Listing page
    public function render_feature_listing_page()
    {
        global $wpdb; // Ensure the global wpdb is available

        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';
        $feature_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        // Fetch the feature details only when editing
        $feature = null;
        if ($action === 'edit' && $feature_id) {
            $feature = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id = %d", $feature_id));
        }

        // Handle form submission and other actions here
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handle_form_submission($action, $feature_id);
        }

        // Handle delete action
        if ($action === 'delete' && $feature_id) {
            $this->delete_feature($feature_id);
        }

        // Get all features for listing
        $features = $this->get_all_features();

        // Pass the data to the template
        include SKD_PL_PLUGIN_PATH . 'templates/admin/feature/feature-listing.php';
    }


    // Handle form submission for Add/Edit
    private function handle_form_submission($action, $feature_id)
    {
        global $wpdb;

        $name = sanitize_text_field($_POST['name']);
        $slug = sanitize_title($_POST['slug']);
        $description = sanitize_textarea_field($_POST['description']);

        // Generate slug if not provided
        if (empty($slug)) {
            $slug = sanitize_title($name);
        }

        // Check for duplicate entries
        $existing_feature = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $this->table_name WHERE slug = %s AND id != %d",
            $slug,
            $feature_id
        ));

        if ($existing_feature) {
            wp_redirect(add_query_arg(['message' => 'duplicate'], admin_url('admin.php?page=skd-pl-feature-listing')));
            exit;
        }

        if ($action === 'edit' && $feature_id) {
            $wpdb->update(
                $this->table_name,
                ['name' => $name, 'slug' => $slug, 'description' => $description],
                ['id' => $feature_id]
            );
        } else {
            $wpdb->insert(
                $this->table_name,
                ['name' => $name, 'slug' => $slug, 'description' => $description]
            );
        }

        wp_redirect(add_query_arg(['message' => 'success'], admin_url('admin.php?page=skd-pl-feature-listing')));
        exit;
    }

    // Delete a feature
    private function delete_feature($feature_id)
    {
        global $wpdb;
        $wpdb->delete($this->table_name, ['id' => $feature_id]);
        wp_redirect(add_query_arg(['message' => 'deleted'], admin_url('admin.php?page=skd-pl-feature-listing')));
        exit;
    }

    // Get all features
    private function get_all_features()
    {
        global $wpdb;
        return $wpdb->get_results("SELECT * FROM $this->table_name ORDER BY id DESC");
    }
}

// new SKD_Plugin_Feature_Listing();
