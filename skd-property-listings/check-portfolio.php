<?php
require_once dirname(__FILE__, 6) . '/wp-load.php';
global $wpdb;
$vda_id = isset($_GET['vda_id']) ? intval($_GET['vda_id']) : 0;
if (!$vda_id) {
    echo "No vda_id provided.";
    exit;
}
$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}skd_pl_portfolio WHERE user_id = %d", $vda_id));
echo "Found " . count($results) . " portfolio items for user_id $vda_id\n";
foreach ($results as $row) {
    echo "- " . $row->title . " (status: " . $row->status . ")\n";
}
