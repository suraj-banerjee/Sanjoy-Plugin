<?php

class SKD_Plugin_Admin_Menu
{
    public static function register_menus()
    {
        // Main menu with "All Professionals" as the main page
        add_menu_page(
            __('All Professionals', 'skd-property-listings'),
            __('interiAssist', 'skd-property-listings'),
            'manage_options',
            'skd-pl-all-listings',
            [__CLASS__, 'render_all_listings'],
            'dashicons-groups',
            25
        );

        // Submenu: All Professionals
        add_submenu_page(
            'skd-pl-all-listings',
            __('All Professionals', 'skd-property-listings'),
            __('All Professionals', 'skd-property-listings'),
            'manage_options',
            'skd-pl-all-listings',
            [__CLASS__, 'render_all_listings']
        );

        // Submenu: Add New Professional
        add_submenu_page(
            'skd-pl-all-listings',
            __('Add New Professional', 'skd-property-listings'),
            __('Add New Professional', 'skd-property-listings'),
            'manage_options',
            'skd-pl-all-listings&action=add',
            [__CLASS__, 'render_add_new']
        );

        // Submenu: Job Board
        add_submenu_page(
            'skd-pl-all-listings',
            __('Job Board', 'skd-property-listings'),
            __('Job Board', 'skd-property-listings'),
            'manage_options',
            'skd-pl-jobs',
            [__CLASS__, 'render_jobs_page']
        );

        // Submenu: Reviews & Ratings
        add_submenu_page(
            'skd-pl-all-listings',
            __('Reviews & Ratings', 'skd-property-listings'),
            __('Reviews & Ratings', 'skd-property-listings'),
            'manage_options',
            'skd-pl-reviews',
            [__CLASS__, 'render_reviews_page']
        );

        // Submenu: Price Plans
        add_submenu_page(
            'skd-pl-all-listings',
            __('Membership Plans', 'skd-property-listings'),
            __('Membership Plans', 'skd-property-listings'),
            'manage_options',
            'skd-pl-price-plans',
            [__CLASS__, 'render_price_plans']
        );

        // Submenu: Coupons
        add_submenu_page(
            'skd-pl-all-listings',
            __('Coupons', 'skd-property-listings'),
            __('Coupons', 'skd-property-listings'),
            'manage_options',
            'skd-pl-coupons',
            [__CLASS__, 'render_coupons']
        );

        // Submenu: Order History
        add_submenu_page(
            'skd-pl-all-listings',
            __('Member Orders', 'skd-property-listings'),
            __('Member Orders', 'skd-property-listings'),
            'manage_options',
            'skd-pl-order-history',
            [__CLASS__, 'render_order_history']
        );

        // Submenu: Software
        add_submenu_page(
            'skd-pl-all-listings',
            __('Software', 'skd-property-listings'),
            __('Software', 'skd-property-listings'),
            'manage_options',
            'skd-pl-skills',
            [__CLASS__, 'render_skills_page']
        );

        // Submenu: Specializations
        add_submenu_page(
            'skd-pl-all-listings',
            __('Specializations', 'skd-property-listings'),
            __('Specializations', 'skd-property-listings'),
            'manage_options',
            'skd-pl-specializations',
            [__CLASS__, 'render_specializations_page']
        );

        // Submenu: Locations (NOT related to VDA dashboard - for physical locations/addresses)
        // add_submenu_page(
        //     'skd-pl-all-listings',
        //     __('Locations', 'skd-property-listings'),
        //     __('Locations', 'skd-property-listings'),
        //     'manage_options',
        //     'skd-pl-locations',
        //     [__CLASS__, 'render_locations']
        // );

        // Submenu: Certifications & Badges
        add_submenu_page(
            'skd-pl-all-listings',
            __('Certifications', 'skd-property-listings'),
            __('Certifications', 'skd-property-listings'),
            'manage_options',
            'skd-pl-certifications',
            [__CLASS__, 'render_certifications_page']
        );

        // Submenu: Portfolio Categories
        add_submenu_page(
            'skd-pl-all-listings',
            __('Portfolio Categories', 'skd-property-listings'),
            __('Portfolio Categories', 'skd-property-listings'),
            'manage_options',
            'skd-pl-portfolio-categories',
            [__CLASS__, 'render_portfolio_categories_page']
        );

        // Submenu: Timezones
        add_submenu_page(
            'skd-pl-all-listings',
            __('Timezones', 'skd-property-listings'),
            __('Timezones', 'skd-property-listings'),
            'manage_options',
            'skd-pl-timezones',
            [__CLASS__, 'render_timezones_page']
        );

        // Submenu: Experience Levels
        add_submenu_page(
            'skd-pl-all-listings',
            __('Experience Levels', 'skd-property-listings'),
            __('Experience Levels', 'skd-property-listings'),
            'manage_options',
            'skd-pl-experience-levels',
            [__CLASS__, 'render_experience_levels_page']
        );

        // Submenu: Response Times
        add_submenu_page(
            'skd-pl-all-listings',
            __('Response Times', 'skd-property-listings'),
            __('Response Times', 'skd-property-listings'),
            'manage_options',
            'skd-pl-response-times',
            [__CLASS__, 'render_response_times_page']
        );

        // Submenu: Languages
        add_submenu_page(
            'skd-pl-all-listings',
            __('Languages Spoken', 'skd-property-listings'),
            __('Languages Spoken', 'skd-property-listings'),
            'manage_options',
            'skd-pl-languages',
            [__CLASS__, 'render_languages_page']
        );

        // Settings (conditionally enabled)
        $enable_setting = get_option('skd_enable_setting', 'no');
        if ($enable_setting === 'yes') {
            add_submenu_page(
                'skd-pl-all-listings',
                __('Settings', 'skd-property-listings'),
                __('Settings', 'skd-property-listings'),
                'manage_options',
                'skd-pl-settings',
                [__CLASS__, 'render_settings']
            );
        }
    }

