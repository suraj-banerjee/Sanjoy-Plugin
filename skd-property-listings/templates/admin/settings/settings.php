<?php if (!defined('ABSPATH')) {
    exit;
} ?>

<div class="wrap">
    <h1>Plugin Settings</h1>
    <form method="post">
        <?php wp_nonce_field('skd_plugin_settings_save'); ?>

        <table class="form-table">
            <tr valign="top">
                <th scope="row"><label for="skd_map_api_key">Google Map API Key</label></th>
                <td><input type="text" id="skd_map_api_key" name="skd_map_api_key" value="<?php echo esc_attr($map_api_key); ?>" class="regular-text" /></td>
            </tr>

            <tr valign="top">
                <th scope="row"><label for="skd_stripe_api_key">Stripe Secret API Key</label></th>
                <td><input type="text" id="skd_stripe_api_key" name="skd_stripe_api_key" value="<?php echo esc_attr($stripe_api_key); ?>" class="regular-text" /></td>
            </tr>
        </table>

        <?php submit_button('Save Settings'); ?>
    </form>
</div>