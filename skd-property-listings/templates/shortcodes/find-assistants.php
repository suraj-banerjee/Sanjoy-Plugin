<?php

/**
 * Template for Find Assistants Directory
 * Fully dynamic VDA listing page with comprehensive filters
 * All data pulled from admin-managed master tables
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

// Get all filter data from database
$software = SKD_PL_VDA_Skills::get_skills(['status' => 'active']);
$timezones = SKD_PL_Timezones::get_timezones(['status' => 'active']);
$availability_types = SKD_PL_Availability_Types::get_availability_types(['status' => 'active']);
$experience_levels = SKD_PL_Experience_Levels::get_experience_levels(['status' => 'active']);
$response_times = SKD_PL_Response_Times::get_response_times(['status' => 'active']);
$languages = SKD_PL_Languages::get_languages(['status' => 'active']);
$project_types = SKD_PL_VDA_Project_Types::get_project_types(['status' => 'active']);
$service_types = SKD_PL_VDA_Service_Types::get_service_types(['status' => 'active']);
$certifications = SKD_PL_Certifications::get_certifications(['status' => 'active']);

// Get initial professionals (will be filtered via AJAX)
$profiles_table = $wpdb->prefix . 'skd_pl_user_profiles';
$users_table = $wpdb->prefix . 'users';

$per_page = 12;
$current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
$offset = ($current_page - 1) * $per_page;

// Count total professionals
$total_count = $wpdb->get_var("
    SELECT COUNT(DISTINCT p.id)
    FROM $profiles_table p
    INNER JOIN $users_table u ON p.user_id = u.ID
    WHERE p.user_type = 'vda'
");

$total_pages = ceil($total_count / $per_page);

// Get professionals for current page
$initial_query = $wpdb->prepare("
    SELECT 
        p.*,
        u.display_name,
        u.user_email,
        u.ID as user_wp_id
    FROM $profiles_table p
    INNER JOIN $users_table u ON p.user_id = u.ID
    WHERE p.user_type = 'vda'
    ORDER BY p.is_featured DESC, p.is_verified DESC, p.rating DESC, p.created_at DESC
    LIMIT %d OFFSET %d
", $per_page, $offset);

$professionals = $wpdb->get_results($initial_query);
?>

<div class="skd-find-assistants-page">
    <div class="skd-assistants-container">
        <div class="skd-assistants-layout">

            <!-- Left Sidebar - Filters -->
            <aside class="skd-filters-sidebar">
                <div class="skd-filters-header">
                    <h3>Filters</h3>
                    <button type="button" class="skd-btn-clear-filters" id="clear-all-filters">Clear</button>
                </div>

                <form id="assistants-filter-form" method="get">

                    <!-- Skills & Software -->
                    <div class="skd-filter-group">
                        <h4 class="skd-filter-title">SKILLS & SOFTWARE</h4>
                        <div class="skd-filter-content">
                            <div class="skd-filter-search">
                                <input type="text" class="skd-filter-search-input" placeholder="Search software..." id="search-software">
                            </div>
                            <div class="skd-filter-options" id="software-options">
                                <?php foreach ($software as $skill): ?>
                                    <label class="skd-checkbox-label">
                                        <input type="checkbox" name="software[]" value="<?php echo esc_attr($skill->slug); ?>">
                                        <span><?php echo esc_html($skill->name); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Experience Level -->
                    <div class="skd-filter-group">
                        <h4 class="skd-filter-title">EXPERIENCE</h4>
                        <div class="skd-filter-content">
                            <?php foreach ($experience_levels as $level): ?>
                                <?php
                                $range = $level->years_max ? "{$level->years_min}-{$level->years_max}" : "{$level->years_min}+";
                                ?>
                                <label class="skd-checkbox-label">
                                    <input type="checkbox" name="experience_level[]" value="<?php echo esc_attr($level->slug); ?>">
                                    <span><?php echo esc_html($level->name); ?> (<?php echo $range; ?> yrs)</span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Hourly Rate -->
                    <div class="skd-filter-group">
                        <h4 class="skd-filter-title">HOURLY RATE</h4>
                        <div class="skd-filter-content">
                            <label class="skd-checkbox-label">
                                <input type="checkbox" name="hourly_rate[]" value="0-10">
                                <span>
                                    < $10/hr</span>
                            </label>
                            <label class="skd-checkbox-label">
                                <input type="checkbox" name="hourly_rate[]" value="10-15">
                                <span>$10–$15/hr</span>
                            </label>
                            <label class="skd-checkbox-label">
                                <input type="checkbox" name="hourly_rate[]" value="15-25">
                                <span>$15–$25/hr</span>
                            </label>
                            <label class="skd-checkbox-label">
                                <input type="checkbox" name="hourly_rate[]" value="25-40">
                                <span>$25–$40/hr</span>
                            </label>
                            <label class="skd-checkbox-label">
                                <input type="checkbox" name="hourly_rate[]" value="40-60">
                                <span>$40–$60/hr</span>
                            </label>
                            <label class="skd-checkbox-label">
                                <input type="checkbox" name="hourly_rate[]" value="60+">
                                <span>$60+</span>
                            </label>
                        </div>
                    </div>

                    <!-- Availability -->
                    <div class="skd-filter-group">
                        <h4 class="skd-filter-title">AVAILABILITY</h4>
                        <div class="skd-filter-content">
                            <?php foreach ($availability_types as $type): ?>
                                <label class="skd-checkbox-label">
                                    <input type="checkbox" name="availability[]" value="<?php echo esc_attr($type->slug); ?>">
                                    <span><?php echo esc_html($type->name); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Timezone -->
                    <div class="skd-filter-group">
                        <h4 class="skd-filter-title">LOCATION</h4>
                        <div class="skd-filter-content">
                            <select name="timezone" class="skd-select-filter">
                                <option value="">Any Time Zone</option>
                                <?php foreach ($timezones as $tz): ?>
                                    <option value="<?php echo esc_attr($tz->value); ?>"><?php echo esc_html($tz->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Badges & Verification -->
                    <div class="skd-filter-group">
                        <h4 class="skd-filter-title">BADGES & VERIFICATION</h4>
                        <div class="skd-filter-content">
                            <?php foreach ($certifications as $cert): ?>
                                <label class="skd-checkbox-label">
                                    <input type="checkbox" name="badges[]" value="<?php echo esc_attr($cert->slug); ?>">
                                    <span><?php echo esc_html($cert->name); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Project Type -->
                    <div class="skd-filter-group">
                        <h4 class="skd-filter-title">PROJECT TYPE</h4>
                        <div class="skd-filter-content">
                            <?php foreach ($project_types as $type): ?>
                                <label class="skd-checkbox-label">
                                    <input type="checkbox" name="project_type[]" value="<?php echo esc_attr($type->slug); ?>">
                                    <span><?php echo esc_html($type->name); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Service Type -->
                    <div class="skd-filter-group">
                        <h4 class="skd-filter-title">SERVICE TYPE</h4>
                        <div class="skd-filter-content">
                            <?php foreach ($service_types as $type): ?>
                                <label class="skd-checkbox-label">
                                    <input type="checkbox" name="service_type[]" value="<?php echo esc_attr($type->slug); ?>">
                                    <span><?php echo esc_html($type->name); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Apply Filters Button -->
                    <div class="skd-filter-actions">
                        <!-- <button type="submit" class="skd-btn skd-btn-primary skd-btn-block">Apply</button> -->
                        <button type="button" class="skd-btn skd-btn-secondary skd-btn-block" id="clear-filters">Clear</button>
                    </div>

                </form>
            </aside>

            <!-- Main Content Area -->
            <main class="skd-assistants-content">

                <!-- Results Header with Sort -->
                <div class="skd-results-header">
                    <div class="skd-results-count">
                        <strong>Showing <span id="results-count"><?php echo count($professionals); ?></span> professionals</strong>
                    </div>
                    <div class="skd-results-sort">
                        <label>Sort: </label>
                        <select name="sort_by" id="sort-professionals" class="skd-select">
                            <option value="newest" selected>Most Recent</option>
                            <option value="rating">Highest Rated</option>
                            <option value="rate_low">Lowest Price</option>
                            <option value="rate_high">Highest Price</option>
                        </select>
                    </div>
                </div>

                <!-- Professionals Grid -->
                <div class="skd-professionals-grid" id="professionals-grid">
                    <!-- Loading Overlay -->
                    <div class="skd-grid-loader" id="grid-loader" style="display: none;">
                        <div class="skd-spinner"></div>
                        <p>Loading...</p>
                    </div>

                    <?php if (!empty($professionals)): ?>
                        <?php foreach ($professionals as $pro): ?>
                            <?php
                            // Get timezone display
                            $timezone_display = 'Remote';
                            if (!empty($pro->timezone)) {
                                $tz = $wpdb->get_row($wpdb->prepare(
                                    "SELECT name, offset FROM {$wpdb->prefix}skd_pl_timezones WHERE id = %d",
                                    $pro->timezone
                                ));
                                if ($tz) {
                                    $timezone_display = $tz->name . ' • ' . $tz->offset;
                                }
                            }

                            // Get skills
                            $pro_skills = json_decode($pro->skills ?? '[]', true);
                            $skills_html = '';
                            if (!empty($pro_skills) && is_array($pro_skills)) {
                                $skills_to_show = array_slice($pro_skills, 0, 4);
                                foreach ($skills_to_show as $skill_id) {
                                    $skill = $wpdb->get_row($wpdb->prepare(
                                        "SELECT name FROM {$wpdb->prefix}skd_pl_skills WHERE id = %d",
                                        $skill_id
                                    ));
                                    if ($skill) {
                                        $skills_html .= '<span class="skd-skill-tag">' . esc_html($skill->name) . '</span>';
                                    }
                                }
                            }

                            // Get specializations for featured skills
                            $specializations = json_decode($pro->specializations ?? '[]', true);
                            $featured_skills = '';
                            if (!empty($specializations) && is_array($specializations)) {
                                $specs_to_show = array_slice($specializations, 0, 2);
                                foreach ($specs_to_show as $spec_id) {
                                    $spec = $wpdb->get_row($wpdb->prepare(
                                        "SELECT name FROM {$wpdb->prefix}skd_pl_specializations WHERE id = %d",
                                        $spec_id
                                    ));
                                    if ($spec) {
                                        $featured_skills .= '<span class="skd-skill-tag-featured"><iconify-icon icon="mdi:check"></iconify-icon> ' . esc_html($spec->name) . '</span>';
                                    }
                                }
                            }

                            // Get project types for icons
                            $project_types = json_decode($pro->project_types ?? '[]', true);
                            $project_icons = '';
                            if (!empty($project_types) && is_array($project_types)) {
                                $icons_map = [
                                    1 => 'mdi:home', // Residential
                                    2 => 'mdi:office-building', // Commercial
                                    3 => 'mdi:silverware-fork-knife', // Hospitality
                                    4 => 'mdi:store', // Retail
                                    5 => 'mdi:pot-steam', // Kitchen & Bath
                                ];
                                $types_to_show = array_slice($project_types, 0, 3);
                                foreach ($types_to_show as $pt_id) {
                                    $icon = $icons_map[$pt_id] ?? 'mdi:cube-outline';
                                    $project_icons .= '<div class="skd-project-icon"><iconify-icon icon="' . $icon . '"></iconify-icon></div>';
                                }
                            }

                            $avatar_url = !empty($pro->avatar_url) ? $pro->avatar_url : get_avatar_url($pro->user_id, ['size' => 200]);
                            $profile_url = home_url('/vda-profile/?vda_id=' . $pro->user_id);
                            ?>
                            <div class="skd-professional-card" data-pro-id="<?php echo $pro->user_id; ?>">
                                <!-- Avatar -->
                                <div class="skd-pro-avatar">
                                    <?php if (!empty($pro->avatar_url)): ?>
                                        <img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($pro->display_name); ?>">
                                    <?php else: ?>
                                        <div class="skd-avatar-placeholder">
                                            <?php echo strtoupper(substr($pro->display_name, 0, 1)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Name & Title -->
                                <div class="skd-pro-header">
                                    <h3 class="skd-pro-name">
                                        <a href="<?php echo esc_url($profile_url); ?>">
                                            <?php echo esc_html($pro->display_name); ?>
                                        </a>
                                        <?php if ($pro->is_verified): ?>
                                            <iconify-icon icon="mdi:check-decagram" class="skd-icon-verified"></iconify-icon>
                                        <?php endif; ?>
                                    </h3>
                                    <p class="skd-pro-title"><?php echo esc_html($pro->tagline ?? 'Individual Designer'); ?></p>
                                </div>

                                <!-- Location & Rate -->
                                <div class="skd-pro-meta">
                                    <span class="skd-pro-location">
                                        <iconify-icon icon="mdi:map-marker"></iconify-icon>
                                        <?php echo esc_html($timezone_display); ?>
                                    </span>
                                    <span class="skd-pro-rate">
                                        <iconify-icon icon="mdi:currency-usd"></iconify-icon>
                                        $<?php echo number_format($pro->hourly_rate ?? 0, 0); ?>/hour
                                    </span>
                                </div>

                                <!-- Description -->
                                <div class="skd-pro-bio">
                                    <?php echo wp_trim_words($pro->bio ?? 'No description available.', 20); ?>
                                </div>

                                <!-- Skills Tags -->
                                <div class="skd-pro-skills">
                                    <?php echo $skills_html; ?>
                                </div>

                                <!-- Featured Skills -->
                                <?php if (!empty($featured_skills)): ?>
                                    <div class="skd-pro-featured-skills">
                                        <?php echo $featured_skills; ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Project Type Icons -->
                                <?php if (!empty($project_icons)): ?>
                                    <div class="skd-pro-project-types">
                                        <?php echo $project_icons; ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Action Buttons -->
                                <div class="skd-pro-actions">
                                    <a href="<?php echo esc_url($profile_url); ?>" class="skd-btn skd-btn-primary">View Profile</a>
                                    <a href="<?php echo esc_url(home_url('/hire-vda/?vda_id=' . $pro->user_id)); ?>" class="skd-btn skd-btn-secondary">Hire / Message</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="skd-no-results">
                            <iconify-icon icon="mdi:account-search"></iconify-icon>
                            <h3>No VDAs found</h3>
                            <p>Try adjusting your filters to see more results.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="skd-pagination">
                        <?php if ($current_page > 1): ?>
                            <a href="?paged=<?php echo ($current_page - 1); ?>" class="skd-page-link skd-page-prev" data-page="<?php echo ($current_page - 1); ?>">
                                <iconify-icon icon="mdi:chevron-left"></iconify-icon> Previous
                            </a>
                        <?php endif; ?>

                        <div class="skd-page-numbers">
                            <?php for ($i = 1; $i <= min($total_pages, 5); $i++): ?>
                                <a href="?paged=<?php echo $i; ?>" class="skd-page-link <?php echo $i === $current_page ? 'active' : ''; ?>" data-page="<?php echo $i; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($total_pages > 5): ?>
                                <span class="skd-page-dots">...</span>
                                <a href="?paged=<?php echo $total_pages; ?>" class="skd-page-link" data-page="<?php echo $total_pages; ?>">
                                    <?php echo $total_pages; ?>
                                </a>
                            <?php endif; ?>
                        </div>

                        <?php if ($current_page < $total_pages): ?>
                            <a href="?paged=<?php echo ($current_page + 1); ?>" class="skd-page-link skd-page-next" data-page="<?php echo ($current_page + 1); ?>">
                                Next <iconify-icon icon="mdi:chevron-right"></iconify-icon>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            </main>

        </div><!-- .skd-assistants-layout -->
    </div><!-- .skd-assistants-container -->
</div>

<script>
    jQuery(document).ready(function($) {
        // Load filters from URL parameters on page load
        function loadFiltersFromURL() {
            const urlParams = new URLSearchParams(window.location.search);

            // Check software/skills checkboxes
            const software = urlParams.getAll('software[]');
            software.forEach(function(id) {
                $('input[name="software[]"][value="' + id + '"]').prop('checked', true);
            });

            // Set timezone
            const timezone = urlParams.get('timezone');
            if (timezone) {
                $('select[name="timezone"]').val(timezone);
            }

            // Set experience level
            const experienceLevel = urlParams.getAll('experience_level[]');
            experienceLevel.forEach(function(level) {
                $('input[name="experience_level[]"][value="' + level + '"]').prop('checked', true);
            });

            // Set hourly rate
            const hourlyRate = urlParams.getAll('hourly_rate[]');
            hourlyRate.forEach(function(rate) {
                $('input[name="hourly_rate[]"][value="' + rate + '"]').prop('checked', true);
            });

            // Set availability
            const availability = urlParams.getAll('availability[]');
            availability.forEach(function(id) {
                $('input[name="availability[]"][value="' + id + '"]').prop('checked', true);
            });

            // Set project type
            const projectType = urlParams.getAll('project_type[]');
            projectType.forEach(function(id) {
                $('input[name="project_type[]"][value="' + id + '"]').prop('checked', true);
            });

            // Set service type
            const serviceType = urlParams.getAll('service_type[]');
            serviceType.forEach(function(id) {
                $('input[name="service_type[]"][value="' + id + '"]').prop('checked', true);
            });

            // Set badges
            const badges = urlParams.getAll('badges[]');
            badges.forEach(function(id) {
                $('input[name="badges[]"][value="' + id + '"]').prop('checked', true);
            });

            // Set rating
            const rating = urlParams.get('rating');
            if (rating) {
                $('input[name="rating"][value="' + rating + '"]').prop('checked', true);
            }

            // Set sort by
            const sortBy = urlParams.get('sort_by');
            if (sortBy) {
                $('#sort-professionals').val(sortBy);
            }

            // If there are filters in URL, trigger filter
            if (window.location.search) {
                filterProfessionals();
            }
        }

        // Real-time filter functionality with URL updates
        function filterProfessionals(page = 1) {
            const formData = $('#assistants-filter-form').serialize();
            const sortBy = $('#sort-professionals').val();

            // Update URL parameters
            const params = new URLSearchParams(formData);
            if (sortBy) params.set('sort_by', sortBy);
            if (page > 1) params.set('paged', page);
            const newUrl = window.location.pathname + '?' + params.toString();
            window.history.pushState({}, '', newUrl);

            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'skd_filter_professionals',
                    filters: formData,
                    sort_by: sortBy,
                    paged: page
                },
                beforeSend: function() {
                    $('#grid-loader').fadeIn(200);
                    $('#professionals-grid > :not(#grid-loader)').css('opacity', '0.3');
                },
                success: function(response) {
                    $('#grid-loader').fadeOut(200);
                    $('#professionals-grid > :not(#grid-loader)').css('opacity', '1');

                    if (response.success) {
                        // Remove all children except loader
                        $('#professionals-grid').children(':not(#grid-loader)').remove();
                        // Append new content after loader
                        $('#professionals-grid').append(response.data.html);
                        $('#results-count').text(response.data.count);

                        // Update or add pagination
                        if (response.data.pagination) {
                            $('.skd-pagination').remove();
                            $('.skd-assistants-content').append(response.data.pagination);
                        } else {
                            $('.skd-pagination').remove();
                        }

                        // Scroll to top of results
                        $('html, body').animate({
                            scrollTop: $('#professionals-grid').offset().top - 100
                        }, 300);
                    } else {
                        $('#professionals-grid').children(':not(#grid-loader)').remove();
                        $('#professionals-grid').append('<div class="skd-no-results"><iconify-icon icon="mdi:account-search"></iconify-icon><h3>No professionals found</h3><p>Try adjusting your filters.</p></div>');
                        $('.skd-pagination').remove();
                    }
                },
                error: function() {
                    $('#grid-loader').fadeOut(200);
                    $('#professionals-grid > :not(#grid-loader)').css('opacity', '1');
                    $('#professionals-grid').children(':not(#grid-loader)').remove();
                    $('#professionals-grid').append('<div class="skd-no-results"><p>Error loading professionals. Please try again.</p></div>');
                }
            });
        }

        // Trigger filter on checkbox/select change
        $('#assistants-filter-form input[type="checkbox"], #assistants-filter-form select').on('change', function() {
            filterProfessionals();
        });

        // Sort change handler
        $('#sort-professionals').on('change', function() {
            filterProfessionals();
        });

        // Pagination click handler (delegated for dynamically added elements)
        $(document).on('click', '.skd-page-link', function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            if (page) {
                filterProfessionals(page);
            }
        });

        // Clear filters handler
        $(document).on('click', '.skd-page-link', function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            if (page) {
                filterProfessionals(page);
            }
        });

        // Search within software filter
        $('#search-software').on('keyup', function() {
            const searchTerm = $(this).val().toLowerCase();
            $('#software-options label').each(function() {
                const text = $(this).text().toLowerCase();
                $(this).toggle(text.includes(searchTerm));
            });
        });

        // Clear all filters
        $('#clear-all-filters, #clear-filters').on('click', function(e) {
            e.preventDefault();
            $('#assistants-filter-form')[0].reset();
            window.history.pushState({}, '', window.location.pathname);
            filterProfessionals();
        });

        // Prevent form submission
        $('#assistants-filter-form').on('submit', function(e) {
            e.preventDefault();
            return false;
        });

        // Load filters from URL on page load
        loadFiltersFromURL();
    });
</script>