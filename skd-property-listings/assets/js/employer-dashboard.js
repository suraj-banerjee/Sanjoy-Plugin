jQuery(document).ready(function ($) {
    'use strict';

    const ajaxUrl = skd_pl_ajax.ajax_url;
    const nonce = skd_pl_ajax.nonce;

    // Tab navigation
    $('.employer-menu-item').on('click', function () {
        const tab = $(this).data('tab');

        $('.employer-menu-item').removeClass('active');
        $(this).addClass('active');

        $('.employer-tab-content').removeClass('active');
        $('#' + tab).addClass('active');

        // Load dynamic content based on tab
        loadTabContent(tab);
    });

    // Load tab content dynamically
    function loadTabContent(tab) {
        switch (tab) {
            case 'my-jobs':
                loadEmployerJobs();
                break;
            case 'find-vdas':
                loadVDASearch();
                break;
            case 'saved-vdas':
                loadSavedVDAs();
                break;
            case 'applications':
                loadApplications();
                break;
        }
    }

    // Load employer jobs
    function loadEmployerJobs(limit = null) {
        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'skd_get_employer_jobs',
                nonce: nonce,
                limit: limit
            },
            beforeSend: function () {
                $('#my-jobs').find('.employer-tab-body').html('<div class="loading">Loading jobs...</div>');
            },
            success: function (response) {
                if (response.success) {
                    $('#my-jobs').find('.employer-tab-body').html(response.data.html);
                } else {
                    showAlert('error', response.data.message);
                }
            },
            error: function () {
                showAlert('error', 'Failed to load jobs');
            }
        });
    }

    // Search VDAs
    function loadVDASearch() {
        const searchVal = $('#vda-search').val() || '';
        const specializationVal = $('#vda-specialization').val() || 0;
        const skillVal = $('#vda-skill').val() || 0;

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'skd_search_vdas',
                nonce: nonce,
                search: searchVal,
                specialization: specializationVal,
                skill: skillVal
            },
            beforeSend: function () {
                $('#vda-search-results').html('<div class="loading">Searching VDAs...</div>');
            },
            success: function (response) {
                if (response.success) {
                    $('#vda-search-results').html(response.data.html);
                } else {
                    showAlert('error', response.data.message);
                }
            },
            error: function () {
                showAlert('error', 'Failed to search VDAs');
            }
        });
    }

    // VDA search form submit
    $('#vda-search-form').on('submit', function (e) {
        e.preventDefault();
        loadVDASearch();
    });

    // Save VDA
    $(document).on('click', '.save-vda', function () {
        const btn = $(this);
        const vdaId = btn.data('id');

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'skd_save_vda',
                nonce: nonce,
                vda_id: vdaId
            },
            beforeSend: function () {
                btn.prop('disabled', true);
            },
            success: function (response) {
                if (response.success) {
                    showAlert('success', response.data.message);
                    btn.html('<iconify-icon icon="mdi:bookmark"></iconify-icon> Saved').addClass('saved');
                } else {
                    showAlert('error', response.data.message);
                    btn.prop('disabled', false);
                }
            },
            error: function () {
                showAlert('error', 'Failed to save VDA');
                btn.prop('disabled', false);
            }
        });
    });

    // Unsave VDA
    $(document).on('click', '.unsave-vda', function () {
        const vdaId = $(this).data('id');
        const card = $(this).closest('.saved-vda-card');

        if (!confirm('Remove this VDA from your saved list?')) {
            return;
        }

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'skd_unsave_vda',
                nonce: nonce,
                vda_id: vdaId
            },
            success: function (response) {
                if (response.success) {
                    card.fadeOut(300, function () {
                        $(this).remove();
                        if ($('.saved-vda-card').length === 0) {
                            loadSavedVDAs();
                        }
                    });
                    showAlert('success', response.data.message);
                } else {
                    showAlert('error', response.data.message);
                }
            },
            error: function () {
                showAlert('error', 'Failed to remove VDA');
            }
        });
    });

    // Load saved VDAs
    function loadSavedVDAs() {
        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'skd_get_saved_vdas',
                nonce: nonce
            },
            beforeSend: function () {
                $('#saved-vdas').find('.employer-tab-body').html('<div class="loading">Loading saved VDAs...</div>');
            },
            success: function (response) {
                if (response.success) {
                    $('#saved-vdas').find('.employer-tab-body').html(response.data.html);
                } else {
                    showAlert('error', response.data.message);
                }
            },
            error: function () {
                showAlert('error', 'Failed to load saved VDAs');
            }
        });
    }

    // Load applications
    function loadApplications() {
        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'skd_get_employer_applications',
                nonce: nonce
            },
            beforeSend: function () {
                $('#applications').find('.employer-tab-body').html('<div class="loading">Loading applications...</div>');
            },
            success: function (response) {
                if (response.success) {
                    $('#applications').find('.employer-tab-body').html(response.data.html);
                } else {
                    showAlert('error', response.data.message);
                }
            },
            error: function () {
                showAlert('error', 'Failed to load applications');
            }
        });
    }

    // Update employer profile
    $('#employer-profile-form').on('submit', function (e) {
        e.preventDefault();
        const formData = $(this).serialize();

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: formData + '&action=skd_update_employer_profile&nonce=' + nonce,
            beforeSend: function () {
                $('#employer-profile-form button[type="submit"]').prop('disabled', true).text('Saving...');
            },
            success: function (response) {
                if (response.success) {
                    showAlert('success', response.data.message);
                } else {
                    showAlert('error', response.data.message);
                }
            },
            error: function () {
                showAlert('error', 'Failed to update profile');
            },
            complete: function () {
                $('#employer-profile-form button[type="submit"]').prop('disabled', false).text('Save Changes');
            }
        });
    });

    // Update employer settings
    $('#employer-settings-form').on('submit', function (e) {
        e.preventDefault();
        const formData = $(this).serialize();

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: formData + '&action=skd_update_employer_settings&nonce=' + nonce,
            beforeSend: function () {
                $('#employer-settings-form button[type="submit"]').prop('disabled', true).text('Saving...');
            },
            success: function (response) {
                if (response.success) {
                    showAlert('success', response.data.message);
                    $('#current_password, #new_password, #confirm_password').val('');
                } else {
                    showAlert('error', response.data.message);
                }
            },
            error: function () {
                showAlert('error', 'Failed to update settings');
            },
            complete: function () {
                $('#employer-settings-form button[type="submit"]').prop('disabled', false).text('Save Changes');
            }
        });
    });

    // Delete job
    $(document).on('click', '.delete-job', function () {
        const jobId = $(this).data('id');
        const card = $(this).closest('.job-card');

        if (!confirm('Are you sure you want to delete this job?')) {
            return;
        }

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'skd_delete_job',
                nonce: nonce,
                job_id: jobId
            },
            success: function (response) {
                if (response.success) {
                    card.fadeOut(300, function () {
                        $(this).remove();
                        if ($('.job-card').length === 0) {
                            loadEmployerJobs();
                        }
                    });
                    showAlert('success', response.data.message);
                } else {
                    showAlert('error', response.data.message);
                }
            },
            error: function () {
                showAlert('error', 'Failed to delete job');
            }
        });
    });

    // View VDA profile
    $(document).on('click', '.view-vda-profile', function () {
        const vdaId = $(this).data('id');
        window.open('/vda-profile?id=' + vdaId, '_blank');
    });

    // Helper function to show alerts
    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
        const alertHtml = `<div class="employer-alert ${alertClass}">${message}</div>`;

        // Remove existing alerts
        $('.employer-alert').remove();

        // Add new alert to the top of active tab
        $('.employer-tab-content.active').prepend(alertHtml);

        // Auto-hide after 5 seconds
        setTimeout(function () {
            $('.employer-alert').fadeOut(300, function () {
                $(this).remove();
            });
        }, 5000);
    }

    // Load initial tab content
    const activeTab = $('.employer-menu-item.active').data('tab');
    if (activeTab) {
        loadTabContent(activeTab);
    }
});
