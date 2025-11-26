<?php

/**
 * Test script to update VDA profile with sample data
 * Run this once to populate demo data
 */

require_once(__DIR__ . '/../../../wp-load.php');

global $wpdb;

$vda_id = 5; // Sarah Johnson

// Sample data
$short_description = "Interior designer specializing in residential spaces with 5+ years of 3D visualization experience.";

$bio = "<p>Hello! I'm Sarah, a passionate interior designer with over 5 years of experience creating beautiful residential spaces. I specialize in transforming ideas into stunning 3D visualizations that help clients see their dream spaces before construction begins.</p>

<p>My expertise lies in residential design, particularly kitchens and bathrooms, where I combine functionality with aesthetic appeal. I'm proficient in SketchUp, V-Ray, and various rendering tools to deliver photorealistic visualizations.</p>

<p>I pride myself on clear communication, meeting deadlines, and going above and beyond to ensure client satisfaction. Whether you need a single room makeover or a complete home renovation design, I'm here to bring your vision to life.</p>";

$what_i_offer = "<ul>
<li>3D modeling and space planning</li>
<li>Photorealistic renderings</li>
<li>Material and finish selections</li>
<li>Design presentations and moodboards</li>
<li>Revisions until you're completely satisfied</li>
</ul>";

// Update profile
$updated = $wpdb->update(
    $wpdb->prefix . 'skd_pl_user_profiles',
    [
        'short_description' => $short_description,
        'bio' => $bio,
        'what_i_offer' => $what_i_offer,
        'years_experience' => 5,
        'languages_spoken' => json_encode(['English', 'Spanish']),
    ],
    ['user_id' => $vda_id],
    ['%s', '%s', '%s', '%d', '%s'],
    ['%d']
);

if ($updated !== false) {
    echo "✅ Profile updated successfully for user ID: $vda_id\n\n";

    // Verify
    $profile = $wpdb->get_row($wpdb->prepare(
        "SELECT user_id, short_description, bio, what_i_offer, years_experience, languages_spoken FROM {$wpdb->prefix}skd_pl_user_profiles WHERE user_id = %d",
        $vda_id
    ));

    echo "Short Description: " . $profile->short_description . "\n\n";
    echo "Bio length: " . strlen($profile->bio) . " chars\n\n";
    echo "What I Offer length: " . strlen($profile->what_i_offer) . " chars\n\n";
    echo "Years Experience: " . $profile->years_experience . "\n\n";
    echo "Languages: " . $profile->languages_spoken . "\n\n";
} else {
    echo "❌ Error updating profile\n";
    echo "Error: " . $wpdb->last_error . "\n";
}