    // Render methods for each submenu

    public static function render_all_listings()
    {
        // Instantiate the Listings class
        $listings = new SKD_Plugin_Listings();
        $listings->render_listings_page();
    }

    public static function render_add_new()
    {
        // Instantiate the Listings class
        $listings = new SKD_Plugin_Listings();
        $listings->render_add_edit_listing_page('add');
    }

    public static function render_skills_page()
    {
        SKD_PL_VDA_Skills::render_page();
    }

    public static function render_specializations_page()
    {
        SKD_PL_VDA_Specializations::render_page();
    }

    public static function render_jobs_page()
    {
        // Job Board management page
        echo '<div class="wrap">';
        echo '<h1>Job Board Management</h1>';
        echo '<p>Manage job postings and applications.</p>';
        echo '<div class="notice notice-info"><p>Job board management interface coming soon...</p></div>';
        echo '</div>';
    }

    public static function render_certifications_page()
    {
        // Certifications management page - use the new certifications class
        SKD_PL_Certifications::render_page();
    }

    public static function render_portfolio_categories_page()
    {
        // Portfolio Categories management page
        SKD_PL_Portfolio_Categories::render_page();
    }

    public static function render_timezones_page()
    {
        SKD_PL_Timezones::render_page();
    }

    public static function render_experience_levels_page()
    {
        SKD_PL_Experience_Levels::render_page();
    }

    public static function render_response_times_page()
    {
        SKD_PL_Response_Times::render_page();
    }

    public static function render_languages_page()
    {
        SKD_PL_Languages::render_page();
    }

    public static function render_reviews_page()
    {
        // Reviews management page
        echo '<div class="wrap">';
        echo '<h1>Reviews & Ratings Management</h1>';
        echo '<p>Manage professional reviews and ratings.</p>';
        echo '<div class="notice notice-info"><p>Reviews management interface coming soon...</p></div>';
        echo '</div>';
    }

    public static function render_locations()
    {
        // Instantiate the Location Management class
        $location_management = new SKD_Plugin_Location_Management();
        $location_management->render_locations_page();
    }

    public static function render_price_plans()
    {
        // Instantiate the Price Plans class
        $price_plans = new SKD_Plugin_Price_Plans();
        $price_plans->render_price_plans_page();
    }

    public static function render_coupons()
    {
        // Instantiate the Coupons class
        $coupons = new SKD_Plugin_Coupons();
        $coupons->render_coupons_page();
    }

    public static function render_order_history()
    {
        // Instantiate the Orders class
        $orders = new SKD_Plugin_Orders();
        $orders->render_orders_page();
    }

    // Settings page render method
    public static function render_settings()
    {
        //Instantiate the settings class
        $settings = new SKD_Plugin_Settings();
        $settings->render_settings_page();
    }
}
