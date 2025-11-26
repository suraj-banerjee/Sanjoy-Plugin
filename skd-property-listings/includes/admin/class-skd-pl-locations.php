<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class SKD_Plugin_Location_Management
{
    private $table_name;

    public function __construct()
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'skd_pl_locations';
    }

    // Render the Locations page
    public function render_locations_page()
    {
        global $wpdb;

        $actionLoc = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';
        $location_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        // Fetch the location details only when editing
        $location = null;
        if ($actionLoc === 'edit' && $location_id) {
            $location = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id = %d", $location_id));
        }

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handle_loc_form_submission($actionLoc, $location_id);
        }

        // Handle delete action
        if ($actionLoc === 'delete' && $location_id) {
            $this->delete_location($location_id);
        }

        // Fetch all locations for listing
        $locations = $this->get_all_locations();

        // Pass the data to the template
        include SKD_PL_PLUGIN_PATH . 'templates/admin/location/locations.php';
    }

    // Handle form submission for Add/Edit
    private function handle_loc_form_submission($action, $location_id)
    {
        global $wpdb;

        $name = sanitize_text_field($_POST['name']);
        $slug = sanitize_title($_POST['slug']);
        $parent_id = intval($_POST['parent_id']);
        $description = sanitize_textarea_field($_POST['description']);
        $image_url = esc_url_raw($_POST['image_url']);

        if (empty($slug)) {
            $slug = sanitize_title($name);
        }

        // Check for duplicate slug
        $existing_location = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE slug = %s AND id != %d",
            $slug,
            $location_id
        ));

        if ($existing_location) {
            wp_redirect(add_query_arg(['message' => 'duplicate'], admin_url('admin.php?page=skd-pl-locations')));
            exit;
        }

        if ($action === 'edit' && $location_id) {
            $wpdb->update(
                $this->table_name,
                compact('name', 'slug', 'parent_id', 'description', 'image_url'),
                ['id' => $location_id]
            );
        } else {
            $wpdb->insert(
                $this->table_name,
                compact('name', 'slug', 'parent_id', 'description', 'image_url')
            );
        }

        wp_redirect(add_query_arg(['message' => 'success'], admin_url('admin.php?page=skd-pl-locations')));
        exit;
    }

    // Delete a location and reassign its children to its parent
    private function delete_location($location_id)
    {
        global $wpdb;

        // Fetch the parent of the location to be deleted
        $parent_location = $wpdb->get_var($wpdb->prepare(
            "SELECT parent_id FROM {$this->table_name} WHERE id = %d",
            $location_id
        ));

        // Reassign child locations to the parent of the location being deleted
        $wpdb->update(
            $this->table_name,
            ['parent_id' => $parent_location],
            ['parent_id' => $location_id]
        );

        // Delete the current location
        $wpdb->delete($this->table_name, ['id' => $location_id]);

        // Redirect with success message
        wp_redirect(add_query_arg(['message' => 'deleted'], admin_url('admin.php?page=skd-pl-locations')));
        exit;
    }

    // Fetch all locations with parent name
    private function get_all_locations()
    {
        global $wpdb;

        $query = "
            SELECT l.*, 
                   p.name AS parent_name 
            FROM {$this->table_name} l
            LEFT JOIN {$this->table_name} p ON l.parent_id = p.id
            ORDER BY l.parent_id ASC, l.name ASC
        ";

        return $wpdb->get_results($query);
    }

    // Build hierarchical location dropdown
    private function get_location_hierarchy_options($locations, $current_location_id = 0, $exclude_id = 0, $depth = 0, $selected_parent_id = 0)
    {
        $parent_map = [];
        foreach ($locations as $location) {
            $parent_map[$location->parent_id][] = $location;
        }

        $excluded_ids = $exclude_id ? $this->get_descendants($locations, $exclude_id) : [];
        $excluded_ids[] = $exclude_id;

        return $this->build_hierarchy($parent_map, $current_location_id, $excluded_ids, $depth, $selected_parent_id);
    }

    private function build_hierarchy($parent_map, $current_location_id, $excluded_ids, $depth, $selected_parent_id)
    {
        $output = '';

        if (!isset($parent_map[$current_location_id])) {
            return $output;
        }

        foreach ($parent_map[$current_location_id] as $location) {
            if (in_array($location->id, $excluded_ids)) {
                continue;
            }

            $indent = str_repeat('&nbsp;&nbsp;', $depth);

            $output .= sprintf(
                '<option value="%d"%s>%s%s</option>',
                $location->id,
                selected($location->id, $selected_parent_id, false),
                $indent,
                esc_html($location->name)
            );

            $output .= $this->build_hierarchy($parent_map, $location->id, $excluded_ids, $depth + 1, $selected_parent_id);
        }

        return $output;
    }

    private function get_descendants($locations, $parent_id)
    {
        $descendants = [];
        foreach ($locations as $location) {
            if ($location->parent_id == $parent_id) {
                $descendants[] = $location->id;
                $descendants = array_merge($descendants, $this->get_descendants($locations, $location->id));
            }
        }
        return $descendants;
    }

    private function render_hierarchical_location_list($locations, $parent_id = 0, $depth = 0)
    {
        $output = '';

        foreach ($locations as $location) {
            if ($location->parent_id == $parent_id) {
                $indent = str_repeat('&mdash; ', $depth);

                $output .= '<tr>';
                $output .= '<td>' . esc_html($location->id) . '</td>';
                $output .= '<td>' . $indent . esc_html($location->name) . '</td>';
                $output .= '<td>' . esc_html($location->slug) . '</td>';
                $output .= '<td>' . esc_html($location->description) . '</td>';
                $output .= '<td>' . ($location->image_url ? '<img src="' . esc_url($location->image_url) . '" alt="" style="width:50px;">' : '-') . '</td>';
                $output .= '<td>';
                $output .= '<a href="' . esc_url(add_query_arg(['action' => 'edit', 'id' => $location->id], admin_url('admin.php?page=skd-pl-locations'))) . '">' . esc_html__('Edit', 'skd-property-listings') . '</a> | ';
                $output .= '<a href="' . esc_url(add_query_arg(['action' => 'delete', 'id' => $location->id], admin_url('admin.php?page=skd-pl-locations'))) . '" onclick="return confirm(\'' . esc_html__('You are about to permanently delete this item. This action cannot be undone. Are you sure you want to delete this?', 'skd-property-listings') . '\');">' . esc_html__('Delete', 'skd-property-listings') . '</a>';
                $output .= '</td>';
                $output .= '</tr>';

                $output .= $this->render_hierarchical_location_list($locations, $location->id, $depth + 1);
            }
        }

        return $output;
    }
}
