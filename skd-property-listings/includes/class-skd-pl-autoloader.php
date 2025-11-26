<?php

class SKD_Plugin_Autoloader
{
    public static function register()
    {
        spl_autoload_register([__CLASS__, 'autoload']);
    }

    private static function autoload($class)
    {
        if (strpos($class, 'SKD_') === false) {
            return;
        }
        $file = SKD_PL_PLUGIN_PATH . 'includes/' . str_replace('_', '-', strtolower($class)) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }

    public static function add_admin_scripts()
    {
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_scripts']);
        //enqueue scripts for the front-end
        add_action('wp_enqueue_scripts', [__CLASS__, 'web_enqueue_scripts']);
    }

    public static function enqueue_scripts()
    {
        wp_enqueue_style('skd-pl-admin-styles', SKD_PL_PLUGIN_URL . 'assets/css/admin-styles.css', [], SKD_PL_VERSION);

        // Enqueue WordPress media uploader scripts
        wp_enqueue_media();
        wp_enqueue_script('skd-pl-admin-scripts', SKD_PL_PLUGIN_URL . 'assets/js/admin-scripts.js', ['jquery'], SKD_PL_VERSION, true);
        //enque google map api
        if (defined('SKD_MAP_API_KEY') && SKD_MAP_API_KEY !== '') {
            wp_enqueue_script(
                'google-map-api',
                'https://maps.googleapis.com/maps/api/js?key=' . SKD_MAP_API_KEY . '&libraries=places',
                [],
                null,
                true
            );
        }

        // Enqueue jQuery UI (Autocomplete)
        wp_enqueue_script('jquery-ui-autocomplete');

        // Enqueue jQuery UI Sortable for certifications page
        if (isset($_GET['page']) && $_GET['page'] === 'skd-pl-certifications') {
            wp_enqueue_script('jquery-ui-sortable');
        }

        // load form builder scripts only on the form builder page
        if (isset($_GET['page']) && $_GET['page'] === 'skd-pl-listing-form-builder') {
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-draggable');
            wp_enqueue_script('jquery-ui-sortable');

            wp_enqueue_script('skd-form-builder-script', SKD_PL_PLUGIN_URL . 'assets/js/form-builder.js', ['jquery', 'jquery-ui-draggable', 'jquery-ui-sortable'], null, true);
            wp_localize_script('skd-form-builder-script', 'skdFormBuilderAjax', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('skd_form_builder_nonce'),
            ]);
            wp_enqueue_style('skd-form-builder-style', SKD_PL_PLUGIN_URL . 'assets/css/form-builder.css');
        }

        // load price plan scripts only on the coupons page
        if (isset($_GET['page']) && $_GET['page'] === 'skd-pl-coupons') {
            wp_enqueue_script('skd-coupon-script', SKD_PL_PLUGIN_URL . 'assets/js/coupon-script.js', ['jquery'], null, true);
            wp_localize_script('skd-coupon-script', 'skdCouponAjax', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('skd_coupon_nonce'),
            ]);
        }
    }

    public static function web_enqueue_scripts()
    {
        wp_enqueue_style('skd-pl-web-styles', SKD_PL_PLUGIN_URL . 'assets/css/web-styles.css', [], SKD_PL_VERSION);

        // VDA specific styles
        wp_enqueue_style('skd-pl-vda-dashboard', SKD_PL_PLUGIN_URL . 'assets/css/vda-dashboard.css', [], SKD_PL_VERSION);
        wp_enqueue_style('skd-pl-vda-profile', SKD_PL_PLUGIN_URL . 'assets/css/vda-profile.css', [], SKD_PL_VERSION);
        wp_enqueue_style('skd-pl-vda-listing', SKD_PL_PLUGIN_URL . 'assets/css/vda-listing.css', [], SKD_PL_VERSION);

        wp_enqueue_script('skd-pl-web-scripts', SKD_PL_PLUGIN_URL . 'assets/js/web-scripts.js', ['jquery'], SKD_PL_VERSION, true);

        // Localize script for AJAX
        wp_localize_script('skd-pl-web-scripts', 'skd_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('skd_ajax_nonce'),
        ]);

        wp_localize_script('skd-pl-web-scripts', 'skd_ajax_object', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('skd_ajax_nonce'),
        ]);

        //slick carousel
        wp_enqueue_style('slick-carousel', SKD_PL_PLUGIN_URL . 'assets/css/slick.css');
        wp_enqueue_style('slick-theme', SKD_PL_PLUGIN_URL . 'assets/css/slick-theme.css');
        wp_enqueue_script('slick-carousel', SKD_PL_PLUGIN_URL . 'assets/js/slick.min.js', ['jquery'], null, true);

        //select2
        wp_enqueue_style('select2', SKD_PL_PLUGIN_URL . 'assets/css/select2.min.css');
        wp_enqueue_script('select2', SKD_PL_PLUGIN_URL . 'assets/js/select2.min.js', ['jquery'], null, true);

        // jQuery Validate
        wp_enqueue_script('jquery-validate', 'https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js', ['jquery'], '1.19.5', true);

        wp_enqueue_script('iconify-icon', SKD_PL_PLUGIN_URL . 'assets/js/iconify-icon.min.js', ['jquery'], null, true);

        //sweetalert2
        wp_enqueue_style('sweetalert2', SKD_PL_PLUGIN_URL . 'assets/css/sweetalert2.min.css');
        wp_enqueue_script('sweetalert2', SKD_PL_PLUGIN_URL . 'assets/js/sweetalert2.all.min.js', ['jquery'], null, true);

        //enque google map api
        if (defined('SKD_MAP_API_KEY') && SKD_MAP_API_KEY !== '') {
            wp_enqueue_script(
                'google-map-api',
                'https://maps.googleapis.com/maps/api/js?key=' . SKD_MAP_API_KEY . '&libraries=places',
                [],
                null,
                true
            );
        }
    }
}

SKD_Plugin_Autoloader::register();
SKD_Plugin_Autoloader::add_admin_scripts();
