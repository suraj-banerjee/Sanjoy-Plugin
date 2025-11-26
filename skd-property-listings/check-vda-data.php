<?php

/**
 * Check VDA profile data in database
 */

require_once(__DIR__ . '/../../../wp-load.php');

global $wpdb;

$vda_id = 5;

echo "=== VDA Profile Data Check ===\n\n";

$profile = $wpdb->get_row($wpdb->prepare(
    "SELECT * FROM {$wpdb->prefix}skd_pl_user_profiles WHERE user_id = %d",
    $vda_id
));

if (!$profile) {
    echo "❌ No profile found for user ID: $vda_id\n";
    exit;
}

echo "✅ Profile found for user ID: $vda_id\n\n";

echo "--- Basic Info ---\n";
echo "Tagline: " . ($profile->tagline ?: 'Not set') . "\n";
echo "Short Description: " . ($profile->short_description ?: 'Not set') . "\n";
echo "Bio length: " . strlen($profile->bio ?: '') . " chars\n";
echo "What I Offer length: " . strlen($profile->what_i_offer ?: '') . " chars\n\n";

echo "--- Pricing ---\n";
echo "Hourly Rate: $" . ($profile->hourly_rate ?: '0') . "\n";
echo "Availability: " . ($profile->availability_status ?: 'Not set') . "\n";
echo "Response Time: " . ($profile->response_time ?: 'Not set') . "\n\n";

echo "--- Experience ---\n";
echo "Experience Level: " . ($profile->experience_level ?: 'Not set') . "\n";
echo "Years: " . ($profile->years_experience ?: '0') . "\n";
echo "Education: " . ($profile->education_level ?: 'Not set') . "\n";
echo "Languages: " . ($profile->languages_spoken ?: 'Not set') . "\n\n";

echo "--- Skills & Services ---\n";
$skills = json_decode($profile->skills ?: '[]', true);
echo "Skills count: " . count($skills) . "\n";
echo "Skills IDs: " . ($profile->skills ?: 'None') . "\n";

$services = json_decode($profile->services_offered ?: '[]', true);
echo "Services count: " . count($services) . "\n";

$project_types = json_decode($profile->project_types ?: '[]', true);
echo "Project Types count: " . count($project_types) . "\n";

$specializations = json_decode($profile->specializations ?: '[]', true);
echo "Specializations count: " . count($specializations) . "\n\n";

echo "--- Stats ---\n";
echo "Total Projects: " . ($profile->total_projects ?: '0') . "\n";
echo "Rating: " . ($profile->rating ?: '0') . "\n";
echo "Job Success Rate: " . ($profile->job_success_rate ?: '0') . "%\n\n";

echo "--- Badges ---\n";
echo "Verified: " . ($profile->is_verified ? 'Yes' : 'No') . "\n";
echo "Featured: " . ($profile->is_featured ? 'Yes' : 'No') . "\n";
echo "Top Rated: " . ($profile->is_top_rated ? 'Yes' : 'No') . "\n\n";

// Check portfolio
$portfolio_count = $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM {$wpdb->prefix}skd_pl_portfolio WHERE user_id = %d",
    $vda_id
));
echo "--- Portfolio ---\n";
echo "Portfolio items: " . $portfolio_count . "\n\n";

// Check certifications
$cert_count = $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM {$wpdb->prefix}skd_pl_user_certifications WHERE user_id = %d AND status = 'approved'",
    $vda_id
));
echo "--- Certifications ---\n";
echo "Approved certifications: " . $cert_count . "\n";

echo "\n=== End of Data Check ===\n";
