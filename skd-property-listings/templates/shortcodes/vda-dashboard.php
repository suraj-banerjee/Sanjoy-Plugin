<?php

/**
 * VDA Dashboard Template
 * Dashboard for Virtual Design Assistants
 */

if (!defined('ABSPATH')) {
    exit;
}

// Enable WordPress debugging
if (!defined('WP_DEBUG')) {
    define('WP_DEBUG', true);
}
if (!defined('WP_DEBUG_DISPLAY')) {
    define('WP_DEBUG_DISPLAY', true);
}

// Check if user is logged in
if (!is_user_logged_in()) {
    echo '<p>Please <a href="' . home_url('/login/') . '">login</a> to access the VDA dashboard.</p>';
    return;
}

global $wpdb;
$user_id = get_current_user_id();
$user = wp_get_current_user();
$user_type = get_user_meta($user_id, 'skd_user_type', true);

if ($user_type !== 'vda') {
    echo '<p>This dashboard is only for VDA users. Your user type: ' . esc_html($user_type) . '</p>';
    return;
}

// Get VDA profile data with error handling
$vda_profile = null;
try {
    $table_name = $wpdb->prefix . 'skd_pl_user_profiles';

    // Check if table exists
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        echo '<p>Error: User profiles table does not exist. Please contact administrator.</p>';
        return;
    }

    $vda_profile = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}skd_pl_user_profiles WHERE user_id = %d",
        $user_id
    ));

    // Get portfolio categories
    $portfolio_categories = $wpdb->get_results(
        "SELECT * FROM {$wpdb->prefix}skd_pl_portfolio_categories WHERE status = 'active' ORDER BY sort_order ASC, name ASC"
    );

    if (!$vda_profile) {
        // Create profile if not exists
        $insert_result = $wpdb->insert(
            $wpdb->prefix . 'skd_pl_user_profiles',
            ['user_id' => $user_id, 'user_type' => 'vda', 'created_at' => current_time('mysql')],
            ['%d', '%s', '%s']
        );

        if ($insert_result === false) {
            echo '<p>Error creating profile: ' . esc_html($wpdb->last_error) . '</p>';
            return;
        }

        $vda_profile = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}skd_pl_user_profiles WHERE user_id = %d",
            $user_id
        ));
    }
} catch (Exception $e) {
    echo '<p>Exception: ' . esc_html($e->getMessage()) . '</p>';
    return;
}

if (!$vda_profile) {
    echo '<p>Error: Could not load or create profile.</p>';
    return;
}

$profile_completeness = $vda_profile->profile_completeness ?? 0;
$has_active_plan = false; // TODO: Check if user has active plan
?>

