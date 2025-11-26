<?php

/**
 * Debug script for saved VDAs
 */

define('WP_USE_THEMES', false);
require_once('../../../../../wp-load.php');

global $wpdb;

echo "<h2>Debug: Saved VDAs</h2>";

// Check current user
$current_user_id = get_current_user_id();
echo "<p>Current User ID: " . $current_user_id . "</p>";

if ($current_user_id) {
    $user = get_userdata($current_user_id);
    echo "<p>User: " . $user->display_name . " (" . implode(', ', $user->roles) . ")</p>";
}

// Check saved VDAs table
$table = $wpdb->prefix . 'skd_pl_saved_vdas';
echo "<h3>Table: " . $table . "</h3>";

// Get all saved VDAs
$all_saved = $wpdb->get_results("SELECT * FROM {$table}");
echo "<p>Total saved VDAs in table: " . count($all_saved) . "</p>";

if ($all_saved) {
    echo "<h4>All Saved VDAs:</h4>";
    echo "<pre>";
    print_r($all_saved);
    echo "</pre>";
}

// Get saved VDAs for current user (if logged in)
if ($current_user_id) {
    $user_saved = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM {$table} WHERE employer_id = %d",
        $current_user_id
    ));
    echo "<h4>Saved VDAs for current user:</h4>";
    echo "<p>Count: " . count($user_saved) . "</p>";
    if ($user_saved) {
        echo "<pre>";
        print_r($user_saved);
        echo "</pre>";
    }
}

// Test the full query from get_saved_vdas
if ($current_user_id) {
    echo "<h3>Full Query Test:</h3>";
    $saved_vdas = $wpdb->get_results($wpdb->prepare(
        "SELECT u.ID as user_id, u.display_name, u.user_email,
         p.tagline, p.hourly_rate, p.avatar_url, p.rating, p.total_reviews,
         p.city, p.country, p.timezone, p.bio, p.skills, p.specializations, 
         p.project_types, p.verified, s.saved_at, s.notes
         FROM {$wpdb->prefix}skd_pl_saved_vdas s
         INNER JOIN {$wpdb->users} u ON s.vda_id = u.ID
         INNER JOIN {$wpdb->prefix}skd_pl_user_profiles p ON u.ID = p.user_id
         WHERE s.employer_id = %d
         ORDER BY s.saved_at DESC",
        $current_user_id
    ));

    echo "<p>Full query result count: " . count($saved_vdas) . "</p>";

    if ($wpdb->last_error) {
        echo "<p style='color: red;'>SQL Error: " . $wpdb->last_error . "</p>";
    }

    if ($saved_vdas) {
        echo "<pre>";
        print_r($saved_vdas);
        echo "</pre>";
    } else {
        echo "<p>No results from full query</p>";

        // Check if profiles exist for the saved VDAs
        $vda_ids = $wpdb->get_col($wpdb->prepare(
            "SELECT vda_id FROM {$table} WHERE employer_id = %d",
            $current_user_id
        ));

        if ($vda_ids) {
            echo "<h4>Checking VDA profiles:</h4>";
            foreach ($vda_ids as $vda_id) {
                $profile = $wpdb->get_row($wpdb->prepare(
                    "SELECT * FROM {$wpdb->prefix}skd_pl_user_profiles WHERE user_id = %d",
                    $vda_id
                ));
                echo "<p>VDA ID " . $vda_id . ": " . ($profile ? "Profile exists" : "<strong>NO PROFILE</strong>") . "</p>";
                if ($profile) {
                    echo "<pre>";
                    print_r($profile);
                    echo "</pre>";
                }
            }
        }
    }
}
