<?php

/**
 * Employer Dashboard Template
 * Dashboard for Employers/Recruiters
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!is_user_logged_in()) {
    echo '<p>Please <a href="' . home_url('/login/') . '">login</a> to access your dashboard.</p>';
    return;
}

global $wpdb;
$user_id = get_current_user_id();
$user = wp_get_current_user();
$user_type = get_user_meta($user_id, 'skd_user_type', true);

if ($user_type !== 'employer') {
    echo '<p>This dashboard is only for Employer users.</p>';
    return;
}

// Get employer profile data (if exists)
$profile = $wpdb->get_row($wpdb->prepare(
    "SELECT * FROM {$wpdb->prefix}skd_pl_employer_profiles WHERE user_id = %d",
    $user_id
));

// Create default profile if doesn't exist
if (!$profile) {
    $wpdb->insert(
        $wpdb->prefix . 'skd_pl_employer_profiles',
        [
            'user_id' => $user_id,
            'company_name' => '',
            'company_size' => '',
            'industry' => '',
            'website' => '',
            'phone' => '',
            'bio' => '',
            'logo_url' => '',
            'created_at' => current_time('mysql')
        ]
    );

    $profile = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}skd_pl_employer_profiles WHERE user_id = %d",
        $user_id
    ));
}

// Get stats
$total_jobs = $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM {$wpdb->prefix}skd_pl_jobs WHERE employer_id = %d",
    $user_id
));

$active_jobs = $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM {$wpdb->prefix}skd_pl_jobs WHERE employer_id = %d AND status = 'active'",
    $user_id
));

$total_applications = $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM {$wpdb->prefix}skd_pl_job_applications ja
     INNER JOIN {$wpdb->prefix}skd_pl_jobs j ON ja.job_id = j.id
     WHERE j.employer_id = %d",
    $user_id
));

$saved_vdas = $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM {$wpdb->prefix}skd_pl_saved_vdas WHERE employer_id = %d",
    $user_id
));

?>

<div class="employer-dashboard-wrapper">
    <!-- Sidebar -->
    <aside class="employer-sidebar">
        <div class="employer-profile-card">
            <div class="employer-avatar">
                <?php if (!empty($profile->logo_url)): ?>
                    <img src="<?php echo esc_url($profile->logo_url); ?>" alt="<?php echo esc_attr($user->display_name); ?>" id="sidebar-avatar-img">
                <?php else: ?>
                    <div class="employer-avatar-placeholder" id="sidebar-avatar-placeholder">
                        <?php echo esc_html(strtoupper(substr($user->display_name, 0, 2))); ?>
                    </div>
                <?php endif; ?>
            </div>
            <h3><?php echo esc_html($profile->company_name ?: $user->display_name); ?></h3>
            <p class="employer-tagline"><?php echo esc_html($profile->industry ?: 'Employer'); ?></p>

            <div class="employer-stats">
                <div class="employer-stat">
                    <span class="stat-value"><?php echo $active_jobs; ?></span>
                    <span class="stat-label">Active Jobs</span>
                </div>
                <div class="employer-stat">
                    <span class="stat-value"><?php echo $total_applications; ?></span>
                    <span class="stat-label">Applications</span>
                </div>
                <div class="employer-stat">
                    <span class="stat-value"><?php echo $saved_vdas; ?></span>
                    <span class="stat-label">Saved VDAs</span>
                </div>
            </div>
        </div>

        <nav class="employer-menu">
            <a href="#" class="employer-menu-item active" data-tab="dashboard">
                <iconify-icon icon="material-symbols:dashboard-outline"></iconify-icon>
                <span>Dashboard</span>
            </a>
            <a href="#" class="employer-menu-item" data-tab="jobs">
                <iconify-icon icon="material-symbols:work-outline"></iconify-icon>
                <span>My Jobs</span>
            </a>
            <a href="#" class="employer-menu-item" data-tab="saved-vdas">
                <iconify-icon icon="material-symbols:bookmark-outline"></iconify-icon>
                <span>Saved VDAs</span>
            </a>
            <a href="#" class="employer-menu-item" data-tab="applications">
                <iconify-icon icon="material-symbols:description-outline"></iconify-icon>
                <span>Applications</span>
            </a>
            <a href="#" class="employer-menu-item" data-tab="settings">
                <iconify-icon icon="material-symbols:settings-outline"></iconify-icon>
                <span>Account Settings</span>
            </a>
            <a href="#" class="employer-menu-item employer-logout" id="employer-logout-btn">
                <iconify-icon icon="material-symbols:logout"></iconify-icon>
                <span>Logout</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="employer-main-content">

        <!-- Dashboard Tab -->
        <div id="tab-dashboard" class="employer-tab-content active">
            <div class="employer-header">
                <h1>Dashboard</h1>
                <p>Welcome back, <?php echo esc_html($user->display_name); ?>!</p>
            </div>

            <!-- Stats Grid -->
            <div class="employer-stats-grid">
                <div class="employer-stat-card">
                    <div class="stat-icon" style="background: #e3f2fd; color: #1976d2;">
                        <iconify-icon icon="material-symbols:work-outline"></iconify-icon>
                    </div>
                    <div class="stat-info">
                        <h4><?php echo $total_jobs; ?></h4>
                        <p>Total Jobs Posted</p>
                    </div>
                </div>
                <div class="employer-stat-card">
                    <div class="stat-icon" style="background: #e8f5e9; color: #388e3c;">
                        <iconify-icon icon="material-symbols:check-circle-outline"></iconify-icon>
                    </div>
                    <div class="stat-info">
                        <h4><?php echo $active_jobs; ?></h4>
                        <p>Active Jobs</p>
                    </div>
                </div>
                <div class="employer-stat-card">
                    <div class="stat-icon" style="background: #fff3e0; color: #f57c00;">
                        <iconify-icon icon="material-symbols:description-outline"></iconify-icon>
                    </div>
                    <div class="stat-info">
                        <h4><?php echo $total_applications; ?></h4>
                        <p>Applications Received</p>
                    </div>
                </div>
                <div class="employer-stat-card">
                    <div class="stat-icon" style="background: #fce4ec; color: #c2185b;">
                        <iconify-icon icon="material-symbols:bookmark-outline"></iconify-icon>
                    </div>
                    <div class="stat-info">
                        <h4><?php echo $saved_vdas; ?></h4>
                        <p>Saved VDAs</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="employer-quick-actions">
                <h2>Quick Actions</h2>
                <div class="action-cards">
                    <div class="action-card" data-tab="jobs" data-action="post-job">
                        <iconify-icon icon="material-symbols:add-circle-outline"></iconify-icon>
                        <h3>Post a Job</h3>
                        <p>Create a new job listing to attract talented VDAs</p>
                    </div>
                    <div class="action-card" data-tab="find-vdas">
                        <iconify-icon icon="material-symbols:search"></iconify-icon>
                        <h3>Find VDAs</h3>
                        <p>Browse and search for virtual design assistants</p>
                    </div>
                    <div class="action-card" data-tab="applications">
                        <iconify-icon icon="material-symbols:description-outline"></iconify-icon>
                        <h3>Review Applications</h3>
                        <p>Check applications for your job postings</p>
                    </div>
                </div>
            </div>

            <!-- Recent Jobs -->
            <div class="employer-section">
                <div class="section-header">
                    <h2>Recent Job Postings</h2>
                    <a href="#" class="view-all-link" data-tab="jobs">View All</a>
                </div>
                <div id="recent-jobs-list"></div>
            </div>
        </div>

        <!-- My Jobs Tab -->
        <div id="tab-jobs" class="employer-tab-content">
            <div class="employer-header">
                <h1>My Jobs</h1>
                <a href="<?php echo home_url('/post-job/'); ?>" class="employer-btn employer-btn-primary" id="post-job-btn">
                    <iconify-icon icon="material-symbols:add"></iconify-icon>
                    Post New Job
                </a>
            </div>
            <div id="jobs-list"></div>
        </div>

        <!-- Saved VDAs Tab -->
        <div id="tab-saved-vdas" class="employer-tab-content">
            <div class="employer-header">
                <h1>Saved VDAs</h1>
                <p>VDAs you've bookmarked for future reference</p>
                <a href="<?php echo home_url('/find-assistants/'); ?>" class="employer-btn employer-btn-primary">
                    <iconify-icon icon="material-symbols:search"></iconify-icon>
                    Find VDAs
                </a>
            </div>
            <div id="saved-vdas-list"></div>
        </div>

        <!-- Applications Tab -->
        <div id="tab-applications" class="employer-tab-content">
            <div class="employer-header">
                <h1>Job Applications</h1>
                <p>Review and manage applications for your job postings</p>
            </div>
            <div id="applications-list"></div>
        </div>

        <!-- Account Settings Tab -->
        <div id="tab-settings" class="employer-tab-content">
            <div class="employer-header">
                <h1>Account Settings</h1>
                <p>Manage your account information and preferences</p>
            </div>

            <!-- Account Settings Content -->
            <div class="employer-settings-wrapper">
                <!-- Change Password Section -->
                <div class="employer-settings-card">
                    <h3><iconify-icon icon="material-symbols:lock-outline"></iconify-icon> Change Password</h3>
                    <form id="change-password-form" class="employer-settings-form">
                        <div class="employer-form-group employer-password-field">
                            <label for="current_password">Current Password *</label>
                            <div class="employer-password-wrapper">
                                <input type="password" id="current_password" name="current_password">
                                <iconify-icon icon="mdi:eye-off" class="employer-password-toggle" onclick="togglePassword('current_password')"></iconify-icon>
                            </div>
                        </div>
                        <div class="employer-form-group employer-password-field">
                            <label for="new_password">New Password *</label>
                            <div class="employer-password-wrapper">
                                <input type="password" id="new_password" name="new_password">
                                <iconify-icon icon="mdi:eye-off" class="employer-password-toggle" onclick="togglePassword('new_password')"></iconify-icon>
                            </div>
                            <small>Minimum 8 characters</small>
                        </div>
                        <div class="employer-form-group employer-password-field">
                            <label for="confirm_password">Confirm New Password *</label>
                            <div class="employer-password-wrapper">
                                <input type="password" id="confirm_password" name="confirm_password">
                                <iconify-icon icon="mdi:eye-off" class="employer-password-toggle" onclick="togglePassword('confirm_password')"></iconify-icon>
                            </div>
                        </div>
                        <button type="submit" class="employer-btn employer-btn-primary">
                            <iconify-icon icon="material-symbols:save-outline"></iconify-icon>
                            Update Password
                        </button>
                    </form>
                </div>

                <!-- Account Information -->
                <div class="employer-settings-card">
                    <h3><iconify-icon icon="material-symbols:person-outline"></iconify-icon> Account Information</h3>
                    <form id="account-info-form" class="employer-settings-form">
                        <div class="employer-form-group">
                            <label for="account_email">Email Address</label>
                            <input type="email" id="account_email" name="email" value="<?php echo esc_attr($user->user_email); ?>" readonly>
                            <small>Contact admin to change your email address</small>
                        </div>
                        <div class="employer-form-row">
                            <div class="employer-form-group">
                                <label for="account_first_name">First Name *</label>
                                <input type="text" id="account_first_name" name="first_name" value="<?php echo esc_attr($user->first_name); ?>">
                            </div>
                            <div class="employer-form-group">
                                <label for="account_last_name">Last Name *</label>
                                <input type="text" id="account_last_name" name="last_name" value="<?php echo esc_attr($user->last_name); ?>">
                            </div>
                        </div>
                        <div class="employer-form-group">
                            <label for="account_display_name">Display Name *</label>
                            <input type="text" id="account_display_name" name="display_name" value="<?php echo esc_attr($user->display_name); ?>">
                        </div>
                        <div class="employer-form-group">
                            <label for="account_phone">Phone Number</label>
                            <input type="tel" id="account_phone" name="phone" value="<?php echo esc_attr(get_user_meta($user_id, 'phone', true)); ?>">
                        </div>
                        <button type="submit" class="employer-btn employer-btn-primary">
                            <iconify-icon icon="material-symbols:save-outline"></iconify-icon>
                            Update Information
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </main>
</div>

<script>
    // Toggle password visibility
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = field.nextElementSibling;

        if (field.type === 'password') {
            field.type = 'text';
            icon.setAttribute('icon', 'mdi:eye');
        } else {
            field.type = 'password';
            icon.setAttribute('icon', 'mdi:eye-off');
        }
    }

    jQuery(document).ready(function($) {
        const nonce = '<?php echo wp_create_nonce('skd_ajax_nonce'); ?>';

        console.log('Employer Dashboard JS Loaded');
        console.log('AJAX URL:', skd_ajax_object.ajax_url);
        console.log('Nonce:', nonce);

        // Tab switching
        $('.employer-menu-item[data-tab]').click(function(e) {
            e.preventDefault();
            if ($(this).hasClass('employer-logout')) return;

            const tab = $(this).data('tab');
            console.log('Tab clicked:', tab);

            $('.employer-menu-item').removeClass('active');
            $(this).addClass('active');
            $('.employer-tab-content').removeClass('active');
            $('#tab-' + tab).addClass('active');

            // Load content for specific tabs
            if (tab === 'jobs') loadJobs();
            if (tab === 'saved-vdas') loadSavedVDAs();
            if (tab === 'applications') loadApplications();
        });

        // Quick action cards
        $('.action-card[data-tab]').click(function() {
            const tab = $(this).data('tab');
            $('.employer-menu-item[data-tab="' + tab + '"]').click();
        });

        // Logout
        $('#employer-logout-btn').click(function(e) {
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



        // Load jobs function
        function loadJobs() {
            console.log('Loading jobs...');
            $.post(skd_ajax_object.ajax_url, {
                action: 'skd_get_employer_jobs',
                nonce: nonce
            }, function(response) {
                console.log('Jobs response:', response);
                if (response.success) {
                    $('#jobs-list').html(response.data.html);
                } else {
                    console.error('Error loading jobs:', response);
                    $('#jobs-list').html('<div class="employer-error">Error loading jobs</div>');
                }
            }).fail(function(xhr, status, error) {
                console.error('AJAX error:', error, xhr.responseText);
                $('#jobs-list').html('<div class="employer-error">Failed to load jobs. Check console for details.</div>');
            });
        }

        // Load saved VDAs
        function loadSavedVDAs() {
            console.log('Loading saved VDAs...');
            $.post(skd_ajax_object.ajax_url, {
                action: 'skd_get_saved_vdas',
                nonce: nonce
            }, function(response) {
                console.log('Saved VDAs response:', response);
                if (response.success) {
                    $('#saved-vdas-list').html(response.data.html);
                } else {
                    console.error('Error loading saved VDAs:', response);
                    $('#saved-vdas-list').html('<div class="employer-error">Error loading saved VDAs</div>');
                }
            }).fail(function(xhr, status, error) {
                console.error('AJAX error:', error, xhr.responseText);
                $('#saved-vdas-list').html('<div class="employer-error">Failed to load saved VDAs</div>');
            });
        }

        // Load applications
        function loadApplications() {
            console.log('Loading applications...');
            $.post(skd_ajax_object.ajax_url, {
                action: 'skd_get_employer_applications',
                nonce: nonce
            }, function(response) {
                console.log('Applications response:', response);
                if (response.success) {
                    $('#applications-list').html(response.data.html);
                } else {
                    console.error('Error loading applications:', response);
                    $('#applications-list').html('<div class="employer-error">Error loading applications</div>');
                }
            }).fail(function(xhr, status, error) {
                console.error('AJAX error:', error, xhr.responseText);
                $('#applications-list').html('<div class="employer-error">Failed to load applications</div>');
            });
        }

        // Load recent jobs on dashboard
        function loadRecentJobs() {
            console.log('Loading recent jobs...');
            $.post(skd_ajax_object.ajax_url, {
                action: 'skd_get_employer_jobs',
                nonce: nonce,
                limit: 5
            }, function(response) {
                console.log('Recent jobs response:', response);
                if (response.success) {
                    $('#recent-jobs-list').html(response.data.html);
                } else {
                    console.error('Error loading recent jobs:', response);
                }
            }).fail(function(xhr, status, error) {
                console.error('AJAX error:', error, xhr.responseText);
            });
        }

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
                if (element.closest('.employer-password-wrapper').length) {
                    error.insertAfter(element.closest('.employer-password-wrapper'));
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
                        action: 'skd_update_employer_password',
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
                    action: 'skd_update_employer_account_info',
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
                            // Update sidebar display name
                            $('.employer-profile-card h3').text(formData.display_name);
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

        // Initial load
        console.log('Running initial load...');
        loadRecentJobs();

        // Post New Job button handler
        $(document).on('click', '#post-job-btn', function(e) {
            // Link will navigate to post-job page
        });

        // View Job Details
        $(document).on('click', '.view-job', function(e) {
            e.preventDefault();
            const jobId = $(this).data('id');

            $.ajax({
                url: skd_ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'skd_get_job_details',
                    nonce: nonce,
                    job_id: jobId
                },
                success: function(response) {
                    if (response.success) {
                        const job = response.data.job;
                        showJobDetailsModal(job);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.data.message || 'Failed to load job details'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load job details'
                    });
                }
            });
        });

        // Edit Job
        $(document).on('click', '.edit-job', function(e) {
            e.preventDefault();
            const jobId = $(this).data('id');
            // Redirect to edit page or open edit modal
            window.location.href = '<?php echo home_url('/post-job/'); ?>?edit=' + jobId;
        });

        // Delete Job
        $(document).on('click', '.delete-job', function(e) {
            e.preventDefault();
            const button = $(this);
            const jobId = button.data('id');

            Swal.fire({
                title: 'Delete Job?',
                text: 'This will permanently delete this job posting and all applications',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete',
                confirmButtonColor: '#d33',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: skd_ajax_object.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'skd_delete_job',
                            nonce: nonce,
                            job_id: jobId
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted',
                                    text: 'Job deleted successfully',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                loadJobs();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.data.message || 'Failed to delete job'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to delete job'
                            });
                        }
                    });
                }
            });
        });

        // Toggle Job Status
        $(document).on('click', '.toggle-job-status', function(e) {
            e.preventDefault();
            const button = $(this);
            const jobId = button.data('id');
            const currentStatus = button.data('status');

            $.ajax({
                url: skd_ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'skd_toggle_job_status',
                    nonce: nonce,
                    job_id: jobId
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Updated',
                            text: 'Job status updated to ' + response.data.new_status,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        loadJobs();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.data.message || 'Failed to update status'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to update job status'
                    });
                }
            });
        });

        // Function to show job details modal
        function showJobDetailsModal(job) {
            const statusClass = job.status === 'open' ? 'status-open' : 'status-closed';
            const statusText = job.status.charAt(0).toUpperCase() + job.status.slice(1);
            const toggleBtnText = job.status === 'open' ? 'Close Applications' : 'Reopen Job';

            Swal.fire({
                title: job.title,
                html: `
                    <div class="job-detail-modal">
                        <div class="job-detail-header">
                            <span class="job-status ${statusClass}">${statusText}</span>
                            <div class="job-stats">
                                <span><iconify-icon icon="material-symbols:description-outline"></iconify-icon> ${job.applications_count} Applications</span>
                                <span><iconify-icon icon="material-symbols:pending-outline"></iconify-icon> ${job.pending_count} Pending</span>
                            </div>
                        </div>
                        <div class="job-detail-body">
                            <div class="job-description">
                                <h4>Description</h4>
                                <p>${job.description}</p>
                            </div>
                            <div class="job-meta-info">
                                <p><strong>Posted:</strong> ${new Date(job.created_at).toLocaleDateString()}</p>
                                <p><strong>Last Updated:</strong> ${new Date(job.updated_at).toLocaleDateString()}</p>
                            </div>
                        </div>
                        <div class="job-detail-actions">
                            <button class="employer-btn employer-btn-sm toggle-job-status" data-id="${job.id}" data-status="${job.status}">
                                ${toggleBtnText}
                            </button>
                            <button class="employer-btn employer-btn-sm edit-job" data-id="${job.id}">
                                <iconify-icon icon="material-symbols:edit"></iconify-icon> Edit
                            </button>
                        </div>
                    </div>
                `,
                width: '600px',
                showConfirmButton: false,
                showCloseButton: true,
                customClass: {
                    container: 'job-detail-swal'
                }
            });
        }

        // Unsave VDA handler (delegated event for dynamically loaded content)
        $(document).on('click', '.unsave-vda', function(e) {
            e.preventDefault();
            const button = $(this);
            const vdaId = button.data('id');

            Swal.fire({
                title: 'Remove VDA?',
                text: 'This VDA will be removed from your saved list',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Remove',
                confirmButtonColor: '#f44336',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: skd_ajax_object.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'skd_unsave_vda',
                            nonce: nonce,
                            vda_id: vdaId
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Removed',
                                    text: 'VDA removed from your saved list',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                // Reload saved VDAs list
                                loadSavedVDAs();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.data.message || 'Failed to remove VDA'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred while removing VDA'
                            });
                        }
                    });
                }
            });
        });
    });
</script>

<!-- jQuery Validation Library -->
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>