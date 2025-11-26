<?php

/**
 * Template for User Dashboard
 * Main dashboard interface for professionals and employers
 */

if (!defined('ABSPATH')) {
    exit;
}

// Check if user is logged in
if (!is_user_logged_in()) {
    echo '<p>Please <a href="' . wp_login_url() . '">login</a> to access your dashboard.</p>';
    return;
}

global $wpdb;
$current_user = wp_get_current_user();
$user_id = $current_user->ID;

// Get user type
$user_type = get_user_meta($user_id, 'skd_user_type', true);

// Get user role and profile data
$listings_table = $wpdb->prefix . 'skd_pl_listings';
$jobs_table = $wpdb->prefix . 'skd_pl_jobs';
$applications_table = $wpdb->prefix . 'skd_pl_job_applications';
$messages_table = $wpdb->prefix . 'skd_pl_messages';
$orders_table = $wpdb->prefix . 'skd_pl_orders';

// Get user's professional profile if exists
$user_profile = $wpdb->get_row($wpdb->prepare(
    "SELECT * FROM $listings_table WHERE user_id = %d",
    $user_id
));

// Get dashboard stats
$stats = [];

if ($user_profile) {
    // Stats for professionals
    $stats['profile_views'] = rand(45, 350); // This would be tracked in real implementation
    $stats['inquiries'] = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $messages_table WHERE recipient_id = %d AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)",
        $user_id
    )) ?: 0;
    $stats['applications_sent'] = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $applications_table WHERE user_id = %d",
        $user_id
    )) ?: 0;
    $stats['saved_jobs'] = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->prefix}skd_pl_saved_jobs WHERE user_id = %d",
        $user_id
    )) ?: 0;
} else {
    // Stats for employers
    $stats['jobs_posted'] = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $jobs_table WHERE user_id = %d",
        $user_id
    )) ?: 0;
    $stats['applications_received'] = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $applications_table a 
         INNER JOIN $jobs_table j ON a.job_id = j.id 
         WHERE j.user_id = %d",
        $user_id
    )) ?: 0;
    $stats['active_jobs'] = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $jobs_table WHERE user_id = %d AND status = 'active'",
        $user_id
    )) ?: 0;
}

$stats['messages'] = $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM $messages_table WHERE recipient_id = %d AND is_read = 0",
    $user_id
)) ?: 0;

// Get recent activity
$recent_applications = [];
$recent_jobs = [];
$recent_messages = [];

if ($user_profile) {
    // Get recent applications sent by user
    $recent_applications = $wpdb->get_results($wpdb->prepare(
        "SELECT a.*, j.title as job_title, j.company_name, j.location 
         FROM $applications_table a 
         INNER JOIN $jobs_table j ON a.job_id = j.id 
         WHERE a.user_id = %d 
         ORDER BY a.applied_at DESC 
         LIMIT 5",
        $user_id
    ));
} else {
    // Get recent jobs posted by employer
    $recent_jobs = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $jobs_table WHERE user_id = %d ORDER BY created_at DESC LIMIT 5",
        $user_id
    ));

    // Get applications for employer's jobs
    $recent_applications = $wpdb->get_results($wpdb->prepare(
        "SELECT a.*, j.title as job_title 
         FROM $applications_table a 
         INNER JOIN $jobs_table j ON a.job_id = j.id 
         WHERE j.user_id = %d 
         ORDER BY a.applied_at DESC 
         LIMIT 5",
        $user_id
    ));
}

// Get recent messages
$recent_messages = $wpdb->get_results($wpdb->prepare(
    "SELECT m.*, u.display_name as sender_name 
     FROM $messages_table m 
     INNER JOIN {$wpdb->users} u ON m.sender_id = u.ID 
     WHERE m.recipient_id = %d 
     ORDER BY m.created_at DESC 
     LIMIT 5",
    $user_id
));

$user_role = $user_profile ? $user_profile->user_role : 'employer';
?>

