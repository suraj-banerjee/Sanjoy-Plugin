<?php

/**
 * Create Demo VDA Users
 * Run this file once to create sample VDA accounts for testing
 * Access: yourdomain.com/wp-content/plugins/skd-property-listings/create-demo-vdas.php
 */

// Load WordPress
require_once('../../../../../wp-load.php');

if (!current_user_can('administrator')) {
    die('Only administrators can run this script.');
}

global $wpdb;

// Demo VDA data
$demo_vdas = [
    [
        'username' => 'sarah_renders',
        'email' => 'sarah.renders@demo.com',
        'display_name' => 'Sarah Mitchell',
        'first_name' => 'Sarah',
        'last_name' => 'Mitchell',
        'tagline' => 'Expert 3D Rendering Specialist | Photorealistic Visualizations',
        'bio' => 'With over 8 years of experience in architectural visualization, I specialize in creating stunning photorealistic 3D renders for interior design projects. My expertise includes V-Ray, Corona Renderer, and advanced post-production techniques.',
        'hourly_rate' => 45.00,
        'experience_level_name' => 'Expert',
        'timezone_name' => 'EST',
        'availability_name' => 'Full-time',
        'skills' => ['3ds Max', 'V-Ray', 'Photoshop', 'AutoCAD'],
        'project_types' => ['Residential', 'Commercial'],
        'service_types' => ['3D Rendering', '3D Modeling'],
        'is_verified' => 1,
        'is_featured' => 1,
        'average_rating' => 4.9
    ],
    [
        'username' => 'mike_autocad',
        'email' => 'mike.cad@demo.com',
        'display_name' => 'Mike Johnson',
        'first_name' => 'Mike',
        'last_name' => 'Johnson',
        'tagline' => 'AutoCAD Expert | Technical Drawing Specialist',
        'bio' => 'Precision-focused CAD technician with 6 years of experience in creating detailed technical drawings and floor plans. I help interior designers bring their concepts to life with accurate and professional CAD documentation.',
        'hourly_rate' => 35.00,
        'experience_level_name' => 'Intermediate',
        'timezone_name' => 'PST',
        'availability_name' => 'Part-time',
        'skills' => ['AutoCAD', 'SketchUp', 'Revit'],
        'project_types' => ['Residential', 'Office'],
        'service_types' => ['CAD Drafting', 'Technical Drawings'],
        'is_verified' => 1,
        'is_featured' => 0,
        'average_rating' => 4.7
    ],
    [
        'username' => 'emma_sketchup',
        'email' => 'emma.design@demo.com',
        'display_name' => 'Emma Rodriguez',
        'first_name' => 'Emma',
        'last_name' => 'Rodriguez',
        'tagline' => 'SketchUp Pro | 3D Space Planning Enthusiast',
        'bio' => 'Creative 3D modeler specializing in SketchUp with 5 years of experience. I create detailed 3D models and space planning solutions that help clients visualize their dream interiors before implementation.',
        'hourly_rate' => 28.00,
        'experience_level_name' => 'Intermediate',
        'timezone_name' => 'CST',
        'availability_name' => 'Full-time',
        'skills' => ['SketchUp', 'V-Ray', 'Photoshop', 'Lumion'],
        'project_types' => ['Residential', 'Hospitality'],
        'service_types' => ['3D Modeling', 'Space Planning'],
        'is_verified' => 0,
        'is_featured' => 0,
        'average_rating' => 4.5
    ],
    [
        'username' => 'david_lumion',
        'email' => 'david.viz@demo.com',
        'display_name' => 'David Chen',
        'first_name' => 'David',
        'last_name' => 'Chen',
        'tagline' => 'Lumion Specialist | Architectural Animation Expert',
        'bio' => '10+ years creating stunning architectural visualizations and animations. I specialize in Lumion, bringing static designs to life with cinematic walkthroughs and immersive 360° presentations.',
        'hourly_rate' => 55.00,
        'experience_level_name' => 'Expert',
        'timezone_name' => 'GMT',
        'availability_name' => 'Full-time',
        'skills' => ['Lumion', 'SketchUp', '3ds Max', 'Photoshop'],
        'project_types' => ['Commercial', 'Hospitality', 'Retail'],
        'service_types' => ['3D Rendering', 'Animation'],
        'is_verified' => 1,
        'is_featured' => 1,
        'average_rating' => 5.0
    ],
    [
        'username' => 'lisa_revit',
        'email' => 'lisa.bim@demo.com',
        'display_name' => 'Lisa Anderson',
        'first_name' => 'Lisa',
        'last_name' => 'Anderson',
        'tagline' => 'Revit BIM Coordinator | Interior Architecture',
        'bio' => 'BIM-certified professional with 7 years of experience in Revit. I create intelligent 3D models with accurate material specifications, helping designers streamline their workflow and reduce errors.',
        'hourly_rate' => 42.00,
        'experience_level_name' => 'Intermediate',
        'timezone_name' => 'EST',
        'availability_name' => 'Full-time',
        'skills' => ['Revit', 'AutoCAD', 'SketchUp'],
        'project_types' => ['Commercial', 'Office'],
        'service_types' => ['BIM Modeling', 'CAD Drafting'],
        'is_verified' => 1,
        'is_featured' => 0,
        'average_rating' => 4.8
    ],
    [
        'username' => 'carlos_corona',
        'email' => 'carlos.render@demo.com',
        'display_name' => 'Carlos Martinez',
        'first_name' => 'Carlos',
        'last_name' => 'Martinez',
        'tagline' => 'Corona Renderer Expert | Realistic Lighting Specialist',
        'bio' => 'Passionate about photorealism with 6 years of experience using Corona Renderer. I create warm, inviting visualizations that showcase materials and lighting with exceptional realism.',
        'hourly_rate' => 38.00,
        'experience_level_name' => 'Intermediate',
        'timezone_name' => 'PST',
        'availability_name' => 'Part-time',
        'skills' => ['3ds Max', 'Corona Renderer', 'Photoshop'],
        'project_types' => ['Residential', 'Hospitality'],
        'service_types' => ['3D Rendering', 'Material Creation'],
        'is_verified' => 0,
        'is_featured' => 0,
        'average_rating' => 4.6
    ],
    [
        'username' => 'priya_enscape',
        'email' => 'priya.design@demo.com',
        'display_name' => 'Priya Sharma',
        'first_name' => 'Priya',
        'last_name' => 'Sharma',
        'tagline' => 'Enscape & VR Visualization Expert',
        'bio' => 'Real-time rendering specialist with 4 years of experience in Enscape. I create interactive VR experiences that allow clients to walk through their designs in real-time.',
        'hourly_rate' => 32.00,
        'experience_level_name' => 'Entry',
        'timezone_name' => 'IST',
        'availability_name' => 'Full-time',
        'skills' => ['Enscape', 'Revit', 'SketchUp'],
        'project_types' => ['Residential', 'Commercial'],
        'service_types' => ['3D Rendering', 'VR Experiences'],
        'is_verified' => 1,
        'is_featured' => 0,
        'average_rating' => 4.4
    ],
    [
        'username' => 'alex_photoshop',
        'email' => 'alex.post@demo.com',
        'display_name' => 'Alex Thompson',
        'first_name' => 'Alex',
        'last_name' => 'Thompson',
        'tagline' => 'Post-Production Artist | Photoshop Wizard',
        'bio' => 'Specialized in post-production and image enhancement with 5 years of experience. I take good renders and make them extraordinary through advanced Photoshop techniques and artistic composition.',
        'hourly_rate' => 30.00,
        'experience_level_name' => 'Intermediate',
        'timezone_name' => 'EST',
        'availability_name' => 'Part-time',
        'skills' => ['Photoshop', 'Illustrator', 'InDesign'],
        'project_types' => ['Residential', 'Commercial', 'Retail'],
        'service_types' => ['Post-Production', 'Presentation Boards'],
        'is_verified' => 0,
        'is_featured' => 0,
        'average_rating' => 4.3
    ]
];

