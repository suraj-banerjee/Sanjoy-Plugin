<?php

class SKD_Plugin_Main
{
    public static function init()
    {
        self::load_dependencies();
        add_action('admin_menu', [__CLASS__, 'register_admin_menu']);
    }

    private static function load_dependencies()
    {
        require_once SKD_PL_PLUGIN_PATH . 'includes/admin/class-skd-pl-admin-menu.php';
        require_once SKD_PL_PLUGIN_PATH . 'includes/admin/class-skd-pl-feature-listing.php';
        require_once SKD_PL_PLUGIN_PATH . 'includes/admin/class-skd-pl-locations.php';
        require_once SKD_PL_PLUGIN_PATH . 'includes/admin/class-skd-pl-tag-listing.php';
        require_once SKD_PL_PLUGIN_PATH . 'includes/admin/class-skd-pl-listing-form-builder.php';
        require_once SKD_PL_PLUGIN_PATH . 'includes/admin/class-skd-pl-price-plans.php';
        require_once SKD_PL_PLUGIN_PATH . 'includes/admin/class-skd-pl-coupons.php';
        require_once SKD_PL_PLUGIN_PATH . 'includes/admin/class-skd-pl-listings.php';
        require_once SKD_PL_PLUGIN_PATH . 'includes/admin/class-skd-pl-orders.php';
        require_once SKD_PL_PLUGIN_PATH . 'includes/admin/class-skd-pl-settings.php';
        // VDA Management
        require_once SKD_PL_PLUGIN_PATH . 'includes/admin/class-skd-pl-vda-skills.php';
        require_once SKD_PL_PLUGIN_PATH . 'includes/admin/class-skd-pl-vda-specializations.php';
        require_once SKD_PL_PLUGIN_PATH . 'includes/admin/class-skd-pl-certifications.php';
        require_once SKD_PL_PLUGIN_PATH . 'includes/admin/class-skd-pl-portfolio-categories.php';
        require_once SKD_PL_PLUGIN_PATH . 'includes/admin/class-skd-pl-vda-project-types.php';
        require_once SKD_PL_PLUGIN_PATH . 'includes/admin/class-skd-pl-vda-service-types.php';
        require_once SKD_PL_PLUGIN_PATH . 'includes/admin/class-skd-pl-availability-types.php';
        require_once SKD_PL_PLUGIN_PATH . 'includes/admin/class-skd-pl-timezones.php';
        require_once SKD_PL_PLUGIN_PATH . 'includes/admin/class-skd-pl-experience-levels.php';
        require_once SKD_PL_PLUGIN_PATH . 'includes/admin/class-skd-pl-response-times.php';
        require_once SKD_PL_PLUGIN_PATH . 'includes/admin/class-skd-pl-languages.php';
        //web
        require_once SKD_PL_PLUGIN_PATH . 'includes/web/class-skd-shortcodes.php';
        require_once SKD_PL_PLUGIN_PATH . 'includes/web/class-skd-pl-vda-profile.php';
        require_once SKD_PL_PLUGIN_PATH . 'includes/web/class-skd-pl-employer.php';

        // Initialize employer functionality
        SKD_PL_Employer::init();
    }

    public static function register_admin_menu()
    {
        SKD_Plugin_Admin_Menu::register_menus();
    }
}
