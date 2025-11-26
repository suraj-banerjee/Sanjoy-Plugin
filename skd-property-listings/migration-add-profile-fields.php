<?php

/**
 * Database migration script to add new profile fields
 * Run this once via WordPress admin or WP-CLI
 */

// Only run if WordPress is loaded
if (!defined('ABSPATH')) {
    die('Cannot access this file directly.');
}

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
        echo "Added column: {$column}\n";
    } else {
        echo "Column already exists: {$column}\n";
    }
}

// Update bio column to TEXT if it's not already
$wpdb->query("ALTER TABLE `{$table}` MODIFY COLUMN `bio` TEXT NULL COMMENT 'Full About Me section with HTML from wp_editor';");
echo "Updated bio column to support HTML content\n";

// Update availability_status column from ENUM to VARCHAR to support new values
$wpdb->query("ALTER TABLE `{$table}` MODIFY COLUMN `availability_status` VARCHAR(50) NULL COMMENT 'full-time, part-time, project-based, contract, freelance, unavailable';");
echo "Updated availability_status column to VARCHAR\n";

// Update experience_level column to add 'not-set' option
$wpdb->query("ALTER TABLE `{$table}` MODIFY COLUMN `experience_level` ENUM('not-set', 'junior', 'mid', 'senior', 'expert') NULL;");
echo "Updated experience_level column to include 'not-set'\n";

// Ensure response_time column exists
$row = $wpdb->get_results("SHOW COLUMNS FROM `{$table}` LIKE 'response_time'");
if (empty($row)) {
    $wpdb->query("ALTER TABLE `{$table}` ADD COLUMN `response_time` VARCHAR(50) NULL COMMENT 'within-1-hour, within-few-hours, within-24-hours, within-48-hours, few-days' AFTER `availability_status`;");
    echo "Added column: response_time\n";
} else {
    echo "Column already exists: response_time\n";
}

echo "Migration completed successfully!\n";
