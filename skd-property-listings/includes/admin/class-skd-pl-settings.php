<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class SKD_Plugin_Settings
{
    public function render_settings_page()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_admin_referer('skd_plugin_settings_save')) {
            $this->handle_form_submission();
            echo '<div class="updated"><p>Settings saved.</p></div>';
        }

        // Get current option values
        $map_api_key = get_option('skd_map_api_key', '');
        $stripe_api_key = get_option('skd_stripe_api_key', '');

        // Include the template
        include SKD_PL_PLUGIN_PATH . 'templates/admin/settings/settings.php';
    }

    private function handle_form_submission()
    {
        // Sanitize and save settings
        if (isset($_POST['skd_map_api_key'])) {
            update_option('skd_map_api_key', sanitize_text_field($_POST['skd_map_api_key']));
        }

        if (isset($_POST['skd_stripe_api_key'])) {
            update_option('skd_stripe_api_key', sanitize_text_field($_POST['skd_stripe_api_key']));
        }
    }
}
