jQuery(document).ready(function ($) {

    $('.shortingTopbar_headericon').on('click', function () {
        $('.mobFilter').toggleClass('activeFilter');
    });

    // // readmore read less jquery code
    // $('.readmoreBtn').click(function () {
    //     $('.readMoreBox').slideToggle();
    //     if ($('.readmoreBtn').text() == "Show More") {
    //         $(this).text("Show less")
    //     } else {
    //         $(this).text("Show more")
    //     }
    // });

    // // input value clear button jquery code

    // $(document).ready(function () {
    //     $('.inputBox').on('input', function () {
    //         if ($(this).val().length > 0) {
    //             $('.cleareBtn').show();
    //         } else {
    //             $('.cleareBtn').hide();
    //         }
    //     });

    //     $('.cleareBtn').on('click', function () {
    //         $('.inputBox').val('');
    //         $(this).hide();
    //     });
    // });

    // scroll or click tab sectio code 

    $(document).ready(function () {
        const tabButtons = $('.fixedtabInner__right__btn');
        const sections = $('.sidebarInnerBox');

        // Smooth scrolling
        tabButtons.on('click', function (e) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: $($.attr(this, 'href')).offset().top - 10
            }, 500);
        });

        // Add activeTab class on scroll
        $(window).on('scroll', function () {
            let current = '';
            sections.each(function () {
                const sectionTop = $(this).offset().top;
                if ($(window).scrollTop() >= sectionTop - 60) {
                    current = $(this).attr('id');
                }
            });

            tabButtons.removeClass('activeTab');
            tabButtons.each(function () {
                if ($(this).attr('href') === `#${current}`) {
                    $(this).addClass('activeTab');
                }
            });
        });
    });

    $(document).ready(function () {
        function isMobileOrTablet() {
            return $(window).width() <= 768; // Adjust 1024 as needed for your tablet breakpoint
        }

        function setupAccordion() {
            if (isMobileOrTablet()) {
                // Accordion functionality for mobile and tablet
                $('.sidebarInnerBox__header').click(function () {
                    var body = $(this).next('.sidebarInnerBox__Body');
                    var allBodies = $('.sidebarInnerBox__Body');

                    if (body.is(':visible')) {
                        body.slideUp();
                    } else {
                        allBodies.slideUp();
                        body.slideDown();
                    }
                });

                // Initial state: Close all bodies except the first one (optional)
                $('.sidebarInnerBox__Body').not(':first').hide();
                //if you want to open the first one by default, uncomment the below line.
                //$('.sidebarInnerBox__Body:first').show();

            } else {
                // Remove click events and show all bodies on desktop.
                $('.sidebarInnerBox__header').off('click');
                $('.sidebarInnerBox__Body').show();
            }
        }

        // Initial setup
        setupAccordion();

        // Re-run setup on window resize
        $(window).resize(setupAccordion);
    });

    // ==============================================
    // Professionals Directory - Filter & Search
    // ==============================================

    if ($('#skd-professionals-filter-form').length) {

        let searchTimeout;
        const $grid = $('#skd-professionals-grid');
        const $loading = $('#skd-professionals-loading');
        const $count = $('#skd-professionals-count');
        const $pagination = $('#skd-professionals-pagination');

        // Collapsible filter sections
        $('.skd-filter-title').on('click', function () {
            const $section = $(this).closest('.skd-collapsible');
            const $content = $section.find('.skd-filter-content');
            const $icon = $(this).find('.skd-toggle-icon');

            $content.slideToggle(300);
            $icon.text($icon.text() === '−' ? '+' : '−');
        });

        // Price preset buttons
        $('.skd-price-preset').on('click', function (e) {
            e.preventDefault();
            const min = $(this).data('min');
            const max = $(this).data('max');
            $('input[name="min_rate"]').val(min);
            $('input[name="max_rate"]').val(max);
            filterProfessionals();
        });

        // Search with debounce
        $('#skd-search-professionals').on('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function () {
                filterProfessionals();
            }, 500);
        });

        // Sort dropdown
        $('#skd-sort-professionals').on('change', function () {
            filterProfessionals();
        });

        // View toggle (grid/list)
        $('.skd-view-btn').on('click', function () {
            const view = $(this).data('view');
            $('.skd-view-btn').removeClass('active');
            $(this).addClass('active');

            if (view === 'list') {
                $grid.removeClass('skd-grid-view').addClass('skd-list-view');
            } else {
                $grid.removeClass('skd-list-view').addClass('skd-grid-view');
            }
        });

        // Clear all filters
        $('.skd-clear-filters').on('click', function (e) {
            e.preventDefault();
            $('#skd-professionals-filter-form')[0].reset();
            filterProfessionals();
        });

        // Form submit
        $('#skd-professionals-filter-form').on('submit', function (e) {
            e.preventDefault();
            filterProfessionals();
        });

        // Main filter function
        function filterProfessionals(page = 1) {
            const formData = {
                action: 'skd_filter_professionals',
                search: $('#skd-search-professionals').val(),
                skills: $('input[name="skills[]"]:checked').map(function () { return $(this).val(); }).get(),
                services: $('input[name="services[]"]:checked').map(function () { return $(this).val(); }).get(),
                specializations: $('input[name="specializations[]"]:checked').map(function () { return $(this).val(); }).get(),
                experience: $('input[name="experience[]"]:checked').map(function () { return $(this).val(); }).get(),
                locations: $('input[name="locations[]"]:checked').map(function () { return $(this).val(); }).get(),
                availability: $('input[name="availability[]"]:checked').map(function () { return $(this).val(); }).get(),
                user_type: $('input[name="user_type[]"]:checked').map(function () { return $(this).val(); }).get(),
                min_rate: $('input[name="min_rate"]').val() || 0,
                max_rate: $('input[name="max_rate"]').val() || 999999,
                verified: $('input[name="verified"]').is(':checked') ? 1 : 0,
                top_rated: $('input[name="top_rated"]').is(':checked') ? 1 : 0,
                featured: $('input[name="featured"]').is(':checked') ? 1 : 0,
                rising_talent: $('input[name="rising_talent"]').is(':checked') ? 1 : 0,
                sort_by: $('#skd-sort-professionals').val(),
                page: page,
                per_page: 24
            };

            // Show loading
            $loading.fadeIn();
            $grid.css('opacity', '0.5');

            $.ajax({
                url: skd_ajax_object.ajax_url,
                type: 'POST',
                data: formData,
                success: function (response) {
                    if (response.success) {
                        updateProfessionalsGrid(response.data);
                    } else {
                        console.error('Filter error:', response.data);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX error:', error);
                },
                complete: function () {
                    $loading.fadeOut();
                    $grid.css('opacity', '1');
                }
            });
        }

        // Update grid with results
        function updateProfessionalsGrid(data) {
            const professionals = data.professionals;
            const totalCount = data.total_count;
            const currentPage = data.current_page;
            const totalPages = data.total_pages;

            // Update count
            $count.text(totalCount + ' professional' + (totalCount !== 1 ? 's' : '') + ' found');

            // Clear grid
            $grid.empty();

            if (professionals.length === 0) {
                $grid.html(`
                    <div class="skd-no-results">
                        <iconify-icon icon="mdi:account-search" class="skd-no-results-icon"></iconify-icon>
                        <h3>No professionals found</h3>
                        <p>Try adjusting your filters or search criteria</p>
                    </div>
                `);
                $pagination.empty();
                return;
            }

            // Render professional cards
            professionals.forEach(function (prof) {
                const card = renderProfessionalCard(prof);
                $grid.append(card);
            });

            // Render pagination
            renderPagination(currentPage, totalPages);

            // Scroll to top of results
            $('html, body').animate({
                scrollTop: $('.skd-results-header').offset().top - 100
            }, 400);
        }

        // Render individual professional card
        function renderProfessionalCard(prof) {
            let skillsHtml = '';
            if (prof.skills && prof.skills.length > 0) {
                skillsHtml = '<div class="skd-card-skills">';
                prof.skills.forEach(function (skill) {
                    skillsHtml += `<span class="skd-skill-tag">${escapeHtml(skill)}</span>`;
                });
                skillsHtml += '</div>';
            }

            let portfolioHtml = '';
            if (prof.portfolio_samples && prof.portfolio_samples.length > 0) {
                portfolioHtml = '<div class="skd-card-portfolio-preview">';
                prof.portfolio_samples.forEach(function (img) {
                    portfolioHtml += `<div class="skd-portfolio-thumb"><img src="${escapeHtml(img)}" alt="Portfolio"></div>`;
                });
                if (prof.portfolio_count > 3) {
                    portfolioHtml += `<div class="skd-portfolio-more">+${prof.portfolio_count - 3}</div>`;
                }
                portfolioHtml += '</div>';
            }

            let ratingHtml = '';
            if (prof.rating > 0) {
                const stars = generateStars(prof.rating);
                ratingHtml = `
                    <div class="skd-rating">
                        <span class="skd-rating-stars">${stars}</span>
                        <span class="skd-rating-value">${prof.rating.toFixed(1)}</span>
                        <span class="skd-rating-count">(${prof.total_reviews})</span>
                    </div>
                `;
            }

            return `
                <div class="skd-professional-card" data-user-id="${prof.id}">
                    <div class="skd-card-header">
                        <div class="skd-card-avatar">
                            <img src="${escapeHtml(prof.avatar)}" alt="${escapeHtml(prof.name)}">
                            ${prof.is_verified ? '<span class="skd-verified-badge" title="Verified"><iconify-icon icon="mdi:check-decagram"></iconify-icon></span>' : ''}
                        </div>
                        ${prof.is_featured ? '<span class="skd-featured-ribbon">Featured</span>' : ''}
                    </div>
                    <div class="skd-card-body">
                        <h3 class="skd-card-name">
                            <a href="${escapeHtml(prof.profile_url)}">${escapeHtml(prof.name)}</a>
                        </h3>
                        ${prof.user_type === 'studio' ? '<span class="skd-card-type skd-type-studio"><iconify-icon icon="mdi:office-building"></iconify-icon> Studio/Agency</span>' : ''}
                        ${prof.tagline ? `<p class="skd-card-tagline">${escapeHtml(prof.tagline)}</p>` : ''}
                        <div class="skd-card-stats">${ratingHtml}</div>
                        <div class="skd-card-meta">
                            ${prof.location ? `<span class="skd-meta-item skd-location"><iconify-icon icon="mdi:map-marker"></iconify-icon> ${escapeHtml(prof.location)}</span>` : ''}
                            ${prof.experience_level ? `<span class="skd-meta-item skd-experience"><iconify-icon icon="mdi:briefcase"></iconify-icon> ${escapeHtml(formatExperience(prof.experience_level))}</span>` : ''}
                        </div>
                        ${prof.hourly_rate > 0 ? `<div class="skd-card-rate"><span class="skd-rate-amount">$${prof.hourly_rate.toFixed(0)}</span><span class="skd-rate-unit">/hour</span></div>` : ''}
                        ${skillsHtml}
                        ${portfolioHtml}
                    </div>
                    <div class="skd-card-footer">
                        <a href="${escapeHtml(prof.profile_url)}" class="skd-btn skd-btn-outline skd-btn-sm">View Profile</a>
                        <button type="button" class="skd-btn skd-btn-primary skd-btn-sm skd-contact-btn" data-user-id="${prof.id}">
                            <iconify-icon icon="mdi:message-text"></iconify-icon> Contact
                        </button>
                    </div>
                </div>
            `;
        }

        // Generate star rating HTML
        function generateStars(rating) {
            let stars = '';
            for (let i = 1; i <= 5; i++) {
                if (i <= Math.floor(rating)) {
                    stars += '<iconify-icon icon="mdi:star"></iconify-icon>';
                } else if (i - 0.5 <= rating) {
                    stars += '<iconify-icon icon="mdi:star-half-full"></iconify-icon>';
                } else {
                    stars += '<iconify-icon icon="mdi:star-outline"></iconify-icon>';
                }
            }
            return stars;
        }

        // Format experience level
        function formatExperience(level) {
            return level.replace(/_/g, ' ').replace(/\b\w/g, function (l) { return l.toUpperCase(); });
        }

        // Render pagination
        function renderPagination(currentPage, totalPages) {
            if (totalPages <= 1) {
                $pagination.empty();
                return;
            }

            let html = '<div class="skd-pagination-wrapper">';

            // Previous button
            if (currentPage > 1) {
                html += `<button class="skd-page-btn skd-page-prev" data-page="${currentPage - 1}">Previous</button>`;
            }

            // Page numbers
            html += '<div class="skd-page-numbers">';
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                    html += `<button class="skd-page-btn ${i === currentPage ? 'active' : ''}" data-page="${i}">${i}</button>`;
                } else if (i === currentPage - 3 || i === currentPage + 3) {
                    html += '<span class="skd-page-dots">...</span>';
                }
            }
            html += '</div>';

            // Next button
            if (currentPage < totalPages) {
                html += `<button class="skd-page-btn skd-page-next" data-page="${currentPage + 1}">Next</button>`;
            }

            html += '</div>';
            $pagination.html(html);

            // Bind pagination click events
            $('.skd-page-btn').on('click', function () {
                const page = parseInt($(this).data('page'));
                filterProfessionals(page);
            });
        }

        // Escape HTML helper
        function escapeHtml(text) {
            if (!text) return '';
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return String(text).replace(/[&<>"']/g, function (m) { return map[m]; });
        }

        // Contact button handler
        $(document).on('click', '.skd-contact-btn', function () {
            const userId = $(this).data('user-id');
            // TODO: Implement contact/message modal
            alert('Contact functionality coming soon for user ID: ' + userId);
        });

    }

    // ========================================
    // User Dashboard Functionality
    // ========================================

    // Global toggle password visibility (works for all password fields)
    $(document).on('click', '.skd-toggle-password', function (e) {
        e.preventDefault();
        var $btn = $(this);
        var $wrapper = $btn.closest('.skd-password-wrapper');
        var $input = $wrapper.find('input');
        var $eyeIcon = $btn.find('.eye-icon');
        var $eyeSlashIcon = $btn.find('.eye-slash-icon');

        if ($input.attr('type') === 'password') {
            $input.attr('type', 'text');
            $eyeIcon.hide();
            $eyeSlashIcon.show();
        } else {
            $input.attr('type', 'password');
            $eyeIcon.show();
            $eyeSlashIcon.hide();
        }
    });

    // Logout handler
    $(document).on('click', '.skd-logout-btn, #skd-logout-btn', function (e) {
        e.preventDefault();

        Swal.fire({
            title: 'Are you sure?',
            text: "You will be logged out of your account",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#667eea',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, logout',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Logging out...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: skd_ajax_object.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'skd_logout_user'
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Logged out!',
                                text: response.data.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = response.data.redirect;
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to logout. Please try again.'
                        });
                    }
                });
            }
        });
    });

    // Password change form handler
    $('#skd-change-password-form').on('submit', function (e) {
        e.preventDefault();

        const $form = $(this);
        const $submitBtn = $form.find('button[type="submit"]');
        const $message = $('#skd-password-message');

        $submitBtn.prop('disabled', true).text('Changing...');
        $message.html('');

        $.ajax({
            url: skd_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'skd_change_password',
                nonce: skd_ajax_object.nonce,
                current_password: $('#current_password').val(),
                new_password: $('#new_password').val(),
                confirm_password: $('#confirm_password').val()
            },
            success: function (response) {
                if (response.success) {
                    $message.html('<div class="skd-alert skd-alert-success">' + response.data.message + '</div>');
                    $form[0].reset();
                } else {
                    $message.html('<div class="skd-alert skd-alert-error">' + response.data.message + '</div>');
                }
            },
            error: function () {
                $message.html('<div class="skd-alert skd-alert-error">An error occurred. Please try again.</div>');
            },
            complete: function () {
                $submitBtn.prop('disabled', false).text('Change Password');
            }
        });
    });

    // Forgot password form handler
    $('#skd-forgot-password-form').on('submit', function (e) {
        e.preventDefault();

        const $form = $(this);
        const $submitBtn = $form.find('button[type="submit"]');
        const $message = $('#skd-forgot-message');

        $submitBtn.prop('disabled', true).text('Sending...');
        $message.html('');

        $.ajax({
            url: skd_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'skd_forgot_password',
                nonce: skd_ajax_object.nonce,
                email: $('#forgot_email').val()
            },
            success: function (response) {
                if (response.success) {
                    let message = '<div class="skd-alert skd-alert-success">' + response.data.message + '</div>';

                    // For development: Show reset link
                    if (response.data.reset_url) {
                        message += '<div class="skd-alert skd-alert-success" style="margin-top:10px;">Reset Link (Dev): <a href="' + response.data.reset_url + '">' + response.data.reset_url + '</a></div>';
                    }

                    $message.html(message);
                    $form[0].reset();
                } else {
                    $message.html('<div class="skd-alert skd-alert-error">' + response.data.message + '</div>');
                }
            },
            error: function () {
                $message.html('<div class="skd-alert skd-alert-error">An error occurred. Please try again.</div>');
            },
            complete: function () {
                $submitBtn.prop('disabled', false).text('Send Reset Link');
            }
        });
    });

    // Reset password form handler
    $('#skd-reset-password-form').on('submit', function (e) {
        e.preventDefault();

        const $form = $(this);
        const $submitBtn = $form.find('button[type="submit"]');
        const $message = $('#skd-reset-message');

        $submitBtn.prop('disabled', true).text('Resetting...');
        $message.html('');

        // Get key and login from URL
        const urlParams = new URLSearchParams(window.location.search);
        const key = urlParams.get('key');
        const login = urlParams.get('login');

        $.ajax({
            url: skd_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'skd_reset_password',
                nonce: skd_ajax_object.nonce,
                key: key,
                login: login,
                password: $('#reset_password').val(),
                confirm_password: $('#reset_confirm_password').val()
            },
            success: function (response) {
                if (response.success) {
                    $message.html('<div class="skd-alert skd-alert-success">' + response.data.message + '</div>');
                    setTimeout(function () {
                        window.location.href = response.data.redirect;
                    }, 2000);
                } else {
                    $message.html('<div class="skd-alert skd-alert-error">' + response.data.message + '</div>');
                }
            },
            error: function () {
                $message.html('<div class="skd-alert skd-alert-error">An error occurred. Please try again.</div>');
            },
            complete: function () {
                $submitBtn.prop('disabled', false).text('Reset Password');
            }
        });
    });

});