<div class="skd-dashboard-wrapper">
    <!-- Dashboard Header -->
    <div class="skd-dashboard-header">
        <div class="skd-header-content">
            <div class="skd-user-welcome">
                <div class="skd-user-avatar">
                    <?php if ($user_profile && $user_profile->skd_logo): ?>
                        <img src="<?php echo esc_url($user_profile->skd_logo); ?>" alt="Profile">
                    <?php else: ?>
                        <div class="skd-avatar-placeholder">
                            <?php echo strtoupper(substr($current_user->display_name, 0, 2)); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="skd-welcome-text">
                    <h1>Welcome back, <?php echo esc_html($current_user->display_name); ?>!</h1>
                    <p class="skd-user-role">
                        <?php
                        if ($user_profile) {
                            echo $user_role === 'studio' ? 'Design Studio' : 'Virtual Design Assistant';
                        } else {
                            echo 'Employer';
                        }
                        ?>
                    </p>
                </div>
            </div>

            <div class="skd-quick-actions">
                <?php if ($user_profile): ?>
                    <a href="/edit-profile/" class="skd-btn skd-btn-outline">
                        <span class="dashicons dashicons-edit"></span>
                        Edit Profile
                    </a>
                    <a href="/find-jobs/" class="skd-btn skd-btn-primary">
                        <span class="dashicons dashicons-search"></span>
                        Find Jobs
                    </a>
                <?php else: ?>
                    <a href="/create-profile/" class="skd-btn skd-btn-outline">
                        <span class="dashicons dashicons-plus"></span>
                        Create Profile
                    </a>
                    <a href="/post-job/" class="skd-btn skd-btn-primary">
                        <span class="dashicons dashicons-megaphone"></span>
                        Post Job
                    </a>
                <?php endif; ?>
                <button id="skd-logout-btn" class="skd-btn skd-btn-secondary skd-logout-btn">
                    <span class="dashicons dashicons-exit"></span>
                    Logout
                </button>
            </div>
        </div>
    </div>

    <!-- Dashboard Navigation -->
    <div class="skd-dashboard-nav">
        <nav class="skd-nav-tabs">
            <button class="skd-nav-tab active" data-tab="overview">
                <span class="dashicons dashicons-dashboard"></span>
                Overview
            </button>

            <?php if ($user_type === 'vda'): ?>
                <button class="skd-nav-tab" data-tab="profile">
                    <span class="dashicons dashicons-admin-users"></span>
                    My Profile
                </button>
            <?php endif; ?>

            <?php if ($user_profile): ?>
                <button class="skd-nav-tab" data-tab="applications">
                    <span class="dashicons dashicons-portfolio"></span>
                    My Applications
                    <?php if ($stats['applications_sent'] > 0): ?>
                        <span class="skd-badge-count"><?php echo $stats['applications_sent']; ?></span>
                    <?php endif; ?>
                </button>
                <button class="skd-nav-tab" data-tab="saved-jobs">
                    <span class="dashicons dashicons-heart"></span>
                    Saved Jobs
                    <?php if ($stats['saved_jobs'] > 0): ?>
                        <span class="skd-badge-count"><?php echo $stats['saved_jobs']; ?></span>
                    <?php endif; ?>
                </button>
            <?php else: ?>
                <button class="skd-nav-tab" data-tab="jobs">
                    <span class="dashicons dashicons-megaphone"></span>
                    My Jobs
                    <?php if ($stats['jobs_posted'] > 0): ?>
                        <span class="skd-badge-count"><?php echo $stats['jobs_posted']; ?></span>
                    <?php endif; ?>
                </button>
                <button class="skd-nav-tab" data-tab="applications">
                    <span class="dashicons dashicons-groups"></span>
                    Applications
                    <?php if ($stats['applications_received'] > 0): ?>
                        <span class="skd-badge-count"><?php echo $stats['applications_received']; ?></span>
                    <?php endif; ?>
                </button>
            <?php endif; ?>

            <button class="skd-nav-tab" data-tab="messages">
                <span class="dashicons dashicons-email"></span>
                Messages
                <?php if ($stats['messages'] > 0): ?>
                    <span class="skd-badge-count"><?php echo $stats['messages']; ?></span>
                <?php endif; ?>
            </button>

            <button class="skd-nav-tab" data-tab="settings">
                <span class="dashicons dashicons-admin-settings"></span>
                Settings
            </button>
        </nav>
    </div>

    <!-- Dashboard Content -->
    <div class="skd-dashboard-content">

        <!-- Overview Tab -->
        <div class="skd-tab-panel active" data-panel="overview">
            <!-- Stats Cards -->
            <div class="skd-stats-grid">
                <?php if ($user_profile): ?>
                    <div class="skd-stat-card">
                        <div class="skd-stat-icon">
                            <span class="dashicons dashicons-visibility"></span>
                        </div>
                        <div class="skd-stat-info">
                            <span class="skd-stat-number"><?php echo $stats['profile_views']; ?></span>
                            <span class="skd-stat-label">Profile Views</span>
                            <span class="skd-stat-change positive">+12% this month</span>
                        </div>
                    </div>

                    <div class="skd-stat-card">
                        <div class="skd-stat-icon">
                            <span class="dashicons dashicons-email-alt"></span>
                        </div>
                        <div class="skd-stat-info">
                            <span class="skd-stat-number"><?php echo $stats['inquiries']; ?></span>
                            <span class="skd-stat-label">New Inquiries</span>
                            <span class="skd-stat-change neutral">This month</span>
                        </div>
                    </div>

                    <div class="skd-stat-card">
                        <div class="skd-stat-icon">
                            <span class="dashicons dashicons-portfolio"></span>
                        </div>
                        <div class="skd-stat-info">
                            <span class="skd-stat-number"><?php echo $stats['applications_sent']; ?></span>
                            <span class="skd-stat-label">Applications</span>
                            <span class="skd-stat-change neutral">Total sent</span>
                        </div>
                    </div>

                    <div class="skd-stat-card">
                        <div class="skd-stat-icon">
                            <span class="dashicons dashicons-heart"></span>
                        </div>
                        <div class="skd-stat-info">
                            <span class="skd-stat-number"><?php echo $stats['saved_jobs']; ?></span>
                            <span class="skd-stat-label">Saved Jobs</span>
                            <span class="skd-stat-change neutral">Ready to apply</span>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="skd-stat-card">
                        <div class="skd-stat-icon">
                            <span class="dashicons dashicons-megaphone"></span>
                        </div>
                        <div class="skd-stat-info">
                            <span class="skd-stat-number"><?php echo $stats['jobs_posted']; ?></span>
                            <span class="skd-stat-label">Jobs Posted</span>
                            <span class="skd-stat-change positive">All time</span>
                        </div>
                    </div>

                    <div class="skd-stat-card">
                        <div class="skd-stat-icon">
                            <span class="dashicons dashicons-groups"></span>
                        </div>
                        <div class="skd-stat-info">
                            <span class="skd-stat-number"><?php echo $stats['applications_received']; ?></span>
                            <span class="skd-stat-label">Applications</span>
                            <span class="skd-stat-change neutral">Total received</span>
                        </div>
                    </div>

                    <div class="skd-stat-card">
                        <div class="skd-stat-icon">
                            <span class="dashicons dashicons-chart-line"></span>
                        </div>
                        <div class="skd-stat-info">
                            <span class="skd-stat-number"><?php echo $stats['active_jobs']; ?></span>
                            <span class="skd-stat-label">Active Jobs</span>
                            <span class="skd-stat-change positive">Currently live</span>
                        </div>
                    </div>

                    <div class="skd-stat-card">
                        <div class="skd-stat-icon">
                            <span class="dashicons dashicons-email"></span>
                        </div>
                        <div class="skd-stat-info">
                            <span class="skd-stat-number"><?php echo $stats['messages']; ?></span>
                            <span class="skd-stat-label">Unread Messages</span>
                            <span class="skd-stat-change neutral">Awaiting response</span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Quick Actions Section -->
            <div class="skd-overview-sections">
                <div class="skd-overview-left">
                    <!-- Recent Activity -->
                    <div class="skd-dashboard-card">
                        <div class="skd-card-header">
                            <h3>Recent Activity</h3>
                            <a href="#" class="skd-view-all">View All</a>
                        </div>
                        <div class="skd-card-body">
                            <?php if (!empty($recent_applications)): ?>
                                <div class="skd-activity-list">
                                    <?php foreach ($recent_applications as $application): ?>
                                        <div class="skd-activity-item">
                                            <div class="skd-activity-icon">
                                                <span class="dashicons dashicons-portfolio"></span>
                                            </div>
                                            <div class="skd-activity-content">
                                                <?php if ($user_profile): ?>
                                                    <p><strong>Applied to:</strong> <?php echo esc_html($application->job_title); ?></p>
                                                    <span class="skd-activity-meta"><?php echo esc_html($application->company_name); ?> • <?php echo human_time_diff(strtotime($application->applied_at)); ?> ago</span>
                                                <?php else: ?>
                                                    <p><strong>New application</strong> for <?php echo esc_html($application->job_title); ?></p>
                                                    <span class="skd-activity-meta">From <?php echo esc_html($application->applicant_name); ?> • <?php echo human_time_diff(strtotime($application->applied_at)); ?> ago</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="skd-activity-status">
                                                <span class="skd-status-badge <?php echo esc_attr($application->status); ?>">
                                                    <?php echo esc_html(ucwords($application->status)); ?>
                                                </span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="skd-empty-state">
                                    <span class="dashicons dashicons-portfolio"></span>
                                    <p>No recent activity</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Recent Messages -->
                    <?php if (!empty($recent_messages)): ?>
                        <div class="skd-dashboard-card">
                            <div class="skd-card-header">
                                <h3>Recent Messages</h3>
                                <a href="#" class="skd-view-all" onclick="switchTab('messages')">View All</a>
                            </div>
                            <div class="skd-card-body">
                                <div class="skd-message-list">
                                    <?php foreach (array_slice($recent_messages, 0, 3) as $message): ?>
                                        <div class="skd-message-item <?php echo $message->is_read ? '' : 'unread'; ?>">
                                            <div class="skd-message-avatar">
                                                <?php echo strtoupper(substr($message->sender_name, 0, 1)); ?>
                                            </div>
                                            <div class="skd-message-content">
                                                <div class="skd-message-header">
                                                    <strong><?php echo esc_html($message->sender_name); ?></strong>
                                                    <span class="skd-message-time"><?php echo human_time_diff(strtotime($message->created_at)); ?> ago</span>
                                                </div>
                                                <p><?php echo esc_html(wp_trim_words($message->message_text, 12)); ?></p>
                                            </div>
                                            <?php if (!$message->is_read): ?>
                                                <div class="skd-unread-indicator"></div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="skd-overview-right">
                    <!-- Profile Completion -->
                    <?php if ($user_profile): ?>
                        <?php
                        $completion_items = [
                            'Profile Photo' => !empty($user_profile->skd_logo),
                            'Professional Summary' => !empty($user_profile->listing_description),
                            'Skills & Software' => !empty($user_profile->skills),
                            'Portfolio Images' => !empty($user_profile->portfolio_images),
                            'Contact Information' => !empty($user_profile->contact_email),
                            'Hourly Rate' => !empty($user_profile->hourly_rate),
                        ];
                        $completed = array_filter($completion_items);
                        $completion_percentage = round((count($completed) / count($completion_items)) * 100);
                        ?>
                        <div class="skd-dashboard-card">
                            <div class="skd-card-header">
                                <h3>Profile Completion</h3>
                                <span class="skd-completion-percentage"><?php echo $completion_percentage; ?>%</span>
                            </div>
                            <div class="skd-card-body">
                                <div class="skd-progress-bar">
                                    <div class="skd-progress-fill" style="width: <?php echo $completion_percentage; ?>%"></div>
                                </div>
                                <div class="skd-completion-items">
                                    <?php foreach ($completion_items as $item => $completed): ?>
                                        <div class="skd-completion-item">
                                            <span class="dashicons <?php echo $completed ? 'dashicons-yes-alt completed' : 'dashicons-minus incomplete'; ?>"></span>
                                            <span class="<?php echo $completed ? 'completed' : 'incomplete'; ?>"><?php echo $item; ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php if ($completion_percentage < 100): ?>
                                    <div class="skd-completion-cta">
                                        <a href="/edit-profile/" class="skd-btn skd-btn-primary skd-btn-small">Complete Profile</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Quick Stats -->
                    <div class="skd-dashboard-card">
                        <div class="skd-card-header">
                            <h3>Quick Actions</h3>
                        </div>
                        <div class="skd-card-body">
                            <div class="skd-quick-action-list">
                                <?php if ($user_profile): ?>
                                    <a href="/find-jobs/" class="skd-quick-action">
                                        <span class="dashicons dashicons-search"></span>
                                        <div>
                                            <strong>Browse Jobs</strong>
                                            <p>Find new opportunities</p>
                                        </div>
                                    </a>
                                    <a href="/edit-profile/" class="skd-quick-action">
                                        <span class="dashicons dashicons-edit"></span>
                                        <div>
                                            <strong>Update Profile</strong>
                                            <p>Keep information current</p>
                                        </div>
                                    </a>
                                    <a href="/academy/" class="skd-quick-action">
                                        <span class="dashicons dashicons-book-alt"></span>
                                        <div>
                                            <strong>Learn & Grow</strong>
                                            <p>Access courses & resources</p>
                                        </div>
                                    </a>
                                <?php else: ?>
                                    <a href="/post-job/" class="skd-quick-action">
                                        <span class="dashicons dashicons-megaphone"></span>
                                        <div>
                                            <strong>Post New Job</strong>
                                            <p>Find talented professionals</p>
                                        </div>
                                    </a>
                                    <a href="/find-assistants/" class="skd-quick-action">
                                        <span class="dashicons dashicons-groups"></span>
                                        <div>
                                            <strong>Browse Professionals</strong>
                                            <p>Find VDAs and studios</p>
                                        </div>
                                    </a>
                                    <a href="/create-profile/" class="skd-quick-action">
                                        <span class="dashicons dashicons-plus"></span>
                                        <div>
                                            <strong>Create Profile</strong>
                                            <p>Showcase your work</p>
                                        </div>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Platform Tips -->
                    <div class="skd-dashboard-card">
                        <div class="skd-card-header">
                            <h3>Tips & Insights</h3>
                        </div>
                        <div class="skd-card-body">
                            <div class="skd-tip-item">
                                <span class="dashicons dashicons-lightbulb"></span>
                                <div>
                                    <?php if ($user_profile): ?>
                                        <p><strong>Tip:</strong> Profiles with portfolio images get 3x more inquiries than those without.</p>
                                    <?php else: ?>
                                        <p><strong>Tip:</strong> Jobs with detailed descriptions receive 40% more quality applications.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Tab (VDA Only) -->
        <?php if ($user_type === 'vda'): ?>
            <div class="skd-tab-panel" data-panel="profile">
                <div class="skd-tab-header">
                    <h2>My Professional Profile</h2>
                    <p>Manage your VDA profile, portfolio, and professional details</p>
                </div>
                <div class="skd-profile-editor-wrapper">
                    <?php echo do_shortcode('[skd_edit_vda_profile]'); ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Applications Tab -->
        <div class="skd-tab-panel" data-panel="applications">
            <div class="skd-tab-header">
                <h2><?php echo $user_profile ? 'My Applications' : 'Received Applications'; ?></h2>
                <div class="skd-tab-filters">
                    <select id="applicationStatus">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="reviewed">Reviewed</option>
                        <option value="interviewed">Interviewed</option>
                        <option value="hired">Hired</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
            </div>
            <div class="skd-applications-content" id="applicationsContent">
                <!-- Applications will be loaded here via AJAX -->
            </div>
        </div>

        <!-- Jobs Tab (for employers) or Saved Jobs Tab (for professionals) -->
        <?php if ($user_profile): ?>
            <div class="skd-tab-panel" data-panel="saved-jobs">
                <div class="skd-tab-header">
                    <h2>Saved Jobs</h2>
                    <div class="skd-tab-filters">
                        <input type="text" id="savedJobsSearch" placeholder="Search saved jobs...">
                    </div>
                </div>
                <div class="skd-saved-jobs-content" id="savedJobsContent">
                    <!-- Saved jobs will be loaded here via AJAX -->
                </div>
            </div>
        <?php else: ?>
            <div class="skd-tab-panel" data-panel="jobs">
                <div class="skd-tab-header">
                    <h2>My Posted Jobs</h2>
                    <a href="/post-job/" class="skd-btn skd-btn-primary">
                        <span class="dashicons dashicons-plus"></span>
                        Post New Job
                    </a>
                </div>
                <div class="skd-jobs-content" id="jobsContent">
                    <!-- Jobs will be loaded here via AJAX -->
                </div>
            </div>
        <?php endif; ?>

        <!-- Messages Tab -->
        <div class="skd-tab-panel" data-panel="messages">
            <div class="skd-tab-header">
                <h2>Messages</h2>
                <button class="skd-btn skd-btn-primary" onclick="openNewMessageModal()">
                    <span class="dashicons dashicons-plus"></span>
                    New Message
                </button>
            </div>
            <div class="skd-messages-content" id="messagesContent">
                <!-- Messages interface will be loaded here -->
            </div>
        </div>

        <!-- Settings Tab -->
        <div class="skd-tab-panel" data-panel="settings">
            <div class="skd-tab-header">
                <h2>Account Settings</h2>
            </div>
            <div class="skd-settings-content">
                <div class="skd-settings-grid">
                    <div class="skd-settings-section">
                        <h3>Account Information</h3>
                        <form id="accountSettingsForm">
                            <div class="skd-form-group">
                                <label>Display Name</label>
                                <input type="text" id="displayName" value="<?php echo esc_attr($current_user->display_name); ?>">
                            </div>
                            <div class="skd-form-group">
                                <label>Email Address</label>
                                <input type="email" id="userEmail" value="<?php echo esc_attr($current_user->user_email); ?>">
                            </div>
                            <button type="submit" class="skd-btn skd-btn-primary">Save Changes</button>
                        </form>
                    </div>

                    <div class="skd-settings-section">
                        <h3>Notification Preferences</h3>
                        <form id="notificationSettingsForm">
                            <div class="skd-checkbox-group">
                                <label class="skd-checkbox-label">
                                    <input type="checkbox" checked>
                                    <span class="skd-checkbox-custom"></span>
                                    Email notifications for new messages
                                </label>
                                <label class="skd-checkbox-label">
                                    <input type="checkbox" checked>
                                    <span class="skd-checkbox-custom"></span>
                                    Job application updates
                                </label>
                                <label class="skd-checkbox-label">
                                    <input type="checkbox">
                                    <span class="skd-checkbox-custom"></span>
                                    Weekly digest of new opportunities
                                </label>
                                <label class="skd-checkbox-label">
                                    <input type="checkbox">
                                    <span class="skd-checkbox-custom"></span>
                                    Marketing emails and promotions
                                </label>
                            </div>
                            <button type="submit" class="skd-btn skd-btn-primary">Update Preferences</button>
                        </form>
                    </div>

                    <div class="skd-settings-section">
                        <h3>Privacy Settings</h3>
                        <form id="privacySettingsForm">
                            <div class="skd-checkbox-group">
                                <label class="skd-checkbox-label">
                                    <input type="checkbox" checked>
                                    <span class="skd-checkbox-custom"></span>
                                    Make profile visible in search
                                </label>
                                <label class="skd-checkbox-label">
                                    <input type="checkbox" checked>
                                    <span class="skd-checkbox-custom"></span>
                                    Allow direct messages from other users
                                </label>
                                <label class="skd-checkbox-label">
                                    <input type="checkbox">
                                    <span class="skd-checkbox-custom"></span>
                                    Show contact information publicly
                                </label>
                            </div>
                            <button type="submit" class="skd-btn skd-btn-primary">Save Privacy Settings</button>
                        </form>
                    </div>

                    <div class="skd-settings-section danger">
                        <h3>Account Actions</h3>
                        <div class="skd-danger-zone">
                            <button class="skd-btn skd-btn-secondary" onclick="exportAccountData()">
                                <span class="dashicons dashicons-download"></span>
                                Export Account Data
                            </button>
                            <button class="skd-btn skd-btn-outline" onclick="deactivateAccount()" style="color: #d93025; border-color: #d93025;">
                                <span class="dashicons dashicons-dismiss"></span>
                                Deactivate Account
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Message Modal -->
<div id="newMessageModal" class="skd-modal" style="display: none;">
    <div class="skd-modal-content">
        <div class="skd-modal-header">
            <h3>New Message</h3>
            <button class="skd-modal-close" onclick="closeNewMessageModal()">&times;</button>
        </div>
        <div class="skd-modal-body">
            <form id="newMessageForm">
                <div class="skd-form-group">
                    <label>To</label>
                    <input type="text" id="messageRecipient" placeholder="Enter username or select from contacts" required>
                </div>
                <div class="skd-form-group">
                    <label>Subject</label>
                    <input type="text" id="messageSubject" required>
                </div>
                <div class="skd-form-group">
                    <label>Message</label>
                    <textarea id="messageText" required rows="6"></textarea>
                </div>
                <div class="skd-form-actions">
                    <button type="button" class="skd-btn skd-btn-secondary" onclick="closeNewMessageModal()">Cancel</button>
                    <button type="submit" class="skd-btn skd-btn-primary">Send Message</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        initializeDashboard();
    });

    function initializeDashboard() {
        // Tab switching functionality
        const tabButtons = document.querySelectorAll('.skd-nav-tab');
        const tabPanels = document.querySelectorAll('.skd-tab-panel');

        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const tabName = this.getAttribute('data-tab');
                switchTab(tabName);
            });
        });

        // Load initial content
        loadApplicationsContent();
        loadMessagesContent();
    }

    function switchTab(tabName) {
        // Remove active class from all tabs and panels
        document.querySelectorAll('.skd-nav-tab').forEach(tab => tab.classList.remove('active'));
        document.querySelectorAll('.skd-tab-panel').forEach(panel => panel.classList.remove('active'));

        // Add active class to selected tab and panel
        document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');
        document.querySelector(`[data-panel="${tabName}"]`).classList.add('active');

        // Load content based on tab
        switch (tabName) {
            case 'profile':
                // Profile content is already loaded via shortcode
                break;
            case 'applications':
                loadApplicationsContent();
                break;
            case 'jobs':
                loadJobsContent();
                break;
            case 'saved-jobs':
                loadSavedJobsContent();
                break;
            case 'messages':
                loadMessagesContent();
                break;
        }
    }

    function loadApplicationsContent() {
        const container = document.getElementById('applicationsContent');
        if (!container) return;

        container.innerHTML = '<div class="skd-loading">Loading applications...</div>';

        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=skd_get_user_applications'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    container.innerHTML = data.data.html;
                } else {
                    container.innerHTML = '<div class="skd-error">Failed to load applications.</div>';
                }
            });
    }

    function loadJobsContent() {
        const container = document.getElementById('jobsContent');
        if (!container) return;

        container.innerHTML = '<div class="skd-loading">Loading jobs...</div>';

        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=skd_get_user_jobs'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    container.innerHTML = data.data.html;
                } else {
                    container.innerHTML = '<div class="skd-error">Failed to load jobs.</div>';
                }
            });
    }

    function loadSavedJobsContent() {
        const container = document.getElementById('savedJobsContent');
        if (!container) return;

        container.innerHTML = '<div class="skd-loading">Loading saved jobs...</div>';

        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=skd_get_saved_jobs'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    container.innerHTML = data.data.html;
                } else {
                    container.innerHTML = '<div class="skd-error">Failed to load saved jobs.</div>';
                }
            });
    }

    function loadMessagesContent() {
        const container = document.getElementById('messagesContent');
        if (!container) return;

        container.innerHTML = '<div class="skd-loading">Loading messages...</div>';

        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=skd_get_user_messages'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    container.innerHTML = data.data.html;
                } else {
                    container.innerHTML = '<div class="skd-error">Failed to load messages.</div>';
                }
            });
    }

    function openNewMessageModal() {
        document.getElementById('newMessageModal').style.display = 'block';
    }

    function closeNewMessageModal() {
        document.getElementById('newMessageModal').style.display = 'none';
        document.getElementById('newMessageForm').reset();
    }

    // Form submissions
    document.getElementById('newMessageForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData();
        formData.append('action', 'skd_send_message');
        formData.append('recipient', document.getElementById('messageRecipient').value);
        formData.append('subject', document.getElementById('messageSubject').value);
        formData.append('message', document.getElementById('messageText').value);

        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Message sent successfully!');
                    closeNewMessageModal();
                    loadMessagesContent();
                } else {
                    alert('Error sending message: ' + (data.data || 'Please try again.'));
                }
            });
    });

    // Settings forms
    document.getElementById('accountSettingsForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData();
        formData.append('action', 'skd_update_account_settings');
        formData.append('display_name', document.getElementById('displayName').value);
        formData.append('email', document.getElementById('userEmail').value);

        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Account settings updated successfully!');
                } else {
                    alert('Error updating settings: ' + (data.data || 'Please try again.'));
                }
            });
    });

    function exportAccountData() {
        if (confirm('Are you sure you want to export your account data? This will generate a downloadable file with all your information.')) {
            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=skd_export_account_data'
                })
                .then(response => response.blob())
                .then(blob => {
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = url;
                    a.download = 'account_data_<?php echo date('Y-m-d'); ?>.json';
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                });
        }
    }

    function deactivateAccount() {
        if (confirm('Are you sure you want to deactivate your account? This action cannot be undone and all your data will be removed.')) {
            if (confirm('This is your final warning. All your data will be permanently deleted. Continue?')) {
                fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'action=skd_deactivate_account'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Account deactivated successfully. You will be logged out.');
                            window.location.href = '/';
                        } else {
                            alert('Error deactivating account: ' + (data.data || 'Please try again.'));
                        }
                    });
            }
        }
    }
</script>