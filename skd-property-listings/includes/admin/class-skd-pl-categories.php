<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class SKD_Plugin_Category_Management
{
    private $table_name;

    public function __construct()
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'skd_pl_categories';
    }

    // Render the Categories page
    public function render_categories_page()
    {
        global $wpdb;

        $actionCat = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';
        $category_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        // Fetch the category details only when editing
        $category = null;
        if ($actionCat === 'edit' && $category_id) {
            $category = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id = %d", $category_id));
        }

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handle_cat_form_submission($actionCat, $category_id);
        }

        // Handle delete action
        if ($actionCat === 'delete' && $category_id) {
            $this->delete_category($category_id);
        }

        // Fetch all categories for listing
        $categories = $this->get_all_categories();

        // Pass the data to the template
        include SKD_PL_PLUGIN_PATH . 'templates/admin/category/categories.php';
    }

    // Handle form submission for Add/Edit
    private function handle_cat_form_submission($action, $category_id)
    {
        global $wpdb;

        $name = sanitize_text_field($_POST['name']);
        $slug = sanitize_title($_POST['slug']);
        $parent_id = intval($_POST['parent_id']);
        $description = sanitize_textarea_field($_POST['description']);
        $icon_url = esc_url_raw($_POST['icon_url']);
        $image_url = esc_url_raw($_POST['image_url']);

        if (empty($slug)) {
            $slug = sanitize_title($name);
        }

        // Check for duplicate slug
        $existing_category = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE slug = %s AND id != %d",
            $slug,
            $category_id
        ));

        if ($existing_category) {
            wp_redirect(add_query_arg(['message' => 'duplicate'], admin_url('admin.php?page=skd-pl-categories')));
            exit;
        }

        if ($action === 'edit' && $category_id) {
            $wpdb->update(
                $this->table_name,
                compact('name', 'slug', 'parent_id', 'description', 'icon_url', 'image_url'),
                ['id' => $category_id]
            );
        } else {
            $wpdb->insert(
                $this->table_name,
                compact('name', 'slug', 'parent_id', 'description', 'icon_url', 'image_url')
            );
        }

        wp_redirect(add_query_arg(['message' => 'success'], admin_url('admin.php?page=skd-pl-categories')));
        exit;
    }

    // Delete a category and its descendants
    private function delete_category($category_id)
    {
        global $wpdb;

        // Fetch the parent of the category to be deleted
        $parent_category = $wpdb->get_var($wpdb->prepare(
            "SELECT parent_id FROM {$this->table_name} WHERE id = %d",
            $category_id
        ));

        // Reassign child categories to the parent of the category being deleted
        $wpdb->update(
            $this->table_name,
            ['parent_id' => $parent_category], // Set new parent
            ['parent_id' => $category_id]     // Update children
        );

        // Delete the current category
        $wpdb->delete($this->table_name, ['id' => $category_id]);

        // Redirect with success message
        wp_redirect(add_query_arg(['message' => 'deleted'], admin_url('admin.php?page=skd-pl-categories')));
        exit;
    }

    // Fetch all categories with parent name
    private function get_all_categories()
    {
        global $wpdb;

        $query = "
            SELECT c.*, 
                   p.name AS parent_name 
            FROM {$this->table_name} c
            LEFT JOIN {$this->table_name} p ON c.parent_id = p.id
            ORDER BY c.parent_id ASC, c.name ASC
        ";

        return $wpdb->get_results($query);
    }

    // Build hierarchical category dropdown, excluding current and its descendants
    private function get_category_hierarchy_options($categories, $current_category_id = 0, $exclude_id = 0, $depth = 0, $selected_parent_id = 0)
    {
        // Build a parent-child relationship map
        $parent_map = [];
        foreach ($categories as $category) {
            $parent_map[$category->parent_id][] = $category;
        }

        // Exclude the current category and its descendants
        $excluded_ids = $exclude_id ? $this->get_descendants($categories, $exclude_id) : [];
        $excluded_ids[] = $exclude_id; // Include the exclude_id itself

        // Generate dropdown options
        return $this->build_hierarchy($parent_map, $current_category_id, $excluded_ids, $depth, $selected_parent_id);
    }

    // Build the dropdown recursively from the parent map
    private function build_hierarchy($parent_map, $current_category_id, $excluded_ids, $depth, $selected_parent_id)
    {
        $output = '';

        if (!isset($parent_map[$current_category_id])) {
            return $output; // No children for this parent
        }

        foreach ($parent_map[$current_category_id] as $category) {
            // Skip excluded categories
            if (in_array($category->id, $excluded_ids)) {
                continue;
            }

            // Indentation for hierarchy
            $indent = str_repeat('&nbsp;&nbsp;', $depth);

            // Add option
            $output .= sprintf(
                '<option value="%d"%s>%s%s</option>',
                $category->id,
                selected($category->id, $selected_parent_id, false), // Pre-select the parent category
                $indent,
                esc_html($category->name)
            );

            // Recursively add child categories
            $output .= $this->build_hierarchy($parent_map, $category->id, $excluded_ids, $depth + 1, $selected_parent_id);
        }

        return $output;
    }

    // Get all descendant IDs of a category
    private function get_descendants($categories, $parent_id)
    {
        $descendants = [];
        foreach ($categories as $category) {
            if ($category->parent_id == $parent_id) {
                $descendants[] = $category->id;
                $descendants = array_merge($descendants, $this->get_descendants($categories, $category->id));
            }
        }
        return $descendants;
    }



    // Function to render categories in a hierarchical structure
    private function render_hierarchical_category_list($categories, $parent_id = 0, $depth = 0)
    {
        $output = '';

        foreach ($categories as $category) {
            if ($category->parent_id == $parent_id) {
                $indent = str_repeat('&mdash; ', $depth); // Add visual indentation

                $output .= '<tr>';
                $output .= '<td>' . esc_html($category->id) . '</td>';
                $output .= '<td>' . $indent . esc_html($category->name) . '</td>';
                $output .= '<td>' . esc_html($category->slug) . '</td>';
                $output .= '<td>' . esc_html($category->description) . '</td>';
                $output .= '<td>' . ($category->icon_url ? '<img src="' . esc_url($category->icon_url) . '" alt="" style="width:20px;height:20px;">' : '-') . '</td>';
                $output .= '<td>' . ($category->image_url ? '<img src="' . esc_url($category->image_url) . '" alt="" style="width:50px;">' : '-') . '</td>';
                $output .= '<td>';
                $output .= '<a href="' . esc_url(add_query_arg(['action' => 'edit', 'id' => $category->id], admin_url('admin.php?page=skd-pl-categories'))) . '">' . esc_html__('Edit', 'skd-property-listings') . '</a> | ';
                $output .= '<a href="' . esc_url(add_query_arg(['action' => 'delete', 'id' => $category->id], admin_url('admin.php?page=skd-pl-categories'))) . '" onclick="return confirm(\'' . esc_html__('You are about to permanently delete this item. This action cannot be undone. Are you sure you want to delete this?', 'skd-property-listings') . '\');">' . esc_html__('Delete', 'skd-property-listings') . '</a>';
                $output .= '</td>';
                $output .= '</tr>';

                // Recursively render child categories
                $output .= $this->render_hierarchical_category_list($categories, $category->id, $depth + 1);
            }
        }

        return $output;
    }
}