<div class="vda-dashboard-wrapper">
    <!-- Dashboard Sidebar -->
    <aside class="vda-sidebar">
        <div class="vda-profile-card">
            <div class="vda-avatar" id="sidebar-avatar">
                <?php if (!empty($vda_profile->avatar_url)): ?>
                    <img src="<?php echo esc_url($vda_profile->avatar_url); ?>" alt="Profile" id="sidebar-avatar-img">
                <?php else: ?>
                    <div class="vda-avatar-placeholder" id="sidebar-avatar-placeholder">
                        <?php echo strtoupper(substr($user->display_name, 0, 2)); ?>
                    </div>
                <?php endif; ?>
                <button type="button" class="vda-avatar-edit-btn" title="Edit Profile Picture">
                    <iconify-icon icon="material-symbols:edit"></iconify-icon>
                </button>
                <?php if ($vda_profile->is_verified): ?>
                    <span class="vda-verified-badge" title="Verified">
                        <iconify-icon icon="material-symbols:verified"></iconify-icon>
                    </span>
                <?php endif; ?>
            </div>
            <!-- Hidden file input for avatar upload -->
            <input type="file" id="avatar-upload" accept="image/*" style="display: none;">
            <h3><?php echo esc_html($user->display_name); ?></h3>
            <?php if (!empty($vda_profile->tagline)): ?>
                <p class="vda-tagline"><?php echo esc_html($vda_profile->tagline); ?></p>
            <?php endif; ?>

            <div class="vda-profile-stats">
                <div class="vda-stat">
                    <span class="vda-stat-value"><?php echo $profile_completeness; ?>%</span>
                    <span class="vda-stat-label">Complete</span>
                </div>
                <div class="vda-stat">
                    <span class="vda-stat-value"><?php echo $vda_profile->total_projects ?? 0; ?></span>
                    <span class="vda-stat-label">Projects</span>
                </div>
                <div class="vda-stat">
                    <span class="vda-stat-value"><?php echo number_format($vda_profile->rating ?? 0, 1); ?></span>
                    <span class="vda-stat-label">Rating</span>
                </div>
            </div>
        </div>

        <nav class="vda-menu">
            <a href="#" class="vda-menu-item active" data-tab="overview">
                <iconify-icon icon="material-symbols:dashboard-outline"></iconify-icon>
                <span>Dashboard</span>
            </a>

            <a href="#" class="vda-menu-item" data-tab="profile">
                <iconify-icon icon="material-symbols:person-outline"></iconify-icon>
                <span>My Profile</span>
            </a>

            <a href="#" class="vda-menu-item" data-tab="settings">
                <iconify-icon icon="material-symbols:settings-outline"></iconify-icon>
                <span>Account Settings</span>
            </a>

            <a href="#" class="vda-menu-item vda-logout" id="vda-logout-btn">
                <iconify-icon icon="material-symbols:logout"></iconify-icon>
                <span>Logout</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="vda-main-content">
        <!-- Overview Tab -->
        <div class="vda-tab-content active" id="tab-overview">
            <div class="vda-header">
                <h1>Welcome back, <?php echo esc_html($user->first_name ?: $user->display_name); ?>!</h1>
                <p>Here's what's happening with your profile</p>
            </div>

            <div class="vda-stats-grid">
                <div class="vda-stat-card">
                    <div class="vda-stat-icon" style="background: #f3e5f5;">
                        <iconify-icon icon="material-symbols:folder-outline" style="color: #7b1fa2;"></iconify-icon>
                    </div>
                    <div class="vda-stat-info">
                        <h4><?php echo $vda_profile->total_projects ?? 0; ?></h4>
                        <p>Portfolio Items</p>
                    </div>
                </div>

                <div class="vda-stat-card">
                    <div class="vda-stat-icon" style="background: #e8f5e9;">
                        <iconify-icon icon="material-symbols:work-outline" style="color: #388e3c;"></iconify-icon>
                    </div>
                    <div class="vda-stat-info">
                        <?php
                        $applications = 0;
                        // Check if table exists before querying
                        $table_name = $wpdb->prefix . 'skd_pl_job_applications';
                        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
                            $applications = $wpdb->get_var($wpdb->prepare(
                                "SELECT COUNT(*) FROM {$wpdb->prefix}skd_pl_job_applications WHERE applicant_id = %d",
                                $user_id
                            ));
                        }
                        ?>
                        <h4><?php echo $applications ?? 0; ?></h4>
                        <p>Applications</p>
                    </div>
                </div>

                <div class="vda-stat-card">
                    <div class="vda-stat-icon" style="background: #fff3e0;">
                        <iconify-icon icon="material-symbols:star-outline" style="color: #f57c00;"></iconify-icon>
                    </div>
                    <div class="vda-stat-info">
                        <h4><?php echo number_format($vda_profile->rating ?? 0, 1); ?> / 5.0</h4>
                        <p>Rating</p>
                    </div>
                </div>
            </div>

            <div class="vda-profile-completion">
                <div class="vda-completion-header">
                    <h3>Profile Completion</h3>
                    <span class="vda-completion-percent"><?php echo $profile_completeness; ?>%</span>
                </div>
                <div class="vda-progress-bar">
                    <div class="vda-progress-fill" style="width: <?php echo $profile_completeness; ?>%"></div>
                </div>
                <?php if ($profile_completeness < 100): ?>
                    <p class="vda-completion-tip">
                        Complete your profile to increase visibility and get more opportunities!
                    </p>
                <?php endif; ?>
            </div>

            <!-- Quick Actions -->
            <div class="vda-quick-actions">
                <h3>Quick Actions</h3>
                <div class="vda-actions-grid">
                    <a href="#" class="vda-action-card" data-tab="profile" data-subtab="basic">
                        <iconify-icon icon="material-symbols:edit-outline"></iconify-icon>
                        <span>Edit Profile</span>
                    </a>
                    <a href="<?php echo home_url('/vda-profile/?vda_id=' . $user_id); ?>" class="vda-action-card" target="_blank">
                        <iconify-icon icon="material-symbols:visibility-outline"></iconify-icon>
                        <span>View Public Profile</span>
                    </a>
                    <a href="<?php echo home_url('/job-board/'); ?>" class="vda-action-card">
                        <iconify-icon icon="material-symbols:work-outline"></iconify-icon>
                        <span>Browse Jobs</span>
                    </a>
                    <a href="#" class="vda-action-card" data-tab="profile" data-subtab="portfolio">
                        <iconify-icon icon="material-symbols:upload-outline"></iconify-icon>
                        <span>Upload Portfolio</span>
                    </a>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="vda-recent-activity">
                <h3>Recent Activity</h3>
                <div class="vda-activity-list">
                    <?php
                    // Collect all recent activities
                    $activities = [];

                    // Get recent job applications
                    if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}skd_pl_job_applications'") == $wpdb->prefix . 'skd_pl_job_applications') {
                        $applications = $wpdb->get_results($wpdb->prepare(
                            "SELECT created_at, status FROM {$wpdb->prefix}skd_pl_job_applications 
                            WHERE applicant_id = %d 
                            ORDER BY created_at DESC 
                            LIMIT 10",
                            $user_id
                        ));
                        foreach ($applications as $app) {
                            $activities[] = [
                                'type' => 'application',
                                'title' => 'Applied to a job',
                                'icon' => 'material-symbols:work-outline',
                                'color' => '#1976d2',
                                'bg' => '#e3f2fd',
                                'time' => strtotime($app->created_at),
                                'badge' => ucfirst($app->status),
                                'badge_class' => 'vda-badge-' . strtolower($app->status)
                            ];
                        }
                    }

                    // Get recent portfolio uploads
                    $portfolio_items = $wpdb->get_results($wpdb->prepare(
                        "SELECT title, created_at FROM {$wpdb->prefix}skd_pl_user_portfolio 
                        WHERE user_id = %d 
                        ORDER BY created_at DESC 
                        LIMIT 10",
                        $user_id
                    ));
                    foreach ($portfolio_items as $item) {
                        $activities[] = [
                            'type' => 'portfolio',
                            'title' => 'Added portfolio: ' . esc_html($item->title),
                            'icon' => 'material-symbols:folder-outline',
                            'color' => '#7c4dff',
                            'bg' => '#ede7f6',
                            'time' => strtotime($item->created_at),
                            'badge' => null
                        ];
                    }

                    // Get recent certifications
                    $certifications = $wpdb->get_results($wpdb->prepare(
                        "SELECT uc.created_at, c.name, uc.status 
                        FROM {$wpdb->prefix}skd_pl_user_certifications uc 
                        JOIN {$wpdb->prefix}skd_pl_certifications c ON uc.certification_id = c.id 
                        WHERE uc.user_id = %d 
                        ORDER BY uc.created_at DESC 
                        LIMIT 10",
                        $user_id
                    ));
                    foreach ($certifications as $cert) {
                        $activities[] = [
                            'type' => 'certification',
                            'title' => 'Earned: ' . esc_html($cert->name),
                            'icon' => 'material-symbols:workspace-premium-outline',
                            'color' => '#f57c00',
                            'bg' => '#fff3e0',
                            'time' => strtotime($cert->created_at),
                            'badge' => ucfirst($cert->status),
                            'badge_class' => 'vda-badge-' . strtolower($cert->status)
                        ];
                    }

                    // Get profile update time
                    if (!empty($vda_profile->updated_at)) {
                        $activities[] = [
                            'type' => 'profile',
                            'title' => 'Updated profile',
                            'icon' => 'material-symbols:person-outline',
                            'color' => '#00897b',
                            'bg' => '#e0f2f1',
                            'time' => strtotime($vda_profile->updated_at),
                            'badge' => null
                        ];
                    }

                    // Sort all activities by time (most recent first)
                    usort($activities, function ($a, $b) {
                        return $b['time'] - $a['time'];
                    });

                    // Take only the 5 most recent
                    $activities = array_slice($activities, 0, 5);

                    if (!empty($activities)):
                        foreach ($activities as $activity):
                            $time_ago = human_time_diff($activity['time'], current_time('timestamp')) . ' ago';
                    ?>
                            <div class="vda-activity-item">
                                <div class="vda-activity-icon" style="background: <?php echo $activity['bg']; ?>;">
                                    <iconify-icon icon="<?php echo $activity['icon']; ?>" style="color: <?php echo $activity['color']; ?>;"></iconify-icon>
                                </div>
                                <div class="vda-activity-content">
                                    <p><strong><?php echo $activity['title']; ?></strong></p>
                                    <span class="vda-activity-time"><?php echo $time_ago; ?></span>
                                </div>
                                <?php if (!empty($activity['badge'])): ?>
                                    <span class="vda-activity-badge <?php echo $activity['badge_class']; ?>">
                                        <?php echo $activity['badge']; ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php
                        endforeach;
                    else:
                        ?>
                        <div class="vda-empty-state">
                            <iconify-icon icon="material-symbols:inbox-outline"></iconify-icon>
                            <p>No recent activity</p>
                            <a href="#" class="vda-btn vda-btn-primary vda-tip-action" data-tab="profile" data-subtab="portfolio">Add Portfolio</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Profile Tips -->
            <?php if ($profile_completeness < 100): ?>
                <div class="vda-profile-tips">
                    <h3>Complete Your Profile</h3>
                    <div class="vda-tips-list">
                        <?php
                        $tips = [];
                        if (empty($vda_profile->bio)) {
                            $tips[] = ['icon' => 'material-symbols:description-outline', 'text' => 'Add a professional bio', 'action' => 'profile', 'subtab' => 'basic'];
                        }
                        if (empty($vda_profile->skills)) {
                            $tips[] = ['icon' => 'material-symbols:star-outline', 'text' => 'List your skills', 'action' => 'profile', 'subtab' => 'skills'];
                        }
                        if (($vda_profile->total_projects ?? 0) == 0) {
                            $tips[] = ['icon' => 'material-symbols:folder-outline', 'text' => 'Upload portfolio items', 'action' => 'profile', 'subtab' => 'portfolio'];
                        }
                        if (empty($vda_profile->hourly_rate)) {
                            $tips[] = ['icon' => 'material-symbols:payments-outline', 'text' => 'Set your hourly rate', 'action' => 'profile', 'subtab' => 'rates'];
                        }

                        foreach ($tips as $tip):
                        ?>
                            <div class="vda-tip-item">
                                <iconify-icon icon="<?php echo $tip['icon']; ?>"></iconify-icon>
                                <span><?php echo $tip['text']; ?></span>
                                <button class="vda-btn vda-btn-secondary vda-btn-sm vda-tip-action" data-tab="<?php echo $tip['action']; ?>" data-subtab="<?php echo $tip['subtab']; ?>">
                                    Complete
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Profile Tab -->
        <div class="vda-tab-content" id="tab-profile">
            <?php
            // Load profile data for editing
            $profile = SKD_PL_VDA_Profile::get_user_profile($user_id);
            $skills = json_decode($profile->skills ?? '[]', true) ?: [];
            $services = json_decode($profile->services_offered ?? '[]', true) ?: [];
            $specializations = json_decode($profile->specializations ?? '[]', true) ?: [];
            $project_types = json_decode($profile->project_types ?? '[]', true) ?: [];
            $service_types = json_decode($profile->service_types ?? '[]', true) ?: [];
            ?>

            <div class="vda-profile-edit-wrapper">
                <div class="vda-profile-header">
                    <h1>Edit Your Profile</h1>
                    <div class="vda-profile-completeness-bar">
                        <span>Profile Completeness: <strong><?php echo $profile->profile_completeness ?? 0; ?>%</strong></span>
                        <div class="vda-progress-bar">
                            <div class="vda-progress-fill" style="width: <?php echo $profile->profile_completeness ?? 0; ?>%"></div>
                        </div>
                    </div>
                </div>

                <div class="vda-profile-tabs">
                    <nav class="vda-tab-nav">
                        <button class="vda-tab-btn active" data-tab="basic">Basic Info</button>
                        <button class="vda-tab-btn" data-tab="skills">Skills & Specializations</button>
                        <button class="vda-tab-btn" data-tab="portfolio">Portfolio</button>
                        <button class="vda-tab-btn" data-tab="rates">Rates & Availability</button>
                        <button class="vda-tab-btn" data-tab="certifications">Certifications/Badges</button>
                    </nav>

                    <div class="vda-profile-tab-content">
                        <!-- Basic Info Tab -->
                        <div class="vda-profile-tab-pane active" id="profile-tab-basic">
                            <form id="basic-info-form" class="vda-settings-form">
                                <!-- Name Fields -->
                                <div class="vda-form-row">
                                    <div class="vda-form-group">
                                        <label>First Name *</label>
                                        <input type="text" name="first_name" value="<?php echo esc_attr($user->first_name); ?>" required>
                                    </div>
                                    <div class="vda-form-group">
                                        <label>Last Name *</label>
                                        <input type="text" name="last_name" value="<?php echo esc_attr($user->last_name); ?>" required>
                                    </div>
                                </div>

                                <!-- Professional Tagline -->
                                <div class="vda-form-group">
                                    <label>Professional Tagline *</label>
                                    <input type="text" name="tagline" value="<?php echo esc_attr($profile->tagline ?? ''); ?>" placeholder="e.g., Expert 3D Rendering Specialist | Interior Design Visualizer" required maxlength="100">
                                    <small>This appears right below your name (max 100 characters)</small>
                                </div>

                                <!-- Short Description -->
                                <div class="vda-form-group">
                                    <label>Short Description *</label>
                                    <textarea name="short_description" rows="3" placeholder="A brief overview of your expertise and what makes you unique..." maxlength="300" required><?php echo esc_textarea($profile->short_description ?? ''); ?></textarea>
                                    <small>Brief summary shown in search results (max 300 characters)</small>
                                </div>

                                <!-- About Me - Rich Text Editor -->
                                <div class="vda-form-group">
                                    <label>About Me *</label>
                                    <?php
                                    $about_me_content = $profile->bio ?? '';
                                    wp_editor($about_me_content, 'about_me', array(
                                        'textarea_name' => 'bio',
                                        'textarea_rows' => 10,
                                        'media_buttons' => false,
                                        'teeny' => false,
                                        'tinymce' => array(
                                            'toolbar1' => 'formatselect,bold,italic,underline,bullist,numlist,link,unlink,blockquote',
                                            'toolbar2' => '',
                                        ),
                                        'quicktags' => array('buttons' => 'strong,em,link,ul,ol,li')
                                    ));
                                    ?>
                                    <small>Tell clients about your background, experience, and expertise</small>
                                </div>

                                <!-- What I Offer - Rich Text Editor -->
                                <div class="vda-form-group">
                                    <label>What I Offer *</label>
                                    <?php
                                    $what_i_offer_content = $profile->what_i_offer ?? '';
                                    wp_editor($what_i_offer_content, 'what_i_offer', array(
                                        'textarea_name' => 'what_i_offer',
                                        'textarea_rows' => 8,
                                        'media_buttons' => false,
                                        'teeny' => false,
                                        'tinymce' => array(
                                            'toolbar1' => 'formatselect,bold,italic,underline,bullist,numlist,link,unlink',
                                            'toolbar2' => '',
                                        ),
                                        'quicktags' => array('buttons' => 'strong,em,link,ul,ol,li')
                                    ));
                                    ?>
                                    <small>List the services and solutions you provide to clients</small>
                                </div>

                                <!-- Location & Timezone -->
                                <div class="vda-form-row">
                                    <div class="vda-form-group">
                                        <label>Country *</label>
                                        <select name="country" class="vda-select" required>
                                            <option value="">Select Country</option>
                                            <?php
                                            $countries = array(
                                                'US' => 'United States',
                                                'GB' => 'United Kingdom',
                                                'CA' => 'Canada',
                                                'AU' => 'Australia',
                                                'IN' => 'India',
                                                'DE' => 'Germany',
                                                'FR' => 'France',
                                                'IT' => 'Italy',
                                                'ES' => 'Spain',
                                                'NL' => 'Netherlands',
                                                'PL' => 'Poland',
                                                'BR' => 'Brazil',
                                                'MX' => 'Mexico',
                                                'AR' => 'Argentina',
                                                'PH' => 'Philippines',
                                                'PK' => 'Pakistan',
                                                'BD' => 'Bangladesh',
                                                'AE' => 'United Arab Emirates',
                                                'SG' => 'Singapore',
                                                'MY' => 'Malaysia',
                                                'TH' => 'Thailand',
                                                'VN' => 'Vietnam',
                                                'ID' => 'Indonesia',
                                                'ZA' => 'South Africa',
                                                'EG' => 'Egypt',
                                                'NG' => 'Nigeria',
                                                'KE' => 'Kenya',
                                                'JP' => 'Japan',
                                                'CN' => 'China',
                                                'KR' => 'South Korea',
                                                'RU' => 'Russia',
                                                'UA' => 'Ukraine',
                                                'TR' => 'Turkey',
                                                'SA' => 'Saudi Arabia',
                                            );
                                            foreach ($countries as $code => $name) {
                                                $selected = ($profile->country ?? '') == $code ? 'selected' : '';
                                                echo "<option value='$code' $selected>$name</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="vda-form-group">
                                        <label>City *</label>
                                        <input type="text" name="city" value="<?php echo esc_attr($profile->city ?? ''); ?>" placeholder="e.g., New York" required>
                                    </div>
                                </div>

                                <!-- Timezone -->
                                <div class="vda-form-group">
                                    <label>Timezone *</label>
                                    <select name="timezone" class="vda-select" required>
                                        <option value="">Select Timezone</option>
                                        <?php
                                        $timezones = SKD_PL_Timezones::get_timezones(['status' => 'active']);
                                        foreach ($timezones as $tz) {
                                            $selected = ($profile->timezone ?? '') == $tz->id ? 'selected' : '';
                                            $display_text = $tz->name . ' - ' . $tz->offset;
                                            echo "<option value='{$tz->id}' $selected>{$display_text}</option>";
                                        }
                                        ?>
                                    </select>
                                    <small>Helps clients know when you're typically available</small>
                                </div> <button type="submit" class="vda-btn vda-btn-primary">
                                    <iconify-icon icon="material-symbols:save-outline"></iconify-icon>
                                    Save Basic Info
                                </button>
                            </form>
                        </div>

                        <!-- Skills & Specializations Tab -->
                        <div class="vda-profile-tab-pane" id="profile-tab-skills">
                            <form id="skills-services-form" class="vda-settings-form">
                                <div class="vda-form-group">
                                    <label>Skills & Software *</label>
                                    <select name="skills[]" multiple class="vda-select2" style="width: 100%">
                                        <?php
                                        $all_skills = SKD_PL_VDA_Skills::get_skills(['status' => 'active']);
                                        foreach ($all_skills as $skill) {
                                            $selected = in_array($skill->id, $skills) ? 'selected' : '';
                                            echo "<option value='{$skill->id}' $selected>{$skill->name}</option>";
                                        }
                                        ?>
                                    </select>
                                    <small>Select the software and design skills you're proficient in (e.g., SketchUp, V-Ray, AutoCAD, 3ds Max)</small>
                                </div>

                                <div class="vda-form-group">
                                    <label>Specializations *</label>
                                    <select name="specializations[]" multiple class="vda-select2" style="width: 100%">
                                        <?php
                                        $all_specializations = SKD_PL_VDA_Specializations::get_specializations(['status' => 'active']);
                                        foreach ($all_specializations as $spec) {
                                            $selected = in_array($spec->id, $specializations) ? 'selected' : '';
                                            echo "<option value='{$spec->id}' $selected>{$spec->name}</option>";
                                        }
                                        ?>
                                    </select>
                                    <small>Select your areas of specialization (e.g., Residential Design, Commercial Spaces, 3D Rendering)</small>
                                </div>

                                <div class="vda-form-group">
                                    <label>Project Types *</label>
                                    <select name="project_types[]" multiple class="vda-select2" style="width: 100%">
                                        <?php
                                        $all_project_types = SKD_PL_VDA_Project_Types::get_project_types(['status' => 'active']);
                                        foreach ($all_project_types as $pt) {
                                            $selected = in_array($pt->id, $project_types) ? 'selected' : '';
                                            echo "<option value='{$pt->id}' $selected>{$pt->name}</option>";
                                        }
                                        ?>
                                    </select>
                                    <small>Select the types of projects you work on (e.g., Residential, Commercial, Hospitality)</small>
                                </div>

                                <div class="vda-form-group">
                                    <label>Service Types *</label>
                                    <select name="service_types[]" multiple class="vda-select2" style="width: 100%">
                                        <?php
                                        $all_service_types = SKD_PL_VDA_Service_Types::get_service_types(['status' => 'active']);
                                        foreach ($all_service_types as $st) {
                                            $selected = in_array($st->id, $service_types) ? 'selected' : '';
                                            echo "<option value='{$st->id}' $selected>{$st->name}</option>";
                                        }
                                        ?>
                                    </select>
                                    <small>Select the specific services you offer (e.g., CAD Drafting, 3D Rendering, Space Planning)</small>
                                </div>

                                <button type="submit" class="vda-btn vda-btn-primary">
                                    <iconify-icon icon="material-symbols:save-outline"></iconify-icon>
                                    Save Skills & Specializations
                                </button>
                            </form>
                        </div>

                        <!-- Portfolio Tab -->
                        <div class="vda-profile-tab-pane" id="profile-tab-portfolio">
                            <div class="vda-portfolio-header">
                                <h3>My Portfolio</h3>
                                <button type="button" class="vda-btn vda-btn-primary" id="add-portfolio-btn">
                                    <iconify-icon icon="material-symbols:add"></iconify-icon>
                                    Add Portfolio
                                </button>
                            </div>

                            <div class="vda-portfolio-grid" id="portfolio-items-list">
                                <p class="vda-loading">Loading portfolio...</p>
                            </div>
                        </div>

                        <!-- Rates & Availability Tab -->
                        <div class="vda-profile-tab-pane" id="profile-tab-rates">
                            <form id="rates-form" class="vda-settings-form">
                                <!-- Rates Section -->
                                <h4 style="margin-bottom: 20px; color: #333;">Rates & Pricing</h4>
                                <div class="vda-form-row">
                                    <div class="vda-form-group">
                                        <label>Hourly Rate (USD) *</label>
                                        <input type="number" name="hourly_rate" value="<?php echo esc_attr($profile->hourly_rate ?? ''); ?>" step="0.01" min="0" placeholder="e.g., 25.00" required>
                                        <small>Your standard hourly rate in US Dollars</small>
                                    </div>
                                    <div class="vda-form-group">
                                        <label>Availability Type *</label>
                                        <select name="availability_status" class="vda-select" required>
                                            <option value="">Select Availability Type</option>
                                            <?php
                                            $availability_types = SKD_PL_Availability_Types::get_availability_types(['status' => 'active']);
                                            foreach ($availability_types as $type) {
                                                $selected = ($profile->availability_status ?? '') == $type->id ? 'selected' : '';
                                                echo "<option value='{$type->id}' $selected>{$type->name}</option>";
                                            }
                                            ?>
                                        </select>
                                        <small>Your preferred work arrangement</small>
                                    </div>
                                </div>

                                <!-- Experience Section -->
                                <h4 style="margin: 30px 0 20px; color: #333;">Experience & Skills</h4>
                                <div class="vda-form-row">
                                    <div class="vda-form-group">
                                        <label>Years of Experience *</label>
                                        <input type="number" name="years_experience" id="years_experience" value="<?php echo esc_attr($profile->years_experience ?? ''); ?>" min="0" max="50" placeholder="e.g., 5" required>
                                        <small>Total years of professional experience</small>
                                    </div>
                                    <div class="vda-form-group">
                                        <label>Experience Level</label>
                                        <input type="text" id="experience_level_display" value="<?php
                                                                                                $years = $profile->years_experience ?? 0;
                                                                                                if ($years == 0) {
                                                                                                    echo 'Not set';
                                                                                                } else {
                                                                                                    $level = SKD_PL_Experience_Levels::get_level_by_years($years);
                                                                                                    if ($level) {
                                                                                                        $range = $level->years_max ? "{$level->years_min}-{$level->years_max}" : "{$level->years_min}+";
                                                                                                        echo esc_attr($level->name . ' (' . $range . ' years)');
                                                                                                    } else {
                                                                                                        echo 'Not set';
                                                                                                    }
                                                                                                }
                                                                                                ?>" readonly style="background: #f5f5f5; cursor: not-allowed;">
                                        <small>Auto-calculated based on years of experience</small>
                                    </div>
                                </div>

                                <!-- Response Time & Languages -->
                                <h4 style="margin: 30px 0 20px; color: #333;">Communication & Availability</h4>
                                <div class="vda-form-row">
                                    <div class="vda-form-group">
                                        <label>Response Time *</label>
                                        <select name="response_time" class="vda-select" required>
                                            <option value="">Select Response Time</option>
                                            <?php
                                            $response_times = SKD_PL_Response_Times::get_response_times(['status' => 'active']);
                                            foreach ($response_times as $rt) {
                                                $selected = ($profile->response_time ?? '') == $rt->id ? 'selected' : '';
                                                echo "<option value='{$rt->id}' $selected>{$rt->name}</option>";
                                            }
                                            ?>
                                        </select>
                                        <small>How quickly you typically respond to inquiries</small>
                                    </div>
                                    <div class="vda-form-group">
                                        <label>Languages Spoken *</label>
                                        <select name="languages_spoken[]" multiple class="vda-select2-languages" style="width: 100%" required>
                                            <?php
                                            $all_languages = SKD_PL_Languages::get_languages(['status' => 'active']);
                                            $user_languages = json_decode($profile->languages_spoken ?? '[]', true) ?: [];
                                            // Ensure user languages are integers for comparison
                                            $user_languages = array_map('intval', $user_languages);

                                            foreach ($all_languages as $lang) {
                                                $selected = in_array(intval($lang->id), $user_languages) ? 'selected' : '';
                                                echo "<option value='{$lang->id}' $selected>{$lang->name}</option>";
                                            }
                                            ?>
                                        </select>
                                        <small>Select all languages you can communicate in</small>
                                    </div>
                                </div>

                                <button type="submit" class="vda-btn vda-btn-primary" style="margin-top: 20px;">
                                    <iconify-icon icon="material-symbols:save-outline"></iconify-icon>
                                    Save Rates & Availability
                                </button>
                            </form>
                        </div>

                        <!-- Certifications/Badges Tab -->
                        <div class="vda-profile-tab-pane" id="profile-tab-certifications">
                            <?php
                            // Get all available certifications from master table
                            global $wpdb;
                            $all_certifications = $wpdb->get_results(
                                "SELECT * FROM {$wpdb->prefix}skd_pl_certifications WHERE status = 'active' ORDER BY sort_order ASC, name ASC"
                            );

                            // Get user's certifications
                            $user_certifications = $wpdb->get_results($wpdb->prepare(
                                "SELECT * FROM {$wpdb->prefix}skd_pl_user_certifications WHERE user_id = %d",
                                $user_id
                            ));

                            // Create a map of user certifications for easy lookup
                            $user_cert_map = [];
                            foreach ($user_certifications as $cert) {
                                $user_cert_map[$cert->certification_id] = $cert;
                            }
                            ?>

                            <div class="vda-certifications-section">
                                <div class="vda-section-header">
                                    <h3>Professional Certifications & Badges</h3>
                                    <p style="color: #666; margin-top: 8px;">Add and manage your professional certifications to build trust and showcase your expertise.</p>
                                </div>

                                <div class="vda-certifications-grid">
                                    <?php foreach ($all_certifications as $cert):
                                        $user_has_cert = isset($user_cert_map[$cert->id]);
                                        $user_cert = $user_has_cert ? $user_cert_map[$cert->id] : null;
                                        $status = $user_cert ? $user_cert->status : 'not-added';
                                    ?>
                                        <div class="vda-cert-card <?php echo $user_has_cert ? 'has-cert' : ''; ?>" data-cert-id="<?php echo $cert->id; ?>">
                                            <div class="vda-cert-badge">
                                                <?php if ($cert->badge_image_url): ?>
                                                    <img src="<?php echo esc_url($cert->badge_image_url); ?>" alt="<?php echo esc_attr($cert->name); ?>">
                                                <?php else: ?>
                                                    <iconify-icon icon="mdi:certificate" style="font-size: 48px; color: #667eea;"></iconify-icon>
                                                <?php endif; ?>
                                            </div>

                                            <div class="vda-cert-info">
                                                <h4><?php echo esc_html($cert->name); ?></h4>
                                                <?php if ($cert->issuer): ?>
                                                    <p class="vda-cert-issuer">Issued by: <?php echo esc_html($cert->issuer); ?></p>
                                                <?php endif; ?>
                                                <?php if ($cert->description): ?>
                                                    <p class="vda-cert-desc"><?php echo esc_html($cert->description); ?></p>
                                                <?php endif; ?>
                                            </div>

                                            <div class="vda-cert-status">
                                                <?php if ($status === 'approved'): ?>
                                                    <span class="vda-badge vda-badge-success">
                                                        <iconify-icon icon="mdi:check-circle"></iconify-icon> Verified
                                                    </span>
                                                <?php elseif ($status === 'pending'): ?>
                                                    <span class="vda-badge vda-badge-warning">
                                                        <iconify-icon icon="mdi:clock"></iconify-icon> Pending Review
                                                    </span>
                                                <?php elseif ($status === 'rejected'): ?>
                                                    <span class="vda-badge vda-badge-danger">
                                                        <iconify-icon icon="mdi:close-circle"></iconify-icon> Rejected
                                                    </span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="vda-cert-actions">
                                                <?php if (!$user_has_cert): ?>
                                                    <button type="button" class="vda-btn vda-btn-sm vda-btn-outline add-cert-btn" data-cert-id="<?php echo $cert->id; ?>" data-requires-verification="<?php echo $cert->verification_required; ?>">
                                                        <iconify-icon icon="mdi:plus"></iconify-icon> Add Certification
                                                    </button>
                                                <?php else: ?>
                                                    <?php if ($user_cert->certificate_file): ?>
                                                        <a href="<?php echo esc_url($user_cert->certificate_file); ?>" target="_blank" class="vda-btn vda-btn-sm vda-btn-outline">
                                                            <iconify-icon icon="mdi:file-document"></iconify-icon> View Certificate
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if ($cert->verification_required && $status !== 'approved'): ?>
                                                        <button type="button" class="vda-btn vda-btn-sm vda-btn-outline edit-cert-btn"
                                                            data-user-cert-id="<?php echo $user_cert->id; ?>"
                                                            data-notes="<?php echo esc_attr($user_cert->notes ?? ''); ?>"
                                                            data-cert-file="<?php echo esc_url($user_cert->certificate_file ?? ''); ?>">
                                                            <iconify-icon icon="mdi:pencil"></iconify-icon> Edit
                                                        </button>
                                                    <?php endif; ?>
                                                    <button type="button" class="vda-btn vda-btn-sm vda-btn-danger remove-cert-btn" data-user-cert-id="<?php echo $user_cert->id; ?>">
                                                        <iconify-icon icon="mdi:delete"></iconify-icon> Remove
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Settings Tab -->
        <div class="vda-tab-content" id="tab-settings">
            <div class="vda-header">
                <h1>Account Settings</h1>
                <p>Manage your account information and preferences</p>
            </div>

            <!-- Account Settings Content -->
            <div class="vda-settings-wrapper">
                <!-- Change Password Section -->
                <div class="vda-settings-card">
                    <h3><iconify-icon icon="material-symbols:lock-outline"></iconify-icon> Change Password</h3>
                    <form id="change-password-form" class="vda-settings-form">
                        <div class="vda-form-group vda-password-field">
                            <label for="current_password">Current Password *</label>
                            <div class="vda-password-wrapper">
                                <input type="password" id="current_password" name="current_password">
                                <iconify-icon icon="mdi:eye-off" class="vda-password-toggle" onclick="togglePassword('current_password')"></iconify-icon>
                            </div>
                        </div>
                        <div class="vda-form-group vda-password-field">
                            <label for="new_password">New Password *</label>
                            <div class="vda-password-wrapper">
                                <input type="password" id="new_password" name="new_password">
                                <iconify-icon icon="mdi:eye-off" class="vda-password-toggle" onclick="togglePassword('new_password')"></iconify-icon>
                            </div>
                            <small>Minimum 8 characters</small>
                        </div>
                        <div class="vda-form-group vda-password-field">
                            <label for="confirm_password">Confirm New Password *</label>
                            <div class="vda-password-wrapper">
                                <input type="password" id="confirm_password" name="confirm_password">
                                <iconify-icon icon="mdi:eye-off" class="vda-password-toggle" onclick="togglePassword('confirm_password')"></iconify-icon>
                            </div>
                        </div>
                        <button type="submit" class="vda-btn vda-btn-primary">
                            <iconify-icon icon="material-symbols:save-outline"></iconify-icon>
                            Update Password
                        </button>
                    </form>
                </div>

                <!-- Account Information -->
                <div class="vda-settings-card">
                    <h3><iconify-icon icon="material-symbols:person-outline"></iconify-icon> Account Information</h3>
                    <form id="account-info-form" class="vda-settings-form">
                        <div class="vda-form-group">
                            <label for="account_email">Email Address</label>
                            <input type="email" id="account_email" name="email" value="<?php echo esc_attr($user->user_email); ?>" readonly>
                            <small>Contact admin to change your email address</small>
                        </div>
                        <div class="vda-form-row">
                            <div class="vda-form-group">
                                <label for="account_first_name">First Name *</label>
                                <input type="text" id="account_first_name" name="first_name" value="<?php echo esc_attr($user->first_name); ?>">
                            </div>
                            <div class="vda-form-group">
                                <label for="account_last_name">Last Name *</label>
                                <input type="text" id="account_last_name" name="last_name" value="<?php echo esc_attr($user->last_name); ?>">
                            </div>
                        </div>
                        <div class="vda-form-group">
                            <label for="account_display_name">Display Name *</label>
                            <input type="text" id="account_display_name" name="display_name" value="<?php echo esc_attr($user->display_name); ?>">
                        </div>
                        <div class="vda-form-group">
                            <label for="account_phone">Phone Number</label>
                            <input type="tel" id="account_phone" name="phone" value="<?php echo esc_attr(get_user_meta($user_id, 'phone', true)); ?>">
                        </div>
                        <button type="submit" class="vda-btn vda-btn-primary">
                            <iconify-icon icon="material-symbols:save-outline"></iconify-icon>
                            Update Information
                        </button>
                    </form>
                </div>

                <!-- Danger Zone -->
                <!-- Commented for future use
                <div class="vda-settings-card vda-danger-zone">
                    <h3><iconify-icon icon="material-symbols:warning-outline"></iconify-icon> Danger Zone</h3>
                    <div class="vda-danger-actions">
                        <div class="vda-danger-item">
                            <div>
                                <h4>Deactivate Account</h4>
                                <p>Temporarily hide your profile from search results</p>
                            </div>
                            <button type="button" class="vda-btn vda-btn-warning" id="deactivate-account-btn">Deactivate</button>
                        </div>
                        <div class="vda-danger-item">
                            <div>
                                <h4>Delete Account</h4>
                                <p>Permanently delete your account and all data</p>
                            </div>
                            <button type="button" class="vda-btn vda-btn-danger" id="delete-account-btn">Delete Account</button>
                        </div>
                    </div>
                </div>
                -->
            </div>
        </div>
    </main>
</div>

<!-- Add/Edit Portfolio Modal -->
<div id="portfolio-modal" class="vda-modal" style="display: none;">
    <div class="vda-modal-content vda-modal-large">
        <span class="vda-modal-close">&times;</span>
        <h2 id="portfolio-modal-title">Add Portfolio Item</h2>

        <form id="portfolio-form" enctype="multipart/form-data">
            <input type="hidden" id="portfolio-id" name="id" value="">

            <div class="vda-form-group">
                <label for="portfolio-title">Project Title *</label>
                <input type="text" id="portfolio-title" name="title" class="vda-input" required placeholder="e.g., Modern Living Room Design">
            </div>

            <div class="vda-form-group">
                <label for="portfolio-category">Category *</label>
                <select id="portfolio-category" name="category_id" class="vda-input" required>
                    <option value="">Select Category</option>
                    <?php if (!empty($portfolio_categories)): ?>
                        <?php foreach ($portfolio_categories as $cat): ?>
                            <option value="<?php echo esc_attr($cat->id); ?>"><?php echo esc_html($cat->name); ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="vda-form-group">
                <label for="portfolio-description">Description</label>
                <div id="portfolio-description-editor-container">
                    <?php
                    wp_editor('', 'portfolio_description', array(
                        'textarea_name' => 'description',
                        'textarea_rows' => 8,
                        'media_buttons' => false,
                        'teeny' => false,
                        'quicktags' => true,
                        'tinymce' => array(
                            'toolbar1' => 'formatselect,bold,italic,underline,bullist,numlist,link,unlink,blockquote',
                            'toolbar2' => '',
                            'height' => 250
                        )
                    ));
                    ?>
                </div>
                <small>Describe the project, design approach, materials used, challenges overcome, etc.</small>
            </div>

            <div class="vda-form-group">
                <label for="portfolio-tags">Tags</label>
                <input type="text" id="portfolio-tags" class="vda-input" placeholder="Enter tags separated by commas (e.g., 3D Rendering, Modern, Residential)">
                <small>Separate tags with commas. These help clients find your work.</small>
            </div>

            <div class="vda-form-group">
                <label for="portfolio-images">Project Images *</label>
                <input type="file" id="portfolio-images" name="images[]" class="vda-input" accept="image/*" multiple>
                <input type="hidden" id="existing-images" name="existing_images" value="">
                <input type="hidden" id="deleted-images" name="deleted_images" value="">
                <small>Upload multiple images (Max 1MB per image). First image will be the featured image.</small>

                <div id="image-preview-grid" class="vda-image-preview-grid" style="margin-top: 15px; display: none;">
                    <!-- Image previews will appear here -->
                </div>
            </div>

            <div class="vda-form-group">
                <label for="portfolio-year">Year Completed</label>
                <input type="number" id="portfolio-year" name="year" class="vda-input" min="2000" max="<?php echo date('Y'); ?>" placeholder="<?php echo date('Y'); ?>">
            </div>

            <div class="vda-modal-actions">
                <button type="submit" class="vda-btn vda-btn-primary">
                    <iconify-icon icon="material-symbols:save-outline"></iconify-icon>
                    Save Portfolio Item
                </button>
                <button type="button" class="vda-btn vda-btn-outline" id="cancel-portfolio">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- View Portfolio Modal -->
<div id="portfolio-view-modal" class="vda-modal" style="display: none;">
    <div class="vda-modal-content vda-modal-large">
        <span class="vda-modal-close">&times;</span>
        <div id="portfolio-view-content">
            <!-- Content will be loaded dynamically -->
        </div>
    </div>
</div>

<script>
    // Toggle password visibility function
    window.togglePassword = function(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = field.nextElementSibling;
        if (field.type === 'password') {
            field.type = 'text';
            icon.setAttribute('icon', 'mdi:eye');
        } else {
            field.type = 'password';
            icon.setAttribute('icon', 'mdi:eye-off');
        }
    };
    jQuery(document).ready(function($) {
        let nonce = '<?php echo wp_create_nonce("skd_ajax_nonce"); ?>';

        // Main tab switching (Dashboard, Profile, Settings)
        $('.vda-menu-item').click(function(e) {
            e.preventDefault();
            if ($(this).hasClass('vda-logout')) return;

            const tab = $(this).data('tab');
            $('.vda-menu-item').removeClass('active');
            $(this).addClass('active');
            $('.vda-tab-content').removeClass('active');
            $('#tab-' + tab).addClass('active');

            // If navigating to Profile tab, go to Basic Info subtab
            if (tab === 'profile') {
                $('.vda-tab-btn').removeClass('active');
                $('.vda-tab-btn[data-tab="basic"]').addClass('active');
                $('.vda-profile-tab-pane').removeClass('active');
                $('#profile-tab-basic').addClass('active');

                // Scroll to top of content area
                setTimeout(function() {
                    $('.vda-main-content').animate({
                        scrollTop: 0
                    }, 400);
                }, 100);
            }
        });

        // Profile sub-tabs switching
        $('.vda-tab-btn').click(function(e) {
            e.preventDefault();
            const tab = $(this).data('tab');
            $('.vda-tab-btn').removeClass('active');
            $(this).addClass('active');
            $('.vda-profile-tab-pane').removeClass('active');
            $('#profile-tab-' + tab).addClass('active');
        });

        // Avatar edit button click - trigger file upload
        $('.vda-avatar-edit-btn').on('click', function() {
            $('#avatar-upload').click();
        });

        // Quick Action cards with data-tab navigation
        $('.vda-action-card[data-tab]').on('click', function(e) {
            e.preventDefault();
            const tab = $(this).data('tab');
            const subtab = $(this).data('subtab');

            // Switch to main tab
            $('.vda-menu-item').removeClass('active');
            $('.vda-menu-item[data-tab="' + tab + '"]').addClass('active');
            $('.vda-tab-content').removeClass('active');
            $('#tab-' + tab).addClass('active');

            // Switch to subtab if specified
            if (subtab) {
                $('.vda-tab-btn').removeClass('active');
                $('.vda-tab-btn[data-tab="' + subtab + '"]').addClass('active');
                $('.vda-profile-tab-pane').removeClass('active');
                $('#profile-tab-' + subtab).addClass('active');

                // Load portfolio if navigating to portfolio tab
                if (subtab === 'portfolio') {
                    setTimeout(loadPortfolioItems, 300);
                }
            }

            // Scroll to top of content area
            setTimeout(function() {
                $('.vda-main-content').animate({
                    scrollTop: 0
                }, 400);
            }, 100);
        });

        // Complete Your Profile tip actions
        $('.vda-tip-action').on('click', function(e) {
            e.preventDefault();
            const tab = $(this).data('tab');
            const subtab = $(this).data('subtab');

            // Switch to main tab
            $('.vda-menu-item').removeClass('active');
            $('.vda-menu-item[data-tab="' + tab + '"]').addClass('active');
            $('.vda-tab-content').removeClass('active');
            $('#tab-' + tab).addClass('active');

            // Switch to subtab if specified
            if (subtab) {
                $('.vda-tab-btn').removeClass('active');
                $('.vda-tab-btn[data-tab="' + subtab + '"]').addClass('active');
                $('.vda-profile-tab-pane').removeClass('active');
                $('#profile-tab-' + subtab).addClass('active');

                // Load portfolio if navigating to portfolio tab
                if (subtab === 'portfolio') {
                    setTimeout(loadPortfolioItems, 300);
                }
            }

            // Scroll to top of content area
            setTimeout(function() {
                $('.vda-main-content').animate({
                    scrollTop: 0
                }, 400);
            }, 100);
        }); // Initialize Select2 for multi-selects
        if (typeof $.fn.select2 !== 'undefined') {
            $('.vda-select2').select2({
                placeholder: 'Select from available options',
                allowClear: true
            });

            // Initialize Select2 for languages (only from database)
            $('.vda-select2-languages').select2({
                placeholder: 'Select languages',
                allowClear: true
            });
        }

        // Auto-calculate experience level based on years (using dynamic levels from database)
        const experienceLevels = <?php echo json_encode(SKD_PL_Experience_Levels::get_experience_levels(['status' => 'active'])); ?>;

        $('#years_experience').on('input change', function() {
            const years = parseInt($(this).val()) || 0;
            let level = 'Not set';

            if (years > 0) {
                for (let i = 0; i < experienceLevels.length; i++) {
                    const expLevel = experienceLevels[i];
                    const minYears = parseInt(expLevel.years_min) || 0;
                    const maxYears = expLevel.years_max ? parseInt(expLevel.years_max) : null;

                    if (years >= minYears && (maxYears === null || years <= maxYears)) {
                        const range = maxYears ? minYears + '-' + maxYears : minYears + '+';
                        level = expLevel.name + ' (' + range + ' years)';
                        break;
                    }
                }
            }

            $('#experience_level_display').val(level);
        });

        // Basic Info Form
        $('#basic-info-form').on('submit', function(e) {
            e.preventDefault();

            // Check if form is valid
            if (!$(this).valid()) {
                return false;
            }

            // Trigger TinyMCE save to update textareas
            if (typeof tinyMCE !== 'undefined') {
                tinyMCE.triggerSave();
            }

            const formData = new FormData(this);
            formData.append('action', 'skd_update_profile_basic');
            formData.append('nonce', nonce);

            $.ajax({
                url: skd_ajax_object.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    Swal.fire({
                        title: 'Saving...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Saved!',
                            text: 'Basic information updated successfully',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        // Update sidebar display name if changed
                        if (response.data.display_name) {
                            $('.vda-profile-card h3').text(response.data.display_name);
                        }
                        // Update tagline in sidebar if changed
                        if (response.data.tagline) {
                            $('.vda-tagline').text(response.data.tagline);
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.data.message || 'Failed to update'
                        });
                    }
                }
            });
        });

        // Skills & Specializations Form
        $('#skills-services-form').on('submit', function(e) {
            e.preventDefault();

            // Check if form is valid
            if (!$(this).valid()) {
                return false;
            }

            $.ajax({
                url: skd_ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'skd_update_profile_skills',
                    nonce: nonce,
                    skills: $('select[name="skills[]"]').val(),
                    specializations: $('select[name="specializations[]"]').val(),
                    project_types: $('select[name="project_types[]"]').val(),
                    service_types: $('select[name="service_types[]"]').val()
                },
                beforeSend: function() {
                    Swal.fire({
                        title: 'Saving...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Saved!',
                            text: 'Skills and specializations updated successfully',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.data.message || 'Failed to update'
                        });
                    }
                }
            });
        });

        // Rates Form
        $('#rates-form').on('submit', function(e) {
            e.preventDefault();

            // Check if form is valid
            if (!$(this).valid()) {
                return false;
            }

            $.ajax({
                url: skd_ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'skd_update_profile_rates',
                    nonce: nonce,
                    hourly_rate: $('input[name="hourly_rate"]').val(),
                    availability_status: $('select[name="availability_status"]').val(),
                    years_experience: $('input[name="years_experience"]').val(),
                    response_time: $('select[name="response_time"]').val(),
                    languages_spoken: $('select[name="languages_spoken[]"]').val()
                },
                beforeSend: function() {
                    Swal.fire({
                        title: 'Saving...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Saved!',
                            text: 'Rates and availability updated successfully',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.data.message || 'Failed to update'
                        });
                    }
                }
            });
        });

        // ============================================
        // AVATAR UPLOAD FUNCTIONALITY
        // ============================================

        $('#avatar-upload').on('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            // Validate file type
            if (!file.type.startsWith('image/')) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File',
                    text: 'Please select an image file'
                });
                return;
            }

            // Validate file size (2MB max)
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: 'Image must be less than 2MB'
                });
                return;
            }

            // Upload image
            const formData = new FormData();
            formData.append('action', 'skd_upload_avatar');
            formData.append('nonce', nonce);
            formData.append('avatar', file);

            $.ajax({
                url: skd_ajax_object.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    Swal.fire({
                        title: 'Uploading...',
                        text: 'Please wait',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Uploaded!',
                            text: 'Profile image updated successfully',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // Update sidebar avatar with cache busting
                        const newImageUrl = response.data.url + '?t=' + new Date().getTime();
                        $('#sidebar-avatar-placeholder').remove();
                        if ($('#sidebar-avatar-img').length) {
                            $('#sidebar-avatar-img').attr('src', newImageUrl);
                        } else {
                            $('#sidebar-avatar').prepend('<img src="' + newImageUrl + '" alt="Profile" id="sidebar-avatar-img">');
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Upload Failed',
                            text: response.data.message || 'Failed to upload image'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Network error. Please try again.'
                    });
                }
            });
        });

        // Remove avatar
        $(document).on('click', '#remove-avatar-btn', function() {
            Swal.fire({
                title: 'Remove Profile Image?',
                text: 'Are you sure you want to remove your profile image?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Remove',
                confirmButtonColor: '#f44336'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(skd_ajax_object.ajax_url, {
                        action: 'skd_remove_avatar',
                        nonce: nonce
                    }, function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Removed!',
                                text: 'Profile image removed',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            // Replace with placeholder
                            const initials = $('.vda-user-info h3').text().substring(0, 2).toUpperCase();
                            $('.vda-avatar-preview').html('<div class="vda-avatar-placeholder-large" id="avatar-placeholder">' + initials + '</div>');
                            $('#sidebar-avatar-img').remove();
                            if ($('#sidebar-avatar-placeholder').length === 0) {
                                $('#sidebar-avatar').prepend('<div class="vda-avatar-placeholder" id="sidebar-avatar-placeholder">' + initials + '</div>');
                            }
                            $('#remove-avatar-btn').remove();
                        } else {
                            Swal.fire('Error', response.data.message || 'Failed to remove', 'error');
                        }
                    });
                }
            });
        });

        // ============================================
        // PORTFOLIO FUNCTIONALITY
        // ============================================

        // Helper function to update featured badge
        function updateFeaturedBadge() {
            $('#image-preview-grid .vda-featured-badge').remove();
            const firstImage = $('#image-preview-grid .vda-image-preview').first();
            if (firstImage.length > 0) {
                firstImage.append('<span class="vda-featured-badge">Featured</span>');
            }
        }

        // Open Add Portfolio Modal
        $('#add-portfolio-btn').on('click', function() {
            $('#portfolio-modal-title').text('Add Portfolio Item');
            $('#portfolio-form')[0].reset();
            $('#portfolio-id').val('');
            $('#existing-images').val('');
            $('#deleted-images').val('');
            $('#image-preview-grid').html('').hide();

            // Clear WordPress editor
            if (typeof tinymce !== 'undefined' && tinymce.get('portfolio_description')) {
                tinymce.get('portfolio_description').setContent('');
            }

            $('#portfolio-modal').css('display', 'block');
        }); // Close Portfolio Modals
        $('.vda-modal-close, #cancel-portfolio').on('click', function() {
            $('#portfolio-modal, #portfolio-view-modal').css('display', 'none');
        });

        // Close modal on outside click
        $(window).on('click', function(e) {
            if ($(e.target).hasClass('vda-modal')) {
                $('.vda-modal').css('display', 'none');
            }
        });

        // Image Preview with validation
        $('#portfolio-images').on('change', function(e) {
            const files = e.target.files;
            if (files.length > 0) {
                let validFiles = [];
                let errors = [];
                const maxSize = 1 * 1024 * 1024; // 1MB in bytes

                Array.from(files).forEach((file, index) => {
                    if (!file.type.startsWith('image/')) {
                        errors.push(`${file.name} is not an image`);
                        return;
                    }
                    if (file.size > maxSize) {
                        errors.push(`${file.name} exceeds 1MB (${(file.size / 1024 / 1024).toFixed(2)}MB)`);
                        return;
                    }
                    validFiles.push(file);
                });

                if (errors.length > 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Files',
                        html: errors.join('<br>'),
                    });
                    // Keep only valid files
                    const dt = new DataTransfer();
                    validFiles.forEach(f => dt.items.add(f));
                    this.files = dt.files;
                }

                if (validFiles.length > 0) {
                    // Remove previous new image previews (keep existing ones)
                    $('#image-preview-grid .vda-image-preview[data-type="new"]').remove();

                    validFiles.forEach((file, index) => {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const preview = `
                                <div class="vda-image-preview" data-type="new" data-index="${index}">
                                    <img src="${e.target.result}" alt="Preview ${index + 1}">
                                    <button type="button" class="vda-delete-image" data-type="new" data-index="${index}">
                                        <iconify-icon icon="material-symbols:close"></iconify-icon>
                                    </button>
                                </div>
                            `;
                            $('#image-preview-grid').append(preview).show();

                            // Update featured badge after all images are loaded
                            updateFeaturedBadge();
                        };
                        reader.readAsDataURL(file);
                    });
                }
            }
        });

        // Delete image from preview
        $(document).on('click', '.vda-delete-image', function() {
            const type = $(this).data('type');
            const imageUrl = $(this).data('url');
            const index = $(this).data('index');

            if (type === 'existing') {
                // Add to deleted images list
                const deletedImages = $('#deleted-images').val();
                const deletedArray = deletedImages ? JSON.parse(deletedImages) : [];
                deletedArray.push(imageUrl);
                $('#deleted-images').val(JSON.stringify(deletedArray));

                // Remove from existing images list
                const existingImages = $('#existing-images').val();
                const existingArray = existingImages ? JSON.parse(existingImages) : [];
                const updatedExisting = existingArray.filter(img => img !== imageUrl);
                $('#existing-images').val(JSON.stringify(updatedExisting));
            } else if (type === 'new') {
                // Remove from file input
                const fileInput = document.getElementById('portfolio-images');
                const dt = new DataTransfer();
                const files = fileInput.files;

                for (let i = 0; i < files.length; i++) {
                    if (i !== index) {
                        dt.items.add(files[i]);
                    }
                }
                fileInput.files = dt.files;
            }

            // Remove preview
            $(this).closest('.vda-image-preview').remove();

            // Update featured badge
            updateFeaturedBadge();

            if ($('#image-preview-grid .vda-image-preview').length === 0) {
                $('#image-preview-grid').hide();
            }
        });

        // Submit Portfolio Form
        $('#portfolio-form').on('submit', function(e) {
            e.preventDefault();

            // Check if form is valid
            if (!$(this).valid()) {
                return false;
            }

            const formData = new FormData(this);
            const isEdit = $('#portfolio-id').val() !== '';

            formData.append('action', isEdit ? 'skd_update_portfolio_item' : 'skd_add_portfolio_item');
            formData.append('nonce', nonce);

            // Get description from WordPress editor
            if (typeof tinymce !== 'undefined') {
                var editor = tinymce.get('portfolio_description');
                if (editor) {
                    var editorContent = editor.getContent();
                    formData.set('description', editorContent);
                }
            }

            // Process tags - send as comma-separated string
            const tagsInput = $('#portfolio-tags').val().trim();
            if (tagsInput) {
                formData.append('tags', tagsInput);
                console.log('Tags being sent:', tagsInput);
            }

            $.ajax({
                url: skd_ajax_object.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    Swal.fire({
                        title: isEdit ? 'Updating...' : 'Adding...',
                        text: 'Please wait',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.data.message || (isEdit ? 'Portfolio updated' : 'Portfolio added'),
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            $('#portfolio-modal').css('display', 'none');
                            loadPortfolioItems();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.data.message || 'Failed to save portfolio item'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Network error. Please try again.'
                    });
                }
            });
        });

        // Load Portfolio Items
        function loadPortfolioItems() {
            $.ajax({
                url: skd_ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'skd_get_portfolio_items',
                    nonce: nonce
                },
                success: function(response) {
                    if (response.success && response.data.items) {
                        const items = response.data.items;
                        let html = '';

                        if (items.length === 0) {
                            html = `
                                <div class="vda-empty-state">
                                    <iconify-icon icon="material-symbols:folder-open-outline" style="font-size: 80px; color: #ccc;"></iconify-icon>
                                    <h3>No Portfolio Items Yet</h3>
                                    <p>Showcase your best work by adding your first portfolio item.</p>
                                    <button type="button" class="vda-btn vda-btn-primary" onclick="$('#add-portfolio-btn').click();">
                                        <iconify-icon icon="material-symbols:add"></iconify-icon>
                                        Add Your First Project
                                    </button>
                                </div>
                            `;
                        } else {
                            items.forEach(item => {
                                // Parse tags - handle both escaped and non-escaped JSON
                                let tagsHtml = '';
                                if (item.tags) {
                                    console.log('Item tags raw:', item.tags);
                                    try {
                                        // Handle escaped JSON from database
                                        let tagsData = item.tags;
                                        // If it's a string that looks like escaped JSON, unescape it
                                        if (typeof tagsData === 'string' && tagsData.includes('\\')) {
                                            // Remove the escaping backslashes
                                            tagsData = tagsData.replace(/\\\"/g, '"');
                                        }
                                        const tags = JSON.parse(tagsData);
                                        console.log('Item tags parsed:', tags);
                                        if (Array.isArray(tags) && tags.length > 0) {
                                            tagsHtml = tags.slice(0, 3).map(tag =>
                                                `<span class="vda-tag">${tag}</span>`
                                            ).join('');
                                            if (tags.length > 3) {
                                                tagsHtml += `<span class="vda-tag">+${tags.length - 3}</span>`;
                                            }
                                        }
                                    } catch (e) {
                                        console.error('Error parsing tags:', e, 'Raw tags:', item.tags);
                                    }
                                } else {
                                    console.log('No tags for item:', item.id);
                                }

                                // Prepare description
                                let descHtml = '';
                                if (item.description) {
                                    const stripped = item.description.replace(/<[^>]*>/g, '');
                                    const truncated = stripped.substring(0, 120);
                                    const ellipsis = stripped.length > 120 ? '...' : '';
                                    descHtml = `<p class="vda-portfolio-desc">${truncated}${ellipsis}</p>`;
                                }

                                // Default placeholder image
                                const placeholderSvg = 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22300%22%3E%3Crect fill=%22%23ddd%22 width=%22400%22 height=%22300%22/%3E%3Ctext fill=%22%23999%22 font-family=%22sans-serif%22 font-size=%2220%22 dy=%2210.5%22 font-weight=%22bold%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22%3ENo Image%3C/text%3E%3C/svg%3E';
                                const imageUrl = item.featured_image || item.image_url || placeholderSvg;

                                // Prepare tags HTML
                                let tagsSection = '';
                                if (tagsHtml) {
                                    tagsSection = '<div class="vda-portfolio-tags">' + tagsHtml + '</div>';
                                }

                                // Prepare year HTML
                                let yearHtml = '';
                                if (item.year) {
                                    yearHtml = '<span><iconify-icon icon="material-symbols:calendar-month-outline"></iconify-icon> ' + item.year + '</span>';
                                }

                                // Prepare category badge
                                let categoryBadge = '';
                                if (item.category_name) {
                                    categoryBadge = '<span class="vda-category-badge">' + item.category_name + '</span>';
                                }

                                html += `
                                    <div class="vda-portfolio-card" data-id="${item.id}">
                                        <div class="vda-portfolio-image">
                                            <img src="${imageUrl}" alt="${item.title}">
                                            ${categoryBadge}
                                        </div>
                                        <div class="vda-portfolio-content">
                                            <h4>${item.title}</h4>
                                            ${descHtml}
                                            ${tagsSection}
                                            <div class="vda-portfolio-meta">
                                                ${yearHtml}
                                            </div>
                                        </div>
                                        <div class="vda-portfolio-actions">
                                            <button class="vda-btn vda-btn-sm vda-btn-outline view-portfolio" data-id="${item.id}">
                                                <iconify-icon icon="material-symbols:visibility-outline"></iconify-icon> View
                                            </button>
                                            <button class="vda-btn vda-btn-sm vda-btn-outline edit-portfolio" data-id="${item.id}">
                                                <iconify-icon icon="material-symbols:edit-outline"></iconify-icon> Edit
                                            </button>
                                            <button class="vda-btn vda-btn-sm vda-btn-danger delete-portfolio" data-id="${item.id}">
                                                <iconify-icon icon="material-symbols:delete-outline"></iconify-icon> Delete
                                            </button>
                                        </div>
                                    </div>
                                `;
                            });
                        }

                        $('#portfolio-items-list').html(html);
                    }
                }
            });
        }

        // View Portfolio Item
        $(document).on('click', '.view-portfolio', function() {
            const itemId = $(this).data('id');

            $.ajax({
                url: skd_ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'skd_get_portfolio_item',
                    nonce: nonce,
                    item_id: itemId
                },
                success: function(response) {
                    if (response.success && response.data.item) {
                        const item = response.data.item;

                        // Parse images with zoom functionality
                        let imagesHtml = '';
                        if (item.images) {
                            try {
                                let imagesData = item.images;
                                if (typeof imagesData === 'string' && imagesData.includes('\\')) {
                                    imagesData = imagesData.replace(/\\"/g, '"');
                                }
                                const images = JSON.parse(imagesData);
                                if (Array.isArray(images) && images.length > 0) {
                                    imagesHtml = images.map(img =>
                                        `<img src="${img}" alt="${item.title}" class="vda-zoomable-image" data-zoom-src="${img}">`
                                    ).join('');
                                }
                            } catch (e) {
                                imagesHtml = `<img src="${item.featured_image || item.image_url}" alt="${item.title}" class="vda-zoomable-image" data-zoom-src="${item.featured_image || item.image_url}">`;
                            }
                        }

                        // Parse tags - handle escaped JSON
                        let tagsHtml = '';
                        if (item.tags) {
                            try {
                                let tagsData = item.tags;
                                // Handle escaped JSON from database
                                if (typeof tagsData === 'string' && tagsData.includes('\\')) {
                                    tagsData = tagsData.replace(/\\\"/g, '"');
                                }
                                const tags = JSON.parse(tagsData);
                                if (Array.isArray(tags) && tags.length > 0) {
                                    tagsHtml = tags.map(tag => `<span class="vda-tag">${tag}</span>`).join('');
                                }
                            } catch (e) {
                                console.error('Error parsing tags in view modal:', e);
                            }
                        }

                        const viewHtml = `
                            <h2>${item.title}</h2>
                            ${item.category_name ? `<p class="vda-category-label"><strong>Category:</strong> ${item.category_name}</p>` : ''}
                            
                            <div class="vda-portfolio-view-images">
                                ${imagesHtml}
                            </div>
                            
                            ${item.description ? `
                                <div class="vda-portfolio-section">
                                    <h3>Description</h3>
                                    <div>${item.description}</div>
                                </div>
                            ` : ''}
                            
                            ${item.year ? `
                                <div class="vda-portfolio-details">
                                    <p><strong>Year:</strong> ${item.year}</p>
                                </div>
                            ` : ''}
                            
                            ${tagsHtml ? `
                                <div class="vda-portfolio-section">
                                    <h3>Tags</h3>
                                    <div class="vda-portfolio-tags">${tagsHtml}</div>
                                </div>
                            ` : ''}
                        `;

                        $('#portfolio-view-content').html(viewHtml);
                        $('#portfolio-view-modal').css('display', 'block');
                    }
                }
            });
        });

        // Edit Portfolio Item
        $(document).on('click', '.edit-portfolio', function() {
            const itemId = $(this).data('id');

            $.ajax({
                url: skd_ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'skd_get_portfolio_item',
                    nonce: nonce,
                    item_id: itemId
                },
                success: function(response) {
                    if (response.success && response.data.item) {
                        const item = response.data.item;

                        $('#portfolio-modal-title').text('Edit Portfolio Item');
                        $('#portfolio-id').val(item.id);
                        $('#portfolio-title').val(item.title);
                        $('#portfolio-category').val(item.category_id);
                        $('#portfolio-year').val(item.year || '');

                        // Set description in WordPress editor
                        if (typeof tinymce !== 'undefined' && tinymce.get('portfolio_description')) {
                            tinymce.get('portfolio_description').setContent(item.description || '');
                        }

                        // Parse and populate tags - handle escaped JSON
                        if (item.tags) {
                            try {
                                let tagsData = item.tags;
                                // Handle escaped JSON from database
                                if (typeof tagsData === 'string' && tagsData.includes('\\')) {
                                    tagsData = tagsData.replace(/\\\"/g, '"');
                                }
                                const tags = JSON.parse(tagsData);
                                if (Array.isArray(tags)) {
                                    $('#portfolio-tags').val(tags.join(', '));
                                }
                            } catch (e) {
                                console.error('Error parsing tags for edit:', e);
                                $('#portfolio-tags').val('');
                            }
                        } else {
                            $('#portfolio-tags').val('');
                        }

                        // Show existing images preview with delete buttons
                        if (item.images) {
                            try {
                                let imagesData = item.images;
                                if (typeof imagesData === 'string' && imagesData.includes('\\')) {
                                    imagesData = imagesData.replace(/\\"/g, '"');
                                }
                                const images = JSON.parse(imagesData);
                                if (Array.isArray(images) && images.length > 0) {
                                    $('#existing-images').val(JSON.stringify(images));
                                    $('#deleted-images').val('');
                                    $('#image-preview-grid').html('').show();
                                    images.forEach((img, index) => {
                                        const preview = `
                                            <div class="vda-image-preview" data-type="existing" data-url="${img}">
                                                <img src="${img}" alt="Existing ${index + 1}">
                                                <button type="button" class="vda-delete-image" data-type="existing" data-url="${img}">
                                                    <iconify-icon icon="material-symbols:close"></iconify-icon>
                                                </button>
                                                ${index === 0 ? '<span class="vda-featured-badge">Featured</span>' : ''}
                                            </div>
                                        `;
                                        $('#image-preview-grid').append(preview);
                                    });
                                }
                            } catch (e) {
                                console.error('Error parsing images:', e);
                            }
                        }

                        $('#portfolio-modal').css('display', 'block');
                    }
                }
            });
        });

        // Image zoom functionality using WordPress lightbox
        $(document).on('click', '.vda-zoomable-image', function(e) {
            e.preventDefault();
            const imgSrc = $(this).data('zoom-src');
            const imgAlt = $(this).attr('alt') || 'Portfolio Image';

            // Create lightbox HTML
            const lightboxHtml = `
                <div class="vda-lightbox" style="display: block;">
                    <div class="vda-lightbox-content">
                        <span class="vda-lightbox-close">&times;</span>
                        <img src="${imgSrc}" alt="${imgAlt}">
                    </div>
                </div>
            `;

            // Append to body
            $('body').append(lightboxHtml);

            // Prevent body scroll
            $('body').css('overflow', 'hidden');

            // Close on click outside or close button
            $('.vda-lightbox, .vda-lightbox-close').on('click', function() {
                $('.vda-lightbox').fadeOut(300, function() {
                    $(this).remove();
                    $('body').css('overflow', '');
                });
            });

            // Prevent image click from closing
            $('.vda-lightbox-content img').on('click', function(e) {
                e.stopPropagation();
            });
        });

        // Delete Portfolio Item
        $(document).on('click', '.delete-portfolio', function() {
            const itemId = $(this).data('id');

            Swal.fire({
                title: 'Delete Portfolio Item?',
                text: 'This action cannot be undone',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f44336',
                confirmButtonText: 'Yes, Delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(skd_ajax_object.ajax_url, {
                        action: 'skd_delete_portfolio_item',
                        nonce: nonce,
                        item_id: itemId
                    }, function(response) {
                        if (response.success) {
                            Swal.fire('Deleted!', 'Portfolio item removed', 'success');
                            loadPortfolioItems();
                        }
                    });
                }
            });
        });

        // Load portfolio items when tab is opened
        $('.vda-menu-item[data-tab="profile"]').on('click', function() {
            setTimeout(loadPortfolioItems, 500);
        });

        // Initialize jQuery Validation for Password Change Form
        $('#change-password-form').validate({
            rules: {
                current_password: {
                    required: true
                },
                new_password: {
                    required: true,
                    minlength: 8
                },
                confirm_password: {
                    required: true,
                    equalTo: '#new_password'
                }
            },
            messages: {
                current_password: {
                    required: 'Please enter your current password'
                },
                new_password: {
                    required: 'Please enter a new password',
                    minlength: 'Password must be at least 8 characters long'
                },
                confirm_password: {
                    required: 'Please confirm your new password',
                    equalTo: 'Passwords do not match'
                }
            },
            errorClass: 'error',
            validClass: 'valid',
            errorElement: 'label',
            errorPlacement: function(error, element) {
                if (element.closest('.vda-password-wrapper').length) {
                    error.insertAfter(element.closest('.vda-password-wrapper'));
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function(form) {
                const currentPassword = $('#current_password').val().trim();
                const newPassword = $('#new_password').val().trim();

                // Additional validation: new password must be different from current
                if (currentPassword === newPassword) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Same Password',
                        text: 'New password must be different from current password'
                    });
                    return false;
                }

                $.ajax({
                    url: skd_ajax_object.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'skd_change_password',
                        nonce: nonce,
                        current_password: currentPassword,
                        new_password: newPassword
                    },
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Updating Password...',
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Password Updated',
                                text: response.data.message || 'Your password has been updated successfully',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            $('#change-password-form')[0].reset();
                            $('#change-password-form').validate().resetForm();

                            // Refresh nonce after password change
                            $.post(skd_ajax_object.ajax_url, {
                                action: 'skd_get_fresh_nonce'
                            }, function(nonceResponse) {
                                if (nonceResponse.success && nonceResponse.data.nonce) {
                                    nonce = nonceResponse.data.nonce;
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Update Failed',
                                text: response.data.message || 'Failed to update password. Please check your current password and try again.'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while updating password. Please try again.'
                        });
                    }
                });

                return false;
            }
        });

        // Initialize jQuery Validation for Account Information Form
        $('#account-info-form').validate({
            rules: {
                first_name: {
                    required: true,
                    maxlength: 50
                },
                last_name: {
                    required: true,
                    maxlength: 50
                },
                display_name: {
                    required: true,
                    maxlength: 100
                },
                phone: {
                    maxlength: 20
                }
            },
            messages: {
                first_name: {
                    required: 'Please enter your first name',
                    maxlength: 'First name must not exceed 50 characters'
                },
                last_name: {
                    required: 'Please enter your last name',
                    maxlength: 'Last name must not exceed 50 characters'
                },
                display_name: {
                    required: 'Please enter a display name',
                    maxlength: 'Display name must not exceed 100 characters'
                },
                phone: {
                    maxlength: 'Phone number must not exceed 20 characters'
                }
            },
            errorClass: 'error',
            validClass: 'valid',
            errorElement: 'label',
            submitHandler: function(form) {
                const formData = {
                    action: 'skd_update_account_info',
                    nonce: nonce,
                    first_name: $('#account_first_name').val(),
                    last_name: $('#account_last_name').val(),
                    display_name: $('#account_display_name').val(),
                    phone: $('#account_phone').val()
                };

                $.ajax({
                    url: skd_ajax_object.ajax_url,
                    type: 'POST',
                    data: formData,
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Updating Information...',
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Updated',
                                text: 'Account information updated successfully',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            // Update sidebar display name without reload
                            $('.vda-user-info h3').text(formData.display_name);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Update Failed',
                                text: response.data.message || 'Failed to update account information'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while updating account information'
                        });
                    }
                });

                return false;
            }
        });

        // Initialize jQuery Validation for Basic Info Form
        $('#basic-info-form').validate({
            rules: {
                first_name: {
                    required: true,
                    minlength: 2,
                    maxlength: 50
                },
                last_name: {
                    required: true,
                    minlength: 2,
                    maxlength: 50
                },
                tagline: {
                    maxlength: 200
                },
                short_description: {
                    maxlength: 300
                },
                country: {
                    required: true
                },
                city: {
                    required: true,
                    minlength: 2
                },
                timezone: {
                    required: true
                }
            },
            messages: {
                first_name: {
                    required: 'Please enter your first name',
                    minlength: 'First name must be at least 2 characters',
                    maxlength: 'First name must not exceed 50 characters'
                },
                last_name: {
                    required: 'Please enter your last name',
                    minlength: 'Last name must be at least 2 characters',
                    maxlength: 'Last name must not exceed 50 characters'
                },
                tagline: {
                    maxlength: 'Tagline must not exceed 200 characters'
                },
                short_description: {
                    maxlength: 'Description must not exceed 300 characters'
                },
                country: {
                    required: 'Please select your country'
                },
                city: {
                    required: 'Please enter your city',
                    minlength: 'City must be at least 2 characters'
                },
                timezone: {
                    required: 'Please select your timezone'
                }
            },
            errorClass: 'error',
            validClass: 'valid',
            errorElement: 'label',
            errorPlacement: function(error, element) {
                error.insertAfter(element);
            }
        });

        // Initialize jQuery Validation for Skills & Services Form
        $('#skills-services-form').validate({
            rules: {
                'skills[]': {
                    required: true
                },
                'specializations[]': {
                    required: true
                }
            },
            messages: {
                'skills[]': {
                    required: 'Please select at least one skill'
                },
                'specializations[]': {
                    required: 'Please select at least one specialization'
                }
            },
            errorClass: 'error',
            validClass: 'valid',
            errorElement: 'label',
            errorPlacement: function(error, element) {
                error.insertAfter(element.next('.select2-container'));
            }
        });

        // Initialize jQuery Validation for Rates Form
        $('#rates-form').validate({
            rules: {
                hourly_rate: {
                    required: true,
                    number: true,
                    min: 0
                },
                availability_status: {
                    required: true
                },
                years_experience: {
                    required: true,
                    digits: true,
                    min: 0,
                    max: 50
                },
                response_time: {
                    required: true
                }
            },
            messages: {
                hourly_rate: {
                    required: 'Please enter your hourly rate',
                    number: 'Please enter a valid number',
                    min: 'Hourly rate must be positive'
                },
                availability_status: {
                    required: 'Please select your availability status'
                },
                years_experience: {
                    required: 'Please enter your years of experience',
                    digits: 'Please enter a valid number',
                    min: 'Years of experience cannot be negative',
                    max: 'Years of experience cannot exceed 50'
                },
                response_time: {
                    required: 'Please select your typical response time'
                }
            },
            errorClass: 'error',
            validClass: 'valid',
            errorElement: 'label',
            errorPlacement: function(error, element) {
                error.insertAfter(element);
            }
        });

        // Initialize jQuery Validation for Portfolio Form
        $('#portfolio-form').validate({
            rules: {
                title: {
                    required: true,
                    minlength: 3,
                    maxlength: 100
                },
                category_id: {
                    required: true
                },
                year: {
                    digits: true,
                    min: 1900,
                    max: new Date().getFullYear()
                },
                tags: {
                    maxlength: 500
                }
            },
            messages: {
                title: {
                    required: 'Please enter a portfolio title',
                    minlength: 'Title must be at least 3 characters',
                    maxlength: 'Title must not exceed 100 characters'
                },
                category_id: {
                    required: 'Please select a category'
                },
                year: {
                    digits: 'Please enter a valid year',
                    min: 'Year must be 1900 or later',
                    max: 'Year cannot be in the future'
                },
                tags: {
                    maxlength: 'Tags must not exceed 500 characters'
                }
            },
            errorClass: 'error',
            validClass: 'valid',
            errorElement: 'label',
            errorPlacement: function(error, element) {
                error.insertAfter(element);
            }
        });

        // Logout with confirmation
        $('#vda-logout-btn').click(function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Logout',
                text: 'Are you sure you want to logout?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Logout',
                confirmButtonColor: '#f44336'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(skd_ajax_object.ajax_url, {
                        action: 'skd_logout_user',
                        nonce: nonce
                    }, function(response) {
                        if (response.success) {
                            window.location.href = '<?php echo home_url('/login/'); ?>';
                        }
                    });
                }
            });
        });

        // ==================== Certifications Management ====================

        // Add Certification
        $(document).on('click', '.add-cert-btn', function() {
            const certId = $(this).data('cert-id');
            const requiresVerification = $(this).data('requires-verification');

            if (requiresVerification == 1) {
                // Show upload dialog
                Swal.fire({
                    title: 'Add Certification',
                    html: `
                        <div style="text-align: left; margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">Upload Certificate (PDF, JPG, PNG)</label>
                            <input type="file" id="cert-upload" accept=".pdf,.jpg,.jpeg,.png" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        </div>
                        <div style="text-align: left;">
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">Notes (Optional)</label>
                            <textarea id="cert-notes" rows="3" placeholder="Additional information about this certification..." maxlength="200" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;"></textarea>
                            <small style="color: #666; display: block; margin-top: 5px;"><span id="cert-notes-count">0</span>/200 characters</small>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Submit for Review',
                    confirmButtonColor: '#667eea',
                    cancelButtonText: 'Cancel',
                    didOpen: () => {
                        // Add character counter for notes
                        $('#cert-notes').on('input', function() {
                            $('#cert-notes-count').text($(this).val().length);
                        });
                    },
                    preConfirm: () => {
                        const fileInput = document.getElementById('cert-upload');
                        if (!fileInput.files.length) {
                            Swal.showValidationMessage('Please upload a certificate file');
                            return false;
                        }
                        return {
                            file: fileInput.files[0],
                            notes: document.getElementById('cert-notes').value
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const formData = new FormData();
                        formData.append('action', 'skd_add_certification');
                        formData.append('nonce', nonce);
                        formData.append('certification_id', certId);
                        formData.append('certificate_file', result.value.file);
                        formData.append('notes', result.value.notes);

                        $.ajax({
                            url: skd_ajax_object.ajax_url,
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Submitted!',
                                        text: 'Your certification has been submitted for review',
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                    // Update the cert card UI
                                    const certCard = $('[data-cert-id="' + certId + '"]');
                                    certCard.addClass('has-cert');
                                    certCard.find('.vda-cert-status').html('<span class="vda-badge vda-badge-warning"><iconify-icon icon="mdi:clock"></iconify-icon> Pending Review</span>');

                                    // Build action buttons
                                    let actionButtons = '';
                                    if (response.data.certificate_file) {
                                        actionButtons += '<a href="' + response.data.certificate_file + '" target="_blank" class="vda-btn vda-btn-sm vda-btn-outline"><iconify-icon icon="mdi:file-document"></iconify-icon> View Certificate</a> ';
                                    }
                                    actionButtons += '<button type="button" class="vda-btn vda-btn-sm vda-btn-outline edit-cert-btn" data-user-cert-id="' + response.data.user_cert_id + '"><iconify-icon icon="mdi:pencil"></iconify-icon> Edit</button> ';
                                    actionButtons += '<button type="button" class="vda-btn vda-btn-sm vda-btn-danger remove-cert-btn" data-user-cert-id="' + response.data.user_cert_id + '"><iconify-icon icon="mdi:delete"></iconify-icon> Remove</button>';

                                    certCard.find('.vda-cert-actions').html(actionButtons);
                                } else {
                                    Swal.fire('Error', response.data.message || 'Failed to add certification', 'error');
                                }
                            }
                        });
                    }
                });
            } else {
                // No verification required - add directly
                $.post(skd_ajax_object.ajax_url, {
                    action: 'skd_add_certification',
                    nonce: nonce,
                    certification_id: certId
                }, function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Added!',
                            text: 'Certification added to your profile',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        // Update the cert card UI
                        const certCard = $('[data-cert-id="' + certId + '"]');
                        certCard.addClass('has-cert');
                        certCard.find('.vda-cert-status').html('<span class="vda-badge vda-badge-success"><iconify-icon icon="mdi:check-circle"></iconify-icon> Verified</span>');
                        certCard.find('.vda-cert-actions').html('<button type="button" class="vda-btn vda-btn-sm vda-btn-danger remove-cert-btn" data-user-cert-id="' + response.data.user_cert_id + '"><iconify-icon icon="mdi:delete"></iconify-icon> Remove</button>');
                    } else {
                        Swal.fire('Error', response.data.message || 'Failed to add certification', 'error');
                    }
                });
            }
        });

        // Edit Certification
        $(document).on('click', '.edit-cert-btn', function() {
            const userCertId = $(this).data('user-cert-id');
            const notes = $(this).data('notes') || '';
            const certFile = $(this).data('cert-file') || '';
            const notesLength = notes.length;

            // Build current file display
            let currentFileHtml = '';
            if (certFile) {
                const fileName = certFile.split('/').pop();
                currentFileHtml = `
                    <div style="background: #f5f5f5; padding: 10px; border-radius: 4px; margin-bottom: 10px;">
                        <small style="color: #666; display: block; margin-bottom: 5px;">Current File:</small>
                        <a href="${certFile}" target="_blank" style="color: #667eea; text-decoration: none; font-weight: 500;">
                            <iconify-icon icon="mdi:file-document" style="vertical-align: middle;"></iconify-icon> ${fileName}
                        </a>
                    </div>
                `;
            }

            Swal.fire({
                title: 'Update Certification',
                html: `
                    <div style="text-align: left; margin-bottom: 15px;">
                        ${currentFileHtml}
                        <label style="display: block; margin-bottom: 5px; font-weight: 600;">Upload New Certificate (Optional)</label>
                        <input type="file" id="cert-upload-edit" accept=".pdf,.jpg,.jpeg,.png" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <small style="color: #666; display: block; margin-top: 5px;">Leave empty to keep current file</small>
                    </div>
                    <div style="text-align: left;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 600;">Notes</label>
                        <textarea id="cert-notes-edit" rows="3" maxlength="200" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">${notes}</textarea>
                        <small style="color: #666; display: block; margin-top: 5px;"><span id="cert-notes-edit-count">${notesLength}</span>/200 characters</small>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Update',
                confirmButtonColor: '#667eea',
                didOpen: () => {
                    // Add character counter for edit notes
                    $('#cert-notes-edit').on('input', function() {
                        $('#cert-notes-edit-count').text($(this).val().length);
                    });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('action', 'skd_update_certification');
                    formData.append('nonce', nonce);
                    formData.append('user_cert_id', userCertId);

                    const fileInput = document.getElementById('cert-upload-edit');
                    if (fileInput.files.length) {
                        formData.append('certificate_file', fileInput.files[0]);
                    }
                    formData.append('notes', document.getElementById('cert-notes-edit').value);

                    $.ajax({
                        url: skd_ajax_object.ajax_url,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Updated!',
                                    text: 'Certification updated successfully. Resubmitted for review.',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                // Update status to pending review if file was uploaded
                                const certCard = $('[data-user-cert-id="' + userCertId + '"]').closest('.vda-cert-card');
                                certCard.find('.vda-cert-status').html('<span class="vda-badge vda-badge-warning"><iconify-icon icon="mdi:clock"></iconify-icon> Pending Review</span>');
                            } else {
                                Swal.fire('Error', response.data.message || 'Failed to update', 'error');
                            }
                        }
                    });
                }
            });
        });

        // Remove Certification
        $(document).on('click', '.remove-cert-btn', function() {
            const userCertId = $(this).data('user-cert-id');

            Swal.fire({
                title: 'Remove Certification?',
                text: 'This will remove the certification from your profile',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Remove',
                confirmButtonColor: '#f44336'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(skd_ajax_object.ajax_url, {
                        action: 'skd_remove_certification',
                        nonce: nonce,
                        user_cert_id: userCertId
                    }, function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Removed!',
                                text: 'Certification removed from your profile',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            // Update the cert card UI
                            const certCard = $('[data-user-cert-id="' + userCertId + '"]').closest('.vda-cert-card');
                            const certId = certCard.data('cert-id');
                            certCard.removeClass('has-cert');
                            certCard.find('.vda-cert-status').html('');
                            certCard.find('.vda-cert-actions').html('<button type="button" class="vda-btn vda-btn-sm vda-btn-outline add-cert-btn" data-cert-id="' + certId + '" data-requires-verification="1"><iconify-icon icon="mdi:plus"></iconify-icon> Add Certification</button>');
                        } else {
                            Swal.fire('Error', response.data.message || 'Failed to remove', 'error');
                        }
                    });
                }
            });
        });
    });
</script>

<!-- jQuery Validation Plugin -->
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/additional-methods.min.js"></script>