echo "<h2>Creating Demo VDA Users...</h2>";
echo "<ul>";

foreach ($demo_vdas as $vda_data) {
    // Check if user already exists
    $existing_user = get_user_by('email', $vda_data['email']);

    if ($existing_user) {
        echo "<li style='color: orange;'>User {$vda_data['display_name']} already exists (ID: {$existing_user->ID})</li>";
        continue;
    }

    // Create WordPress user
    $user_id = wp_create_user(
        $vda_data['username'],
        '12345678', // Password
        $vda_data['email']
    );

    if (is_wp_error($user_id)) {
        echo "<li style='color: red;'>Failed to create {$vda_data['display_name']}: " . $user_id->get_error_message() . "</li>";
        continue;
    }

    // Update user meta
    wp_update_user([
        'ID' => $user_id,
        'display_name' => $vda_data['display_name'],
        'first_name' => $vda_data['first_name'],
        'last_name' => $vda_data['last_name']
    ]);

    update_user_meta($user_id, 'skd_user_type', 'vda');

    // Get IDs from database
    $experience_level_id = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM {$wpdb->prefix}skd_pl_experience_levels WHERE name = %s LIMIT 1",
        $vda_data['experience_level_name']
    ));

    $timezone_id = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM {$wpdb->prefix}skd_pl_timezones WHERE name LIKE %s LIMIT 1",
        '%' . $vda_data['timezone_name'] . '%'
    ));

    $availability_id = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM {$wpdb->prefix}skd_pl_availability_types WHERE name LIKE %s LIMIT 1",
        '%' . $vda_data['availability_name'] . '%'
    ));

    // Create user profile
    $profile_data = [
        'user_id' => $user_id,
        'user_type' => 'vda',
        'tagline' => $vda_data['tagline'],
        'bio' => $vda_data['bio'],
        'hourly_rate' => $vda_data['hourly_rate'],
        'experience_level_id' => $experience_level_id ?: 1,
        'timezone_id' => $timezone_id ?: 1,
        'availability_id' => $availability_id ?: 1,
        'is_verified' => $vda_data['is_verified'],
        'is_featured' => $vda_data['is_featured'],
        'rating' => $vda_data['average_rating'],
        'created_at' => current_time('mysql'),
        'updated_at' => current_time('mysql')
    ];

    $wpdb->insert($wpdb->prefix . 'skd_pl_user_profiles', $profile_data);

    // Add skills
    foreach ($vda_data['skills'] as $skill_name) {
        $skill_id = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}skd_pl_vda_skills WHERE name = %s LIMIT 1",
            $skill_name
        ));

        if ($skill_id) {
            $wpdb->insert(
                $wpdb->prefix . 'skd_pl_user_skills',
                ['user_id' => $user_id, 'skill_id' => $skill_id],
                ['%d', '%d']
            );
        }
    }

    // Add project types
    foreach ($vda_data['project_types'] as $project_type_name) {
        $project_type_id = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}skd_pl_vda_project_types WHERE name = %s LIMIT 1",
            $project_type_name
        ));

        if ($project_type_id) {
            $wpdb->insert(
                $wpdb->prefix . 'skd_pl_user_project_types',
                ['user_id' => $user_id, 'project_type_id' => $project_type_id],
                ['%d', '%d']
            );
        }
    }

    // Add service types
    foreach ($vda_data['service_types'] as $service_type_name) {
        $service_type_id = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}skd_pl_vda_service_types WHERE name LIKE %s LIMIT 1",
            '%' . $service_type_name . '%'
        ));

        if ($service_type_id) {
            $wpdb->insert(
                $wpdb->prefix . 'skd_pl_user_service_types',
                ['user_id' => $user_id, 'service_type_id' => $service_type_id],
                ['%d', '%d']
            );
        }
    }

    echo "<li style='color: green;'>✓ Created {$vda_data['display_name']} (Username: {$vda_data['username']}, Password: 12345678)</li>";
}

echo "</ul>";
echo "<h3>Demo VDA Creation Complete!</h3>";
echo "<p><strong>Login Credentials for all demo VDAs:</strong></p>";
echo "<ul>";
foreach ($demo_vdas as $vda) {
    echo "<li>Username: <strong>{$vda['username']}</strong> | Password: <strong>12345678</strong> | Email: {$vda['email']}</li>";
}
echo "</ul>";
echo "<p><a href='" . home_url('/find-assistants/') . "'>View Find Assistants Page</a></p>";
echo "<p style='color: red;'><strong>IMPORTANT:</strong> Delete this file after use for security!</p>";
