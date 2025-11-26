<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class SKD_PL_Database
{
    /**
     * Create necessary database tables
     */
    public static function create_tables()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // Table for Feature Listing
        $feature_table = $wpdb->prefix . 'skd_pl_features';
        $sql = "CREATE TABLE $feature_table (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(200) NOT NULL,
            slug VARCHAR(200) NOT NULL,
            description TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY slug (slug)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);

        // Table for Category
        $category_table = $wpdb->prefix . 'skd_pl_categories';
        $sql = "CREATE TABLE $category_table (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL,
            parent_id BIGINT(20) UNSIGNED DEFAULT 0,
            description TEXT,
            icon_url VARCHAR(255),
            image_url VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY slug (slug)
        ) $charset_collate;";
        dbDelta($sql);

        //Table for location
        $location_table = $wpdb->prefix . 'skd_pl_locations';
        $sql = "CREATE TABLE $location_table (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL,
            parent_id BIGINT(20) UNSIGNED DEFAULT 0,
            description TEXT DEFAULT NULL,
            image_url VARCHAR(255) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY slug (slug)
        ) $charset_collate;";
        dbDelta($sql);

        //table for tags
        $tags_table = $wpdb->prefix . 'skd_pl_tags';
        $sql = "CREATE TABLE $tags_table (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(200) NOT NULL,
            slug VARCHAR(200) NOT NULL,
            description TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY slug (slug)
        ) $charset_collate;";
        dbDelta($sql);

        //table for Listing Form Builder
        $listing_form_builder_table = $wpdb->prefix . 'skd_form_builder';
        $sql = "CREATE TABLE $listing_form_builder_table (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            field_name VARCHAR(255) NOT NULL,
            field_type VARCHAR(50) NOT NULL,
            field_options TEXT DEFAULT NULL,
            field_placeholder VARCHAR(255) DEFAULT NULL,
            field_required TINYINT(1) DEFAULT 0,
            sort_order INT DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) $charset_collate;";
        dbDelta($sql);

        //table for price plans
        $price_plans_table = $wpdb->prefix . 'skd_pl_price_plans';
        $sql = "CREATE TABLE $price_plans_table (
            id INT(11) NOT NULL AUTO_INCREMENT,
            plan_name VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL,
            plan_description TEXT NULL,
            plan_type VARCHAR(50) NOT NULL,
            price DECIMAL(10,2) NOT NULL DEFAULT 0,
            add_gst_rate TINYINT(1) NOT NULL DEFAULT 0,
            gst_rate DECIMAL(10,2) NOT NULL DEFAULT 0,
            gst_type VARCHAR(50) NULL,
            is_free TINYINT(1) NOT NULL DEFAULT 0,
            listing_duration INT(11) NULL,
            duration_unit VARCHAR(20) NULL,
            never_expire TINYINT(1) NOT NULL DEFAULT 0,
            featured_the_list VARCHAR(10) NOT NULL DEFAULT 'no',
            featured_the_list_hide TINYINT(1) NOT NULL DEFAULT 0,
            enable_subscription VARCHAR(10) NOT NULL DEFAULT 'no',
            enable_subscription_hide TINYINT(1) NOT NULL DEFAULT 0,
            no_of_listing INT(11) NULL,
            mark_as_unlimited TINYINT(1) NOT NULL DEFAULT 0,
            no_of_feature_listing INT(11) NULL,
            mark_feature_unlimited TINYINT(1) NOT NULL DEFAULT 0,
            contact_owner VARCHAR(10) NOT NULL DEFAULT 'no',
            contact_owner_hide TINYINT(1) NOT NULL DEFAULT 0,
            customer_review VARCHAR(10) NOT NULL DEFAULT 'no',
            customer_review_hide TINYINT(1) NOT NULL DEFAULT 0,
            mark_as_sold VARCHAR(10) NOT NULL DEFAULT 'no',
            mark_as_sold_hide TINYINT(1) NOT NULL DEFAULT 0,
            recomend_plan VARCHAR(10) NOT NULL DEFAULT 'no',
            hide_from_plan VARCHAR(10) NOT NULL DEFAULT 'no',
            plan_status VARCHAR(50) NOT NULL DEFAULT 'draft',
            listing_sorting_order INT(11) NOT NULL DEFAULT 0,
            business_name VARCHAR(10) NOT NULL DEFAULT 'no',
            business_name_hide TINYINT(1) NOT NULL DEFAULT 0,
            pricing_fld VARCHAR(10) NOT NULL DEFAULT 'no',
            pricing_fld_hide TINYINT(1) NOT NULL DEFAULT 0,
            location_fld VARCHAR(10) NOT NULL DEFAULT 'no',
            location_fld_hide TINYINT(1) NOT NULL DEFAULT 0,
            location_fld_limit INT(11) NULL,
            location_fld_limit_unlimited TINYINT(1) NOT NULL DEFAULT 0,
            tag_fld VARCHAR(10) NOT NULL DEFAULT 'no',
            tag_fld_hide TINYINT(1) NOT NULL DEFAULT 0,
            tag_fld_limit INT(11) NULL,
            tag_fld_limit_unlimited TINYINT(1) NOT NULL DEFAULT 0,
            category_fld VARCHAR(10) NOT NULL DEFAULT 'no',
            category_fld_hide TINYINT(1) NOT NULL DEFAULT 0,
            category_fld_limit INT(11) NULL,
            category_fld_limit_unlimited TINYINT(1) NOT NULL DEFAULT 0,
            phone_fld VARCHAR(10) NOT NULL DEFAULT 'no',
            phone_fld_hide TINYINT(1) NOT NULL DEFAULT 0,
            phone_2_fld VARCHAR(10) NOT NULL DEFAULT 'no',
            phone_2_fld_hide TINYINT(1) NOT NULL DEFAULT 0,
            email_fld VARCHAR(10) NOT NULL DEFAULT 'no',
            email_fld_hide TINYINT(1) NOT NULL DEFAULT 0,
            website_fld VARCHAR(10) NOT NULL DEFAULT 'no',
            website_fld_hide TINYINT(1) NOT NULL DEFAULT 0,
            map_fld VARCHAR(10) NOT NULL DEFAULT 'no',
            map_fld_hide TINYINT(1) NOT NULL DEFAULT 0,
            hide_owner_form_listing VARCHAR(10) NOT NULL DEFAULT 'no',
            cntct_owner_price_pln_hide TINYINT(1) NOT NULL DEFAULT 0,
            tagline_fld VARCHAR(10) NOT NULL DEFAULT 'no',
            tagline_fld_hide TINYINT(1) NOT NULL DEFAULT 0,
            address_fld VARCHAR(10) NOT NULL DEFAULT 'no',
            address_fld_hide TINYINT(1) NOT NULL DEFAULT 0,
            social_info VARCHAR(10) NOT NULL DEFAULT 'no',
            social_info_hide TINYINT(1) NOT NULL DEFAULT 0,
            zip_post_code VARCHAR(10) NOT NULL DEFAULT 'no',
            zip_post_code_hide TINYINT(1) NOT NULL DEFAULT 0,
            description_fld VARCHAR(10) NOT NULL DEFAULT 'no',
            description_fld_hide TINYINT(1) NOT NULL DEFAULT 0,
            description_fld_limit INT(11) NULL,
            description_fld_limit_unlimited TINYINT(1) NOT NULL DEFAULT 0,
            video_fld VARCHAR(10) NOT NULL DEFAULT 'no',
            video_fld_hide TINYINT(1) NOT NULL DEFAULT 0,   
            images_fld VARCHAR(10) NOT NULL DEFAULT 'no',
            images_fld_hide TINYINT(1) NOT NULL DEFAULT 0,
            images_fld_limit INT(11) NULL,
            images_fld_limit_unlimited TINYINT(1) NOT NULL DEFAULT 0,
            online_business VARCHAR(10) NOT NULL DEFAULT 'no',
            online_business_hide TINYINT(1) NOT NULL DEFAULT 0,
            upload_logo_images VARCHAR(10) NOT NULL DEFAULT 'no',
            upload_logo_images_hide TINYINT(1) NOT NULL DEFAULT 0,
            view_count VARCHAR(10) NOT NULL DEFAULT 'no',
            view_count_hide TINYINT(1) NOT NULL DEFAULT 0,
            contact_details VARCHAR(10) NOT NULL DEFAULT 'no',
            contact_details_hide TINYINT(1) NOT NULL DEFAULT 0,
            max_team_members INT(11) NULL DEFAULT 1 COMMENT 'Maximum team members for studio/agency plans. -1 for unlimited',
            created_date DATETIME NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY slug (slug)
        ) $charset_collate;";
        dbDelta($sql);

        //table for coupons
        $coupons_table = $wpdb->prefix . 'skd_pl_coupons';
        $sql = "CREATE TABLE $coupons_table (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            title VARCHAR(255) NOT NULL,
            description TEXT NULL,
            coupon_code VARCHAR(50) NOT NULL,
            discount_type ENUM('percentage', 'fixed_cart', 'fixed_product') NOT NULL,
            discount_amount DECIMAL(10,2) NOT NULL,
            product_ids TEXT NULL,
            expiry_date DATE NULL,
            usage_limit INT NULL,
            gst_exemption ENUM('apply_gst', 'exempt_gst') NOT NULL DEFAULT 'apply_gst',
            coupon_status VARCHAR(50) NOT NULL DEFAULT 'draft',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY coupon_code (coupon_code)
        ) $charset_collate;";
        dbDelta($sql);

        //table for professional profiles (renamed from listing)
        $listing_table = $wpdb->prefix . 'skd_pl_listings';
        $sql = "CREATE TABLE $listing_table (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT DEFAULT 0,
            plan_id BIGINT UNSIGNED NOT NULL,
            order_id INT DEFAULT 0,
            listing_type ENUM('regular', 'featured') DEFAULT 'regular',
            is_feature TINYINT(1) DEFAULT 0,
            expiration_date DATETIME DEFAULT NULL,
            listing_title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL,
            listing_description TEXT NULL,
            tagline VARCHAR(255) NULL,
            price DECIMAL(10,2),
            view_count INT DEFAULT 0,
            contact_details VARCHAR(255) NULL,
            contact_phone VARCHAR(20) NULL,
            contact_phone2 VARCHAR(20) NULL,
            contact_email VARCHAR(255) NULL,
            contact_zip VARCHAR(20) NULL,
            contact_website VARCHAR(255) NULL,
            hide_owner_form TINYINT(1) DEFAULT 0,
            social_info TEXT NULL, -- Store social networks as JSON
            list_address TEXT NULL,
            is_online_only TINYINT(1) DEFAULT 0,
            manual_coordinates TINYINT(1) DEFAULT 0,
            latitude VARCHAR(255) NULL,
            longitude VARCHAR(255) NULL,
            skd_logo VARCHAR(255) NULL,
            skd_gallery TEXT NULL, -- Store multiple image URLs as JSON
            video VARCHAR(255) NULL,
            privacy_policy TINYINT(1) DEFAULT 0,
            listing_status ENUM('publish', 'pending', 'draft') NOT NULL DEFAULT 'pending',
            skd_header_image VARCHAR(255) NULL,
            category_ids TEXT NULL, -- Store category IDs as JSON
            location_ids TEXT NULL, -- Store location IDs as JSON
            features TEXT NULL, -- Store feature names as JSON
            tags TEXT NULL, -- Store tag names as JSON
            -- Professional Profile Fields
            user_role ENUM('vda', 'studio', 'employer') NOT NULL DEFAULT 'vda',
            hourly_rate DECIMAL(10,2) NULL,
            rate_type ENUM('hourly', 'project', 'monthly') DEFAULT 'hourly',
            experience_level ENUM('junior', 'mid', 'expert') DEFAULT 'junior',
            availability ENUM('full-time', 'part-time', 'project-based', 'unavailable') DEFAULT 'full-time',
            timezone VARCHAR(50) NULL,
            languages TEXT NULL, -- Store languages as JSON
            portfolio_images TEXT NULL, -- Store portfolio URLs as JSON
            skills TEXT NULL, -- Store skill IDs as JSON
            services TEXT NULL, -- Store service IDs as JSON
            specializations TEXT NULL, -- Store specialization IDs as JSON
            certifications TEXT NULL, -- Store certification/badge IDs as JSON
            rating DECIMAL(3,2) DEFAULT 0.00,
            total_reviews INT DEFAULT 0,
            total_projects INT DEFAULT 0,
            response_time INT DEFAULT 0, -- in hours
            is_verified TINYINT(1) DEFAULT 0,
            team_size INT DEFAULT 1,
            founded_year INT NULL,
            company_type VARCHAR(100) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY slug (slug)
        ) $charset_collate;";
        dbDelta($sql);

        //table for order
        $order_table = $wpdb->prefix . 'skd_pl_orders';
        $sql = "CREATE TABLE $order_table (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT DEFAULT 0,
            plan_id BIGINT UNSIGNED NOT NULL,
            coupon_id BIGINT UNSIGNED NULL,
            coupon_details TEXT NULL,
            discount_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
            final_price DECIMAL(10,2) NOT NULL DEFAULT 0,
            order_status ENUM('pending', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
            payment_method ENUM('paypal', 'stripe', 'bank_transfer', 'free') NULL,
            payment_status ENUM('pending', 'completed', 'failed') NULL,
            payment_date DATETIME DEFAULT NULL,
            payment_amount DECIMAL(10,2) NULL,
            payment_currency VARCHAR(10) NULL,
            payment_transaction_id VARCHAR(255) NULL,
            payment_details TEXT NULL,
            subscription_id VARCHAR(255) NULL,
            is_subscription VARCHAR(10) NULL,
            subscription_cancel VARCHAR(10) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) $charset_collate;";
        dbDelta($sql);

        //table for coupon usage
        $coupon_usage_table = $wpdb->prefix . 'skd_pl_coupon_usage';
        $sql = "CREATE TABLE $coupon_usage_table (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            coupon_id BIGINT UNSIGNED NOT NULL,
            usage_count INT NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY user_coupon (user_id, coupon_id)
        ) $charset_collate;";
        dbDelta($sql);

        //table for listing contact form
        $listing_contact_form_table = $wpdb->prefix . 'skd_pl_listing_contact_form';
        $sql = "CREATE TABLE $listing_contact_form_table (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            listing_id BIGINT UNSIGNED NOT NULL,
            user_id INT DEFAULT 0,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(20) NULL,
            message TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) $charset_collate;";
        dbDelta($sql);

        //table for skills and software
        $skills_table = $wpdb->prefix . 'skd_pl_skills';
        $sql = "CREATE TABLE $skills_table (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            slug VARCHAR(100) NOT NULL,
            category ENUM('software', 'design_skill', 'technical_skill', 'soft_skill') NOT NULL DEFAULT 'software',
            description TEXT NULL,
            icon_url VARCHAR(255) NULL,
            is_featured TINYINT(1) DEFAULT 0,
            sort_order INT DEFAULT 0,
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY slug (slug)
        ) $charset_collate;";
        dbDelta($sql);

        //table for services offered
        $services_table = $wpdb->prefix . 'skd_pl_services';
        $sql = "CREATE TABLE $services_table (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(200) NOT NULL,
            slug VARCHAR(200) NOT NULL,
            description TEXT NULL,
            category VARCHAR(100) NULL,
            icon_url VARCHAR(255) NULL,
            is_popular TINYINT(1) DEFAULT 0,
            sort_order INT DEFAULT 0,
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY slug (slug)
        ) $charset_collate;";
        dbDelta($sql);

        //table for specializations
        $specializations_table = $wpdb->prefix . 'skd_pl_specializations';
        $sql = "CREATE TABLE $specializations_table (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(200) NOT NULL,
            slug VARCHAR(200) NOT NULL,
            description TEXT NULL,
            parent_id BIGINT UNSIGNED DEFAULT 0,
            icon_url VARCHAR(255) NULL,
            sort_order INT DEFAULT 0,
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY slug (slug)
        ) $charset_collate;";
        dbDelta($sql);

        //table for certifications and badges
        $certifications_table = $wpdb->prefix . 'skd_pl_certifications';
        $sql = "CREATE TABLE $certifications_table (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(200) NOT NULL,
            slug VARCHAR(200) NOT NULL,
            description TEXT NULL,
            issuer VARCHAR(200) NULL,
            badge_image_url VARCHAR(255) NULL,
            verification_required TINYINT(1) DEFAULT 1,
            sort_order INT DEFAULT 0,
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY slug (slug)
        ) $charset_collate;";
        dbDelta($sql);

        //table for project types
        $project_types_table = $wpdb->prefix . 'skd_pl_project_types';
        $sql = "CREATE TABLE $project_types_table (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(200) NOT NULL,
            slug VARCHAR(200) NOT NULL,
            description TEXT NULL,
            icon_url VARCHAR(255) NULL,
            sort_order INT DEFAULT 0,
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY slug (slug)
        ) $charset_collate;";
        dbDelta($sql);

        //table for service types
        $service_types_table = $wpdb->prefix . 'skd_pl_service_types';
        $sql = "CREATE TABLE $service_types_table (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(200) NOT NULL,
            slug VARCHAR(200) NOT NULL,
            description TEXT NULL,
            icon_url VARCHAR(255) NULL,
            sort_order INT DEFAULT 0,
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY slug (slug)
        ) $charset_collate;";
        dbDelta($sql);

        //table for availability types
        $availability_types_table = $wpdb->prefix . 'skd_pl_availability_types';
        $sql = "CREATE TABLE $availability_types_table (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(200) NOT NULL,
            slug VARCHAR(200) NOT NULL,
            description TEXT NULL,
            sort_order INT DEFAULT 0,
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY slug (slug)
        ) $charset_collate;";
        dbDelta($sql);

        //table for timezones
        $timezones_table = $wpdb->prefix . 'skd_pl_timezones';
        $sql = "CREATE TABLE $timezones_table (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(200) NOT NULL,
            value VARCHAR(200) NOT NULL,
            offset VARCHAR(10) NULL,
            sort_order INT DEFAULT 0,
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY value (value)
        ) $charset_collate;";
        dbDelta($sql);

        //table for experience levels
        $experience_levels_table = $wpdb->prefix . 'skd_pl_experience_levels';
        $sql = "CREATE TABLE $experience_levels_table (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(200) NOT NULL,
            slug VARCHAR(200) NOT NULL,
            years_min INT DEFAULT 0,
            years_max INT NULL,
            description TEXT NULL,
            sort_order INT DEFAULT 0,
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY slug (slug)
        ) $charset_collate;";
        dbDelta($sql);

        //table for response times
        $response_times_table = $wpdb->prefix . 'skd_pl_response_times';
        $sql = "CREATE TABLE $response_times_table (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(200) NOT NULL,
            slug VARCHAR(200) NOT NULL,
            description TEXT NULL,
            sort_order INT DEFAULT 0,
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY slug (slug)
        ) $charset_collate;";
        dbDelta($sql);

        //table for languages
        $languages_table = $wpdb->prefix . 'skd_pl_languages';
        $sql = "CREATE TABLE $languages_table (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(200) NOT NULL,
            code VARCHAR(10) NOT NULL,
            native_name VARCHAR(200) NULL,
            sort_order INT DEFAULT 0,
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY code (code)
        ) $charset_collate;";
        dbDelta($sql);

        //table for user certifications
        $user_certifications_table = $wpdb->prefix . 'skd_pl_user_certifications';
        $sql = "CREATE TABLE $user_certifications_table (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            certification_id BIGINT UNSIGNED NOT NULL,
            status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
            certificate_file VARCHAR(255) NULL,
            verification_date DATE NULL,
            expiry_date DATE NULL,
            notes TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY user_cert (user_id, certification_id)
        ) $charset_collate;";
        dbDelta($sql);

        //table for reviews and ratings
        $reviews_table = $wpdb->prefix . 'skd_pl_reviews';
        $sql = "CREATE TABLE $reviews_table (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            listing_id BIGINT UNSIGNED NOT NULL,
            reviewer_id INT NOT NULL,
            reviewer_name VARCHAR(255) NOT NULL,
            reviewer_email VARCHAR(255) NOT NULL,
            rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
            review_title VARCHAR(255) NULL,
            review_text TEXT NOT NULL,
            project_type VARCHAR(100) NULL,
            work_quality_rating INT NULL CHECK (work_quality_rating BETWEEN 1 AND 5),
            communication_rating INT NULL CHECK (communication_rating BETWEEN 1 AND 5),
            deadline_rating INT NULL CHECK (deadline_rating BETWEEN 1 AND 5),
            value_rating INT NULL CHECK (value_rating BETWEEN 1 AND 5),
            would_recommend TINYINT(1) DEFAULT 1,
            status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) $charset_collate;";
        dbDelta($sql);

        //table for job postings
        $jobs_table = $wpdb->prefix . 'skd_pl_jobs';
        $sql = "CREATE TABLE $jobs_table (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            employer_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            requirements TEXT NULL,
            budget_min DECIMAL(10,2) NULL,
            budget_max DECIMAL(10,2) NULL,
            budget_type ENUM('hourly', 'project', 'monthly') DEFAULT 'project',
            project_duration VARCHAR(100) NULL,
            skill_requirements TEXT NULL, -- JSON array of skill IDs
            experience_required ENUM('junior', 'mid', 'expert', 'any') DEFAULT 'any',
            job_type ENUM('one-time', 'ongoing', 'contract') DEFAULT 'one-time',
            remote_work TINYINT(1) DEFAULT 1,
            location VARCHAR(255) NULL,
            applications_count INT DEFAULT 0,
            status ENUM('open', 'closed', 'filled', 'cancelled') DEFAULT 'open',
            featured TINYINT(1) DEFAULT 0,
            expires_at DATETIME NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY slug (slug)
        ) $charset_collate;";
        dbDelta($sql);

        //table for job applications
        $job_applications_table = $wpdb->prefix . 'skd_pl_job_applications';
        $sql = "CREATE TABLE $job_applications_table (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            job_id BIGINT UNSIGNED NOT NULL,
            applicant_id INT NOT NULL,
            cover_letter TEXT NOT NULL,
            proposed_rate DECIMAL(10,2) NULL,
            estimated_duration VARCHAR(100) NULL,
            portfolio_samples TEXT NULL, -- JSON array of URLs
            status ENUM('pending', 'shortlisted', 'rejected', 'hired') DEFAULT 'pending',
            employer_notes TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY job_applicant (job_id, applicant_id)
        ) $charset_collate;";
        dbDelta($sql);

        //table for messages
        $messages_table = $wpdb->prefix . 'skd_pl_messages';
        $sql = "CREATE TABLE $messages_table (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            sender_id INT NOT NULL,
            receiver_id INT NOT NULL,
            subject VARCHAR(255) NULL,
            message TEXT NOT NULL,
            thread_id VARCHAR(100) NULL,
            related_type ENUM('job', 'profile', 'general') DEFAULT 'general',
            related_id BIGINT UNSIGNED NULL,
            is_read TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX sender_receiver (sender_id, receiver_id),
            INDEX thread_id (thread_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Table for User Profiles (Extended user information for each role)
        $user_profiles_table = $wpdb->prefix . 'skd_pl_user_profiles';
        $sql = "CREATE TABLE $user_profiles_table (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            user_type ENUM('vda', 'studio', 'employer') NOT NULL,
            company_name VARCHAR(255) NULL,
            tagline TEXT NULL,
            short_description TEXT NULL COMMENT 'Brief 300-char description of the professional',
            bio TEXT NULL COMMENT 'Full About Me section with HTML from wp_editor',
            what_i_offer TEXT NULL COMMENT 'What I Offer section with HTML from wp_editor',
            skills TEXT NULL COMMENT 'JSON array of skill IDs',
            certifications TEXT NULL COMMENT 'JSON array of certification IDs',
            specializations TEXT NULL COMMENT 'JSON array of specialization IDs',
            services_offered TEXT NULL COMMENT 'JSON array of service IDs',
            project_types TEXT NULL COMMENT 'JSON array of project type IDs',
            service_types TEXT NULL COMMENT 'JSON array of service type IDs',
            hourly_rate DECIMAL(10,2) NULL,
            fixed_rate DECIMAL(10,2) NULL,
            pricing_model ENUM('hourly', 'fixed', 'both', 'negotiable') DEFAULT 'negotiable',
            location_id BIGINT(20) UNSIGNED NULL,
            country VARCHAR(100) NULL,
            city VARCHAR(100) NULL,
            address TEXT NULL,
            website_url VARCHAR(255) NULL,
            linkedin_url VARCHAR(255) NULL,
            behance_url VARCHAR(255) NULL,
            instagram_url VARCHAR(255) NULL,
            pinterest_url VARCHAR(255) NULL,
            portfolio_url VARCHAR(255) NULL,
            avatar_url VARCHAR(255) NULL,
            cover_image_url VARCHAR(255) NULL,
            years_experience INT NULL,
            experience_level ENUM('not-set', 'junior', 'mid', 'senior', 'expert') NULL,
            education_level VARCHAR(100) NULL COMMENT 'Highest degree: Bachelor, Master, PhD, etc.',
            languages_spoken TEXT NULL COMMENT 'JSON array of languages',
            timezone VARCHAR(50) NULL,
            availability_status VARCHAR(50) NULL COMMENT 'full-time, part-time, project-based, contract, freelance, unavailable',
            response_time VARCHAR(50) NULL COMMENT 'within-1-hour, within-few-hours, within-24-hours, within-48-hours, few-days',
            rating DECIMAL(2,1) DEFAULT 0.0,
            total_reviews INT DEFAULT 0,
            total_projects INT DEFAULT 0,
            total_earnings DECIMAL(12,2) DEFAULT 0.00,
            job_success_rate INT DEFAULT 100 COMMENT 'Percentage 0-100',
            profile_views INT DEFAULT 0,
            profile_completeness INT DEFAULT 0 COMMENT 'Percentage 0-100',
            is_verified TINYINT(1) DEFAULT 0,
            is_featured TINYINT(1) DEFAULT 0,
            is_top_rated TINYINT(1) DEFAULT 0,
            verification_date DATETIME NULL,
            last_active DATETIME NULL,
            joined_date DATETIME NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY user_id (user_id),
            INDEX user_type (user_type),
            INDEX location_id (location_id),
            INDEX experience_level (experience_level),
            INDEX rating (rating),
            INDEX job_success_rate (job_success_rate),
            INDEX is_verified (is_verified),
            INDEX is_featured (is_featured),
            INDEX is_top_rated (is_top_rated),
            INDEX availability_status (availability_status)
        ) $charset_collate;";
        dbDelta($sql);

        // Table for User Portfolio/Projects
        $portfolio_table = $wpdb->prefix . 'skd_pl_user_portfolio';
        $sql = "CREATE TABLE $portfolio_table (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT NULL,
            category_id BIGINT(20) UNSIGNED NULL COMMENT 'Portfolio category ID from master table',
            tags TEXT NULL COMMENT 'JSON array of tag strings',
            software_used TEXT NULL COMMENT 'JSON array: SketchUp, V-Ray, AutoCAD, etc.',
            images TEXT NULL COMMENT 'JSON array of image URLs',
            featured_image VARCHAR(255) NULL,
            year VARCHAR(4) NULL,
            client_name VARCHAR(255) NULL,
            project_url VARCHAR(255) NULL,
            project_duration VARCHAR(100) NULL COMMENT 'e.g., 2 weeks, 3 months',
            budget_range VARCHAR(100) NULL COMMENT 'e.g., $5000-$10000',
            is_featured TINYINT(1) DEFAULT 0,
            sort_order INT DEFAULT 0,
            views_count INT DEFAULT 0,
            likes_count INT DEFAULT 0,
            status ENUM('published', 'draft', 'pending') DEFAULT 'published',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX user_id (user_id),
            INDEX category_id (category_id),
            INDEX is_featured (is_featured),
            INDEX status (status)
        ) $charset_collate;";
        dbDelta($sql);

        // Table for Portfolio Categories (Master table managed by admin)
        $portfolio_categories_table = $wpdb->prefix . 'skd_pl_portfolio_categories';
        $sql = "CREATE TABLE $portfolio_categories_table (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(200) NOT NULL,
            slug VARCHAR(200) NOT NULL,
            description TEXT NULL,
            icon_url VARCHAR(255) NULL,
            sort_order INT DEFAULT 0,
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY slug (slug),
            INDEX status (status)
        ) $charset_collate;";
        dbDelta($sql);

        // Table for Studio/Agency Team Members
        $studio_members_table = $wpdb->prefix . 'skd_pl_studio_members';
        $sql = "CREATE TABLE $studio_members_table (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            studio_id BIGINT(20) UNSIGNED NOT NULL COMMENT 'User ID of the studio/agency',
            vda_id BIGINT(20) UNSIGNED NOT NULL COMMENT 'User ID of the VDA member',
            role_in_studio VARCHAR(100) NULL COMMENT 'e.g., Lead Designer, Junior Designer',
            join_date DATE NULL,
            status ENUM('active', 'inactive', 'pending_invitation', 'left') DEFAULT 'pending_invitation',
            permissions TEXT NULL COMMENT 'JSON array of permissions within the studio',
            hourly_rate DECIMAL(10,2) NULL COMMENT 'Member rate within this studio',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY studio_vda (studio_id, vda_id),
            INDEX studio_id (studio_id),
            INDEX vda_id (vda_id),
            INDEX status (status)
        ) $charset_collate;";
        dbDelta($sql);

        // Table for Skills (Referenced from user profiles)
        $skills_table = $wpdb->prefix . 'skd_pl_skills';
        $sql = "CREATE TABLE $skills_table (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL,
            category VARCHAR(100) NULL COMMENT 'software, design_skill, technical_skill, soft_skill',
            description TEXT NULL,
            icon_url VARCHAR(255) NULL,
            is_featured TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY slug (slug),
            INDEX category (category),
            INDEX is_featured (is_featured)
        ) $charset_collate;";
        dbDelta($sql);

        // Table for Services (What VDAs/Studios offer)
        $services_table = $wpdb->prefix . 'skd_pl_services';
        $sql = "CREATE TABLE $services_table (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL,
            category VARCHAR(100) NULL COMMENT 'visualization, technical, design, presentation, management, consultation',
            description TEXT NULL,
            icon_url VARCHAR(255) NULL,
            is_popular TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY slug (slug),
            INDEX category (category),
            INDEX is_popular (is_popular)
        ) $charset_collate;";
        dbDelta($sql);

        // Table for Specializations
        $specializations_table = $wpdb->prefix . 'skd_pl_specializations';
        $sql = "CREATE TABLE $specializations_table (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL,
            parent_id BIGINT(20) UNSIGNED DEFAULT 0,
            description TEXT NULL,
            icon_url VARCHAR(255) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY slug (slug),
            INDEX parent_id (parent_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Table for Certifications and Badges
        $certifications_table = $wpdb->prefix . 'skd_pl_certifications';
        $sql = "CREATE TABLE $certifications_table (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL,
            issuer VARCHAR(255) NULL,
            description TEXT NULL,
            badge_url VARCHAR(255) NULL,
            verification_required TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY slug (slug),
            INDEX issuer (issuer),
            INDEX verification_required (verification_required)
        ) $charset_collate;";
        dbDelta($sql);

        // Table for User Reviews and Ratings
        $reviews_table = $wpdb->prefix . 'skd_pl_reviews';
        $sql = "CREATE TABLE $reviews_table (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            reviewer_id BIGINT(20) UNSIGNED NOT NULL COMMENT 'User who gave the review',
            reviewee_id BIGINT(20) UNSIGNED NOT NULL COMMENT 'User being reviewed',
            project_id BIGINT(20) UNSIGNED NULL COMMENT 'Related project if any',
            rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
            review_title VARCHAR(255) NULL,
            review_text TEXT NULL,
            communication_rating INT NULL CHECK (communication_rating >= 1 AND communication_rating <= 5),
            quality_rating INT NULL CHECK (quality_rating >= 1 AND quality_rating <= 5),
            timeliness_rating INT NULL CHECK (timeliness_rating >= 1 AND timeliness_rating <= 5),
            is_featured TINYINT(1) DEFAULT 0,
            status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX reviewer_id (reviewer_id),
            INDEX reviewee_id (reviewee_id),
            INDEX rating (rating),
            INDEX status (status),
            INDEX is_featured (is_featured)
        ) $charset_collate;";
        dbDelta($sql);

        // Table for Job Listings
        $jobs_table = $wpdb->prefix . 'skd_pl_jobs';
        $sql = "CREATE TABLE $jobs_table (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            employer_id BIGINT(20) UNSIGNED NOT NULL COMMENT 'User ID of the employer',
            job_title VARCHAR(255) NOT NULL,
            job_description TEXT NULL,
            job_type ENUM('full-time', 'part-time', 'contract', 'project-based') DEFAULT 'project-based',
            experience_level ENUM('junior', 'mid', 'senior', 'expert', 'any') DEFAULT 'any',
            budget_type ENUM('hourly', 'fixed', 'negotiable') DEFAULT 'negotiable',
            budget_min DECIMAL(10,2) NULL,
            budget_max DECIMAL(10,2) NULL,
            currency VARCHAR(10) DEFAULT 'USD',
            duration VARCHAR(100) NULL COMMENT 'e.g., 2 weeks, 1 month, 3-6 months',
            required_skills TEXT NULL COMMENT 'JSON array of skill IDs',
            required_software TEXT NULL COMMENT 'JSON array of software names',
            preferred_timezone VARCHAR(100) NULL,
            location_id BIGINT(20) UNSIGNED NULL,
            remote_work ENUM('remote', 'onsite', 'hybrid') DEFAULT 'remote',
            category_id BIGINT(20) UNSIGNED NULL,
            specialization_ids TEXT NULL COMMENT 'JSON array of specialization IDs',
            project_deadline DATE NULL,
            attachments TEXT NULL COMMENT 'JSON array of file URLs',
            status ENUM('draft', 'active', 'closed', 'filled', 'cancelled') DEFAULT 'draft',
            visibility ENUM('public', 'private', 'invited_only') DEFAULT 'public',
            total_applications INT DEFAULT 0,
            total_invitations INT DEFAULT 0,
            views_count INT DEFAULT 0,
            is_featured TINYINT(1) DEFAULT 0,
            featured_until DATETIME NULL,
            expires_at DATETIME NULL,
            filled_at DATETIME NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX employer_id (employer_id),
            INDEX status (status),
            INDEX job_type (job_type),
            INDEX experience_level (experience_level),
            INDEX category_id (category_id),
            INDEX location_id (location_id),
            INDEX is_featured (is_featured),
            INDEX created_at (created_at)
        ) $charset_collate;";
        dbDelta($sql);

        // Table for Job Applications
        $job_applications_table = $wpdb->prefix . 'skd_pl_job_applications';
        $sql = "CREATE TABLE $job_applications_table (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            job_id BIGINT(20) UNSIGNED NOT NULL,
            applicant_id BIGINT(20) UNSIGNED NOT NULL COMMENT 'User ID of VDA or Studio',
            cover_letter TEXT NULL,
            proposed_rate DECIMAL(10,2) NULL,
            proposed_timeline VARCHAR(100) NULL,
            portfolio_links TEXT NULL COMMENT 'JSON array of portfolio URLs',
            attachments TEXT NULL COMMENT 'JSON array of file URLs',
            status ENUM('pending', 'shortlisted', 'interviewed', 'accepted', 'rejected', 'withdrawn') DEFAULT 'pending',
            employer_notes TEXT NULL,
            applicant_rating INT NULL CHECK (applicant_rating >= 1 AND applicant_rating <= 5),
            is_invited TINYINT(1) DEFAULT 0 COMMENT 'Whether this was from an invitation',
            viewed_by_employer TINYINT(1) DEFAULT 0,
            viewed_at DATETIME NULL,
            applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_application (job_id, applicant_id),
            INDEX job_id (job_id),
            INDEX applicant_id (applicant_id),
            INDEX status (status),
            INDEX is_invited (is_invited),
            INDEX applied_at (applied_at)
        ) $charset_collate;";
        dbDelta($sql);

        // Table for Direct Invitations (Employer to VDA/Studio)
        $invitations_table = $wpdb->prefix . 'skd_pl_invitations';
        $sql = "CREATE TABLE $invitations_table (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            sender_id BIGINT(20) UNSIGNED NOT NULL COMMENT 'User ID of sender (employer or studio)',
            receiver_id BIGINT(20) UNSIGNED NOT NULL COMMENT 'User ID of receiver (VDA or employer)',
            invitation_type ENUM('job_invite', 'studio_invite', 'collaboration', 'network') DEFAULT 'job_invite',
            job_id BIGINT(20) UNSIGNED NULL COMMENT 'Related job if job_invite',
            message TEXT NULL,
            custom_rate DECIMAL(10,2) NULL,
            status ENUM('pending', 'accepted', 'declined', 'expired') DEFAULT 'pending',
            expires_at DATETIME NULL,
            viewed TINYINT(1) DEFAULT 0,
            viewed_at DATETIME NULL,
            responded_at DATETIME NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX sender_id (sender_id),
            INDEX receiver_id (receiver_id),
            INDEX invitation_type (invitation_type),
            INDEX job_id (job_id),
            INDEX status (status),
            INDEX created_at (created_at)
        ) $charset_collate;";
        dbDelta($sql);

        // Table for Portfolio Items
        $portfolio_table = $wpdb->prefix . 'skd_pl_portfolio';
        $sql = "CREATE TABLE $portfolio_table (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT NULL,
            project_type VARCHAR(100) NULL COMMENT 'e.g., Residential, Commercial, Interior, Exterior',
            category_id BIGINT(20) UNSIGNED NULL,
            specialization_ids TEXT NULL COMMENT 'JSON array of specialization IDs',
            software_used TEXT NULL COMMENT 'JSON array of software names',
            services_provided TEXT NULL COMMENT 'JSON array of service IDs',
            image_url VARCHAR(500) NOT NULL,
            thumbnail_url VARCHAR(500) NULL,
            additional_images TEXT NULL COMMENT 'JSON array of image URLs',
            video_url VARCHAR(500) NULL,
            project_url VARCHAR(500) NULL COMMENT 'External project link',
            client_name VARCHAR(255) NULL,
            project_date DATE NULL,
            project_duration VARCHAR(100) NULL,
            is_featured TINYINT(1) DEFAULT 0,
            sort_order INT DEFAULT 0,
            views_count INT DEFAULT 0,
            likes_count INT DEFAULT 0,
            status ENUM('draft', 'published', 'private') DEFAULT 'published',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX user_id (user_id),
            INDEX category_id (category_id),
            INDEX is_featured (is_featured),
            INDEX status (status),
            INDEX sort_order (sort_order),
            INDEX created_at (created_at)
        ) $charset_collate;";
        dbDelta($sql);

        // Table for Education & Qualifications
        $education_table = $wpdb->prefix . 'skd_pl_education';
        $sql = "CREATE TABLE $education_table (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            degree_type VARCHAR(100) NULL COMMENT 'e.g., Bachelor, Master, Diploma, Certificate',
            field_of_study VARCHAR(255) NULL COMMENT 'e.g., Interior Design, Architecture',
            institution_name VARCHAR(255) NULL,
            location VARCHAR(255) NULL,
            start_date DATE NULL,
            end_date DATE NULL,
            is_current TINYINT(1) DEFAULT 0,
            grade VARCHAR(50) NULL,
            description TEXT NULL,
            sort_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX user_id (user_id),
            INDEX is_current (is_current),
            INDEX sort_order (sort_order)
        ) $charset_collate;";
        dbDelta($sql);

        // Table for Work Experience
        $experience_table = $wpdb->prefix . 'skd_pl_experience';
        $sql = "CREATE TABLE $experience_table (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            job_title VARCHAR(255) NOT NULL,
            company_name VARCHAR(255) NULL,
            location VARCHAR(255) NULL,
            employment_type ENUM('full-time', 'part-time', 'freelance', 'contract', 'internship') DEFAULT 'full-time',
            start_date DATE NULL,
            end_date DATE NULL,
            is_current TINYINT(1) DEFAULT 0,
            description TEXT NULL,
            skills_used TEXT NULL COMMENT 'JSON array of skills',
            sort_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX user_id (user_id),
            INDEX is_current (is_current),
            INDEX sort_order (sort_order)
        ) $charset_collate;";
        dbDelta($sql);

        // Table for Saved/Favorited Items
        $favorites_table = $wpdb->prefix . 'skd_pl_favorites';
        $sql = "CREATE TABLE $favorites_table (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            item_type ENUM('vda', 'studio', 'job', 'portfolio') NOT NULL,
            item_id BIGINT(20) UNSIGNED NOT NULL,
            notes TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_favorite (user_id, item_type, item_id),
            INDEX user_id (user_id),
            INDEX item_type (item_type),
            INDEX item_id (item_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Add more tables here as the project grows
        // Example:
        // $another_table = $wpdb->prefix . 'skd_pl_another_table';
        // $sql = "CREATE TABLE $another_table ( ... )";
        // dbDelta($sql);
    }

    /**
     * Delete database tables on plugin uninstall
     */
    public static function delete_tables()
    {
        global $wpdb;

        // // Delete Feature Listing Table
        // $feature_table = $wpdb->prefix . 'skd_pl_features';
        // $wpdb->query("DROP TABLE IF EXISTS $feature_table");

        // // Delete Category Table
        // $category_table = $wpdb->prefix . 'skd_pl_categories';
        // $wpdb->query("DROP TABLE IF EXISTS $category_table");

        // // Delete Location Table
        // $location_table = $wpdb->prefix . 'skd_pl_locations';
        // $wpdb->query("DROP TABLE IF EXISTS $location_table");

        // // Delete Tags Table
        // $tags_table = $wpdb->prefix . 'skd_pl_tags';
        // $wpdb->query("DROP TABLE IF EXISTS $tags_table");

        // // Delete Listing Form Builder Table
        // $listing_form_builder_table = $wpdb->prefix . 'skd_form_builder';
        // $wpdb->query("DROP TABLE IF EXISTS $listing_form_builder_table");

        // // Delete Price Plans Table
        // $price_plans_table = $wpdb->prefix . 'skd_pl_price_plans';
        // $wpdb->query("DROP TABLE IF EXISTS $price_plans_table");

        // // Delete Coupons Table
        // $coupons_table = $wpdb->prefix . 'skd_pl_coupons';
        // $wpdb->query("DROP TABLE IF EXISTS $coupons_table");

        // // Delete Listing Table
        // $listing_table = $wpdb->prefix . 'skd_pl_listings';
        // $wpdb->query("DROP TABLE IF EXISTS $listing_table");

        // // Delete Order Table
        // $order_table = $wpdb->prefix . 'skd_pl_orders';
        // $wpdb->query("DROP TABLE IF EXISTS $order_table");

        // // Delete Coupon Usage Table
        // $coupon_usage_table = $wpdb->prefix . 'skd_pl_coupon_usage';
        // $wpdb->query("DROP TABLE IF EXISTS $coupon_usage_table");

        // // Delete Listing Contact Form Table
        // $listing_contact_form_table = $wpdb->prefix . 'skd_pl_listing_contact_form';
        // $wpdb->query("DROP TABLE IF EXISTS $listing_contact_form_table");

        // Add more tables here as the project grows
        // Example:
        // $another_table = $wpdb->prefix . 'skd_pl_another_table';
        // $wpdb->query("DROP TABLE IF EXISTS $another_table");
    }
}
