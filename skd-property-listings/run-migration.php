<?php

/**
 * Temporary migration runner - Access via WordPress admin
 * URL: /wp-admin/admin.php?page=skd-run-migration
 * DELETE THIS FILE AFTER RUNNING MIGRATION
 */

// Add admin menu temporarily
add_action('admin_menu', function () {
    add_menu_page(
        'Run Migration',
        'Run Migration',
        'manage_options',
        'skd-run-migration',
        'skd_run_migration_page',
        'dashicons-database',
        99
    );
});

function skd_run_migration_page()
{
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized access');
    }

    echo '<div class="wrap">';
    echo '<h1>Database Migration</h1>';

    if (isset($_GET['run']) && $_GET['run'] === 'yes') {
        echo '<div style="background: #fff; padding: 20px; border: 1px solid #ccc;">';
        echo '<h2>Migration Output:</h2>';
        echo '<pre>';

        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_user_profiles';

        // Check if columns exist before adding
        $columns_to_add = [
            'short_description' => "ALTER TABLE `{$table}` ADD COLUMN `short_description` TEXT NULL COMMENT 'Brief 300-char description of the professional' AFTER `tagline`;",
            'what_i_offer' => "ALTER TABLE `{$table}` ADD COLUMN `what_i_offer` TEXT NULL COMMENT 'What I Offer section with HTML from wp_editor' AFTER `bio`;"
        ];

        foreach ($columns_to_add as $column => $sql) {
            $row = $wpdb->get_results("SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");
            if (empty($row)) {
                $wpdb->query($sql);
                echo "✓ Added column: {$column}\n";
            } else {
                echo "• Column already exists: {$column}\n";
            }
        }

        // Update bio column to TEXT if it's not already
        $wpdb->query("ALTER TABLE `{$table}` MODIFY COLUMN `bio` TEXT NULL COMMENT 'Full About Me section with HTML from wp_editor';");
        echo "✓ Updated bio column to support HTML content\n";

        // Update availability_status column from ENUM to VARCHAR to support new values
        $wpdb->query("ALTER TABLE `{$table}` MODIFY COLUMN `availability_status` VARCHAR(50) NULL COMMENT 'full-time, part-time, project-based, contract, freelance, unavailable';");
        echo "✓ Updated availability_status column from ENUM to VARCHAR\n";

        // Update experience_level column to add 'not-set' option
        $wpdb->query("ALTER TABLE `{$table}` MODIFY COLUMN `experience_level` ENUM('not-set', 'junior', 'mid', 'senior', 'expert') NULL;");
        echo "✓ Updated experience_level column to include 'not-set'\n";

        // Ensure response_time column exists
        $row = $wpdb->get_results("SHOW COLUMNS FROM `{$table}` LIKE 'response_time'");
        if (empty($row)) {
            $wpdb->query("ALTER TABLE `{$table}` ADD COLUMN `response_time` VARCHAR(50) NULL COMMENT 'within-1-hour, within-few-hours, within-24-hours, within-48-hours, few-days' AFTER `availability_status`;");
            echo "✓ Added column: response_time\n";
        } else {
            echo "• Column already exists: response_time\n";
        }

        echo "\n✅ Migration completed successfully!\n";
        echo "\n⚠️  IMPORTANT: Delete run-migration.php file after running this migration.\n";
        echo '</pre>';
        echo '</div>';
    } else {
        echo '<div class="notice notice-warning" style="padding: 20px;">';
        echo '<h2>⚠️ About to run database migration</h2>';
        echo '<p>This will update the following columns in <code>' . $wpdb->prefix . 'skd_pl_user_profiles</code>:</p>';
        echo '<ul>';
        echo '<li>Add <code>short_description</code> and <code>what_i_offer</code> columns</li>';
        echo '<li>Update <code>bio</code> column to TEXT</li>';
        echo '<li><strong>Change <code>availability_status</code> from ENUM to VARCHAR(50)</strong></li>';
        echo '<li><strong>Update <code>experience_level</code> ENUM to include "not-set"</strong></li>';
        echo '<li>Add <code>response_time</code> column</li>';
        echo '</ul>';
        echo '<p><strong>This fixes the issue where Availability Type and Response Time were not saving.</strong></p>';
        echo '<p><a href="?page=skd-run-migration&run=yes" class="button button-primary" style="font-size: 16px; padding: 10px 20px;">Run Migration Now</a></p>';
        echo '</div>';
    }

    echo '</div>';
}
