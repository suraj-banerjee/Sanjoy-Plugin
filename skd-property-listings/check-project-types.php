<?php

/**
 * Check project types and languages for VDA
 */

require_once(__DIR__ . '/../../../wp-load.php');

global $wpdb;

$vda_id = 5;

echo "=== Checking Project Types & Languages ===\n\n";

$profile = $wpdb->get_row($wpdb->prepare(
    "SELECT project_types, languages_spoken FROM {$wpdb->prefix}skd_pl_user_profiles WHERE user_id = %d",
    $vda_id
));

echo "--- Project Types ---\n";
echo "Raw data: " . ($profile->project_types ?: 'NULL') . "\n";
$pt_array = json_decode($profile->project_types ?: '[]', true);
echo "Decoded: " . print_r($pt_array, true) . "\n";

if (!empty($pt_array)) {
    echo "Fetching names:\n";
    foreach ($pt_array as $type_id) {
        $type = $wpdb->get_row($wpdb->prepare(
            "SELECT id, name, icon FROM {$wpdb->prefix}skd_pl_project_types WHERE id = %d",
            $type_id
        ));
        if ($type) {
            echo "  ID $type_id: {$type->name} (icon: {$type->icon})\n";
        } else {
            echo "  ID $type_id: NOT FOUND\n";
        }
    }
}

echo "\n--- Languages ---\n";
echo "Raw data: " . ($profile->languages_spoken ?: 'NULL') . "\n";
$lang_array = json_decode($profile->languages_spoken ?: '[]', true);
echo "Decoded: " . print_r($lang_array, true) . "\n";

if (!empty($lang_array)) {
    echo "Fetching names:\n";
    foreach ($lang_array as $lang_id) {
        $lang = $wpdb->get_row($wpdb->prepare(
            "SELECT id, name FROM {$wpdb->prefix}skd_pl_languages WHERE id = %d AND status = 'active'",
            $lang_id
        ));
        if ($lang) {
            echo "  ID $lang_id: {$lang->name}\n";
        } else {
            echo "  ID $lang_id: NOT FOUND\n";
        }
    }
}

echo "\n=== All Project Types in Database ===\n";
$all_types = $wpdb->get_results("SELECT id, name, icon, status FROM {$wpdb->prefix}skd_pl_project_types ORDER BY id");
foreach ($all_types as $t) {
    echo "ID {$t->id}: {$t->name} (Status: {$t->status})\n";
}

echo "\n=== All Languages in Database ===\n";
$all_langs = $wpdb->get_results("SELECT id, name, code, status FROM {$wpdb->prefix}skd_pl_languages ORDER BY id");
foreach ($all_langs as $l) {
    echo "ID {$l->id}: {$l->name} ({$l->code}) - Status: {$l->status}\n";
}
