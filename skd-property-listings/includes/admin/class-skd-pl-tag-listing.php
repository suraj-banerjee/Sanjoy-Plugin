<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class SKD_Plugin_Tag_Listing
{
    private $table_name;

    public function __construct()
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'skd_pl_tags';
    }

    // Render the Tag Listing page
    public function render_tag_listing_page()
    {
        global $wpdb;

        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';
        $tag_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        // Fetch the tag details only when editing
        $tag = null;
        if ($action === 'edit' && $tag_id) {
            $tag = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id = %d", $tag_id));
        }

        // Handle form submission and other actions here
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handle_form_submission($action, $tag_id);
        }

        // Handle delete action
        if ($action === 'delete' && $tag_id) {
            $this->delete_tag($tag_id);
        }

        // Get all tags for listing
        $tags = $this->get_all_tags();

        // Pass the data to the template
        include SKD_PL_PLUGIN_PATH . 'templates/admin/tag/tag-listing.php';
    }

    // Handle form submission for Add/Edit
    private function handle_form_submission($action, $tag_id)
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
        $existing_tag = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $this->table_name WHERE slug = %s AND id != %d",
            $slug,
            $tag_id
        ));

        if ($existing_tag) {
            wp_redirect(add_query_arg(['message' => 'duplicate'], admin_url('admin.php?page=skd-pl-tags')));
            exit;
        }

        if ($action === 'edit' && $tag_id) {
            $wpdb->update(
                $this->table_name,
                ['name' => $name, 'slug' => $slug, 'description' => $description],
                ['id' => $tag_id]
            );
        } else {
            $wpdb->insert(
                $this->table_name,
                ['name' => $name, 'slug' => $slug, 'description' => $description]
            );
        }

        wp_redirect(add_query_arg(['message' => 'success'], admin_url('admin.php?page=skd-pl-tags')));
        exit;
    }

    // Delete a tag
    private function delete_tag($tag_id)
    {
        global $wpdb;
        $wpdb->delete($this->table_name, ['id' => $tag_id]);
        wp_redirect(add_query_arg(['message' => 'deleted'], admin_url('admin.php?page=skd-pl-tags')));
        exit;
    }

    // Get all tags
    private function get_all_tags()
    {
        global $wpdb;
        return $wpdb->get_results("SELECT * FROM $this->table_name ORDER BY id DESC");
    }
}
